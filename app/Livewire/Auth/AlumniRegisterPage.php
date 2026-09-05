<?php

namespace App\Livewire\Auth;

use App\Enums\RoleEnum;
use App\Models\AlumniProfile;
use App\Models\ProgramKeahlian;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class AlumniRegisterPage extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $tahun_lulus = '';

    public ?string $program_keahlian_id = null;

    public string $nis_klaim = '';

    public function register()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'tahun_lulus' => ['required', 'integer', 'min:1980', 'max:'.now()->year],
            'program_keahlian_id' => ['nullable', 'exists:program_keahlians,id'],
            'nis_klaim' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => RoleEnum::ALUMNI,
        ]);

        AlumniProfile::create([
            'user_id' => $user->id,
            'tahun_lulus' => $this->tahun_lulus,
            'program_keahlian_id' => $this->program_keahlian_id,
            'nis_klaim' => $this->nis_klaim ?: null,
            'is_verified' => false,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('portal.dashboard');
    }

    public function render(): View
    {
        return view('livewire.auth.alumni-register-page', [
            'programKeahlians' => ProgramKeahlian::query()->active()->orderBy('name')->get(),
        ])->layout('components.layouts.auth', ['title' => 'Daftar Akun Alumni']);
    }
}
