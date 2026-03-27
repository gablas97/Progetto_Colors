<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountDeactivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $reactivationUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Il tuo account Colors è stato eliminato');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.account-deactivated');
    }
}
