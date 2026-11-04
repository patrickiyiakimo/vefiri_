<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_suspend_a_user_account(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.suspend', $user))
            ->assertRedirect();

        $this->assertNotNull($user->refresh()->suspended_at);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSeeText('Suspended');
    }

    public function test_an_admin_can_reactivate_a_suspended_user_account(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->suspended()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.reactivate', $user))
            ->assertRedirect();

        $this->assertNull($user->refresh()->suspended_at);
    }

    public function test_a_suspended_user_cannot_sign_in(): void
    {
        User::factory()->suspended()->create([
            'email' => 'ada@example.com',
            'password' => 'password123',
        ]);

        $this->postJson(route('login.store'), [
            'email' => 'ada@example.com',
            'password' => 'password123',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->assertGuest();
    }

    public function test_a_suspended_user_is_signed_out_of_existing_sessions(): void
    {
        $user = User::factory()->suspended()->create();

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_an_admin_can_delete_a_user_account_without_deleting_order_history(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect();

        $this->assertSoftDeleted($user);
        $this->assertDatabaseHas('orders', [
            'id' => $order->getKey(),
            'user_id' => $user->getKey(),
        ]);
    }

    public function test_admins_cannot_moderate_themselves_or_other_admins(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.suspend', $admin))
            ->assertForbidden();

        $this->actingAs($admin)
            ->patch(route('admin.users.suspend', $otherAdmin))
            ->assertForbidden();
    }

    public function test_an_admin_can_suspend_a_vendor_from_the_vendors_page(): void
    {
        $admin = User::factory()->admin()->create();
        $vendor = Vendor::factory()->create();

        $this->actingAs($admin)
            ->patch(route('admin.vendors.suspend', $vendor))
            ->assertRedirect();

        $this->assertNotNull($vendor->user->refresh()->suspended_at);

        $this->actingAs($admin)
            ->get(route('admin.vendors.index'))
            ->assertOk()
            ->assertSeeText('Suspended');
    }

    public function test_suspended_vendor_products_are_hidden_from_the_storefront(): void
    {
        $vendor = Vendor::factory()->create();
        $product = Product::factory()->for($vendor)->create();

        $vendor->user->forceFill(['suspended_at' => now()])->save();

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSeeText($product->name);

        $this->get(route('products.show', $product))
            ->assertNotFound();

        $this->get(route('vendors.index'))
            ->assertOk()
            ->assertDontSeeText($vendor->store_name);
    }
}
