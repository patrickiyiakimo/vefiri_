<?php

namespace App\Mail;

use App\Mail\Concerns\UsesVefiriBrand;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginNotificationMail extends Mailable
{
    use Queueable, SerializesModels, UsesVefiriBrand;

    public function __construct(
        public User $user,
        public string $signedInAt,
        public ?string $ipAddress,
        public ?string $device,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New sign-in to your Vefiri account',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.login-notification',
            with: [
                ...$this->brandData(),
                'user' => $this->user,
                'signedInAt' => $this->signedInAt,
                'ipAddress' => $this->ipAddress,
                'device' => $this->device,
                'accountUrl' => route('account.show'),
            ],
        );
    }
}
