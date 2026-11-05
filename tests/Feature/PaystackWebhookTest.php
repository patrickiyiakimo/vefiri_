<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PaystackWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET_KEY = 'sk_test_webhook_secret';

    protected function setUp(): void
    {
        parent::setUp();

        config(['paystack.secret_key' => self::SECRET_KEY]);
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

    private function payload(string $reference): string
    {
        return json_encode([
            'event' => 'charge.success',
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
