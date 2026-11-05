<?php

namespace App\Mail;

use App\Mail\Concerns\UsesVefiriBrand;
use App\Models\VendorApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorApplicationReceivedMail extends Mailable
{
    use Queueable, SerializesModels, UsesVefiriBrand;

    public function __construct(public VendorApplication $application) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We received your Vefiri vendor application',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vendors.application-received',
            with: [
                ...$this->brandData(),
                'application' => $this->application,
                'applicationUrl' => route('vendors.status'),
            ],
        );
    }
}
