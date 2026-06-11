<?php

use App\Mail\OrderClosedMail;
use App\Mail\OrderPendingMail;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\ReceiptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

// ─── Helpers ─────────────────────────────────────────────────────────────────

function makeUserCustomer(string $type = 'C'): User
{
    $user = User::factory()->create(['user_type' => $type]);

    if ($type === 'C') {
        Customer::create([
            'id'                   => $user->id,
            'nif'                  => '123456789',
            'address'              => 'Rua Teste 1',
            'default_payment_type' => 'Visa',
            'default_payment_ref'  => '4111111111111111',
        ]);
    }

    return $user;
}

function makeClosedOrder(User $user): Order
{
    return Order::create([
        'status'       => 'closed',
        'customer_id'  => $user->id,
        'date'         => now()->toDateString(),
        'total_price'  => 30.00,
        'nif'          => '123456789',
        'address'      => 'Rua Teste 1',
        'payment_type' => 'Visa',
        'payment_ref'  => '4111111111111111',
        'receipt_url'  => 'pdf_receipts/receipt_order_1.pdf',
    ]);
}

// ─── OrderPolicy – downloadReceipt ───────────────────────────────────────────

test('anonymous user cannot download receipt (401)', function () {
    $customer = makeUserCustomer('C');
    $order    = makeClosedOrder($customer);

    $this->get(route('orders.receipt.download', $order))
        ->assertRedirect(route('login'));
});

test('employee cannot download receipt (403)', function () {
    $employee = makeUserCustomer('F');
    $customer = makeUserCustomer('C');
    $order    = makeClosedOrder($customer);

    $this->actingAs($employee)
        ->get(route('orders.receipt.download', $order))
        ->assertForbidden();
});

test('another customer cannot download someone elses receipt (403)', function () {
    $customer1 = makeUserCustomer('C');
    $customer2 = makeUserCustomer('C');
    $order     = makeClosedOrder($customer1);

    $this->actingAs($customer2)
        ->get(route('orders.receipt.download', $order))
        ->assertForbidden();
});

test('customer cannot download receipt of a pending order (403)', function () {
    $customer = makeUserCustomer('C');
    $order    = Order::create([
        'status' => 'pending', 'customer_id' => $customer->id,
        'date' => now()->toDateString(), 'total_price' => 30.00,
        'nif' => '123456789', 'address' => 'Rua', 'payment_type' => 'Visa',
        'payment_ref' => '4111111111111111',
    ]);

    $this->actingAs($customer)
        ->get(route('orders.receipt.download', $order))
        ->assertForbidden();
});

test('customer can download their own closed order receipt', function () {
    $customer = makeUserCustomer('C');
    $order    = makeClosedOrder($customer);

    // Put a fake PDF in the fake disk
    Storage::fake('local');
    Storage::disk('local')->put($order->receipt_url, '%PDF-fake');

    $this->actingAs($customer)
        ->get(route('orders.receipt.download', $order))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

test('admin can download any closed order receipt', function () {
    $admin    = makeUserCustomer('A');
    $customer = makeUserCustomer('C');
    $order    = makeClosedOrder($customer);

    Storage::fake('local');
    Storage::disk('local')->put($order->receipt_url, '%PDF-fake');

    $this->actingAs($admin)
        ->get(route('orders.receipt.download', $order))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

// ─── ReceiptService ───────────────────────────────────────────────────────────

test('ReceiptService stores PDF in pdf_receipts and updates receipt_url', function () {
    Storage::fake('local');
    Mail::fake();

    $customer = makeUserCustomer('C');
    $order    = Order::create([
        'status' => 'closed', 'customer_id' => $customer->id,
        'date' => now()->toDateString(), 'total_price' => 30.00,
        'nif' => '123456789', 'address' => 'Rua Teste 1', 'payment_type' => 'Visa',
        'payment_ref' => '4111111111111111',
    ]);

    app(ReceiptService::class)->generateAndSend($order);

    $expectedPath = 'pdf_receipts/receipt_order_' . $order->id . '.pdf';
    Storage::disk('local')->assertExists($expectedPath);

    $order->refresh();
    expect($order->receipt_url)->toBe($expectedPath);
});

test('ReceiptService sends OrderClosedMail to customer email', function () {
    Storage::fake('local');
    Mail::fake();

    $customer = makeUserCustomer('C');
    $order    = Order::create([
        'status' => 'closed', 'customer_id' => $customer->id,
        'date' => now()->toDateString(), 'total_price' => 30.00,
        'nif' => '123456789', 'address' => 'Rua Teste 1', 'payment_type' => 'Visa',
        'payment_ref' => '4111111111111111',
    ]);

    app(ReceiptService::class)->generateAndSend($order);

    Mail::assertSent(OrderClosedMail::class, fn ($mail) => $mail->hasTo($customer->email));
});

// ─── OrderPendingMail dispatch on checkout ────────────────────────────────────

test('OrderPendingMail is sent after successful checkout', function () {
    Mail::fake();

    $customer = makeUserCustomer('C');
    Session::put('cart', []);
    Http::fake(['*' => Http::response(['id' => 'txn_1'], 201)]);

    // Seed FK dependencies
    DB::table('colors')->insertOrIgnore(['code' => 'white', 'name' => 'White']);
    if (DB::table('tshirt_images')->where('id', 1)->doesntExist()) {
        DB::table('tshirt_images')->insert([
            'id' => 1, 'name' => 'Tee', 'image_url' => 'tee.png',
            'customer_id' => null, 'category_id' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    Session::put('cart', ['1_white_M' => [
        'tshirt_image_id' => 1, 'image_type' => 'catalog',
        'name' => 'Tee', 'image_url' => 'tee.png', 'customer_id' => null,
        'color_code' => 'white', 'color_name' => 'White', 'size' => 'M',
        'qty' => 2, 'unit_price' => 15.00, 'subtotal' => 30.00,
    ]]);

    $this->actingAs($customer)->post(route('cart.checkout.store'), [
        'nif' => '123456789', 'address' => 'Rua Flores 1, Lisboa',
        'payment_type' => 'Visa', 'payment_ref' => '4111111111111111', 'notes' => null,
    ]);

    Mail::assertSent(OrderPendingMail::class);
});
