<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Mail\DeliveryConfirmationMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminOrderPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_all_orders_and_customer_delivery_notes(): void
    {
        $admin = User::factory()->admin()->create();

        $firstOrder = Order::factory()->paid()->create([
            'customer_name' => 'Ada Obi',
            'customer_email' => 'ada@example.com',
            'delivery_notes' => 'Please call when you arrive at the gate.',
        ]);

        $secondOrder = Order::factory()->create([
            'customer_name' => 'Bola Ade',
            'customer_email' => 'bola@example.com',
            'delivery_notes' => 'Drop the parcel with the security desk.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSeeText($firstOrder->payment_reference)
            ->assertSeeText($secondOrder->payment_reference)
            ->assertSeeText('Ada Obi')
            ->assertSeeText('Bola Ade')
            ->assertSeeText('Please call when you arrive at the gate.')
            ->assertSeeText('Drop the parcel with the security desk.');
    }

    public function test_admin_can_view_an_order_with_the_customer_delivery_note(): void
    {
        $admin = User::factory()->admin()->create();

        $order = Order::factory()->paid()->create([
            'customer_email' => 'customer@example.com',
            'delivery_notes' => 'Ring the bell twice and wait by the blue gate.',
        ]);

        OrderItem::factory()->for($order)->create([
            'product_name' => 'Handmade leather bag',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSeeText($order->payment_reference)
            ->assertSeeText('customer@example.com')
            ->assertSeeText('Handmade leather bag')
            ->assertSeeText('Ring the bell twice and wait by the blue gate.');
    }

    public function test_admin_can_mark_a_paid_order_as_delivered_and_notify_the_customer(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();
        $order = Order::factory()->paid()->create([
            'customer_email' => 'customer@example.com',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.orders.delivered', $order))
            ->assertRedirect()
            ->assertSessionHas('status');

        $order->refresh();

        $this->assertSame(OrderStatus::Delivered, $order->status);
        $this->assertNotNull($order->delivered_at);

        Mail::assertQueued(DeliveryConfirmationMail::class, function (DeliveryConfirmationMail $mail) use ($order): bool {
            return $mail->hasTo('customer@example.com')
                && $mail->order->is($order);
        });
    }

    public function test_only_paid_orders_can_be_marked_as_delivered_and_notifications_are_not_duplicated(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();
        $pendingOrder = Order::factory()->create();
        $deliveredOrder = Order::factory()->delivered()->create([
            'customer_email' => 'customer@example.com',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.orders.delivered', $pendingOrder))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame(OrderStatus::Pending, $pendingOrder->fresh()->status);

        $this->actingAs($admin)
            ->patch(route('admin.orders.delivered', $deliveredOrder))
            ->assertRedirect()
            ->assertSessionHas('error');

        Mail::assertNothingQueued();
    }

    public function test_customers_cannot_access_admin_orders(): void
    {
        $customer = User::factory()->create();
        $order = Order::factory()->create();

        $this->actingAs($customer)
            ->get(route('admin.orders.index'))
            ->assertForbidden();

        $this->actingAs($customer)
            ->get(route('admin.orders.show', $order))
            ->assertForbidden();

        $this->actingAs($customer)
            ->patch(route('admin.orders.delivered', $order))
            ->assertForbidden();
    }
}
