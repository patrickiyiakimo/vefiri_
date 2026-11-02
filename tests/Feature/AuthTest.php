<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_register_pages_render(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSeeText('Welcome back');

        $this->get(route('register'))
            ->assertOk()
            ->assertSeeText('Create your account');

        $this->get(route('register', ['type' => 'vendor']))
            ->assertOk()
            ->assertSeeText('Become a verified vendor');
    }

    public function test_a_user_can_register_from_the_modal(): void
    {
        $response = $this->postJson(route('register.store'), [
            'name' => 'Ada Okafor',
            'email' => 'ada@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertOk();
        $response->assertJsonPath('redirect', route('home'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => 'Ada Okafor',
            'email' => 'ada@example.com',
        ]);
    }

    public function test_a_user_can_sign_in(): void
    {
        User::factory()->create([
            'email' => 'ada@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson(route('login.store'), [
            'email' => 'ada@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk();
        $response->assertJsonPath('redirect', route('home'));
        $this->assertAuthenticated();
    }

    public function test_invalid_credentials_return_validation_errors(): void
    {
        User::factory()->create(['email' => 'ada@example.com']);

        $response = $this->postJson(route('login.store'), [
            'email' => 'ada@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
        $this->assertGuest();
    }
}
