<?php

namespace App\Mail;

use App\Mail\Concerns\UsesVefiriBrand;
use App\Models\Vendor;
use App\Models\VendorApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorApplicationApprovedMail extends Mailable
{
    use Queueable, SerializesModels, UsesVefiriBrand;

    public function __construct(
        public VendorApplication $application,
        public Vendor $vendor,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Vefiri vendor application has been approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vendors.application-approved',
            with: [
                ...$this->brandData(),
                'application' => $this->application,
                'vendor' => $this->vendor,
                'vendorDashboardUrl' => route('vendor.dashboard'),
                'applicationUrl' => route('vendors.status'),
            ],
        );
    }
}
