<?php

namespace App\Services;

use App\Mail\OrderClosedMail;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/**
 * Generates the PDF receipt for a closed order, stores it in
 * storage/app/private/pdf_receipts/, updates the order's receipt_url
 * column, and sends the OrderClosedMail with the PDF attached.
 */
class ReceiptService
{
    /**
     * Generate, store and e-mail the PDF receipt for the given order.
     * The order must already be loaded with its 'items' and 'customer' relations.
     */
    public function generateAndSend(Order $order): void
    {
        $order->loadMissing(['items', 'customer.user' => fn ($q) => $q->withTrashed()]);

        // Build the PDF from a dedicated Blade view
        $pdf = Pdf::loadView('pdf.receipt', ['order' => $order]);

        // Store in storage/app/private/pdf_receipts/ (never publicly accessible)
        $relativePath = 'pdf_receipts/receipt_order_' . $order->id . '.pdf';

        Storage::disk('local')->put($relativePath, $pdf->output());

        // Persist the path in the database for future downloads
        $order->update(['receipt_url' => $relativePath]);

        // Send the e-mail with the PDF attached
        $customerUser = $order->customer->user ?? null;
        if ($customerUser) {
            Mail::to($customerUser->email)->send(new OrderClosedMail($order));
        }
    }
}
