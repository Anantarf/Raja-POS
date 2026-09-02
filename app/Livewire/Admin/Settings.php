<?php

namespace App\Livewire\Admin;

use App\Models\Location;
use App\Models\PaymentMethod;
use Livewire\Component;

class Settings extends Component
{
    public $activeTab = 'LOCATIONS'; // LOCATIONS, PAYMENT_METHODS

    // Location Form
    public $locationName = '';
    public $locationCode = '';

    // Payment Method Form
    public $pmName = '';
    public $pmType = 'CASH';

    public function addLocation()
    {
        $this->validate([
            'locationName' => 'required|string|max:255',
            'locationCode' => 'required|string|max:50|unique:locations,code',
        ]);

        Location::create([
            'name' => $this->locationName,
            'code' => strtoupper($this->locationCode),
            'is_active' => true,
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
            'is_active' => true,
        ]);

        $this->pmName = '';
        $this->dispatch('notify', message: 'Metode pembayaran baru ditambahkan.', type: 'success');
    }

    public function render()
    {
        return view('livewire.admin.settings', [
            'locations' => Location::all(),
            'paymentMethods' => PaymentMethod::all(),
        ])->layout('components.layouts.admin', ['title' => 'Pengaturan Toko - Raja POS']);
    }
}
