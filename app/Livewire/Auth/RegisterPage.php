<?php

namespace App\Livewire\Auth;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class RegisterPage extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Hanya Orang Tua yang boleh self-register (lihat
     * RoleEnum::registrableRoles()) — Siswa dibuatkan Tata Usaha lewat
     * fitur Import yang sudah ada.
     */
    public function register()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => RoleEnum::PARENT,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('portal.link-child');
    }

    public function render(): View
    {
        return view('livewire.auth.register-page')->layout('components.layouts.auth', [
            'title' => 'Daftar Akun Orang Tua',
        ]);
    }
}
