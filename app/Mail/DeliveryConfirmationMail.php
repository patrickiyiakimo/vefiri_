<?php

namespace App\Mail;

use App\Mail\Concerns\UsesVefiriBrand;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeliveryConfirmationMail extends Mailable
{
    use Queueable, SerializesModels, UsesVefiriBrand;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Vefiri order has been delivered',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.delivered',
            with: [
                ...$this->brandData(),
                'order' => $this->order,
                'orderUrl' => route('orders.show', $this->order),
            ],
        );
    }
}
