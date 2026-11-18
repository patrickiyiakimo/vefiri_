<?php

namespace Tests\Feature;

use App\Models\ProductPhotoInquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductPhotoInquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_customer_product_photos(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();

        $damaged = ProductPhotoInquiry::factory()->damaged()->create([
            'customer_name' => 'Ada Obi',
            'customer_email' => 'ada@example.com',
            'message' => 'The handle arrived broken.',
            'image_path' => 'product-inquiries/broken.jpg',
        ]);
        $request = ProductPhotoInquiry::factory()->productRequest()->create([
            'customer_name' => 'Bola Ade',
            'customer_email' => 'bola@example.com',
            'message' => 'Do you have this in blue?',
            'image_path' => 'product-inquiries/wanted.png',
        ]);

        Storage::disk('public')->put($damaged->image_path, 'fake-image');
        Storage::disk('public')->put($request->image_path, 'fake-image');

        $this->actingAs($admin)
            ->get(route('admin.product-photo-inquiries.index'))
            ->assertOk()
            ->assertSeeText('Ada Obi')
            ->assertSeeText('ada@example.com')
            ->assertSeeText('The handle arrived broken.')
            ->assertSeeText('ORD-')
            ->assertSeeText('Damaged product / delivery issue')
            ->assertSeeText('Bola Ade')
            ->assertSeeText('bola@example.com')
            ->assertSeeText('Do you have this in blue?')
            ->assertSeeText('Product availability request')
            ->assertSee('product-inquiries/broken.jpg', false)
            ->assertSee('product-inquiries/wanted.png', false);
    }

    public function test_customer_cannot_see_admin_product_photos(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get(route('admin.product-photo-inquiries.index'))
            ->assertForbidden();
    }
}
