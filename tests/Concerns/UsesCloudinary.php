<?php

namespace Tests\Concerns;

use App\Models\Vendor;

trait UsesCloudinary
{
    protected function configureCloudinary(): void
    {
        config([
            'services.cloudinary.cloud' => 'test-cloud',
            'services.cloudinary.key' => '1234567890',
            'services.cloudinary.secret' => 'test-secret',
            'services.cloudinary.api_base' => 'https://api.cloudinary.com',
            'services.cloudinary.public_id_prefix' => 'cloudinary/vendor-products',
            'services.cloudinary.delivery_transformations' => 'f_auto,q_auto,c_limit,w_1200',
            'services.cloudinary.signature_ttl_minutes' => 55,
        ]);
    }

    protected function cloudinaryImagePayload(Vendor $vendor): array
    {
        $response = $this->actingAs($vendor->user)
            ->postJson(route('vendor.product-images.signature'))
            ->assertCreated();

        $publicId = (string) $response->json('publicId');
        $version = 1234567890;
        $signature = sha1("public_id={$publicId}&version={$version}test-secret");

        return [
            'image_token' => (string) $response->json('token'),
            'image_version' => (string) $version,
            'image_signature' => $signature,
        ];
    }
}
