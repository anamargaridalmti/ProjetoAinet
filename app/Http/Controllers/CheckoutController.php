<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Mail\OrderPendingMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(public CartService $cartService) {}

    /**
     * Displays the checkout form, pre-filled with the customer's saved details.
     * Redirects anonymous users to login (handled by the auth middleware on the route).
     */
    public function show(Request $request): View|RedirectResponse
    {
        $cart = $this->cartService->recalculate($this->cartService->getCart());

        if (empty($cart)) {
            return redirect()->route('cart.show')
                ->with('error', 'O seu carrinho está vazio. Adicione produtos antes de fazer checkout.');
        }

        $total    = $this->cartService->grandTotal();
        $customer = auth()->user()->customer;

        return view('checkout.show', compact('cart', 'total', 'customer'));
    }

    /**
     * Processes the checkout:
     * 1. Calls the external payments API.
     * 2. On success (201), records the Order and OrderItems inside a transaction.
     * 3. Clears the cart session and redirects to the orders history page.
     */
    public function store(CheckoutRequest $request): RedirectResponse
    {
        $cart = $this->cartService->recalculate($this->cartService->getCart());

        if (empty($cart)) {
            return redirect()->route('cart.show')
                ->with('error', 'O seu carrinho está vazio.');
        }

        $total    = $this->cartService->grandTotal();
        $user     = auth()->user();
        $customer = $user->customer ?? \App\Models\Customer::firstOrCreate(
            ['id' => $user->id],
            ['nif' => null, 'address' => null, 'default_payment_type' => null, 'default_payment_ref' => null]
        );
        $data     = $request->validated();

        // --- 1. Call external Payments API ---
        $paymentResponse = Http::timeout(15)->post('https://ainet-payments-api.vercel.app/api/payments', [
            'type'      => $data['payment_type'],
            'reference' => $data['payment_ref'],
            'value'     => round($total, 2),
        ]);

        if ($paymentResponse->status() !== 201) {
            return back()->withInput()
                ->with('error', 'O pagamento não foi processado. Verifique os seus dados e tente novamente.');
        }

        // --- 2. Record Order + OrderItems inside a transaction ---
        DB::transaction(function () use ($cart, $total, $customer, $data): void {
            $order = Order::create([
                'status'       => 'pending',
                'customer_id'  => $customer->id,
                'date'         => now()->toDateString(),
                'total_price'  => $total,
                'nif'          => $data['nif'],
                'address'      => $data['address'],
                'payment_type' => $data['payment_type'],
                'payment_ref'  => $data['payment_ref'],
                'notes'        => $data['notes'] ?? null,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'       => $order->id,
                    'tshirt_image_id' => $item['tshirt_image_id'],
                    'color_code'     => $item['color_code'],
                    'size'           => $item['size'],
                    'qty'            => $item['qty'],
                    'unit_price'     => $item['unit_price'],
                    'sub_total'      => $item['subtotal'],
                ]);
            }
        });

        // --- 3. Send confirmation e-mail ---
        $order = Order::with('customer.user')->where('customer_id', $customer->id)->latest()->first();
        if ($order && $order->customer?->user) {
            Mail::to($order->customer->user->email)->send(new OrderPendingMail($order));
        }

        // --- 4. Clear cart and redirect ---
        $this->cartService->clearCart();

        return redirect()->route('orders.index')
            ->with('success', 'A sua encomenda foi registada com sucesso! Obrigado pela sua compra 🎉');
    }
}
