<?php

namespace Tests\Feature;

use App\Models\Favourite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavouritePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_wishlist_page_renders_the_client_side_removal_hooks(): void
    {
        $user = User::factory()->create();
        Favourite::factory()->for($user)->create(['product_key' => 'headphones']);

        $this->actingAs($user)
            ->get(route('favourites.index'))
            ->assertOk()
            ->assertSeeText('Earphone Clip Wireless Bluetooth Headphone')
            ->assertSee('data-wishlist-summary', false)
            ->assertSee('data-wishlist-empty', false)
            ->assertSee('data-wishlist-grid', false)
            ->assertSee('data-wishlist-item', false);
    }

    public function test_removing_a_wishlist_item_returns_the_remaining_ids(): void
    {
        $user = User::factory()->create();
        Favourite::factory()->for($user)->create(['product_key' => 'headphones']);

        $this->actingAs($user)
            ->deleteJson(route('favourites.destroy', 'headphones'))
            ->assertOk()
            ->assertJson([
                'active' => false,
                'ids' => [],
            ]);

        $this->assertDatabaseMissing('favourites', [
            'user_id' => $user->getKey(),
            'product_key' => 'headphones',
        ]);
    }
}
