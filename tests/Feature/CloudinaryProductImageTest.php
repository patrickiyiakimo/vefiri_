<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\UsesCloudinary;
use Tests\TestCase;

class CloudinaryProductImageTest extends TestCase
{
    use RefreshDatabase;
    use UsesCloudinary;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configureCloudinary();
    }

    public function test_only_vendors_can_request_an_upload_signature(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson(route('vendor.product-images.signature'))
            ->assertForbidden();

        $vendor = Vendor::factory()->create();

        $this->actingAs($vendor->user)
            ->postJson(route('vendor.product-images.signature'))
            ->assertCreated()
            ->assertJsonStructure(['token', 'url', 'publicId', 'apiKey', 'timestamp', 'signature']);
    }

    public function test_a_tampered_cloudinary_response_signature_is_rejected(): void
    {
        $vendor = Vendor::factory()->create();
        $image = $this->cloudinaryImagePayload($vendor);
        $image['image_signature'] = str_repeat('0', 40);

        $this->actingAs($vendor->user)
            ->post(route('vendor.products.store'), $this->productPayload($vendor, $image))
            ->assertSessionHasErrors('image_signature');

        $this->assertSame(0, Product::query()->count());
    }

    public function test_an_upload_token_cannot_be_used_by_another_vendor(): void
    {
        $owner = Vendor::factory()->create();
        $otherVendor = Vendor::factory()->create();
        $image = $this->cloudinaryImagePayload($owner);

        $this->actingAs($otherVendor->user)
            ->post(route('vendor.products.store'), $this->productPayload($otherVendor, $image))
            ->assertSessionHasErrors('image_token');

        $this->assertSame(0, $otherVendor->products()->count());
    }

    public function test_replacing_an_image_deletes_the_previous_cloudinary_asset(): void
    {
        Http::preventStrayRequests();
        Http::fake(['api.cloudinary.com/*' => Http::response(['result' => 'ok'])]);

        $vendor = Vendor::factory()->create();
        $oldImagePath = 'cloudinary/vendor-products/'.$vendor->id.'/old-image';
        $product = Product::factory()->for($vendor)->create(['image_path' => $oldImagePath]);
        $image = $this->cloudinaryImagePayload($vendor);

        $this->actingAs($vendor->user)
            ->patch(route('vendor.products.update', $product), $this->productPayload($vendor, $image))
            ->assertRedirect(route('vendor.products.index'));

        $product->refresh();
        $this->assertStringStartsWith('cloudinary/vendor-products/'.$vendor->id.'/', $product->image_path);
        $this->assertNotSame($oldImagePath, $product->image_path);

        Http::assertSent(fn (ClientRequest $request): bool => str_contains($request->url(), '/image/destroy')
            && str_contains($request->body(), $oldImagePath));
        Http::assertNotSent(fn (ClientRequest $request): bool => str_contains($request->url(), '/image/destroy')
            && str_contains($request->body(), (string) $product->image_path));
    }

    public function test_deleting_a_product_removes_its_cloudinary_image(): void
    {
        Http::preventStrayRequests();
        Http::fake(['api.cloudinary.com/*' => Http::response(['result' => 'ok'])]);

        $vendor = Vendor::factory()->create();
        $imagePath = 'cloudinary/vendor-products/'.$vendor->id.'/product-image';
        $product = Product::factory()->for($vendor)->create(['image_path' => $imagePath]);

        $this->actingAs($vendor->user)
            ->delete(route('vendor.products.destroy', $product))
            ->assertRedirect(route('vendor.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Http::assertSent(fn (ClientRequest $request): bool => str_contains($request->url(), '/image/destroy')
            && str_contains($request->body(), $imagePath));
    }

    public function test_a_cloudinary_deletion_failure_does_not_block_product_removal(): void
    {
        Http::preventStrayRequests();
        Http::fake(['api.cloudinary.com/*' => Http::response(['error' => ['message' => 'Unavailable']], 500)]);

        $vendor = Vendor::factory()->create();
        $product = Product::factory()->for($vendor)->create([
            'image_path' => 'cloudinary/vendor-products/'.$vendor->id.'/product-image',
        ]);

        $this->actingAs($vendor->user)
            ->delete(route('vendor.products.destroy', $product))
            ->assertRedirect(route('vendor.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    private function productPayload(Vendor $vendor, array $image): array
    {
        return [
            'name' => 'Cloudinary Product',
            'category' => "Women's Fashion",
            'description' => 'A product whose image is delivered from Cloudinary instead of the application server.',
            'price' => 45000,
            'stock' => 12,
            ...$image,
            'is_active' => '1',
        ];
    }
}
