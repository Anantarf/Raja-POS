<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
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

        $throttleKey = Str::transliterate(Str::lower($this->username)).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('username', "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.");
            return;
        }

        if (Auth::attempt(['username' => $this->username, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($throttleKey);
            session()->regenerate();
            return redirect()->intended('/pos');
        }

        RateLimiter::hit($throttleKey, 60);

        $this->addError('username', 'Username atau password yang Anda masukkan tidak sesuai.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.guest');
    }
}
