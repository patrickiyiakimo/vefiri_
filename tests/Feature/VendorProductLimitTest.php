<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VendorProductLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'vendor.free_product_limit' => 10,
            'vendor.product_slots.price' => 100_000,
            'vendor.product_slots.bundles' => [10, 20, 30, 40],
        ]);
    }

    public function test_free_vendors_can_upload_their_tenth_product(): void
    {
        Storage::fake('public');

        $vendor = Vendor::factory()->create();
        Product::factory()->count(9)->for($vendor)->create();

        $this->actingAs($vendor->user)
            ->from(route('vendor.products.create'))
            ->post(route('vendor.products.store'), $this->productPayload())
            ->assertRedirect(route('vendor.products.index'))
            ->assertSessionHas('status');

        $this->assertSame(10, $vendor->products()->count());
    }

    public function test_free_vendors_cannot_upload_more_than_ten_products(): void
    {
        Storage::fake('public');

        $vendor = Vendor::factory()->create();
        Product::factory()->count(10)->for($vendor)->create();

        $this->actingAs($vendor->user)
            ->post(route('vendor.products.store'), $this->productPayload())
            ->assertRedirect(route('vendor.premium.show'))
            ->assertSessionHas('limit_reached', true);

        $this->assertSame(10, $vendor->products()->count());
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_hidden_products_count_towards_the_free_product_limit(): void
    {
        Storage::fake('public');

        $vendor = Vendor::factory()->create();
        Product::factory()->count(10)->for($vendor)->inactive()->create();

        $this->actingAs($vendor->user)
            ->post(route('vendor.products.store'), $this->productPayload())
            ->assertRedirect(route('vendor.premium.show'))
            ->assertSessionHas('limit_reached', true);

        $this->assertSame(10, $vendor->products()->count());
    }

    public function test_vendors_with_purchased_slots_can_exceed_the_free_product_limit(): void
    {
        Storage::fake('public');

        $vendor = Vendor::factory()->withProductSlots(10)->create();
        Product::factory()->count(10)->for($vendor)->create();

        $this->actingAs($vendor->user)
            ->post(route('vendor.products.store'), $this->productPayload())
            ->assertRedirect(route('vendor.products.index'));

        $this->assertSame(11, $vendor->products()->count());
    }

    public function test_purchased_slots_are_permanent_and_raise_the_total_limit(): void
    {
        $vendor = Vendor::factory()->withProductSlots(20)->create();

        $this->assertSame(30, $vendor->productLimit());
        $this->assertFalse($vendor->hasReachedProductLimit());

        Product::factory()->count(30)->for($vendor)->create();

        $this->assertTrue($vendor->fresh()->hasReachedProductLimit());
    }

    public function test_the_create_page_redirects_to_premium_when_the_limit_is_reached(): void
    {
        $vendor = Vendor::factory()->create();
        Product::factory()->count(10)->for($vendor)->create();

        $this->actingAs($vendor->user)
            ->get(route('vendor.products.create'))
            ->assertRedirect(route('vendor.premium.show'))
            ->assertSessionHas('limit_reached', true)
            ->assertSessionHas('status');
    }

    public function test_the_premium_page_shows_the_product_limit_modal(): void
    {
        $vendor = Vendor::factory()->create();

        $this->actingAs($vendor->user)
            ->withSession(['limit_reached' => true])
            ->get(route('vendor.premium.show'))
            ->assertOk()
            ->assertSee('data-limit-modal', false)
            ->assertSeeText("You've used all 10 product slots")
            ->assertSee('form="premium-payment-form"', false);
    }

    public function test_the_products_page_shows_a_persistent_limit_notice(): void
    {
        $vendor = Vendor::factory()->create();
        Product::factory()->count(10)->for($vendor)->create();

        $this->actingAs($vendor->user)
            ->get(route('vendor.products.index'))
            ->assertOk()
            ->assertSeeText('You have reached your product limit')
            ->assertSeeText('You have uploaded 10 of 10 products');
    }

    /**
     * @return array<string, mixed>
     */
    private function productPayload(): array
    {
        return [
            'name' => 'Handwoven Leather Tote',
            'category' => "Women's Fashion",
            'description' => 'A handwoven leather tote bag made by artisans in Aba with a durable lined interior.',
            'price' => 45000,
            'stock' => 12,
            'image' => UploadedFile::fake()->image('tote.jpg', 800, 800),
            'is_active' => '1',
        ];
    }
}
