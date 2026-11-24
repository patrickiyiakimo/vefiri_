<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnVendorProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendors_cannot_add_their_own_products_to_the_cart(): void
    {
        $vendor = Vendor::factory()->create();
        $product = Product::factory()->for($vendor)->create();

        $this->actingAs($vendor->user)
            ->postJson(route('cart.items.store'), ['product_id' => $product->catalogKey()])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('product_id');

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_vendors_cannot_add_their_own_products_to_the_wishlist(): void
    {
        $vendor = Vendor::factory()->create();
        $product = Product::factory()->for($vendor)->create();

        $this->actingAs($vendor->user)
            ->postJson(route('favourites.store'), ['product_id' => $product->catalogKey()])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('product_id');

        $this->assertDatabaseCount('favourites', 0);
    }

    public function test_vendors_can_still_add_another_vendors_product_to_the_cart(): void
    {
        $vendor = Vendor::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($vendor->user)
            ->postJson(route('cart.items.store'), ['product_id' => $product->catalogKey()])
            ->assertOk();

        $this->assertDatabaseHas('cart_items', ['product_key' => $product->catalogKey()]);
    }

    public function test_vendors_can_still_add_another_vendors_product_to_the_wishlist(): void
    {
        $vendor = Vendor::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($vendor->user)
            ->postJson(route('favourites.store'), ['product_id' => $product->catalogKey()])
            ->assertOk();

        $this->assertDatabaseHas('favourites', ['product_key' => $product->catalogKey()]);
    }
}
