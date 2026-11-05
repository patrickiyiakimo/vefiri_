<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    }
}
