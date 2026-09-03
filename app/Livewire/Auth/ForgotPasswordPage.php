<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPasswordPage extends Component
{
    public string $email = '';

    public ?string $status = null;

    public function sendResetLink()
    {
        $this->validate(['email' => ['required', 'email']]);

        $result = Password::sendResetLink(['email' => $this->email]);

        if ($result === Password::RESET_LINK_SENT) {
            $this->status = 'Tautan reset kata sandi sudah dikirim ke email kamu.';
            $this->reset('email');

            return;
        }

        $this->addError('email', 'Email tidak ditemukan di sistem kami.');
    }

    public function render(): View
    {
        return view('livewire.auth.forgot-password-page')->layout('components.layouts.auth', [
            'title' => 'Lupa Kata Sandi',
        ]);
    }
}
