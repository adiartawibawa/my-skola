<?php

namespace App\Livewire;

use App\Mail\NewContactMessageMail;
use App\Models\ContactMessage;
use App\Settings\NotificationSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Throwable;

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

    /**
     * Kegagalan kirim notifikasi (mis. SMTP salah setting) sengaja tidak
     * menggagalkan submit form — pesan tetap tersimpan di database,
     * cuma staf tidak dapat email pemberitahuan. Dicatat ke log untuk
     * ditelusuri lewat halaman Mail Server (tombol "Kirim Email Uji Coba").
     */
    protected function notifyStaff(ContactMessage $contactMessage): void
    {
        $settings = app(NotificationSettings::class);

        if (! $settings->notify_on_contact_message || blank($settings->notify_email)) {
            return;
        }

        try {
            Mail::to($settings->notify_email)->send(new NewContactMessageMail($contactMessage));
        } catch (Throwable $e) {
            Log::error('Gagal mengirim notifikasi pesan kontak: '.$e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.contact-page')->layout('components.layouts.guest', [
            'title' => 'Kontak',
            'description' => 'Hubungi '.config('app.name').' untuk informasi pendaftaran, kerja sama, atau pertanyaan lainnya.',
        ]);
    }
}
