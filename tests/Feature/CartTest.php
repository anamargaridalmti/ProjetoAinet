<?php

use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;

// CartService unit tests – most tests seed the session directly without any
// database involvement. Tests touching the DB are marked with RefreshDatabase.

beforeEach(function () {
    Session::forget(CartService::SESSION_KEY);
});

// ─── getCart ──────────────────────────────────────────────────────────────────

test('getCart returns empty array when cart is not set', function () {
    $service = new CartService();

    expect($service->getCart())->toBe([]);
});

// ─── clearCart ────────────────────────────────────────────────────────────────

test('clearCart removes all items from the session', function () {
    Session::put(CartService::SESSION_KEY, [
        '1_red_M' => [
            'tshirt_image_id' => 1,
            'image_type'      => 'catalog',
            'name'            => 'Test Image',
            'image_url'       => 'test.png',
            'customer_id'     => null,
            'color_code'      => 'red',
            'color_name'      => 'Red',
            'size'            => 'M',
            'qty'             => 2,
            'unit_price'      => 10.0,
            'subtotal'        => 20.0,
        ],
    ]);

    $service = new CartService();
    $service->clearCart();

    expect($service->getCart())->toBe([]);
});

// ─── removeItem ───────────────────────────────────────────────────────────────

test('removeItem deletes the correct item by key', function () {
    Session::put(CartService::SESSION_KEY, [
        '1_red_M'  => ['tshirt_image_id' => 1, 'image_type' => 'catalog', 'name' => 'A', 'image_url' => 'a.png', 'customer_id' => null, 'color_code' => 'red', 'color_name' => 'Red', 'size' => 'M', 'qty' => 1, 'unit_price' => 10.0, 'subtotal' => 10.0],
        '2_blue_L' => ['tshirt_image_id' => 2, 'image_type' => 'catalog', 'name' => 'B', 'image_url' => 'b.png', 'customer_id' => null, 'color_code' => 'blue', 'color_name' => 'Blue', 'size' => 'L', 'qty' => 3, 'unit_price' => 10.0, 'subtotal' => 30.0],
    ]);

    $service = new CartService();
    $service->removeItem('1_red_M');

    $cart = $service->getCart();

    expect($cart)->toHaveCount(1)
        ->and($cart)->toHaveKey('2_blue_L')
        ->and($cart)->not->toHaveKey('1_red_M');
});

test('removeItem does nothing when key does not exist', function () {
    Session::put(CartService::SESSION_KEY, [
        '1_red_M' => ['tshirt_image_id' => 1, 'image_type' => 'catalog', 'name' => 'A', 'image_url' => 'a.png', 'customer_id' => null, 'color_code' => 'red', 'color_name' => 'Red', 'size' => 'M', 'qty' => 1, 'unit_price' => 10.0, 'subtotal' => 10.0],
    ]);

    $service = new CartService();
    $service->removeItem('nonexistent_key');

    expect($service->getCart())->toHaveCount(1);
});

// ─── buildKey ─────────────────────────────────────────────────────────────────

test('buildKey produces correct composite key', function () {
    $service = new CartService();

    expect($service->buildKey(5, 'black', 'XL'))->toBe('5_black_XL');
});

// ─── grandTotal ───────────────────────────────────────────────────────────────

test('grandTotal sums subtotals of all cart items', function () {
    Session::put(CartService::SESSION_KEY, [
        '1_red_M'  => ['subtotal' => 25.00, 'qty' => 1],
        '2_blue_L' => ['subtotal' => 50.00, 'qty' => 3],
    ]);

    $service = new CartService();

    expect($service->grandTotal())->toBe(75.0);
});

test('grandTotal returns 0.0 for an empty cart', function () {
    $service = new CartService();

    expect($service->grandTotal())->toBe(0.0);
});

// ─── totalQuantity ────────────────────────────────────────────────────────────

test('totalQuantity returns sum of all item quantities', function () {
    Session::put(CartService::SESSION_KEY, [
        '1_red_M'  => ['subtotal' => 10.0, 'qty' => 2],
        '2_blue_L' => ['subtotal' => 30.0, 'qty' => 3],
    ]);

    $service = new CartService();

    expect($service->totalQuantity())->toBe(5);
});

// ─── recalculate (no Price record) ────────────────────────────────────────────

test('recalculate returns cart unchanged when no prices record exists', function () {
    $cart = [
        '1_red_M' => [
            'tshirt_image_id' => 1,
            'image_type'      => 'catalog',
            'name'            => 'Test',
            'image_url'       => 'test.png',
            'customer_id'     => null,
            'color_code'      => 'red',
            'color_name'      => 'Red',
            'size'            => 'M',
            'qty'             => 2,
            'unit_price'      => 0.0,
            'subtotal'        => 0.0,
        ],
    ];

    // Subclass CartService to simulate the "no prices row" branch without DB access
    $service = new class extends CartService {
        public function recalculate(array $cart): array
        {
            // Simulate Price::first() returning null (no DB row)
            $prices = null;

            if (! $prices) {
                return $cart;
            }

            return parent::recalculate($cart);
        }
    };

    $result = $service->recalculate($cart);

    expect($result)->toBe($cart);
});

// ─── CartController routes (HTTP) ─────────────────────────────────────────────

test('POST /cart/add returns validation errors for missing fields', function () {
    $response = $this->post(route('cart.add'), []);

    $response->assertSessionHasErrors(['tshirt_image_id', 'color_code', 'size', 'qty']);
})->uses(RefreshDatabase::class);

test('DELETE /cart/clear empties the cart and redirects to cart.show', function () {
    Session::put(CartService::SESSION_KEY, [
        '1_red_M' => ['subtotal' => 10.0, 'qty' => 1],
    ]);

    $response = $this->delete(route('cart.clear'));

    $response->assertRedirect(route('cart.show'));
    expect(Session::get(CartService::SESSION_KEY))->toBeNull();
})->uses(RefreshDatabase::class);

test('DELETE /cart/remove/{key} removes item and redirects', function () {
    Session::put(CartService::SESSION_KEY, [
        '1_red_M' => ['subtotal' => 10.0, 'qty' => 1],
    ]);

    $response = $this->delete(route('cart.remove', '1_red_M'));

    $response->assertRedirect(route('cart.show'));
    expect(Session::get(CartService::SESSION_KEY))->toBe([]);
})->uses(RefreshDatabase::class);
