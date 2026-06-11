<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    /**
     * Lists the authenticated customer's orders, most recent first.
     */
    public function index(Request $request): View
    {
        $customer = auth()->user()->customer;

        $orders = Order::where('customer_id', $customer->id)
            ->with('items')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Download/view the PDF receipt for an order.
     *
     * Authorization: Admins always, customers only for their own closed orders.
     * Employees and strangers receive 403 Forbidden.
     * The file is served from storage/app/private (never from public/).
     */
    public function downloadReceipt(Order $order): StreamedResponse|Response
    {
        // Enforce OrderPolicy@downloadReceipt – throws 403 on failure
        Gate::authorize('downloadReceipt', $order);

        if (! $order->receipt_url) {
            abort(404, 'O recibo ainda não está disponível para esta encomenda.');
        }

        $disk = \Illuminate\Support\Facades\Storage::disk('local');

        if (! $disk->exists($order->receipt_url)) {
            abort(404, 'Ficheiro de recibo não encontrado.');
        }

        $filename = 'recibo_encomenda_' . $order->id . '.pdf';

        return $disk->download($order->receipt_url, $filename, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generates the PDF receipt when an order is closed (called by staff/admin controller).
     */
    public function generateReceipt(Order $order, ReceiptService $receiptService): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('update', $order);

        $receiptService->generateAndSend($order);

        return back()->with('success', 'Recibo gerado e enviado ao cliente com sucesso.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
