<?php

namespace App\Services;

use App\Jobs\ProcessAuditLogJob;
use App\Models\BalanceAccount;
use App\Models\BalanceTransaction;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PosService
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Process POS checkout atomically inside a DB Transaction.
     *
     * @param  array  $cartItems  format: [['product' => Product, 'quantity' => int], ...]
     * @param  array  $paymentsData  format: [['payment_method_id' => int, 'balance_account_id' => ?int, 'amount' => float, 'reference_number' => ?string], ...]
     */
    public function processCheckout(
        User $cashier,
        array $cartItems,
        array $paymentsData,
        ?string $notes = null,
        ?string $idempotencyKey = null
    ): Sale {
        $lockKey = $idempotencyKey ? 'checkout_idempotency_'.$idempotencyKey : 'checkout_cashier_'.$cashier->id;
        $lock = Cache::lock($lockKey, 10);
        if (! $lock->get()) {
            throw new InvalidArgumentException('Transaksi sedang diproses. Mohon tunggu sejenak.');
        }

        try {
            if ($idempotencyKey) {
                $existingSale = Sale::where('idempotency_key', $idempotencyKey)->first();
                if ($existingSale) {
                    return $existingSale;
                }
            }

            return $this->executeCheckoutProcess(
                $cashier,
                $cartItems,
                $paymentsData,
                $notes,
                $idempotencyKey
            );
        } finally {
            $lock->release();
        }
    }

    protected function executeCheckoutProcess(
        User $cashier,
        array $cartItems,
        array $paymentsData,
        ?string $notes = null,
        ?string $idempotencyKey = null
    ): Sale {
        if (empty($cartItems)) {
            throw new InvalidArgumentException('Keranjang belanja kosong.');
        }

        if (empty($paymentsData)) {
            throw new InvalidArgumentException('Metode pembayaran wajib diisi.');
        }

        $location = $cashier->location;

        if (! $location || $location->status !== 'ACTIVE') {
            throw new InvalidArgumentException('Lokasi kerja kasir belum diatur atau tidak aktif.');
        }

        // 1. Validate items (Price Complete & Stock sufficiency)
        $subtotal = 0;
        $totalCost = 0;
        $validatedItems = [];

        foreach ($cartItems as $item) {
            /** @var Product $product */
            $product = $item['product'];
            $qty = (int) $item['quantity'];

            if ($qty <= 0) {
                continue;
            }

            $isService = $product->product_type === 'LAYANAN';
            $costPrice = $isService ? (float) ($item['cost_price'] ?? 0) : (float) $product->cost_price;
            $sellingPrice = $isService ? (float) ($item['price'] ?? 0) : (float) $product->selling_price;
            $nameSnapshot = $isService && ! empty($item['name']) ? $item['name'] : $product->name;

            // Block INCOMPLETE price status if no valid custom price provided
            if ($product->price_status === 'INCOMPLETE' && $sellingPrice <= 0) {
                throw new InvalidArgumentException("Produk '{$product->name}' memiliki status harga INCOMPLETE (modal/jual 0). Harap lengkapi harga sebelum checkout.");
            }

            // [CELAH #7] LAYANAN server-side final guard
            if ($product->product_type === 'LAYANAN') {
                // Minimum nominal Rp 1.000
                if ($sellingPrice < 1000) {
                    throw new InvalidArgumentException("Nominal layanan '{$product->name}' terlalu kecil (minimum Rp 1.000).");
                }
                // Margin must be positive: selling >= cost
                if ($sellingPrice < $costPrice) {
                    throw new InvalidArgumentException("Margin negatif terdeteksi pada '{$product->name}'. Harga jual tidak boleh lebih kecil dari modal.");
                }
                // Quantity must be 1 per line
                if ($qty > 1) {
                    throw new InvalidArgumentException("Item layanan '{$product->name}' hanya bisa 1 transaksi per baris.");
                }
            }

            // Validate physical stock availability (PRD Bab 7.2)
            if ($product->product_type === 'PHYSICAL') {
                $availableStock = Inventory::where('product_id', $product->id)
                    ->where('location_id', $location->id)
                    ->value('quantity') ?? 0;

                if ($availableStock < $qty) {
                    throw new InvalidArgumentException("Stok produk '{$product->name}' tidak mencukupi (Tersedia: {$availableStock}, Diminta: {$qty}).");
                }
            }

            $itemSubtotal = $sellingPrice * $qty;
            $itemCostSubtotal = $costPrice * $qty;

            $subtotal += $itemSubtotal;
            $totalCost += $itemCostSubtotal;

            $validatedItems[] = [
                'product' => $product,
                'quantity' => $qty,
                'name' => $nameSnapshot,
                'cost_price' => $costPrice,
                'selling_price' => $sellingPrice,
                'subtotal' => $itemSubtotal,
            ];
        }

        if (empty($validatedItems)) {
            throw new InvalidArgumentException('Keranjang belanja tidak memiliki item valid.');
        }

        $totalAmount = $subtotal;
        $validatedPayments = [];
        $totalPaid = 0;

        foreach ($paymentsData as $paymentData) {
            $amount = (float) ($paymentData['amount'] ?? 0);
            $paymentMethodId = $paymentData['payment_method_id'] ?? null;
            $paymentMethod = $paymentMethodId ? PaymentMethod::where('status', 'ACTIVE')->find($paymentMethodId) : null;

            if (! $paymentMethod) {
                throw new InvalidArgumentException('Metode pembayaran tidak valid atau tidak aktif.');
            }

            if ($amount <= 0) {
                throw new InvalidArgumentException('Nominal pembayaran harus lebih dari 0.');
            }

            $balanceAccountId = $paymentData['balance_account_id'] ?? null;
            if (! $balanceAccountId && $paymentMethod->type === 'CASH') {
                $balanceAccountId = BalanceAccount::forUserLocation($cashier)->where('code', 'CASH')->where('status', 'ACTIVE')->value('id')
                    ?? BalanceAccount::forUserLocation($cashier)->where('account_type', 'CASH')->where('status', 'ACTIVE')->value('id');
            } elseif (! $balanceAccountId && $paymentMethod->type === 'QRIS') {
                $balanceAccountId = BalanceAccount::forUserLocation($cashier)->where('code', 'QRIS')->where('status', 'ACTIVE')->value('id')
                    ?? BalanceAccount::forUserLocation($cashier)->where('account_type', 'QRIS')->where('status', 'ACTIVE')->value('id');
            }

            if (! $balanceAccountId) {
                throw new InvalidArgumentException("Akun tujuan pembayaran {$paymentMethod->name} wajib dipilih.");
            }

            $balanceAccount = BalanceAccount::forUserLocation($cashier)->where('status', 'ACTIVE')->find($balanceAccountId);
            if (! $balanceAccount) {
                throw new InvalidArgumentException('Akun saldo pembayaran tidak valid, tidak aktif, atau di luar lokasi cabang Anda.');
            }

            $validAccountTypes = match ($paymentMethod->type) {
                'CASH' => ['CASH'],
                'QRIS' => ['QRIS'],
                'TRANSFER' => ['BANK'],
                'E_WALLET' => ['E_WALLET'],
                default => [],
            };

            if ($validAccountTypes && ! in_array($balanceAccount->account_type, $validAccountTypes, true)) {
                throw new InvalidArgumentException("Akun saldo tidak sesuai untuk metode pembayaran {$paymentMethod->name}.");
            }

            $validatedPayments[] = [
                'payment_method' => $paymentMethod,
                'balance_account_id' => $balanceAccount->id,
                'amount' => $amount,
                'change_amount' => 0,
                'reference_number' => $paymentData['reference_number'] ?? null,
            ];
            $totalPaid += $amount;
        }

        if ($totalPaid < $totalAmount) {
            throw new InvalidArgumentException('Jumlah pembayaran (Rp'.number_format($totalPaid, 0, ',', '.').') kurang dari total belanja (Rp'.number_format($totalAmount, 0, ',', '.').').');
        }

        $changeAmount = $totalPaid - $totalAmount;
        $cashPayments = collect($validatedPayments)->filter(fn ($payment) => $payment['payment_method']->type === 'CASH');
        $cashTendered = $cashPayments->sum('amount');
        if ($changeAmount > 0 && $cashTendered < $changeAmount) {
            throw new InvalidArgumentException('Kembalian hanya dapat diberikan dari pembayaran tunai.');
        }
        if ($changeAmount > 0) {
            $remainingChange = $changeAmount;
            foreach ($validatedPayments as $index => $payment) {
                if ($payment['payment_method']->type !== 'CASH' || $remainingChange <= 0) {
                    continue;
                }

                $allocatedChange = min($payment['amount'], $remainingChange);
                $validatedPayments[$index]['change_amount'] = $allocatedChange;
                $remainingChange -= $allocatedChange;
            }

            if ($remainingChange > 0) {
                throw new InvalidArgumentException('Akun kas untuk kembalian tidak tersedia.');
            }
        }
        $grossProfit = $totalAmount - $totalCost;

        // 2. Perform Atomic Transaction
        $sale = DB::transaction(function () use (
            $cashier,
            $location,
            $validatedItems,
            $validatedPayments,
            $subtotal,
            $totalAmount,
            $totalPaid,
            $changeAmount,
            $totalCost,
            $grossProfit,
            $notes,
            $idempotencyKey
        ) {
            // Generate Transaction Number server-side with retry collision protection
            $attempts = 0;
            do {
                $datePrefix = date('Ymd');
                $invoiceNumber = 'TRX-'.$datePrefix.'-'.Str::upper(Str::random(6));
                $exists = Sale::where('invoice_number', $invoiceNumber)->exists();
                $attempts++;
            } while ($exists && $attempts < 5);

            // Create Sale
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'idempotency_key' => $idempotencyKey,
                'cashier_id' => $cashier->id,
                'location_id' => $location->id,
                'transaction_date' => now(),
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'amount_paid' => $totalPaid,
                'change_amount' => $changeAmount,
                'total_cost' => $totalCost,
                'gross_profit' => $grossProfit,
                'status' => 'COMPLETED',
                'notes' => $notes,
            ]);

            // Create SaleItems & Deduct Stock
            foreach ($validatedItems as $vItem) {
                /** @var Product $product */
                $product = $vItem['product'];
                $qty = $vItem['quantity'];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name_snapshot' => $vItem['name'],
                    'product_code_snapshot' => $product->code,
                    'product_type_snapshot' => $product->product_type,
                    'product_subtype_snapshot' => $product->product_subtype,
                    'modal_account_snapshot' => $product->defaultBalanceAccount?->code,
                    'quantity' => $qty,
                    'cost_price' => $vItem['cost_price'],
                    'selling_price' => $vItem['selling_price'],
                    'discount_amount' => 0,
                    'subtotal' => $vItem['subtotal'],
                ]);

                // Deduct inventory for PHYSICAL products
                if ($product->product_type === 'PHYSICAL') {
                    $this->inventoryService->adjustStock(
                        product: $product,
                        location: $location,
                        quantityChange: -$qty,
                        movementType: 'SALE',
                        notes: 'Penjualan POS #'.$invoiceNumber,
                        user: $cashier,
                        reference: $sale
                    );
                }
            }

            // Create Payments & Balance Transactions
            foreach ($validatedPayments as $pData) {
                $pm = $pData['payment_method'];
                $balanceAccountId = $pData['balance_account_id'];

                Payment::create([
                    'sale_id' => $sale->id,
                    'payment_method_id' => $pm->id,
                    'balance_account_id' => $balanceAccountId,
                    'amount' => $pData['amount'],
                    'change_amount' => $pData['change_amount'],
                    'reference_number' => $pData['reference_number'] ?? null,
                    'status' => 'COMPLETED',
                    'paid_at' => now(),
                ]);

                // Update Balance Account
                if ($balanceAccountId) {
                    $account = BalanceAccount::whereKey($balanceAccountId)->lockForUpdate()->first();
                    if ($account) {
                        $before = $account->current_balance;
                        $after = $before + $pData['amount'];

                        $account->update(['current_balance' => $after]);

                        BalanceTransaction::create([
                            'transaction_number' => BalanceTransaction::generateTransactionNumber('TRX'),
                            'transaction_type' => 'SALE_RECEIPT',
                            'destination_account_id' => $account->id,
                            'amount' => $pData['amount'],
                            'balance_before' => $before,
                            'balance_after' => $after,
                            'reference_type' => Sale::class,
                            'reference_id' => $sale->id,
                            'description' => "Penerimaan pembayaran {$pm?->name} untuk POS #{$invoiceNumber}",
                            'created_by' => $cashier->id,
                            'transaction_date' => now(),
                        ]);
                    }
                }
            }

            // Handle change exits from the same CASH payment accounts recorded above.
            foreach ($validatedPayments as $pData) {
                if ($pData['change_amount'] <= 0) {
                    continue;
                }

                $cashAccount = BalanceAccount::whereKey($pData['balance_account_id'])->where('status', 'ACTIVE')->lockForUpdate()->first();
                if (! $cashAccount) {
                    throw new InvalidArgumentException('Akun kas untuk kembalian tidak valid atau tidak aktif.');
                }

                $beforeCash = $cashAccount->current_balance;
                $afterCash = $beforeCash - $pData['change_amount'];

                $cashAccount->update(['current_balance' => $afterCash]);

                BalanceTransaction::create([
                    'transaction_number' => BalanceTransaction::generateTransactionNumber('TRX'),
                    'transaction_type' => 'WITHDRAWAL',
                    'source_account_id' => $cashAccount->id,
                    'amount' => $pData['change_amount'],
                    'balance_before' => $beforeCash,
                    'balance_after' => $afterCash,
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'description' => "Kembalian kasir untuk POS #{$invoiceNumber}",
                    'created_by' => $cashier->id,
                    'transaction_date' => now(),
                ]);
            }

            return $sale;
        });

        ProcessAuditLogJob::dispatch(
            action: 'POS_CHECKOUT',
            description: "Penjualan POS #{$sale->invoice_number} berhasil diproses (Total: Rp".number_format($sale->total_amount, 0, ',', '.').")",
            userId: $cashier->id,
            context: [
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'total_amount' => $sale->total_amount,
                'location_id' => $location->id,
                'idempotency_key' => $idempotencyKey,
            ],
            locationId: $location->id
        );

        return $sale;
    }
}
