<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $username = '';
    public $password = '';
    public $remember = false;

    public function login()
    {
        $this->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $this->username, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            return redirect()->intended('/pos');
        }

        $this->addError('username', 'Username atau password yang Anda masukkan tidak sesuai.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.guest');
    }
}
