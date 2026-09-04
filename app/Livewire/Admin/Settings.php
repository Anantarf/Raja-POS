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
    public $activeTab = 'STORE_SETTINGS'; // STORE_SETTINGS, USERS, ROLES, PAYMENT_METHODS, LOCATIONS

    public array $allowedTabs = ['STORE_SETTINGS', 'USERS', 'ROLES', 'PAYMENT_METHODS', 'LOCATIONS'];

    // Location Form
    public $locationName = '';

    public $locationCode = '';

    // Payment Method Form
    public $pmName = '';

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
    }

    public function addUser()
    {
        abort_unless(auth()->user()->can('settings.manage'), 403);

        $this->validate([
            'userName' => 'required|string|max:255',
            'userUsername' => 'required|string|max:255|unique:users,username',
            'userPassword' => 'required|string|min:4',
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
            'pmType' => 'required|in:CASH,QRIS,TRANSFER,E_WALLET',
        ]);

        PaymentMethod::create([
            'name' => $this->pmName,
            'code' => Str::upper(Str::slug($this->pmName, '_')),
            'type' => $this->pmType,
            'status' => 'ACTIVE',
        ]);

        $this->pmName = '';
        $this->dispatch('notify', message: 'Metode pembayaran berhasil ditambahkan.', type: 'success');
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
