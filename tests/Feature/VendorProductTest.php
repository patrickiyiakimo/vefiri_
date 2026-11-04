<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VendorProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_vendors_can_access_the_vendor_dashboard(): void
    {
        $vendor = Vendor::factory()->create();

        $this->actingAs($vendor->user)
            ->get(route('vendor.dashboard'))
            ->assertOk()
            ->assertSeeText('Vendor dashboard')
            ->assertSeeText($vendor->store_name);
    }

    public function test_customers_and_admins_cannot_access_the_vendor_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('vendor.dashboard'))
            ->assertForbidden();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('vendor.dashboard'))
            ->assertForbidden();
    }

    public function test_verified_vendors_can_upload_a_product_and_it_appears_on_the_storefront(): void
    {
        Storage::fake('public');

        $vendor = Vendor::factory()->create();

        $response = $this->actingAs($vendor->user)->post(route('vendor.products.store'), [
            'name' => 'Handwoven Leather Tote',
            'category' => "Women's Fashion",
            'description' => 'A handwoven leather tote bag made by artisans in Aba with a durable lined interior.',
            'price' => 45000,
            'compare_price' => 60000,
            'stock' => 12,
            'image' => UploadedFile::fake()->image('tote.jpg', 800, 800),
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('vendor.products.index'));

        $product = Product::query()->where('name', 'Handwoven Leather Tote')->firstOrFail();

        $this->assertSame($vendor->id, $product->vendor_id);
        $this->assertSame('handwoven-leather-tote', $product->slug);
        $this->assertTrue($product->is_active);
        Storage::disk('public')->assertExists($product->image_path);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Fresh from verified vendors')
            ->assertSeeText('Handwoven Leather Tote');

        $this->get(route('products.show', $product->catalogKey()))
            ->assertOk()
            ->assertSeeText('Handwoven Leather Tote')
            ->assertSeeText($vendor->store_name)
            ->assertSeeText('Verified vendor');
    }

    public function test_vendors_can_view_their_forms_and_update_their_own_product(): void
    {
        $vendor = Vendor::factory()->create();
        $product = Product::factory()->for($vendor)->create();

        $this->actingAs($vendor->user)
            ->get(route('vendor.products.create'))
            ->assertOk()
            ->assertSeeText('Upload a new product');

        $this->actingAs($vendor->user)
            ->get(route('vendor.products.edit', $product))
            ->assertOk()
            ->assertSeeText('Edit product');

        $this->actingAs($vendor->user)
            ->patch(route('vendor.products.update', $product), [
                'name' => 'Updated Vendor Product',
                'category' => $product->category,
                'description' => 'An updated product description that is long enough to pass validation checks.',
                'price' => 25000,
                'stock' => 4,
                'is_active' => '0',
            ])
            ->assertRedirect(route('vendor.products.index'));

        $product->refresh();

        $this->assertSame('Updated Vendor Product', $product->name);
        $this->assertSame(25000, $product->price);
        $this->assertSame(4, $product->stock);
        $this->assertFalse($product->is_active);
    }

    public function test_vendors_cannot_update_another_vendors_product(): void
    {
        $product = Product::factory()->create();

        $this->actingAs(Vendor::factory()->create()->user)
            ->patch(route('vendor.products.update', $product), [
                'name' => 'Hijacked product',
                'category' => $product->category,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
            ])
            ->assertForbidden();

        $this->assertNotSame('Hijacked product', $product->fresh()->name);
    }

    public function test_product_images_use_the_configured_storage_disk_url(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('vendor-products/tote.jpg', 'image-bytes');

        $product = Product::factory()->create([
            'image_path' => 'vendor-products/tote.jpg',
        ]);

        $this->assertStringContainsString('vendor-products/tote.jpg', $product->imageUrl());
    }

    public function test_missing_product_images_fall_back_to_a_placeholder(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create([
            'image_path' => 'vendor-products/missing.jpg',
        ]);

        $this->assertSame(asset('images/product-placeholder.svg'), $product->imageUrl());
    }

    public function test_hidden_products_are_not_shown_on_the_storefront(): void
    {
        $product = Product::factory()->inactive()->create([
            'name' => 'Hidden Vendor Product',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSeeText('Hidden Vendor Product');

        $this->get(route('products.show', $product->catalogKey()))->assertNotFound();
    }
}
