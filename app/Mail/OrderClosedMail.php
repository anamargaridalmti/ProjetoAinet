<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class OrderClosedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'FunShirt – Encomenda #' . $this->order->id . ' concluída – Recibo em anexo',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.closed',
        );
    }

    /**
     * Attaches the PDF receipt stored in storage/app/private/pdf_receipts/.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        if (! $this->order->receipt_url) {
            return [];
        }

        $absolutePath = Storage::disk('local')->path($this->order->receipt_url);

        return [
            Attachment::fromPath($absolutePath)
                ->as('recibo_encomenda_' . $this->order->id . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
