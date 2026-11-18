<?php

namespace Database\Factories;

use App\Enums\ProductPhotoInquiryType;
use App\Models\ProductPhotoInquiry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductPhotoInquiry>
 */
class ProductPhotoInquiryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'order_reference' => null,
            'type' => fake()->randomElement(ProductPhotoInquiryType::cases()),
            'message' => fake()->sentence(),
            'image_path' => 'product-inquiries/sample.jpg',
        ];
    }

    public function damaged(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => ProductPhotoInquiryType::DamagedProduct,
            'order_reference' => 'ORD-'.fake()->numerify('######'),
        ]);
    }

    public function productRequest(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => ProductPhotoInquiryType::ProductRequest,
            'order_reference' => null,
        ]);
    }
}
