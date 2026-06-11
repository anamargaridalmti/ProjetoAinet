<?php

namespace App\Services;

use App\Models\Color;
use App\Models\Price;
use App\Models\TshirtImage;
use Illuminate\Support\Facades\Session;

/**
 * Manages the shopping cart stored exclusively in the server-side session.
 * No database table is used for the cart; items persist across login events
 * because the session is preserved on authentication.
 *
 * Session key: 'cart'
 * Cart item structure:
 * @phpstan-type CartItem array{
 *   tshirt_image_id: int,
 *   image_type: 'catalog'|'own',
 *   name: string,
 *   image_url: string,
 *   customer_id: int|null,
 *   color_code: string,
 *   color_name: string,
 *   size: string,
 *   qty: int,
 *   unit_price: float,
 *   subtotal: float,
 * }
 */
class CartService
{
    public const SESSION_KEY = 'cart';

    /**
     * Returns the raw cart array from the session.
     *
     * @return array<string, mixed>
     */
    public function getCart(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    /**
     * Adds an item to the cart. If an item with the same image, colour and
     * size already exists its quantity is incremented rather than duplicated.
     */
    public function addItem(
        int $tshirtImageId,
        string $colorCode,
        string $size,
        int $qty
    ): void {
        $image = TshirtImage::findOrFail($tshirtImageId);
        $color = Color::findOrFail($colorCode);

        $cart = $this->getCart();
        $cartKey = $this->buildKey($tshirtImageId, $colorCode, $size);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['qty'] += $qty;
        } else {
            $cart[$cartKey] = [
                'tshirt_image_id' => $image->id,
                'image_type'      => is_null($image->customer_id) ? 'catalog' : 'own',
                'name'            => $image->name,
                'image_url'       => $image->image_url,
                'customer_id'     => $image->customer_id,
                'color_code'      => $color->code,
                'color_name'      => $color->name,
                'size'            => $size,
                'qty'             => $qty,
                'unit_price'      => 0.0,
                'subtotal'        => 0.0,
            ];
        }

        $cart = $this->recalculate($cart);
        Session::put(self::SESSION_KEY, $cart);
    }

    /**
     * Updates an existing cart item's quantity, colour and/or size.
     * If the new quantity is 0 or less the item is removed automatically.
     * If the new colour+size combination matches another existing line,
     * the quantities are merged.
     */
    public function updateItem(string $key, int $qty, string $colorCode, string $size): void
    {
        $cart = $this->getCart();

        if (! isset($cart[$key])) {
            return;
        }

        if ($qty <= 0) {
            $this->removeItem($key);

            return;
        }

        $color = Color::findOrFail($colorCode);
        $newKey = $this->buildKey($cart[$key]['tshirt_image_id'], $colorCode, $size);

        if ($newKey !== $key && isset($cart[$newKey])) {
            // Merge into the existing matching line
            $cart[$newKey]['qty'] += $qty;
            unset($cart[$key]);
        } else {
            $cart[$key]['qty']        = $qty;
            $cart[$key]['color_code'] = $color->code;
            $cart[$key]['color_name'] = $color->name;
            $cart[$key]['size']       = $size;

            if ($newKey !== $key) {
                $cart[$newKey] = $cart[$key];
                unset($cart[$key]);
            }
        }

        $cart = $this->recalculate($cart);
        Session::put(self::SESSION_KEY, $cart);
    }

    /**
     * Removes a single item from the cart by its session key.
     */
    public function removeItem(string $key): void
    {
        $cart = $this->getCart();
        unset($cart[$key]);
        Session::put(self::SESSION_KEY, $cart);
    }

    /**
     * Empties the entire cart from the session.
     */
    public function clearCart(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Calculates and returns the grand total for all items in the cart.
     */
    public function grandTotal(): float
    {
        return (float) array_sum(array_column($this->getCart(), 'subtotal'));
    }

    /**
     * Returns the total number of individual units across all cart lines.
     */
    public function totalQuantity(): int
    {
        return (int) array_sum(array_column($this->getCart(), 'qty'));
    }

    /**
     * Recalculates unit_price and subtotal for every cart item based on
     * current prices and quantity discount thresholds.
     *
     * @param  array<string, mixed>  $cart
     * @return array<string, mixed>
     */
    public function recalculate(array $cart): array
    {
        $prices = Price::first();

        if (! $prices) {
            return $cart;
        }

        foreach ($cart as $key => $item) {
            $applyDiscount = $item['qty'] >= $prices->qty_discount;
            $isCatalog     = ($item['image_type'] ?? 'catalog') === 'catalog';

            $unitPrice = match (true) {
                $isCatalog && $applyDiscount   => (float) $prices->unit_price_catalog_discount,
                $isCatalog && ! $applyDiscount => (float) $prices->unit_price_catalog,
                ! $isCatalog && $applyDiscount => (float) $prices->unit_price_own_discount,
                default                        => (float) $prices->unit_price_own,
            };

            $cart[$key]['unit_price'] = $unitPrice;
            $cart[$key]['subtotal']   = round($unitPrice * $item['qty'], 2);
        }

        return $cart;
    }

    /**
     * Builds the composite session key for a cart item.
     */
    public function buildKey(int $tshirtImageId, string $colorCode, string $size): string
    {
        return "{$tshirtImageId}_{$colorCode}_{$size}";
    }
}
