<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_storefront_home_page_renders_with_vefiri_branding(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSeeText("Today's deals");
        $response->assertSeeText('More to love');
        $response->assertSeeText('Vefiri');
        $response->assertSeeText('Nationwide');
        $response->assertSeeText('Become a verified vendor');
        $response->assertDontSeeText('United States');
        $response->assertDontSeeText('AliExpress');
        $response->assertSee('data-carousel-track', false);
        $response->assertSee('data-add-to-cart', false);
        $response->assertSee('data-cart-item-template', false);
        $response->assertSee('data-auth-modal', false);
        $response->assertSee('data-auth-open', false);
        $response->assertSee('favicon.ico', false);
        $response->assertSee('images/vefiri-logo.jpeg', false);
        $response->assertSee('data-load-more', false);
        $response->assertDontSee('.avif', false);
        $response->assertSee('code.jivosite.com/widget/c8ze2HijnI', false);
    }

    public function test_promo_banner_calls_to_action_link_to_working_pages(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee(route('checkout.show', ['coupon' => 'NEW5']), false);
        $response->assertSee(route('search', ['choice' => 1]), false);
        $response->assertSee(route('register', ['type' => 'vendor']), false);
    }

    public function test_hero_buttons_link_to_working_pages(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $hero = Str::between($response->getContent(), 'aria-label="Featured promotions"', '</section>');

        $this->assertStringContainsString(route('search'), $hero);
        $this->assertStringContainsString(route('search', ['choice' => 1]), $hero);
        $this->assertStringContainsString(route('home', ['#' => 'super-deals']), $hero);
        $this->assertStringContainsString(route('checkout.show', ['coupon' => 'NEW5']), $hero);
    }

    public function test_the_welcome_coupon_email_field_is_prefilled_for_signed_in_users(): void
    {
        $user = User::factory()->create(['email' => 'ada@example.com']);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('value="ada@example.com"', false);
    }

    public function test_verified_vendors_do_not_see_vendor_application_calls_to_action(): void
    {
        $vendor = Vendor::factory()->create();

        $this->actingAs($vendor->user)
            ->get(route('home'))
            ->assertOk()
            ->assertDontSeeText('Become a verified vendor')
            ->assertDontSeeText('Sell on Vefiri')
            ->assertSeeText('Vendor dashboard');
    }

    public function test_explore_business_links_verified_vendors_to_the_vendor_dashboard(): void
    {
        $vendor = Vendor::factory()->create();

        $this->actingAs($vendor->user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee(route('vendor.dashboard'), false)
            ->assertDontSee(route('register', ['type' => 'vendor']), false);
    }

    public function test_explore_business_links_signed_in_buyers_to_the_vendor_application(): void
    {
        $buyer = User::factory()->create();

        $this->actingAs($buyer)
            ->get(route('home'))
            ->assertOk()
            ->assertSee(route('vendors.apply'), false);
    }
}
