<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_in_users_can_view_their_cart_page(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->for($user)->create();
        CartItem::factory()->for($cart)->create([
            'product_key' => 'headphones',
            'quantity' => 2,
            'unit_price' => 6400,
        ]);

        $this->actingAs($user)
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSeeText('Shopping cart')
            ->assertSeeText('Earphone Clip Wireless Bluetooth Headphone')
            ->assertSeeText('12,800');
    }

    public function test_the_cart_endpoint_still_returns_json_for_ajax_requests(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->for($user)->create();
        CartItem::factory()->for($cart)->create([
            'product_key' => 'headphones',
            'quantity' => 2,
            'unit_price' => 6400,
        ]);

        $this->actingAs($user)
            ->getJson(route('cart.index'))
            ->assertOk()
            ->assertJsonPath('count', 2)
            ->assertJsonPath('subtotal', 12800);
    }

    public function test_users_can_update_item_quantities_from_the_cart_page(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->for($user)->create();
        $item = CartItem::factory()->for($cart)->create([
            'product_key' => 'headphones',
            'quantity' => 1,
            'unit_price' => 6400,
        ]);

        $this->actingAs($user)
            ->patch(route('cart.items.update', 'headphones'), ['quantity' => 3])
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('status');

        $this->assertSame(3, $item->fresh()->quantity);
    }

    public function test_users_can_remove_items_from_the_cart_page(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->for($user)->create();
        $item = CartItem::factory()->for($cart)->create(['product_key' => 'headphones']);

        $this->actingAs($user)
            ->delete(route('cart.items.destroy', 'headphones'))
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }
}
