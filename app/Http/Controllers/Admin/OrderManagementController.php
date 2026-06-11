<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Services\ReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderManagementController extends Controller
{
    public function __construct(private readonly ReceiptService $receiptService) {}

    /**
     * Lists orders according to the authenticated user's role.
     *
     * - Employee (F): only sees 'pending' orders, no filters.
     * - Admin (A): sees all orders with filters (status, customer, date).
     */
    public function index(Request $request): View
    {
        $userType = auth()->user()->user_type;

        if ($userType === 'F') {
            // Employees only see pending orders, ordered by date ascending (oldest first)
            $orders = Order::with('customer.user')
                ->where('status', 'pending')
                ->orderBy('date')
                ->orderBy('id')
                ->paginate(20);

            return view('admin.orders.employee', compact('orders'));
        }

        // Admin: full listing with filters
        $query = Order::with('customer.user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $orders    = $query->orderByDesc('date')->orderByDesc('id')->paginate(20)->withQueryString();
        $customers = Customer::with('user')->get()->sortBy(fn ($c) => $c->user?->name);

        return view('admin.orders.admin', compact('orders', 'customers'));
    }

    /**
     * Close a pending order: generate PDF receipt and send e-mail.
     *
     * Employees (F) and Admins (A) can close pending orders.
     */
    public function close(Order $order): RedirectResponse
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Apenas encomendas pendentes podem ser fechadas.');
        }

        $order->update(['status' => 'closed']);

        // G6: generate PDF + send OrderClosedMail with attachment
        $this->receiptService->generateAndSend($order);

        return redirect()->route('admin.orders.index')
            ->with('success', "Encomenda #{$order->id} fechada com sucesso. Recibo gerado e enviado ao cliente.");
    }

    /**
     * Cancel an order (Admin only).
     * The reason for cancellation is optional.
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        // Only Admins may cancel
        if (auth()->user()->user_type !== 'A') {
            abort(403, 'Apenas administradores podem cancelar encomendas.');
        }

        if ($order->status === 'closed') {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Encomendas já fechadas não podem ser canceladas.');
        }

        $order->update([
            'status'                  => 'canceled',
            'reason_for_cancellation' => $request->input('reason') ?: null,
        ]);

        return redirect()->route('admin.orders.index')
            ->with('success', "Encomenda #{$order->id} cancelada com sucesso.");
    }
}
