<?php

namespace Database\Factories;

use App\Enums\VendorApplicationStatus;
use App\Models\User;
use App\Models\VendorApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VendorApplication>
 */
class VendorApplicationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'business_name' => fake()->company().' Limited',
            'store_name' => fake()->unique()->company(),
            'business_email' => fake()->unique()->companyEmail(),
            'phone' => fake()->numerify('080########'),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'business_registration_number' => fake()->numerify('RC######'),
            'description' => fake()->paragraph(),
            'product_categories' => fake()->randomElements(['Consumer Electronics', 'Home & Garden', 'Beauty & Health'], 2),
            'status' => VendorApplicationStatus::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VendorApplicationStatus::Approved,
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VendorApplicationStatus::Rejected,
            'reviewed_at' => now(),
            'admin_note' => 'Please provide a valid business registration number.',
        ]);
    }
}
