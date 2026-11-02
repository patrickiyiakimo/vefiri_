<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_in_users_can_view_their_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Ada Okafor',
            'email' => 'ada@example.com',
        ]);

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSeeText('My account')
            ->assertSeeText('Ada Okafor')
            ->assertSeeText('ada@example.com')
            ->assertSeeText('Account details');
    }

    public function test_vendors_see_a_link_to_their_vendor_dashboard(): void
    {
        $vendor = Vendor::factory()->create();

        $this->actingAs($vendor->user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSeeText('Vendor dashboard')
            ->assertSee(route('vendor.dashboard'), false);
    }

    public function test_users_can_update_their_profile_details(): void
    {
        $user = User::factory()->create([
            'name' => 'Ada Okafor',
            'email' => 'ada@example.com',
        ]);

        $this->actingAs($user)
            ->patch(route('account.profile.update'), [
                'name' => 'Ada N. Okafor',
                'email' => 'ada.okafor@example.com',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Ada N. Okafor',
            'email' => 'ada.okafor@example.com',
        ]);
    }

    public function test_users_cannot_take_another_users_email_address(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);
        $user = User::factory()->create(['email' => 'ada@example.com']);

        $this->actingAs($user)
            ->patch(route('account.profile.update'), [
                'name' => 'Ada Okafor',
                'email' => 'taken@example.com',
            ])
            ->assertSessionHasErrors('email');

        $this->assertSame('ada@example.com', $user->fresh()->email);
    }

    public function test_users_can_update_their_password(): void
    {
        $user = User::factory()->create(['password' => 'old-password']);

        $this->actingAs($user)
            ->put(route('account.password.update'), [
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_users_must_provide_their_current_password_to_set_a_new_one(): void
    {
        $user = User::factory()->create(['password' => 'old-password']);

        $this->actingAs($user)
            ->put(route('account.password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('account.show'))
            ->assertRedirect(route('login'));
    }
}
