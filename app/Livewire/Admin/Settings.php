<?php

namespace App\Livewire\Admin;

use App\Models\Location;
use App\Models\PaymentMethod;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Component;

class Settings extends Component
{
    public $activeTab = 'STORE_SETTINGS'; // STORE_SETTINGS, PRINTER_SETTINGS, USERS, ROLES, PAYMENT_METHODS, LOCATIONS

    public array $allowedTabs = ['STORE_SETTINGS', 'PRINTER_SETTINGS', 'USERS', 'ROLES', 'PAYMENT_METHODS', 'LOCATIONS'];

    // Printer & Receipt Form
    public $storeName = 'Raja Aksesoris';

    public $receiptPaperWidth = '58mm';

    public $printMode = 'BROWSER';

    public $autoPrint = true;

    public $receiptHeaderTagline = 'Retail Management System';

    public $receiptAddress = '';

    public $receiptPhone = '';

    public $receiptFooterText = 'Terima Kasih Telah Berbelanja! Kepuasan Anda Adalah Kebanggaan Kami.';

    public $showCashierName = true;

    // Location Form
    public $locationName = '';

    public $locationCode = '';

    // Payment Method Form
    public $pmName = '';

    public $pmReceiptLabel = '';

    public array $paymentMethodLabels = [];

    public $pmType = 'CASH';

    // User Form
    public $userName = '';

    public $userUsername = '';

    public $userPassword = '';

    public $userRoleId = '';

    public $userLocationId = '';

    public function mount(?string $section = null): void
    {
        $tab = strtoupper(str_replace('-', '_', $section ?: 'store-settings'));
        $this->activeTab = in_array($tab, $this->allowedTabs, true) ? $tab : 'STORE_SETTINGS';

        $this->storeName = Setting::get('store_name', 'Raja Aksesoris');
        $this->receiptPaperWidth = Setting::get('receipt_paper_width', '58mm');
        $this->printMode = Setting::get('print_mode', 'BROWSER');
        $this->autoPrint = Setting::get('auto_print', '1') === '1';
        $this->receiptHeaderTagline = Setting::get('receipt_header_tagline', 'Retail Management System');
        $this->receiptAddress = Setting::get('receipt_address', '');
        $this->receiptPhone = Setting::get('receipt_phone', '');
        $this->receiptFooterText = Setting::get('receipt_footer_text', 'Terima Kasih Telah Berbelanja! Kepuasan Anda Adalah Kebanggaan Kami.');
        $this->showCashierName = Setting::get('show_cashier_name', '1') === '1';
        $this->paymentMethodLabels = PaymentMethod::query()
            ->pluck('receipt_label', 'id')
            ->map(fn ($label) => $label ?? '')
            ->toArray();
    }

    public function savePrinterSettings(): void
    {
        abort_unless(auth()->user()->can('settings.manage'), 403);

        $this->validate([
            'storeName' => 'required|string|max:255',
            'receiptPaperWidth' => 'required|in:58mm,80mm',
            'printMode' => 'required|in:BROWSER,RAWBT,WEB_BLUETOOTH',
            'autoPrint' => 'boolean',
            'receiptHeaderTagline' => 'nullable|string|max:255',
            'receiptAddress' => 'nullable|string|max:500',
            'receiptPhone' => 'nullable|string|max:50',
            'receiptFooterText' => 'nullable|string|max:500',
            'showCashierName' => 'boolean',
        ]);

        Setting::set('store_name', $this->storeName);
        Setting::set('receipt_paper_width', $this->receiptPaperWidth);
        Setting::set('print_mode', $this->printMode);
        Setting::set('auto_print', $this->autoPrint ? '1' : '0');
        Setting::set('receipt_header_tagline', $this->receiptHeaderTagline ?: '');
        Setting::set('receipt_address', $this->receiptAddress ?: '');
        Setting::set('receipt_phone', $this->receiptPhone ?: '');
        Setting::set('receipt_footer_text', $this->receiptFooterText ?: '');
        Setting::set('show_cashier_name', $this->showCashierName ? '1' : '0');

        $this->dispatch('notify', message: 'Pengaturan printer dan struk berhasil disimpan.', type: 'success');
    }

    public function addUser()
    {
        abort_unless(auth()->user()->can('settings.manage'), 403);

        $this->validate([
            'userName' => 'required|string|max:255',
            'userUsername' => 'required|string|max:255|unique:users,username',
            'userPassword' => 'required|string|min:6',
            'userRoleId' => 'required|exists:roles,id',
            'userLocationId' => 'required|exists:locations,id',
        ]);

        User::create([
            'name' => $this->userName,
            'username' => $this->userUsername,
            'password' => bcrypt($this->userPassword),
            'role_id' => $this->userRoleId,
            'location_id' => $this->userLocationId,
            'status' => 'ACTIVE',
        ]);

        $this->userName = '';
        $this->userUsername = '';
        $this->userPassword = '';
        $this->userRoleId = '';
        $this->userLocationId = '';

        $this->dispatch('notify', message: 'Pengguna baru berhasil ditambahkan.', type: 'success');
    }

    public function addLocation()
    {
        abort_unless(auth()->user()->can('settings.manage'), 403);

        $this->validate([
            'locationName' => 'required|string|max:255',
            'locationCode' => 'required|string|max:50|unique:locations,code',
        ]);

        Location::create([
            'name' => $this->locationName,
            'code' => strtoupper($this->locationCode),
            'status' => 'ACTIVE',
        ]);

        $this->locationName = '';
        $this->locationCode = '';
        $this->dispatch('notify', message: 'Cabang toko berhasil ditambahkan.', type: 'success');
    }

    public function addPaymentMethod()
    {
        abort_unless(auth()->user()->can('settings.manage'), 403);

        $this->validate([
            'pmName' => 'required|string|max:255|unique:payment_methods,name',
            'pmReceiptLabel' => 'nullable|string|max:20',
            'pmType' => 'required|in:CASH,QRIS,TRANSFER,E_WALLET',
        ]);

        PaymentMethod::create([
            'name' => $this->pmName,
            'receipt_label' => filled($this->pmReceiptLabel) ? trim($this->pmReceiptLabel) : null,
            'code' => Str::upper(Str::slug($this->pmName, '_')),
            'type' => $this->pmType,
            'status' => 'ACTIVE',
        ]);

        $this->pmName = '';
        $this->pmReceiptLabel = '';
        $this->paymentMethodLabels = PaymentMethod::query()
            ->pluck('receipt_label', 'id')
            ->map(fn ($label) => $label ?? '')
            ->toArray();
        $this->dispatch('notify', message: 'Metode pembayaran berhasil ditambahkan.', type: 'success');
    }

    public function updatePaymentMethodReceiptLabel(int $paymentMethodId): void
    {
        abort_unless(auth()->user()->can('settings.manage'), 403);

        $this->validate([
            "paymentMethodLabels.{$paymentMethodId}" => 'nullable|string|max:20',
        ]);

        $paymentMethod = PaymentMethod::findOrFail($paymentMethodId);
        $label = trim((string) ($this->paymentMethodLabels[$paymentMethodId] ?? ''));

        $paymentMethod->update([
            'receipt_label' => $label !== '' ? $label : null,
        ]);

        $this->paymentMethodLabels[$paymentMethodId] = $paymentMethod->receipt_label ?? '';
        $this->dispatch('notify', message: 'Nama cetak struk berhasil disimpan.', type: 'success');
    }

    public function render()
    {
        return view('livewire.admin.settings', [
            'locations' => Location::all(),
            'paymentMethods' => PaymentMethod::all(),
            'users' => User::with('role')->orderBy('name')->get(),
            'roles' => Role::withCount('users')->orderBy('name')->get(),
            'settings' => Setting::query()->orderBy('key')->get(),
        ])->layout('components.layouts.admin', ['title' => 'Pengaturan Toko']);
    }
}
