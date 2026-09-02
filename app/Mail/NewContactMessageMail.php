<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public ContactMessage $contactMessage)
    {
        //
    }

    public function build(): self
    {
        return $this->subject('Pesan Baru dari Formulir Kontak — '.config('app.name'))
            ->markdown('emails.contact-message');
    }
}
