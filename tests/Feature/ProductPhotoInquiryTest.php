<?php

namespace Tests\Feature;

use App\Enums\ProductPhotoInquiryType;
use App\Models\ProductPhotoInquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductPhotoInquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_send_a_product_photo(): void
    {
        Storage::fake('public');

        $response = $this->post(route('product-photo-inquiries.store'), [
            'customer_name' => 'Ada Obi',
            'customer_email' => 'ada@example.com',
            'type' => ProductPhotoInquiryType::DamagedProduct->value,
            'order_reference' => 'ORD-123456',
            'message' => 'The handle arrived broken.',
            'image' => UploadedFile::fake()->image('broken-handle.jpg', 800, 600),
        ]);

        $response->assertCreated();

        $inquiry = ProductPhotoInquiry::query()->firstOrFail();

        $this->assertDatabaseHas('product_photo_inquiries', [
            'id' => $inquiry->getKey(),
            'user_id' => null,
            'customer_name' => 'Ada Obi',
            'customer_email' => 'ada@example.com',
            'order_reference' => 'ORD-123456',
            'type' => ProductPhotoInquiryType::DamagedProduct->value,
            'message' => 'The handle arrived broken.',
            'image_path' => $inquiry->image_path,
        ]);
        Storage::disk('public')->assertExists($inquiry->image_path);
    }

    public function test_signed_in_customer_is_linked_to_the_inquiry(): void
    {
        Storage::fake('public');

        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->post(route('product-photo-inquiries.store'), [
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'type' => ProductPhotoInquiryType::ProductRequest->value,
                'message' => 'Please tell me when this is available again.',
                'image' => UploadedFile::fake()->image('wanted-product.png', 640, 640),
            ])
            ->assertCreated();

        $this->assertDatabaseHas('product_photo_inquiries', [
            'user_id' => $customer->getKey(),
            'customer_email' => $customer->email,
            'type' => ProductPhotoInquiryType::ProductRequest->value,
        ]);
    }

    public function test_a_photo_is_required(): void
    {
        $this->post(route('product-photo-inquiries.store'), [
            'customer_name' => 'Ada Obi',
            'customer_email' => 'ada@example.com',
            'type' => ProductPhotoInquiryType::DamagedProduct->value,
        ])->assertSessionHasErrors('image');
    }

    public function test_storefront_exposes_the_camera_upload_control(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-photo-inquiry-open', false)
            ->assertSee('data-photo-inquiry-modal', false)
            ->assertSeeText('Send a product photo');
    }
}
