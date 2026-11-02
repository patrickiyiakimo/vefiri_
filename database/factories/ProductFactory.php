<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(4, true);
        $price = fake()->numberBetween(5_000, 500_000);

        return [
            'vendor_id' => Vendor::factory(),
            'name' => Str::title($name),
            'slug' => fake()->unique()->slug(4),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement([
                "Women's Fashion",
                "Men's Fashion",
                'Phones & Accessories',
                'Consumer Electronics',
                'Home & Garden',
                'Beauty & Health',
                'Sports & Outdoors',
                'Toys & Games',
                'Tools',
                'Automobiles',
            ]),
            'price' => $price,
            'compare_price' => null,
            'stock' => fake()->numberBetween(0, 100),
            'image_path' => 'vendor-products/placeholder.webp',
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
