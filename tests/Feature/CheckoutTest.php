<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use App\Services\ProductCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'paystack.secret_key' => 'sk_test_secret',
            'paystack.base_url' => 'https://api.paystack.co',
            'paystack.currency' => 'NGN',
            'paystack.callback_url' => 'https://vefiri.test/checkout/callback',
            'checkout.tax_rate' => 7.5,
            'checkout.tax_after_discount' => true,
            'checkout.shipping_fee' => 0,
        ]);
    }

    public function test_guests_cannot_initialize_checkout(): void
    {
        $this->postJson(route('checkout.store'), [])->assertUnauthorized();
    }

    public function test_the_checkout_page_shows_the_cart_summary_and_payment_form(): void
    {
        $user = User::factory()->create([
            'name' => 'Ada Obi',
            'email' => 'ada@example.com',
        ]);
        $product = $this->createVendorProduct(10_000);
        $cart = Cart::factory()->for($user)->create();
        CartItem::factory()->for($cart)->create([
            'product_key' => $product->catalogKey(),
            'quantity' => 2,
            'unit_price' => 10_000,
        ]);

        $this->actingAs($user)
            ->get(route('checkout.show'))
            ->assertOk()
            ->assertSeeText('Secure checkout')
            ->assertSeeText($product->name)
            ->assertSeeText('20,000')
            ->assertSeeText('Tax (7.5%)')
            ->assertSeeText('21,500')
            ->assertSeeText('ada@example.com')
            ->assertSeeText('Delivery address')
            ->assertSee('name="delivery_address"', false)
            ->assertSee('name="delivery_city"', false)
            ->assertSee('name="delivery_state"', false)
            ->assertSee('data-checkout-form', false)
            ->assertSee('data-idempotency-key', false)
            ->assertSee('data-checkout-items', false);
    }

    public function test_the_cart_drawer_links_to_checkout(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('home'))
            ->assertOk()
            ->assertSee('href="'.route('checkout.show').'"', false);
    }

    public function test_the_checkout_page_redirects_to_the_cart_when_it_is_empty(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('checkout.show'))
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('status');
    }

    public function test_guests_cannot_view_the_checkout_page(): void
    {
        $this->get(route('checkout.show'))->assertRedirect(route('login'));
    }

    public function test_the_callback_page_shows_a_confirmed_payment(): void
    {
        $user = User::factory()->create();
        Order::factory()->for($user)->paid()->create([
            'payment_reference' => 'VEF_CALLBACK_PAID',
        ]);

        $this->actingAs($user)
            ->get(route('checkout.callback', ['reference' => 'VEF_CALLBACK_PAID']))
            ->assertOk()
            ->assertSeeText('Payment confirmed')
            ->assertSeeText('VEF_CALLBACK_PAID');
    }

    public function test_the_callback_page_shows_a_pending_payment(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/verify/VEF_CALLBACK_PENDING' => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'pending',
                    'reference' => 'VEF_CALLBACK_PENDING',
                    'amount' => 12_800,
                    'currency' => 'NGN',
                ],
            ]),
        ]);

        $user = User::factory()->create();
        Order::factory()->for($user)->create([
            'payment_reference' => 'VEF_CALLBACK_PENDING',
            'total' => 12_800,
        ]);

        $this->actingAs($user)
            ->get(route('checkout.callback', ['reference' => 'VEF_CALLBACK_PENDING']))
            ->assertOk()
            ->assertSeeText('Payment is being confirmed');
    }

    public function test_the_callback_page_confirms_a_successful_payment_and_removes_purchased_cart_items(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/verify/VEF_CALLBACK_CONFIRMED' => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'reference' => 'VEF_CALLBACK_CONFIRMED',
                    'amount' => 12_800,
                    'currency' => 'NGN',
                ],
            ]),
        ]);

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create([
            'payment_reference' => 'VEF_CALLBACK_CONFIRMED',
            'total' => 12_800,
        ]);
        $order->items()->create([
            'vendor_id' => null,
            'product_id' => null,
            'product_key' => 'headphones',
            'product_name' => 'Headphones',
            'unit_price' => 6_400,
            'quantity' => 2,
            'line_total' => 12_800,
        ]);

        $cart = Cart::factory()->for($user)->create();
        CartItem::factory()->for($cart)->create(['product_key' => 'headphones']);
        CartItem::factory()->for($cart)->create(['product_key' => 'chair']);

        $this->actingAs($user)
            ->get(route('checkout.callback', ['reference' => 'VEF_CALLBACK_CONFIRMED']))
            ->assertOk()
            ->assertSeeText('Payment confirmed');

        $order->refresh();

        $this->assertSame('paid', $order->status->value);
        $this->assertNotNull($order->paid_at);
        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->getKey(),
            'product_key' => 'headphones',
        ]);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->getKey(),
            'product_key' => 'chair',
        ]);
    }

    public function test_the_callback_page_does_not_confirm_a_payment_with_a_mismatched_amount(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/verify/VEF_CALLBACK_MISMATCH' => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'reference' => 'VEF_CALLBACK_MISMATCH',
                    'amount' => 100,
                    'currency' => 'NGN',
                ],
            ]),
        ]);

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create([
            'payment_reference' => 'VEF_CALLBACK_MISMATCH',
            'total' => 12_800,
        ]);
        $order->items()->create([
            'vendor_id' => null,
            'product_id' => null,
            'product_key' => 'headphones',
            'product_name' => 'Headphones',
            'unit_price' => 6_400,
            'quantity' => 2,
            'line_total' => 12_800,
        ]);

        $cart = Cart::factory()->for($user)->create();
        CartItem::factory()->for($cart)->create(['product_key' => 'headphones']);

        $this->actingAs($user)
            ->get(route('checkout.callback', ['reference' => 'VEF_CALLBACK_MISMATCH']))
            ->assertOk()
            ->assertSeeText('Payment is being confirmed');

        $this->assertSame('pending', $order->fresh()->status->value);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->getKey(),
            'product_key' => 'headphones',
        ]);
    }

    public function test_the_callback_page_does_not_expose_another_users_order(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        Order::factory()->for($owner)->create([
            'payment_reference' => 'VEF_CALLBACK_PRIVATE',
        ]);

        $this->actingAs($otherUser)
            ->get(route('checkout.callback', ['reference' => 'VEF_CALLBACK_PRIVATE']))
            ->assertOk()
            ->assertSeeText('We could not find this payment')
            ->assertDontSeeText('Payment confirmed')
            ->assertDontSeeText('Payment is being confirmed');
    }

    public function test_it_creates_an_order_and_initializes_a_single_account_payment(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/abc123'],
            ]),
        ]);

        $user = User::factory()->create([
            'name' => 'Ada Obi',
            'email' => 'ada@example.com',
        ]);

        $fashion = $this->createVendorProduct(10_000);
        $electronics = $this->createVendorProduct(5_000);

        $response = $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', 'checkout-abc-123')
            ->postJson(route('checkout.store'), $this->checkoutPayload([
                ['product_id' => $fashion->catalogKey(), 'quantity' => 2],
                ['product_id' => $electronics->catalogKey(), 'quantity' => 1],
            ]));

        $response->assertOk()
            ->assertJsonPath('checkout_url', 'https://checkout.paystack.com/abc123')
            ->assertJsonStructure(['order_id', 'reference', 'checkout_url']);

        $order = Order::query()->where('idempotency_key', 'checkout-abc-123')->firstOrFail();

        $this->assertSame(2_500_000, $order->subtotal);
        $this->assertSame(0, $order->discount_total);
        $this->assertSame(2_500_000, $order->taxable_amount);
        $this->assertSame(187_500, $order->tax_total);
        $this->assertSame(0, $order->shipping_total);
        $this->assertSame('7.50', $order->tax_rate);
        $this->assertTrue($order->tax_after_discount);
        $this->assertSame(2_687_500, $order->total);
        $this->assertSame('pending', $order->status->value);
        $this->assertSame('https://checkout.paystack.com/abc123', $order->checkout_url);
        $this->assertSame('ada@example.com', $order->customer_email);
        $this->assertSame('12 Marina Road', $order->delivery_address);
        $this->assertSame('Flat 3', $order->delivery_address_line_2);
        $this->assertSame('Lagos', $order->delivery_city);
        $this->assertSame('Lagos', $order->delivery_state);
        $this->assertSame('100001', $order->delivery_postal_code);
        $this->assertSame('Call on arrival', $order->delivery_notes);
        $this->assertCount(2, $order->items);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->getKey(),
            'product_id' => $fashion->getKey(),
            'vendor_id' => $fashion->vendor_id,
            'unit_price' => 1_000_000,
            'quantity' => 2,
            'line_total' => 2_000_000,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->getKey(),
            'product_id' => $electronics->getKey(),
            'vendor_id' => $electronics->vendor_id,
            'unit_price' => 500_000,
            'quantity' => 1,
            'line_total' => 500_000,
        ]);

        Http::assertSent(function (ClientRequest $request) use ($order, $fashion, $electronics): bool {
            $data = $request->data();
            $vendors = collect($data['metadata']['vendors'])->keyBy('vendor_id');

            return $request->url() === 'https://api.paystack.co/transaction/initialize'
                && $request->method() === 'POST'
                && $request->hasHeader('Authorization', 'Bearer sk_test_secret')
                && $data['email'] === 'ada@example.com'
                && $data['amount'] === 2_687_500
                && $data['reference'] === $order->payment_reference
                && ! array_key_exists('split', $data)
                && $data['metadata']['order_id'] === $order->getKey()
                && $data['metadata']['idempotency_key'] === 'checkout-abc-123'
                && $vendors[$fashion->vendor_id]['store_name'] === $fashion->vendor->store_name
                && $vendors[$fashion->vendor_id]['line_total'] === 2_000_000
                && $vendors[$electronics->vendor_id]['store_name'] === $electronics->vendor->store_name
                && $vendors[$electronics->vendor_id]['line_total'] === 500_000;
        });
    }

    public function test_the_checkout_page_shows_an_applied_fixed_coupon(): void
    {
        $user = User::factory()->create();
        $product = $this->createVendorProduct(10_000);
        $cart = Cart::factory()->for($user)->create();
        CartItem::factory()->for($cart)->create([
            'product_key' => $product->catalogKey(),
            'quantity' => 2,
            'unit_price' => 10_000,
        ]);
        Coupon::factory()->fixed(50_000)->create(['code' => 'SAVE500']);

        $this->actingAs($user)
            ->get(route('checkout.show', ['coupon' => 'save500']))
            ->assertOk()
            ->assertSeeText('SAVE500 applied')
            ->assertSeeText('Discount (SAVE500)')
            ->assertSeeText('20,963')
            ->assertSee('name="coupon_code" value="SAVE500"', false);
    }

    public function test_the_checkout_page_shows_an_invalid_coupon_message_without_a_discount(): void
    {
        $user = User::factory()->create();
        $product = $this->createVendorProduct(10_000);
        $cart = Cart::factory()->for($user)->create();
        CartItem::factory()->for($cart)->create([
            'product_key' => $product->catalogKey(),
            'quantity' => 2,
            'unit_price' => 10_000,
        ]);

        $this->actingAs($user)
            ->get(route('checkout.show', ['coupon' => 'NOTREAL']))
            ->assertOk()
            ->assertSeeText('This coupon is invalid or has expired.')
            ->assertSeeText('21,500')
            ->assertSee('name="coupon_code" value=""', false);
    }

    public function test_it_creates_an_order_with_a_fixed_coupon(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/coupon'],
            ]),
        ]);

        $user = User::factory()->create();
        $product = $this->createVendorProduct(10_000);
        $coupon = Coupon::factory()->fixed(50_000)->create(['code' => 'SAVE500']);

        $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', 'checkout-coupon-fixed')
            ->postJson(route('checkout.store'), $this->checkoutPayload([
                ['product_id' => $product->catalogKey(), 'quantity' => 2],
            ], 'save500'))
            ->assertOk();

        $order = Order::query()->where('idempotency_key', 'checkout-coupon-fixed')->firstOrFail();

        $this->assertSame(2_000_000, $order->subtotal);
        $this->assertSame(50_000, $order->discount_total);
        $this->assertSame(1_950_000, $order->taxable_amount);
        $this->assertSame(146_250, $order->tax_total);
        $this->assertSame(0, $order->shipping_total);
        $this->assertSame(2_096_250, $order->total);
        $this->assertSame($coupon->getKey(), $order->coupon_id);
        $this->assertSame('SAVE500', $order->coupon_code);

        Http::assertSent(fn (ClientRequest $request): bool => $request->data()['amount'] === 2_096_250
            && $request->data()['metadata']['coupon_code'] === 'SAVE500');
    }

    public function test_a_vendor_coupon_only_discounts_the_matching_vendor_items(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/vendor-coupon'],
            ]),
        ]);

        $user = User::factory()->create();
        $fashion = $this->createVendorProduct(10_000);
        $electronics = $this->createVendorProduct(10_000);
        $coupon = Coupon::factory()
            ->percentage(10)
            ->for($fashion->vendor)
            ->create(['code' => 'FASHION10']);

        $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', 'checkout-coupon-vendor')
            ->postJson(route('checkout.store'), $this->checkoutPayload([
                ['product_id' => $fashion->catalogKey(), 'quantity' => 1],
                ['product_id' => $electronics->catalogKey(), 'quantity' => 1],
            ], 'FASHION10'))
            ->assertOk();

        $order = Order::query()->where('idempotency_key', 'checkout-coupon-vendor')->firstOrFail();

        $this->assertSame($coupon->getKey(), $order->coupon_id);
        $this->assertSame(2_000_000, $order->subtotal);
        $this->assertSame(100_000, $order->discount_total);
        $this->assertSame(1_900_000, $order->taxable_amount);
        $this->assertSame(142_500, $order->tax_total);
        $this->assertSame(2_042_500, $order->total);
    }

    public function test_a_vendor_coupon_is_rejected_when_no_cart_item_belongs_to_that_vendor(): void
    {
        Http::fake();

        $user = User::factory()->create();
        $fashion = $this->createVendorProduct(10_000);
        $otherVendorProduct = $this->createVendorProduct(10_000);
        Coupon::factory()->fixed(50_000)->for($fashion->vendor)->create(['code' => 'FASHION500']);

        $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', 'checkout-coupon-ineligible')
            ->postJson(route('checkout.store'), $this->checkoutPayload([
                ['product_id' => $otherVendorProduct->catalogKey(), 'quantity' => 1],
            ], 'FASHION500'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('coupon_code')
            ->assertJsonPath(
                'errors.coupon_code.0',
                'This coupon does not apply to any items in your cart.',
            );

        $this->assertSame(0, Order::query()->count());
        Http::assertNothingSent();
    }

    public function test_an_expired_coupon_cannot_start_checkout(): void
    {
        Http::fake();

        $user = User::factory()->create();
        $product = $this->createVendorProduct(10_000);
        Coupon::factory()->expired()->create(['code' => 'EXPIRED500']);

        $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', 'checkout-coupon-expired')
            ->postJson(route('checkout.store'), $this->checkoutPayload([
                ['product_id' => $product->catalogKey(), 'quantity' => 1],
            ], 'EXPIRED500'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('coupon_code')
            ->assertJsonPath(
                'errors.coupon_code.0',
                'This coupon is invalid or has expired.',
            );

        $this->assertSame(0, Order::query()->count());
        Http::assertNothingSent();
    }

    public function test_tax_can_be_calculated_before_the_discount(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/tax-before'],
            ]),
        ]);
        config(['checkout.tax_after_discount' => false]);

        $user = User::factory()->create();
        $product = $this->createVendorProduct(10_000);
        Coupon::factory()->fixed(50_000)->create(['code' => 'SAVE500']);

        $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', 'checkout-tax-before-discount')
            ->postJson(route('checkout.store'), $this->checkoutPayload([
                ['product_id' => $product->catalogKey(), 'quantity' => 2],
            ], 'SAVE500'))
            ->assertOk();

        $order = Order::query()->where('idempotency_key', 'checkout-tax-before-discount')->firstOrFail();

        $this->assertFalse($order->tax_after_discount);
        $this->assertSame(2_000_000, $order->taxable_amount);
        $this->assertSame(150_000, $order->tax_total);
        $this->assertSame(2_100_000, $order->total);
    }

    public function test_a_free_shipping_coupon_removes_the_shipping_fee(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/free-shipping'],
            ]),
        ]);
        config(['checkout.shipping_fee' => 50_000]);

        $user = User::factory()->create();
        $product = $this->createVendorProduct(10_000);
        Coupon::factory()->freeShipping()->create(['code' => 'FREESHIP']);

        $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', 'checkout-free-shipping')
            ->postJson(route('checkout.store'), $this->checkoutPayload([
                ['product_id' => $product->catalogKey(), 'quantity' => 1],
            ], 'FREESHIP'))
            ->assertOk();

        $order = Order::query()->where('idempotency_key', 'checkout-free-shipping')->firstOrFail();

        $this->assertSame(1_000_000, $order->subtotal);
        $this->assertSame(0, $order->discount_total);
        $this->assertSame(75_000, $order->tax_total);
        $this->assertSame(0, $order->shipping_total);
        $this->assertSame(1_075_000, $order->total);
    }

    public function test_the_callback_confirms_a_coupon_order_when_revalidation_still_matches(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/verify/VEF_COUPON_VALID' => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'reference' => 'VEF_COUPON_VALID',
                    'amount' => 2_096_250,
                    'currency' => 'NGN',
                ],
            ]),
        ]);

        $user = User::factory()->create();
        $coupon = Coupon::factory()->fixed(50_000)->create(['code' => 'SAVE500']);
        $order = Order::factory()->for($user)->create([
            'payment_reference' => 'VEF_COUPON_VALID',
            'subtotal' => 2_000_000,
            'discount_total' => 50_000,
            'taxable_amount' => 1_950_000,
            'tax_total' => 146_250,
            'shipping_total' => 0,
            'tax_rate' => 7.5,
            'tax_after_discount' => true,
            'coupon_id' => $coupon->getKey(),
            'coupon_code' => 'SAVE500',
            'total' => 2_096_250,
        ]);
        $order->items()->create([
            'vendor_id' => null,
            'product_id' => null,
            'product_key' => 'headphones',
            'product_name' => 'Headphones',
            'unit_price' => 1_000_000,
            'quantity' => 2,
            'line_total' => 2_000_000,
        ]);
        $cart = Cart::factory()->for($user)->create();
        CartItem::factory()->for($cart)->create(['product_key' => 'headphones']);

        $this->actingAs($user)
            ->get(route('checkout.callback', ['reference' => 'VEF_COUPON_VALID']))
            ->assertOk()
            ->assertSeeText('Payment confirmed');

        $order->refresh();

        $this->assertSame('paid', $order->status->value);
        $this->assertNotNull($order->paid_at);
        $this->assertArrayNotHasKey('coupon_revalidation_failed', $order->metadata ?? []);
        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->getKey(),
            'product_key' => 'headphones',
        ]);
    }

    public function test_coupon_revalidation_blocks_payment_when_the_coupon_is_no_longer_active(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/verify/VEF_COUPON_DISABLED' => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'reference' => 'VEF_COUPON_DISABLED',
                    'amount' => 2_096_250,
                    'currency' => 'NGN',
                ],
            ]),
        ]);

        $user = User::factory()->create();
        $coupon = Coupon::factory()->fixed(50_000)->create(['code' => 'SAVE500']);
        $order = Order::factory()->for($user)->create([
            'payment_reference' => 'VEF_COUPON_DISABLED',
            'subtotal' => 2_000_000,
            'discount_total' => 50_000,
            'taxable_amount' => 1_950_000,
            'tax_total' => 146_250,
            'shipping_total' => 0,
            'tax_rate' => 7.5,
            'tax_after_discount' => true,
            'coupon_id' => $coupon->getKey(),
            'coupon_code' => 'SAVE500',
            'total' => 2_096_250,
        ]);
        $order->items()->create([
            'vendor_id' => null,
            'product_id' => null,
            'product_key' => 'headphones',
            'product_name' => 'Headphones',
            'unit_price' => 1_000_000,
            'quantity' => 2,
            'line_total' => 2_000_000,
        ]);
        $cart = Cart::factory()->for($user)->create();
        CartItem::factory()->for($cart)->create(['product_key' => 'headphones']);
        $coupon->update(['is_active' => false]);

        $this->actingAs($user)
            ->get(route('checkout.callback', ['reference' => 'VEF_COUPON_DISABLED']))
            ->assertOk()
            ->assertSeeText('Payment is being confirmed');

        $order->refresh();

        $this->assertSame('pending', $order->status->value);
        $this->assertSame('coupon', $order->metadata['coupon_revalidation_failed']['reason']);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->getKey(),
            'product_key' => 'headphones',
        ]);
    }

    public function test_coupon_revalidation_blocks_payment_when_the_order_totals_no_longer_match(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/verify/VEF_COUPON_TOTAL_MISMATCH' => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'reference' => 'VEF_COUPON_TOTAL_MISMATCH',
                    'amount' => 2_096_250,
                    'currency' => 'NGN',
                ],
            ]),
        ]);

        $user = User::factory()->create();
        $coupon = Coupon::factory()->fixed(50_000)->create(['code' => 'SAVE500']);
        $order = Order::factory()->for($user)->create([
            'payment_reference' => 'VEF_COUPON_TOTAL_MISMATCH',
            'subtotal' => 2_000_000,
            'discount_total' => 50_000,
            'taxable_amount' => 1_950_000,
            'tax_total' => 0,
            'shipping_total' => 0,
            'tax_rate' => 7.5,
            'tax_after_discount' => true,
            'coupon_id' => $coupon->getKey(),
            'coupon_code' => 'SAVE500',
            'total' => 2_096_250,
        ]);
        $order->items()->create([
            'vendor_id' => null,
            'product_id' => null,
            'product_key' => 'headphones',
            'product_name' => 'Headphones',
            'unit_price' => 1_000_000,
            'quantity' => 2,
            'line_total' => 2_000_000,
        ]);

        $this->actingAs($user)
            ->get(route('checkout.callback', ['reference' => 'VEF_COUPON_TOTAL_MISMATCH']))
            ->assertOk()
            ->assertSeeText('Payment is being confirmed');

        $order->refresh();

        $this->assertSame('pending', $order->status->value);
        $this->assertSame('totals', $order->metadata['coupon_revalidation_failed']['reason']);
    }

    public function test_replaying_an_idempotency_key_returns_the_existing_checkout(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/once'],
            ]),
        ]);

        $user = User::factory()->create();
        $product = $this->createVendorProduct(5_000);
        $payload = $this->checkoutPayload([
            ['product_id' => $product->catalogKey(), 'quantity' => 1],
        ]);

        $first = $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', 'checkout-replayed')
            ->postJson(route('checkout.store'), $payload)
            ->assertOk();

        $second = $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', 'checkout-replayed')
            ->postJson(route('checkout.store'), $payload)
            ->assertOk();

        $this->assertSame(
            $first->json('checkout_url'),
            $second->json('checkout_url'),
        );
        $this->assertSame($first->json('order_id'), $second->json('order_id'));
        $this->assertSame(1, Order::query()->count());
        Http::assertSentCount(1);
    }

    public function test_an_idempotency_key_cannot_be_reused_by_another_user(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/owned'],
            ]),
        ]);

        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        $product = $this->createVendorProduct(5_000);

        $this->actingAs($firstUser)
            ->withHeader('X-Idempotency-Key', 'checkout-owned')
            ->postJson(route('checkout.store'), $this->checkoutPayload([
                ['product_id' => $product->catalogKey(), 'quantity' => 1],
            ]))
            ->assertOk();

        $this->actingAs($secondUser)
            ->withHeader('X-Idempotency-Key', 'checkout-owned')
            ->postJson(route('checkout.store'), $this->checkoutPayload([
                ['product_id' => $product->catalogKey(), 'quantity' => 1],
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('idempotency_key');

        $this->assertSame(1, Order::query()->count());
    }

    public function test_static_catalog_products_can_be_checked_out(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/static'],
            ]),
        ]);

        $user = User::factory()->create();
        $product = app(ProductCatalog::class)->find('headphones');
        $unitPrice = $product['price'] * 100;

        $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', 'checkout-static')
            ->postJson(route('checkout.store'), $this->checkoutPayload([
                ['product_id' => 'headphones', 'quantity' => 2],
            ]))
            ->assertOk()
            ->assertJsonPath('checkout_url', 'https://checkout.paystack.com/static');

        $order = Order::query()->where('idempotency_key', 'checkout-static')->firstOrFail();

        $this->assertSame($unitPrice * 2, $order->subtotal);
        $this->assertSame(96_000, $order->tax_total);
        $this->assertSame(1_376_000, $order->total);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->getKey(),
            'vendor_id' => null,
            'product_id' => null,
            'product_key' => 'headphones',
            'product_name' => $product['title'],
            'unit_price' => $unitPrice,
            'quantity' => 2,
            'line_total' => $unitPrice * 2,
        ]);

        Http::assertSent(function (ClientRequest $request): bool {
            $data = $request->data();

            return $data['amount'] === 1_376_000
            && ! array_key_exists('split', $data)
            && $data['metadata']['vendors'] === [];
        });
    }

    public function test_products_from_suspended_vendors_cannot_be_checked_out(): void
    {
        Http::fake();

        $user = User::factory()->create();
        $product = $this->createVendorProduct(5_000);

        $product->vendor->user->forceFill(['suspended_at' => now()])->save();

        $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', 'checkout-suspended-vendor')
            ->postJson(route('checkout.store'), $this->checkoutPayload([
                ['product_id' => $product->catalogKey(), 'quantity' => 1],
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('items');

        $this->assertSame(0, Order::query()->count());
        Http::assertNothingSent();
    }

    public function test_vendors_do_not_need_a_paystack_subaccount_to_check_out(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/single-account'],
            ]),
        ]);

        $user = User::factory()->create();
        $product = $this->createVendorProduct(5_000);

        $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', 'checkout-no-subaccount')
            ->postJson(route('checkout.store'), $this->checkoutPayload([
                ['product_id' => $product->catalogKey(), 'quantity' => 1],
            ]))
            ->assertOk();

        $this->assertSame(1, Order::query()->count());
    }

    public function test_products_with_insufficient_stock_are_rejected(): void
    {
        Http::fake();

        $user = User::factory()->create();
        $product = $this->createVendorProduct(5_000, stock: 1);

        $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', 'checkout-stock')
            ->postJson(route('checkout.store'), $this->checkoutPayload([
                ['product_id' => $product->catalogKey(), 'quantity' => 5],
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('items');

        $this->assertSame(0, Order::query()->count());
        Http::assertNothingSent();
    }

    public function test_the_idempotency_key_must_be_sent_in_the_header(): void
    {
        Http::fake();

        $user = User::factory()->create();
        $product = $this->createVendorProduct(5_000);

        $this->actingAs($user)
            ->postJson(route('checkout.store'), $this->checkoutPayload([
                ['product_id' => $product->catalogKey(), 'quantity' => 1],
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('idempotency_key');

        Http::assertNothingSent();
    }

    public function test_a_delivery_address_is_required_before_payment(): void
    {
        Http::fake();

        $user = User::factory()->create();
        $product = $this->createVendorProduct(5_000);

        $this->actingAs($user)
            ->withHeader('X-Idempotency-Key', 'checkout-no-address')
            ->postJson(route('checkout.store'), [
                'items' => [['product_id' => $product->catalogKey(), 'quantity' => 1]],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'delivery_address',
                'delivery_city',
                'delivery_state',
            ]);

        Http::assertNothingSent();
    }

    /**
     * @param  array<int, array{product_id: string, quantity: int}>  $items
     * @return array<string, mixed>
     */
    private function checkoutPayload(array $items, ?string $couponCode = null): array
    {
        return [
            'items' => $items,
            'coupon_code' => $couponCode,
            'customer_name' => 'Ada Obi',
            'customer_email' => 'ada@example.com',
            'customer_phone' => '08012345678',
            'delivery_address' => '12 Marina Road',
            'delivery_address_line_2' => 'Flat 3',
            'delivery_city' => 'Lagos',
            'delivery_state' => 'Lagos',
            'delivery_postal_code' => '100001',
            'delivery_notes' => 'Call on arrival',
        ];
    }

    private function createVendorProduct(int $price, int $stock = 10): Product
    {
        $vendor = Vendor::factory()->create();

        return Product::factory()->for($vendor)->create([
            'price' => $price,
            'stock' => $stock,
            'is_active' => true,
        ]);
    }
}
