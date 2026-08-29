<?php

namespace App\Livewire;

use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ContactPage extends Component
{
    public string $name = '';

    public string $email = '';

    public string $subject = '';

    public string $message = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ];
    }

    public function submit(): void
    {
        $data = $this->validate();

        ContactMessage::create($data);

        $this->reset('name', 'email', 'subject', 'message');

        session()->flash('contact_success', 'Pesan kamu berhasil terkirim. Kami akan membalas secepatnya.');
    }

    public function render(): View
    {
        return view('livewire.contact-page')->layout('components.layouts.guest', [
            'title' => 'Kontak',
            'description' => 'Hubungi '.config('app.name').' untuk informasi pendaftaran, kerja sama, atau pertanyaan lainnya.',
        ]);
    }
}
