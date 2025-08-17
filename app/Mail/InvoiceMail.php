<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $pdf;
    public $orderItems;
    public $web;

    /**
     * Create a new message instance.
     */
    public function __construct($order,$orderItems,$pdf,$web)
    {
        $this->order = $order;
        $this->orderItems = $orderItems;
        $this->pdf   = $pdf;
        $this->web   = $web;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Invoice #' . $this->order->order_id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'invoice', // <-- create this blade
            with: [
                'order' => $this->order,
                'orderItems' => $this->orderItems,
                'web' => $this->web,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdf->output(), 'invoice_' . $this->order->order_id . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
