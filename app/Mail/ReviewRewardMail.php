<?php

namespace App\Mail;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewRewardMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Discount $discount
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [new Address($this->user->email, $this->user->first_name . ' ' . $this->user->last_name)],
            subject: 'Grazie per la tua recensione! Ecco il tuo sconto — Colors S.r.l.',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.review-reward');
    }
}
