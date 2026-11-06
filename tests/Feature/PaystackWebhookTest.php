<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\VendorPlan;
use App\Enums\VendorPremiumPaymentStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\VendorPremiumPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PaystackWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET_KEY = 'sk_test_webhook_secret';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'paystack.secret_key' => self::SECRET_KEY,
            'paystack.base_url' => 'https://api.paystack.co',
            'vendor.premium.price' => 500_000,
            'vendor.premium.currency' => 'NGN',
            'vendor.premium.duration_days' => 30,
            'checkout.tax_rate' => 7.5,
            'checkout.tax_after_discount' => true,
            'checkout.shipping_fee' => 0,
        ]);
    }

    public function test_a_signed_charge_success_webhook_marks_the_order_as_paid(): void
    {
        $order = Order::factory()->create([
            'payment_reference' => 'VEF_WEBHOOK_SUCCESS',
            'status' => OrderStatus::Pending,
            'paid_at' => null,
        ]);

        $this->sendWebhook($this->payload('VEF_WEBHOOK_SUCCESS'))
            ->assertOk()
            ->assertJson(['status' => 'success']);

        $order->refresh();

        $this->assertSame(OrderStatus::Paid, $order->status);
        $this->assertNotNull($order->paid_at);
    }

    public function test_a_webhook_does_not_mark_an_order_paid_when_coupon_revalidation_fails(): void
    {
        $coupon = Coupon::factory()->fixed(50_000)->create(['code' => 'SAVE500']);
        $order = Order::factory()->create([
            'payment_reference' => 'VEF_WEBHOOK_COUPON_DISABLED',
            'status' => OrderStatus::Pending,
            'paid_at' => null,
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
        $coupon->update(['is_active' => false]);

        $this->sendWebhook($this->payload('VEF_WEBHOOK_COUPON_DISABLED'))
            ->assertOk()
            ->assertJson(['status' => 'ignored']);

        $order->refresh();

        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertNull($order->paid_at);
        $this->assertSame('coupon', $order->metadata['coupon_revalidation_failed']['reason']);
    }

    public function test_a_successful_webhook_removes_only_the_purchased_items_from_the_cart(): void
    {
        $order = Order::factory()->create([
            'payment_reference' => 'VEF_WEBHOOK_CART',
            'status' => OrderStatus::Pending,
            'paid_at' => null,
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

        $cart = Cart::factory()->for($order->user)->create();
        CartItem::factory()->for($cart)->create(['product_key' => 'headphones']);
        CartItem::factory()->for($cart)->create(['product_key' => 'chair']);

        $this->sendWebhook($this->payload('VEF_WEBHOOK_CART'))->assertOk();

        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->getKey(),
            'product_key' => 'headphones',
        ]);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->getKey(),
            'product_key' => 'chair',
        ]);
    }

    public function test_replaying_a_success_webhook_does_not_change_the_paid_timestamp(): void
    {
        $order = Order::factory()->create([
            'payment_reference' => 'VEF_WEBHOOK_REPLAY',
            'status' => OrderStatus::Pending,
            'paid_at' => null,
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

        $this->sendWebhook($this->payload('VEF_WEBHOOK_REPLAY'))->assertOk();
        $paidAt = $order->fresh()->paid_at;

        $cart = Cart::factory()->for($order->user)->create();
        CartItem::factory()->for($cart)->create(['product_key' => 'headphones']);

        $this->travel(5)->minutes();

        $this->sendWebhook($this->payload('VEF_WEBHOOK_REPLAY'))->assertOk();

        $this->assertTrue($paidAt->equalTo($order->fresh()->paid_at));
        $this->assertSame(OrderStatus::Paid, $order->fresh()->status);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->getKey(),
            'product_key' => 'headphones',
        ]);
    }

    public function test_replaying_a_success_webhook_does_not_downgrade_a_delivered_order(): void
    {
        $deliveredAt = now()->subDay();

        $order = Order::factory()->delivered()->create([
            'payment_reference' => 'VEF_WEBHOOK_DELIVERED',
            'delivered_at' => $deliveredAt,
        ]);

        $originalDeliveredAt = $order->delivered_at;

        $this->sendWebhook($this->payload('VEF_WEBHOOK_DELIVERED'))->assertOk();

        $order->refresh();

        $this->assertSame(OrderStatus::Delivered, $order->status);
        $this->assertTrue($originalDeliveredAt->equalTo($order->delivered_at));
    }

    public function test_a_webhook_with_an_invalid_signature_is_rejected(): void
    {
        $order = Order::factory()->create([
            'payment_reference' => 'VEF_WEBHOOK_INVALID',
            'status' => OrderStatus::Pending,
        ]);

        $this->sendWebhook($this->payload('VEF_WEBHOOK_INVALID'), 'invalid-signature')
            ->assertUnauthorized();

        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
    }

    public function test_a_missing_signature_is_rejected(): void
    {
        $order = Order::factory()->create([
            'payment_reference' => 'VEF_WEBHOOK_MISSING',
            'status' => OrderStatus::Pending,
        ]);

        $this->call(
            'POST',
            route('webhooks.paystack'),
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            $this->payload('VEF_WEBHOOK_MISSING'),
        )->assertUnauthorized();

        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
    }

    public function test_webhooks_for_unknown_references_are_acknowledged_but_ignored(): void
    {
        $this->sendWebhook($this->payload('VEF_DOES_NOT_EXIST'))
            ->assertOk()
            ->assertJson(['status' => 'ignored']);
    }

    public function test_non_success_events_are_ignored(): void
    {
        $order = Order::factory()->create([
            'payment_reference' => 'VEF_WEBHOOK_FAILED',
            'status' => OrderStatus::Pending,
        ]);

        $this->sendWebhook(json_encode([
            'event' => 'charge.failed',
            'data' => ['reference' => 'VEF_WEBHOOK_FAILED'],
        ], JSON_THROW_ON_ERROR))
            ->assertOk()
            ->assertJson(['status' => 'ignored']);

        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
    }

    public function test_a_signed_premium_charge_success_webhook_upgrades_the_vendor(): void
    {
        $this->travelTo('2026-01-01 12:00:00');
        $this->fakePremiumVerification('VEF_PREM_SUCCESS');

        $vendor = Vendor::factory()->create();
        $payment = VendorPremiumPayment::factory()->for($vendor)->create([
            'reference' => 'VEF_PREM_SUCCESS',
            'amount' => 500_000,
            'currency' => 'NGN',
        ]);

        $this->sendWebhook($this->payload('VEF_PREM_SUCCESS'))
            ->assertOk()
            ->assertJson(['status' => 'success']);

        $payment->refresh();
        $vendor->refresh();

        $this->assertSame(VendorPremiumPaymentStatus::Paid, $payment->status);
        $this->assertNotNull($payment->paid_at);
        $this->assertSame(VendorPlan::Premium, $vendor->plan);
        $this->assertTrue($vendor->isPremium());
        $this->assertTrue($vendor->premium_started_at->equalTo(now()));
        $this->assertTrue($vendor->premium_until->equalTo(now()->addDays(30)));
    }

    public function test_replaying_a_premium_success_webhook_does_not_extend_the_entitlement_twice(): void
    {
        $this->travelTo('2026-01-01 12:00:00');
        $this->fakePremiumVerification('VEF_PREM_REPLAY');

        $vendor = Vendor::factory()->create();
        $payment = VendorPremiumPayment::factory()->for($vendor)->create([
            'reference' => 'VEF_PREM_REPLAY',
            'amount' => 500_000,
            'currency' => 'NGN',
        ]);

        $this->sendWebhook($this->payload('VEF_PREM_REPLAY'))->assertOk();
        $premiumUntil = $vendor->fresh()->premium_until;

        $this->travel(5)->days();

        $this->sendWebhook($this->payload('VEF_PREM_REPLAY'))->assertOk();

        $this->assertTrue($premiumUntil->equalTo($vendor->fresh()->premium_until));
        $this->assertSame(VendorPremiumPaymentStatus::Paid, $payment->fresh()->status);
        Http::assertSentCount(1);
    }

    public function test_a_premium_webhook_with_a_mismatched_amount_does_not_upgrade_the_vendor(): void
    {
        $this->fakePremiumVerification('VEF_PREM_MISMATCH', amount: 100);

        $vendor = Vendor::factory()->create();
        $payment = VendorPremiumPayment::factory()->for($vendor)->create([
            'reference' => 'VEF_PREM_MISMATCH',
            'amount' => 500_000,
            'currency' => 'NGN',
        ]);

        $this->sendWebhook($this->payload('VEF_PREM_MISMATCH'))
            ->assertOk()
            ->assertJson(['status' => 'ignored']);

        $this->assertSame(VendorPremiumPaymentStatus::Pending, $payment->fresh()->status);
        $this->assertFalse($vendor->fresh()->isPremium());
    }

    public function test_a_premium_webhook_renews_from_the_existing_expiry_date(): void
    {
        $this->travelTo('2026-01-01 12:00:00');
        $this->fakePremiumVerification('VEF_PREM_RENEWAL');

        $vendor = Vendor::factory()->premium()->create([
            'premium_until' => now()->addDays(10),
        ]);
        VendorPremiumPayment::factory()->for($vendor)->create([
            'reference' => 'VEF_PREM_RENEWAL',
            'amount' => 500_000,
            'currency' => 'NGN',
        ]);

        $this->sendWebhook($this->payload('VEF_PREM_RENEWAL'))->assertOk();

        $this->assertTrue($vendor->fresh()->premium_until->equalTo(now()->addDays(40)));
    }

    public function test_a_premium_webhook_with_an_invalid_signature_does_not_upgrade_the_vendor(): void
    {
        $vendor = Vendor::factory()->create();
        $payment = VendorPremiumPayment::factory()->for($vendor)->create([
            'reference' => 'VEF_PREM_INVALID',
            'amount' => 500_000,
            'currency' => 'NGN',
        ]);

        $this->sendWebhook($this->payload('VEF_PREM_INVALID'), 'invalid-signature')
            ->assertUnauthorized();

        $this->assertSame(VendorPremiumPaymentStatus::Pending, $payment->fresh()->status);
        $this->assertFalse($vendor->fresh()->isPremium());
    }

    public function test_a_failed_premium_charge_marks_the_payment_as_failed(): void
    {
        Http::preventStrayRequests();

        $vendor = Vendor::factory()->create();
        $payment = VendorPremiumPayment::factory()->for($vendor)->create([
            'reference' => 'VEF_PREM_FAILED',
            'amount' => 500_000,
            'currency' => 'NGN',
        ]);

        $this->sendWebhook($this->payload('VEF_PREM_FAILED', 'charge.failed'))
            ->assertOk()
            ->assertJson(['status' => 'success']);

        $this->assertSame(VendorPremiumPaymentStatus::Failed, $payment->fresh()->status);
        $this->assertNull($payment->fresh()->paid_at);
        $this->assertFalse($vendor->fresh()->isPremium());
    }

    private function fakePremiumVerification(
        string $reference,
        int $amount = 500_000,
        string $currency = 'NGN',
    ): void {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/verify/'.$reference => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'reference' => $reference,
                    'amount' => $amount,
                    'currency' => $currency,
                ],
            ]),
        ]);
    }

    private function payload(string $reference, string $event = 'charge.success'): string
    {
        return json_encode([
            'event' => $event,
            'data' => ['reference' => $reference],
        ], JSON_THROW_ON_ERROR);
    }

    private function sendWebhook(string $payload, ?string $signature = null): TestResponse
    {
        return $this->call(
            'POST',
            route('webhooks.paystack'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X_PAYSTACK_SIGNATURE' => $signature ?? hash_hmac('sha256', $payload, self::SECRET_KEY),
            ],
            $payload,
        );
    }
}
