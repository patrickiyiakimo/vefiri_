<?php

namespace App\Mail;

use App\Mail\Concerns\UsesVefiriBrand;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels, UsesVefiriBrand;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Vefiri',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.welcome',
            with: [
                ...$this->brandData(),
                'user' => $this->user,
                'accountUrl' => route('account.show'),
                'homeUrl' => route('home'),
            ],
        );
    }
}
