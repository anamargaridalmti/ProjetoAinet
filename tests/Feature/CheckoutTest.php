<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

uses(RefreshDatabase::class);

// ─── Helpers ──────────────────────────────────────────────────────────────────

/**
 * Builds a minimal cart item for session injection (no DB queries needed).
 *
 * @return array<string, mixed>
 */
function makeCartItem(int $imageId = 1): array
{
    return [
        'tshirt_image_id' => $imageId,
        'image_type'      => 'catalog',
        'name'            => 'Test Tee',
        'image_url'       => 'test.png',
        'customer_id'     => null,
        'color_code'      => 'white',
        'color_name'      => 'White',
        'size'            => 'M',
        'qty'             => 2,
        'unit_price'      => 15.00,
        'subtotal'        => 30.00,
    ];
}

/**
 * Builds valid Visa checkout form data.
 *
 * @return array<string, string>
 */
function validCheckoutData(): array
{
    return [
        'nif'          => '123456789',
        'address'      => 'Rua das Flores 123, 1000-001 Lisboa',
        'payment_type' => 'Visa',
        'payment_ref'  => '4111111111111111',
        'notes'        => '',
    ];
}

/**
 * Creates a User + linked Customer row.
 */
function makeUserWithCustomer(): User
{
    $user = User::factory()->create(['user_type' => 'C']);

    Customer::create([
        'id'                   => $user->id,
        'nif'                  => '987654321',
        'address'              => 'Rua Teste',
        'default_payment_type' => 'Visa',
        'default_payment_ref'  => '4000000000000000',
    ]);

    return $user;
}

/**
 * Seeds the minimum DB records required for order_items FK constraints:
 * a tshirt_image (id=1) and a color (code='white').
 */
function seedOrderDependencies(): void
{
    DB::table('colors')->insertOrIgnore([
        'code' => 'white',
        'name' => 'White',
    ]);

    if (DB::table('tshirt_images')->where('id', 1)->doesntExist()) {
        DB::table('tshirt_images')->insert([
            'id'          => 1,
            'name'        => 'Test Tee',
            'image_url'   => 'test.png',
            'customer_id' => null,
            'category_id' => null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
}

// ─── Authorization ────────────────────────────────────────────────────────────

test('GET /checkout redirects anonymous users to login', function () {
    $this->get(route('cart.checkout'))
        ->assertRedirectToRoute('login');
});

test('POST /checkout redirects anonymous users to login', function () {
    $this->post(route('cart.checkout.store'), validCheckoutData())
        ->assertRedirectToRoute('login');
});

// ─── show() ───────────────────────────────────────────────────────────────────

test('checkout page is accessible to authenticated users with a non-empty cart', function () {
    $user = makeUserWithCustomer();

    Session::put(CartService::SESSION_KEY, ['1_white_M' => makeCartItem()]);

    $this->actingAs($user)
        ->get(route('cart.checkout'))
        ->assertOk()
        ->assertViewIs('checkout.show')
        ->assertViewHas('cart')
        ->assertViewHas('total')
        ->assertViewHas('customer');
});

test('checkout page redirects to cart when cart is empty', function () {
    $user = makeUserWithCustomer();

    Session::forget(CartService::SESSION_KEY);

    $this->actingAs($user)
        ->get(route('cart.checkout'))
        ->assertRedirectToRoute('cart.show');
});

// ─── Validation ───────────────────────────────────────────────────────────────

test('checkout fails when NIF is not 9 digits', function () {
    $user = makeUserWithCustomer();
    Session::put(CartService::SESSION_KEY, ['1_white_M' => makeCartItem()]);

    Http::fake(['*' => Http::response([], 201)]);

    $this->actingAs($user)
        ->post(route('cart.checkout.store'), array_merge(validCheckoutData(), ['nif' => '123']))
        ->assertSessionHasErrors('nif');
});

test('checkout fails when payment_type is not allowed', function () {
    $user = makeUserWithCustomer();
    Session::put(CartService::SESSION_KEY, ['1_white_M' => makeCartItem()]);

    $this->actingAs($user)
        ->post(route('cart.checkout.store'), array_merge(validCheckoutData(), ['payment_type' => 'MasterCard']))
        ->assertSessionHasErrors('payment_type');
});

test('Visa payment_ref must be 16 digits starting with 4', function () {
    $user = makeUserWithCustomer();
    Session::put(CartService::SESSION_KEY, ['1_white_M' => makeCartItem()]);

    $this->actingAs($user)
        ->post(route('cart.checkout.store'), array_merge(validCheckoutData(), [
            'payment_type' => 'Visa',
            'payment_ref'  => '5111111111111111', // starts with 5 – invalid
        ]))
        ->assertSessionHasErrors('payment_ref');
});

test('PayPal payment_ref must be a valid email', function () {
    $user = makeUserWithCustomer();
    Session::put(CartService::SESSION_KEY, ['1_white_M' => makeCartItem()]);

    $this->actingAs($user)
        ->post(route('cart.checkout.store'), array_merge(validCheckoutData(), [
            'payment_type' => 'PayPal',
            'payment_ref'  => 'not-an-email',
        ]))
        ->assertSessionHasErrors('payment_ref');
});

test('MB WAY payment_ref must be 9 digits starting with 9', function () {
    $user = makeUserWithCustomer();
    Session::put(CartService::SESSION_KEY, ['1_white_M' => makeCartItem()]);

    $this->actingAs($user)
        ->post(route('cart.checkout.store'), array_merge(validCheckoutData(), [
            'payment_type' => 'MB WAY',
            'payment_ref'  => '8123456789', // starts with 8 – invalid
        ]))
        ->assertSessionHasErrors('payment_ref');
});

test('valid PayPal email passes validation', function () {
    $user = makeUserWithCustomer();
    seedOrderDependencies();
    Session::put(CartService::SESSION_KEY, ['1_white_M' => makeCartItem()]);

    Http::fake(['*' => Http::response(['id' => 'txn_1'], 201)]);

    $this->actingAs($user)
        ->post(route('cart.checkout.store'), [
            'nif'          => '123456789',
            'address'      => 'Rua das Flores 123, Lisboa',
            'payment_type' => 'PayPal',
            'payment_ref'  => 'customer@example.com',
            'notes'        => null,
        ])
        ->assertSessionHasNoErrors();
});

// ─── store() – Payment API integration ───────────────────────────────────────

test('failed payment API response redirects back with error and does not create order', function () {
    $user = makeUserWithCustomer();
    Session::put(CartService::SESSION_KEY, ['1_white_M' => makeCartItem()]);

    Http::fake(['*' => Http::response(['error' => 'Declined'], 422)]);

    $this->actingAs($user)
        ->post(route('cart.checkout.store'), validCheckoutData())
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(Order::count())->toBe(0);
});

test('successful checkout creates order and order_items in the database', function () {
    $user = makeUserWithCustomer();
    seedOrderDependencies();
    $cart = ['1_white_M' => makeCartItem(1)];
    Session::put(CartService::SESSION_KEY, $cart);

    Http::fake(['*' => Http::response(['id' => 'txn_ok'], 201)]);

    $this->actingAs($user)
        ->post(route('cart.checkout.store'), validCheckoutData())
        ->assertSessionHasNoErrors()
        ->assertRedirectToRoute('orders.index');

    expect(Order::count())->toBe(1);
    expect(OrderItem::count())->toBe(1);

    $order = Order::first();
    expect($order->status)->toBe('pending');
    expect($order->payment_type)->toBe('Visa');
    expect((float) $order->total_price)->toBe(30.0);
});

test('successful checkout clears the cart session', function () {
    $user = makeUserWithCustomer();
    seedOrderDependencies();
    Session::put(CartService::SESSION_KEY, ['1_white_M' => makeCartItem()]);

    Http::fake(['*' => Http::response(['id' => 'txn_ok'], 201)]);

    $this->actingAs($user)
        ->post(route('cart.checkout.store'), validCheckoutData());

    expect(Session::get(CartService::SESSION_KEY))->toBeNull();
});

test('unit_price and sub_total in order_items match the cart session values exactly', function () {
    $user = makeUserWithCustomer();
    seedOrderDependencies();
    $item = makeCartItem();
    Session::put(CartService::SESSION_KEY, ['1_white_M' => $item]);

    Http::fake(['*' => Http::response(['id' => 'txn_ok'], 201)]);

    $this->actingAs($user)
        ->post(route('cart.checkout.store'), validCheckoutData());

    $orderItem = OrderItem::first();
    expect((float) $orderItem->unit_price)->toBe($item['unit_price']);
    expect((float) $orderItem->sub_total)->toBe($item['subtotal']);
});
