<?php

namespace Database\Factories;

use App\Enums\VendorPlan;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vendor>
 */
class VendorFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $storeName = fake()->unique()->company();

        return [
            'user_id' => User::factory()->vendor(),
            'business_name' => $storeName.' Limited',
            'store_name' => $storeName,
            'business_email' => fake()->unique()->companyEmail(),
            'phone' => fake()->numerify('080########'),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'description' => fake()->paragraph(),
            'plan' => VendorPlan::Free,
            'premium_started_at' => null,
            'premium_until' => null,
        ];
    }

    public function premium(): static
    {
        return $this->state(fn (array $attributes): array => [
            'plan' => VendorPlan::Premium,
            'premium_started_at' => now(),
            'premium_until' => now()->addDays(30),
        ]);
    }

    public function expiredPremium(): static
    {
        return $this->state(fn (array $attributes): array => [
            'plan' => VendorPlan::Premium,
            'premium_started_at' => now()->subDays(60),
            'premium_until' => now()->subDay(),
        ]);
    }
}
