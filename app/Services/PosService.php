<?php

namespace App\Services;

use App\Models\BalanceAccount;
use App\Models\BalanceTransaction;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
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
        ?string $notes = null
    ): Sale {
        if (empty($cartItems)) {
            throw new InvalidArgumentException('Keranjang belanja kosong.');
        }

        if (empty($paymentsData)) {
            throw new InvalidArgumentException('Metode pembayaran wajib diisi.');
        }

        $location = Location::where('code', 'RAJA-BANGO')->first()
            ?? Location::where('status', 'ACTIVE')->first();

        if (! $location) {
            throw new InvalidArgumentException('Lokasi aktif belum tersedia.');
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

            // Block INCOMPLETE price status (PRD Bab 7.1)
            if ($product->price_status === 'INCOMPLETE') {
                throw new InvalidArgumentException("Produk '{$product->name}' memiliki status harga INCOMPLETE (modal/jual 0). Harap lengkapi harga sebelum checkout.");
            }

            // Validate physical stock availability (PRD Bab 72)
            if ($product->product_type === 'PHYSICAL') {
                $availableStock = Inventory::where('product_id', $product->id)
                    ->where('location_id', $location->id)
                    ->value('quantity') ?? 0;

                if ($availableStock < $qty) {
                    throw new InvalidArgumentException("Stok produk '{$product->name}' tidak mencukupi (Tersedia: {$availableStock}, Diminta: {$qty}).");
                }
            }

            $itemSubtotal = $product->selling_price * $qty;
            $itemCostSubtotal = $product->cost_price * $qty;

            $subtotal += $itemSubtotal;
            $totalCost += $itemCostSubtotal;

            $validatedItems[] = [
                'product' => $product,
                'quantity' => $qty,
                'cost_price' => $product->cost_price,
                'selling_price' => $product->selling_price,
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
                $balanceAccountId = BalanceAccount::where('code', 'CASH')->where('status', 'ACTIVE')->value('id');
            } elseif (! $balanceAccountId && $paymentMethod->type === 'QRIS') {
                $balanceAccountId = BalanceAccount::where('code', 'QRIS')->where('status', 'ACTIVE')->value('id');
            }

            if (! $balanceAccountId) {
                throw new InvalidArgumentException("Akun tujuan pembayaran {$paymentMethod->name} wajib dipilih.");
            }

            $balanceAccount = BalanceAccount::where('status', 'ACTIVE')->find($balanceAccountId);
            if (! $balanceAccount) {
                throw new InvalidArgumentException('Akun saldo pembayaran tidak valid atau tidak aktif.');
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
                'reference_number' => $paymentData['reference_number'] ?? null,
            ];
            $totalPaid += $amount;
        }

        if ($totalPaid < $totalAmount) {
            throw new InvalidArgumentException('Jumlah pembayaran (Rp'.number_format($totalPaid, 0, ',', '.').') kurang dari total belanja (Rp'.number_format($totalAmount, 0, ',', '.').').');
        }

        $changeAmount = $totalPaid - $totalAmount;
        $grossProfit = $totalAmount - $totalCost;

        // 2. Perform Atomic Transaction
        return DB::transaction(function () use (
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
            $notes
        ) {
            // Generate Invoice Number server-side (PRD Bab 55)
            $datePrefix = date('Ymd');
            $invoiceNumber = 'INV-'.$datePrefix.'-'.now()->format('His').'-'.Str::upper(Str::random(8));

            // Create Sale
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'cashier_id' => $cashier->id,
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
                    'product_name_snapshot' => $product->name,
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
            $cashAccount = BalanceAccount::where('code', 'CASH')->first();

            foreach ($validatedPayments as $pData) {
                $pm = $pData['payment_method'];
                $balanceAccountId = $pData['balance_account_id'];

                Payment::create([
                    'sale_id' => $sale->id,
                    'payment_method_id' => $pm->id,
                    'balance_account_id' => $balanceAccountId,
                    'amount' => $pData['amount'],
                    'change_amount' => 0,
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
                            'transaction_number' => 'TRX-'.date('YmdHis').'-'.sprintf('%03d', rand(100, 999)),
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

            // Handle Change Amount exit from CASH account (PRD Bab 72)
            if ($changeAmount > 0 && $cashAccount) {
                $cashAccount = $cashAccount->fresh();
                $beforeCash = $cashAccount->current_balance;
                $afterCash = $beforeCash - $changeAmount;

                $cashAccount->update(['current_balance' => $afterCash]);

                BalanceTransaction::create([
                    'transaction_number' => 'TRX-'.date('YmdHis').'-CHG',
                    'transaction_type' => 'WITHDRAWAL',
                    'source_account_id' => $cashAccount->id,
                    'amount' => $changeAmount,
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
    }
}
