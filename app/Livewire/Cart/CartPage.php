<?php

namespace App\Livewire\Cart;

use App\Models\Color;
use App\Models\Price;
use App\Services\CartService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class CartPage extends Component
{
    /** @var array<string, mixed> */
    public array $cart = [];

    public float $total = 0.0;

    public ?object $prices = null;

    /**
     * Tracks per-item edit state: ['key' => ['qty' => x, 'color_code' => y, 'size' => z]]
     *
     * @var array<string, array{qty: int, color_code: string, size: string}>
     */
    public array $items = [];

    /** @var array<int, object> */
    public array $colors = [];

    public function mount(CartService $cartService): void
    {
        $this->colors = Color::orderBy('name')->get()->all();
        $this->prices = Price::first();
        $this->refreshCart($cartService);
    }

    /**
     * Updates an individual cart item (qty, colour, size).
     * Removing automatically happens when qty reaches 0.
     */
    public function updateItem(CartService $cartService, string $key): void
    {
        $data = $this->items[$key] ?? [];

        $qty       = (int) ($data['qty'] ?? 0);
        $colorCode = (string) ($data['color_code'] ?? '');
        $size      = (string) ($data['size'] ?? '');

        $this->validate([
            "items.{$key}.qty"        => 'required|integer|min:0',
            "items.{$key}.color_code" => 'required|exists:colors,code',
            "items.{$key}.size"       => 'required|in:XS,S,M,L,XL',
        ]);

        $cartService->updateItem($key, $qty, $colorCode, $size);
        $this->refreshCart($cartService);
    }

    /**
     * Removes a single item from the cart.
     */
    public function removeItem(CartService $cartService, string $key): void
    {
        $cartService->removeItem($key);
        $this->refreshCart($cartService);
    }

    /**
     * Empties the whole cart.
     */
    public function clearCart(CartService $cartService): void
    {
        $cartService->clearCart();
        $this->refreshCart($cartService);
    }

    /**
     * Reloads cart data from the session and syncs the editable items array.
     */
    private function refreshCart(CartService $cartService): void
    {
        $this->cart  = $cartService->recalculate($cartService->getCart());
        $this->total = $cartService->grandTotal();

        // Sync editable state with current cart (preserves existing edits)
        $this->items = [];
        foreach ($this->cart as $key => $item) {
            $this->items[$key] = [
                'qty'        => $item['qty'],
                'color_code' => $item['color_code'],
                'size'       => $item['size'],
            ];
        }
    }

    #[Layout('layouts.app')]
    public function render(): \Illuminate\View\View
    {
        return view('livewire.cart.cart-page')->layout('layouts.app', ['title' => 'O Meu Carrinho']);
    }
}
