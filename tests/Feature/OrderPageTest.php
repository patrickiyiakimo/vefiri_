<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('orders.index'))
            ->assertRedirect(route('login'));
    }

    public function test_customers_only_see_their_own_orders(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->createOrder($user, ['payment_reference' => 'VEF_MY_ORDER']);
        $this->createOrder($otherUser, ['payment_reference' => 'VEF_OTHER_ORDER']);

        $this->actingAs($user)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSeeText('VEF_MY_ORDER')
            ->assertDontSeeText('VEF_OTHER_ORDER');
    }

    public function test_the_orders_page_lists_order_and_item_details(): void
    {
        $user = User::factory()->create();

        $order = $this->createOrder($user, [
            'payment_reference' => 'VEF_LISTED_ORDER',
            'status' => OrderStatus::Paid,
            'paid_at' => now(),
            'total' => 7_000_000,
            'delivery_city' => 'Lekki',
            'delivery_state' => 'Lagos',
        ]);

        $this->actingAs($user)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSeeText('My orders')
            ->assertSeeText('VEF_LISTED_ORDER')
            ->assertSeeText('Paid')
            ->assertSeeText('Wireless Headphones')
            ->assertSeeText('Ada Store')
            ->assertSeeText('70,000')
            ->assertSeeText('Lekki, Lagos')
            ->assertSee(route('orders.show', $order), false);
    }

    public function test_the_orders_page_shows_an_empty_state(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSeeText('You have not placed an order yet')
            ->assertSee(route('search'), false);
    }

    public function test_customers_can_view_the_details_of_their_own_order(): void
    {
        $user = User::factory()->create();

        $order = $this->createOrder($user, [
            'payment_reference' => 'VEF_ORDER_DETAIL',
            'status' => OrderStatus::Paid,
            'paid_at' => now(),
            'customer_name' => 'Ada Obi',
            'customer_email' => 'ada@example.com',
            'customer_phone' => '08012345678',
            'delivery_address' => '12 Admiralty Way',
            'delivery_city' => 'Lekki',
            'delivery_state' => 'Lagos',
            'delivery_notes' => 'Call on arrival',
            'total' => 7_000_000,
        ]);

        $this->actingAs($user)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSeeText('VEF_ORDER_DETAIL')
            ->assertSeeText('Payment summary')
            ->assertSeeText('Delivery details')
            ->assertSeeText('Wireless Headphones')
            ->assertSeeText('Ada Store')
            ->assertSeeText('35,000')
            ->assertSeeText('70,000')
            ->assertSeeText('Ada Obi')
            ->assertSeeText('12 Admiralty Way')
            ->assertSeeText('Lekki, Lagos')
            ->assertSeeText('Call on arrival');
    }

    public function test_customers_cannot_view_another_users_order(): void
    {
        $otherUser = User::factory()->create();
        $order = $this->createOrder($otherUser, ['payment_reference' => 'VEF_PRIVATE_ORDER']);

        $this->actingAs(User::factory()->create())
            ->get(route('orders.show', $order))
            ->assertNotFound();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createOrder(User $user, array $attributes = []): Order
    {
        $order = Order::factory()->for($user)->create($attributes);

        $vendor = Vendor::factory()->create(['store_name' => 'Ada Store '.fake()->unique()->numberBetween(1, 999_999)]);
        $product = Product::factory()->for($vendor)->create(['name' => 'Wireless Headphones']);

        $order->items()->create([
            'vendor_id' => $vendor->id,
            'product_id' => $product->id,
            'product_key' => $product->catalogKey(),
            'product_name' => $product->name,
            'unit_price' => 3_500_000,
            'quantity' => 2,
            'line_total' => 7_000_000,
        ]);

        return $order;
    }
}
