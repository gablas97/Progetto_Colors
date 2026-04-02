<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $resetUrl;

    public function __construct(
        public User $user,
        string $token
    ) {
        $this->resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [new Address($this->user->email, $this->user->first_name . ' ' . $this->user->last_name)],
            subject: 'Reimposta la tua password - Colors S.r.l.',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.reset-password');
    }
}
