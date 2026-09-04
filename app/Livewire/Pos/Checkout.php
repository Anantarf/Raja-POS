<?php

namespace App\Livewire\Pos;

use App\Models\BalanceAccount;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Services\PosService;
use Livewire\Component;

class Checkout extends Component
{
    public string $search = '';

    public ?int $selectedCategory = null;

    public string $selectedType = 'ALL'; // ALL, PHYSICAL, DIGITAL, LAYANAN

    public string $viewMode = 'grid'; // 'grid' or 'list'

    public function setViewMode(string $mode): void
    {
        $this->viewMode = in_array($mode, ['grid', 'list']) ? $mode : 'grid';
    }

    public int $perPage = 36;

    public function updatedSearch(): void
    {
        $this->perPage = 36;
    }

    public function updatedSelectedCategory(): void
    {
        $this->perPage = 36;
    }

    public function updatedSelectedType(): void
    {
        $this->perPage = 36;
    }

    public function loadMore(): void
    {
        $this->perPage += 36;
    }

    public array $cart = []; // [product_id => ['product_id' => int, 'name' => string, 'code' => string, 'price' => float, 'cost_price' => float, 'quantity' => int, 'stock' => int, 'price_status' => string, 'type' => string]]

    public array $payments = []; // [['payment_method_id' => int, 'balance_account_id' => ?int, 'amount' => float, 'reference_number' => ?string]]

    public ?string $notes = null;

    public ?int $completedSaleId = null;

    public ?string $completedInvoiceNumber = null;

    public float $completedChangeAmount = 0;

    public bool $showSuccessModal = false;

    // PPOB Dynamic Open-Nominal Modal Properties
    public bool $showPpobModal = false;

    public ?int $selectedPpobProductId = null;

    public ?string $selectedPpobProductName = '';

    public float $ppobBillAmount = 0;

    public float $ppobStoreAdminFee = 3000;

    public float $ppobVendorAdminFee = 1500;

    public function mount()
    {
        $this->initPaymentEntries();
    }

    public function initPaymentEntries()
    {
        $defaultCashPm = PaymentMethod::where('type', 'CASH')->first() ?? PaymentMethod::first();
        $cashAccount = BalanceAccount::where('code', 'CASH')->first();

        $this->payments = [
            [
                'payment_method_id' => $defaultCashPm?->id,
                'balance_account_id' => $cashAccount?->id,
                'amount' => 0,
                'reference_number' => '',
            ],
        ];
    }

    public string $ppobModalLabel = 'Nominal Layanan'; // Dynamic label shown in modal

    public function openPpobModal(int $productId)
    {
        $product = Product::find($productId);
        if (! $product) {
            return;
        }

        $this->selectedPpobProductId = $product->id;
        $this->selectedPpobProductName = $product->name;
        $this->ppobBillAmount = 0;
        $this->ppobStoreAdminFee = 3000;
        $this->ppobVendorAdminFee = 1500;

        // Set contextual label based on product name keywords
        $name = strtoupper($product->name);
        if (str_contains($name, 'TOP UP') || str_contains($name, 'TOPUP') || str_contains($name, 'DANA') || str_contains($name, 'GOPAY') || str_contains($name, 'OVO') || str_contains($name, 'SHOPEEPAY')) {
            $this->ppobModalLabel = 'Nominal Top Up';
        } elseif (str_contains($name, 'TRANSFER') || str_contains($name, 'BANK MAS')) {
            $this->ppobModalLabel = 'Nominal Transfer';
        } elseif (str_contains($name, 'TOKEN PLN') || str_contains($name, 'PLN TOKEN')) {
            $this->ppobModalLabel = 'Nominal Token';
        } elseif (str_contains($name, 'TARIK TUNAI')) {
            $this->ppobModalLabel = 'Nominal Tarik Tunai';
        } elseif (str_contains($name, 'TAGIHAN') || str_contains($name, 'PASCA')) {
            $this->ppobModalLabel = 'Nominal Tagihan';
        } else {
            $this->ppobModalLabel = 'Nominal Layanan';
        }

        $this->showPpobModal = true;
    }

    public function confirmAddPpobToCart()
    {
        if (! $this->selectedPpobProductId) {
            return;
        }

        $product = Product::find($this->selectedPpobProductId);
        if (! $product) {
            return;
        }

        // [CELAH #1] Minimum nominal Rp 1.000
        if ($this->ppobBillAmount < 1000) {
            $this->dispatch('notify', message: 'Nominal minimum adalah Rp 1.000.', type: 'warning');

            return;
        }

        // [CELAH #2] Maximum nominal Rp 10.000.000 (10 juta)
        if ($this->ppobBillAmount > 10000000) {
            $this->dispatch('notify', message: 'Nominal maksimum adalah Rp 10.000.000.', type: 'warning');

            return;
        }

        // [CELAH #3] Fee tidak boleh negatif
        if ($this->ppobStoreAdminFee < 0 || $this->ppobVendorAdminFee < 0) {
            $this->dispatch('notify', message: 'Biaya admin tidak boleh negatif.', type: 'warning');

            return;
        }

        // [CELAH #4] Store fee harus >= vendor fee (margin harus positif atau nol)
        if ($this->ppobStoreAdminFee < $this->ppobVendorAdminFee) {
            $this->dispatch('notify', message: 'Biaya admin toko harus lebih besar atau sama dengan biaya vendor (margin tidak boleh negatif).', type: 'warning');

            return;
        }

        $sellingPrice = (float) ($this->ppobBillAmount + $this->ppobStoreAdminFee);
        $costPrice = (float) ($this->ppobBillAmount + $this->ppobVendorAdminFee);

        $cartKey = 'PPOB_'.$product->id.'_'.time();
        $displayName = $product->name.' (Rp '.number_format($this->ppobBillAmount, 0, ',', '.').')';

        $this->cart[$cartKey] = [
            'product_id' => $product->id,
            'name' => $displayName,
            'code' => $product->code,
            'price' => $sellingPrice,
            'cost_price' => $costPrice,
            'quantity' => 1,
            'stock' => 999999,
            'price_status' => 'COMPLETE',
            'type' => $product->product_type,
            'is_ppob_open' => true,
            'bill_amount' => $this->ppobBillAmount,
            'admin_fee' => $this->ppobStoreAdminFee,
        ];

        $this->showPpobModal = false;
        $this->updateDefaultPaymentAmount();
        $this->dispatch('notify', message: "Tagihan {$displayName} berhasil ditambahkan ke keranjang.", type: 'success');
    }

    public function addToCart(int $productId)
    {
        $product = Product::find($productId);
        if (! $product) {
            return;
        }

        // All LAYANAN products always use the open-nominal modal
        if ($product->product_type === 'LAYANAN') {
            $this->openPpobModal($product->id);

            return;
        }

        // Other products with incomplete price also open the modal
        if ($product->price_status === 'INCOMPLETE') {
            $this->openPpobModal($product->id);

            return;
        }

        $location = auth()->user()?->location ?? Location::where('code', 'RAJA-BANGO')->first() ?? Location::first();
        $currentStock = 999999;

        if ($product->product_type === 'PHYSICAL') {
            $currentStock = Inventory::where('product_id', $product->id)
                ->where('location_id', $location?->id)
                ->value('quantity') ?? 0;
        }

        $existingQty = $this->cart[$productId]['quantity'] ?? 0;

        if ($product->product_type === 'PHYSICAL' && ($existingQty + 1) > $currentStock) {
            $this->dispatch('notify', message: "Stok '{$product->name}' Tidak Mencukupi (Tersedia: {$currentStock}).", type: 'warning');

            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'price' => (float) $product->selling_price,
                'cost_price' => (float) $product->cost_price,
                'quantity' => 1,
                'stock' => $currentStock,
                'price_status' => $product->price_status,
                'type' => $product->product_type,
            ];
        }

        $this->updateDefaultPaymentAmount();
    }

    public function updateQuantity(int|string $cartKey, int $qty)
    {
        if (! isset($this->cart[$cartKey])) {
            return;
        }

        // [CELAH #5] LAYANAN (PPOB open-nominal) items are always quantity 1
        $item = $this->cart[$cartKey];
        if (! empty($item['is_ppob_open']) || $item['type'] === 'LAYANAN') {
            $this->dispatch('notify', message: 'Item layanan hanya bisa 1 transaksi per baris. Tambah lagi jika perlu.', type: 'info');

            return;
        }

        if ($qty <= 0) {
            $this->removeFromCart($cartKey);

            return;
        }

        if ($item['type'] === 'PHYSICAL' && $qty > $item['stock']) {
            $this->dispatch('notify', message: "Stok tidak mencukupi (Maksimal: {$item['stock']}).", type: 'warning');

            return;
        }

        $this->cart[$cartKey]['quantity'] = $qty;
        $this->updateDefaultPaymentAmount();
    }

    public function removeFromCart(int|string $cartKey)
    {
        unset($this->cart[$cartKey]);
        $this->updateDefaultPaymentAmount();
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->initPaymentEntries();
    }

    public function addPaymentRow()
    {
        $defaultCashPm = PaymentMethod::where('type', 'CASH')->first();
        $this->payments[] = [
            'payment_method_id' => $defaultCashPm?->id,
            'balance_account_id' => null,
            'amount' => 0,
            'reference_number' => '',
        ];
    }

    public function removePaymentRow(int $index)
    {
        if (count($this->payments) > 1) {
            unset($this->payments[$index]);
            $this->payments = array_values($this->payments);
        }
    }

    public function updatedPayments($value, $key)
    {
        foreach ($this->payments as $idx => $pay) {
            if (isset($pay['amount']) && (float) $pay['amount'] > 1000000000) {
                $this->payments[$idx]['amount'] = 1000000000;
            }
        }
    }

    public function setExactPayment()
    {
        $total = min(1000000000, $this->grand_total);
        if (isset($this->payments[0])) {
            $this->payments[0]['amount'] = $total;
        }
    }

    public function setPaymentAmount(float $amount)
    {
        if (isset($this->payments[0])) {
            $this->payments[0]['amount'] = min(1000000000, max(0, $amount));
        }
    }

    public function addNominalToPayment(float $nominal)
    {
        if (isset($this->payments[0])) {
            $current = (float) ($this->payments[0]['amount'] ?? 0);
            $this->payments[0]['amount'] = min(1000000000, $current + $nominal);
        }
    }

    public function updateDefaultPaymentAmount()
    {
        if (count($this->payments) === 1) {
            $this->payments[0]['amount'] = min(1000000000, $this->grand_total);
        }
    }

    public function getSubtotalProperty(): float
    {
        return array_reduce($this->cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0.0);
    }

    public function getGrandTotalProperty(): float
    {
        return $this->subtotal;
    }

    public function getTotalPaidProperty(): float
    {
        return array_sum(array_column($this->payments, 'amount'));
    }

    public function getChangeAmountProperty(): float
    {
        return max(0, $this->total_paid - $this->grand_total);
    }

    public function processCheckout()
    {
        if (empty($this->cart)) {
            $this->dispatch('notify', message: 'Keranjang belanja masih kosong.', type: 'danger');

            return;
        }

        if ($this->total_paid < $this->grand_total) {
            $this->dispatch('notify', message: 'Jumlah pembayaran kurang dari total belanja.', type: 'danger');

            return;
        }

        try {
            $cartPayload = [];
            foreach ($this->cart as $item) {
                $product = Product::find($item['product_id']);
                if (! $product) {
                    continue;
                }

                // [CELAH #6] Re-validate LAYANAN/PPOB cart item price integrity server-side
                // Prevent browser console $wire.set('cart.key.price', 1) manipulation
                if (! empty($item['is_ppob_open']) || $product->product_type === 'LAYANAN') {
                    $billAmount = (float) ($item['bill_amount'] ?? 0);
                    $adminFee = (float) ($item['admin_fee'] ?? 0);
                    $expectedPrice = $billAmount + $adminFee;

                    // If price in cart doesn't match bill+admin, reject
                    if (abs((float) ($item['price'] ?? 0) - $expectedPrice) > 1) {
                        throw new \InvalidArgumentException("Harga item '{$product->name}' tidak valid. Kemungkinan ada manipulasi data.");
                    }

                    // Minimum nominal Rp 1.000 at POS level too
                    if ($billAmount < 1000) {
                        throw new \InvalidArgumentException("Nominal layanan '{$product->name}' harus minimal Rp 1.000.");
                    }
                }

                $cartPayload[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'] ?? $product->selling_price,
                    'cost_price' => $item['cost_price'] ?? $product->cost_price,
                    'name' => $item['name'] ?? $product->name,
                ];
            }

            $sale = app(PosService::class)->processCheckout(
                cashier: auth()->user(),
                cartItems: $cartPayload,
                paymentsData: $this->payments,
                notes: $this->notes
            );

            $this->completedSaleId = $sale->id;
            $this->completedInvoiceNumber = $sale->invoice_number;
            $this->completedChangeAmount = (float) $sale->change_amount;
            $this->showSuccessModal = true;

            $this->clearCart();
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'danger');
        }
    }

    public function closeSuccessModal()
    {
        $this->showSuccessModal = false;
    }

    public function render()
    {
        $location = auth()->user()?->location ?? Location::where('code', 'RAJA-BANGO')->first() ?? Location::first();

        $query = Product::query()
            ->with([
                'category',
                'brand',
                'inventories' => fn ($inventoryQuery) => $inventoryQuery->where('location_id', $location?->id),
            ])
            ->where('status', 'ACTIVE');

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('code', 'like', "%{$this->search}%")
                    ->orWhere('barcode', 'like', "%{$this->search}%")
                    ->orWhere('name', 'like', "%{$this->search}%");
            });
        }

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->selectedType !== 'ALL') {
            $query->where('product_type', $this->selectedType);
        }

        $totalProductsCount = (clone $query)->count();
        $products = $query->orderBy('name')->take($this->perPage)->get();

        $categories = Category::where('status', 'ACTIVE')->orderBy('name')->get();
        $paymentMethods = PaymentMethod::where('status', 'ACTIVE')->get();
        $balanceAccounts = BalanceAccount::where('status', 'ACTIVE')->get();
        $cashBalance = (float) (BalanceAccount::where('code', 'CASH')->value('current_balance') ?? 0);

        return view('livewire.pos.checkout', [
            'products' => $products,
            'totalProductsCount' => $totalProductsCount,
            'categories' => $categories,
            'paymentMethods' => $paymentMethods,
            'balanceAccounts' => $balanceAccounts,
            'location' => $location,
            'cashBalance' => $cashBalance,
        ])->layout('components.layouts.app');
    }
}
