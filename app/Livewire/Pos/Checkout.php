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
    public string $selectedType = 'ALL'; // ALL, PHYSICAL, DIGITAL, SERVICE

    public array $cart = []; // [product_id => ['product_id' => int, 'name' => string, 'code' => string, 'price' => float, 'cost_price' => float, 'quantity' => int, 'stock' => int, 'price_status' => string, 'type' => string]]
    public array $payments = []; // [['payment_method_id' => int, 'balance_account_id' => ?int, 'amount' => float, 'reference_number' => ?string]]

    public ?string $notes = null;
    public ?int $completedSaleId = null;
    public ?string $completedInvoiceNumber = null;
    public float $completedChangeAmount = 0;
    public bool $showSuccessModal = false;

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
            ]
        ];
    }

    public function addToCart(int $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        if ($product->price_status === 'INCOMPLETE') {
            $this->dispatch('notify', message: "Produk '{$product->name}' Memiliki Harga INCOMPLETE dan Tidak Bisa Dijual.", type: 'danger');
            return;
        }

        $location = Location::where('code', 'RAJA-BANGO')->first() ?? Location::first();
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

    public function updateQuantity(int $productId, int $qty)
    {
        if (!isset($this->cart[$productId])) {
            return;
        }

        if ($qty <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        $item = $this->cart[$productId];
        if ($item['type'] === 'PHYSICAL' && $qty > $item['stock']) {
            $this->dispatch('notify', message: "Stok tidak mencukupi (Maksimal: {$item['stock']}).", type: 'warning');
            return;
        }

        $this->cart[$productId]['quantity'] = $qty;
        $this->updateDefaultPaymentAmount();
    }

    public function removeFromCart(int $productId)
    {
        unset($this->cart[$productId]);
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

    public function setExactPayment()
    {
        $total = $this->grand_total;
        if (isset($this->payments[0])) {
            $this->payments[0]['amount'] = $total;
        }
    }

    public function updateDefaultPaymentAmount()
    {
        if (count($this->payments) === 1) {
            $this->payments[0]['amount'] = $this->grand_total;
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
                if ($product) {
                    $cartPayload[] = [
                        'product' => $product,
                        'quantity' => $item['quantity'],
                    ];
                }
            }

            $sale = app(PosService::class)->processCheckout(
                cashier: auth()->user(),
                cartItems: $cartPayload,
                paymentsData: $this->payments,
                notes: $this->notes
            );

            $this->completedSaleId = $sale->id;
            $this->completedInvoiceNumber = $sale->invoice_number;
            $this->completedChangeAmount = $sale->change_amount;
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
        $location = Location::where('code', 'RAJA-BANGO')->first() ?? Location::first();

        $query = Product::query()
            ->with(['category', 'brand'])
            ->where('status', 'ACTIVE');

        if (!empty($this->search)) {
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

        $products = $query->orderBy('name')->paginate(16);
        $categories = Category::where('status', 'ACTIVE')->orderBy('name')->get();
        $paymentMethods = PaymentMethod::where('status', 'ACTIVE')->get();
        $balanceAccounts = BalanceAccount::where('status', 'ACTIVE')->get();
        $cashBalance = (float) (BalanceAccount::where('code', 'CASH')->value('current_balance') ?? 0);

        return view('livewire.pos.checkout', [
            'products' => $products,
            'categories' => $categories,
            'paymentMethods' => $paymentMethods,
            'balanceAccounts' => $balanceAccounts,
            'location' => $location,
            'cashBalance' => $cashBalance,
        ])->layout('components.layouts.app');
    }
}
