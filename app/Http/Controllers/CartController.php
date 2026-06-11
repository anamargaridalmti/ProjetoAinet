<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(public CartService $cartService) {}

    /**
     * Displays the shopping cart page with up-to-date prices.
     */
    public function show(): View
    {
        $cart   = $this->cartService->recalculate($this->cartService->getCart());
        $total  = $this->cartService->grandTotal();
        $prices = \App\Models\Price::first();

        return view('cart.show', compact('cart', 'total', 'prices'));
    }

    /**
     * Adds an item to the cart, merging quantity if the combination already exists.
     */
    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tshirt_image_id' => 'required|exists:tshirt_images,id',
            'color_code'      => 'required|exists:colors,code',
            'size'            => 'required|in:XS,S,M,L,XL',
            'qty'             => 'required|integer|min:1',
        ]);

        $this->cartService->addItem(
            tshirtImageId: (int) $validated['tshirt_image_id'],
            colorCode: $validated['color_code'],
            size: $validated['size'],
            qty: (int) $validated['qty'],
        );

        return redirect()->route('cart.show')
            ->with('status', 'Item adicionado ao carrinho com sucesso!');
    }

    /**
     * Updates an existing cart line (quantity, colour or size).
     * Removes the item if quantity reaches zero.
     */
    public function update(Request $request, string $key): RedirectResponse
    {
        $cart = $this->cartService->getCart();

        if (! isset($cart[$key])) {
            return redirect()->route('cart.show')
                ->with('error', 'Item não encontrado no carrinho.');
        }

        $validated = $request->validate([
            'qty'        => 'required|integer|min:0',
            'color_code' => 'required|exists:colors,code',
            'size'       => 'required|in:XS,S,M,L,XL',
        ]);

        $this->cartService->updateItem(
            key: $key,
            qty: (int) $validated['qty'],
            colorCode: $validated['color_code'],
            size: $validated['size'],
        );

        $message = (int) $validated['qty'] <= 0
            ? 'Item removido do carrinho.'
            : 'Carrinho atualizado!';

        return redirect()->route('cart.show')->with('status', $message);
    }

    /**
     * Removes a single item from the cart.
     */
    public function destroy(string $key): RedirectResponse
    {
        $this->cartService->removeItem($key);

        return redirect()->route('cart.show')
            ->with('status', 'O item foi removido do carrinho.');
    }

    /**
     * Empties the entire cart in a single operation.
     */
    public function clear(): RedirectResponse
    {
        $this->cartService->clearCart();

        return redirect()->route('cart.show')
            ->with('status', 'O seu carrinho está vazio.');
    }
}
