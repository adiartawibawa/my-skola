<?php

namespace App\Livewire\Portal;

use App\Enums\GuardianRelationshipType;
use App\Models\GuardianStudent;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class LinkChildPage extends Component
{
    public string $nisn = '';

    public string $tanggal_lahir = '';

    public string $relationship_type = '';

    public function link()
    {
        $this->validate([
            'nisn' => ['required', 'string', 'max:20'],
            'tanggal_lahir' => ['required', 'date'],
            'relationship_type' => ['required', Rule::in(array_column(GuardianRelationshipType::cases(), 'value'))],
        ]);

        // Cegah brute-force kombinasi NISN — dibatasi per akun, bukan
        // per IP, karena penyerang paling mungkin adalah orang tua lain
        // yang login sah tapi mencoba menebak data anak orang lain.
        $throttleKey = 'link-child:'.auth()->id();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'nisn' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $student = Student::query()
            ->where('nisn', $this->nisn)
            ->where('tanggal_lahir', $this->tanggal_lahir)
            ->first();

        if (! $student) {
            RateLimiter::hit($throttleKey, 600);

            $this->addError('nisn', 'Data tidak ditemukan. Periksa kembali NISN dan tanggal lahir.');

            return;
        }

        if (auth()->user()->isParentOf($student)) {
            $this->addError('nisn', 'Anak ini sudah tertaut ke akun kamu.');

            return;
        }

        RateLimiter::clear($throttleKey);

        GuardianStudent::create([
            'user_id' => auth()->id(),
            'student_id' => $student->id,
            'relationship_type' => $this->relationship_type,
            'verified_at' => now(),
        ]);

        session()->flash('link_success', 'Berhasil menautkan data: '.$student->user->name);

        $this->reset('nisn', 'tanggal_lahir', 'relationship_type');
    }

    public function render(): View
    {
        return view('livewire.portal.link-child-page', [
            'linkedStudents' => auth()->user()->students()->with('user')->get(),
        ])->layout('components.layouts.app', [
            'title' => 'Tautkan Data Anak',
        ]);
    }
}
