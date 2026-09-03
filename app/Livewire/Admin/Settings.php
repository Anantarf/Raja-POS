<?php

namespace App\Livewire\Admin;

use App\Models\Location;
use App\Models\PaymentMethod;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Livewire\Component;

class Settings extends Component
{
    public $activeTab = 'STORE_SETTINGS'; // STORE_SETTINGS, USERS, ROLES, PAYMENT_METHODS, LOCATIONS

    public array $allowedTabs = ['STORE_SETTINGS', 'USERS', 'ROLES', 'PAYMENT_METHODS', 'LOCATIONS'];

    // Location Form
    public $locationName = '';

    public $locationCode = '';

    // Payment Method Form
    public $pmName = '';

    public $pmType = 'CASH';

    public function mount(?string $section = null): void
    {
        $tab = strtoupper(str_replace('-', '_', $section ?: 'store-settings'));
        $this->activeTab = in_array($tab, $this->allowedTabs, true) ? $tab : 'STORE_SETTINGS';
    }

    public function addLocation()
    {
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
        $this->dispatch('notify', message: 'Lokasi cabang baru ditambahkan.', type: 'success');
    }

    public function addPaymentMethod()
    {
        $this->validate([
            'pmName' => 'required|string|max:255',
            'pmType' => 'required|in:CASH,QRIS,TRANSFER,E_WALLET',
        ]);

        PaymentMethod::create([
            'name' => $this->pmName,
            'type' => $this->pmType,
            'status' => 'ACTIVE',
        ]);

        $this->pmName = '';
        $this->dispatch('notify', message: 'Metode pembayaran baru ditambahkan.', type: 'success');
    }

    public function render()
    {
        return view('livewire.admin.settings', [
            'locations' => Location::all(),
            'paymentMethods' => PaymentMethod::all(),
            'users' => User::with('role')->orderBy('name')->get(),
            'roles' => Role::withCount('users')->orderBy('name')->get(),
            'settings' => Setting::orderBy('key')->get(),
        ])->layout('components.layouts.admin', ['title' => 'Pengaturan Toko - Raja POS']);
    }
}
