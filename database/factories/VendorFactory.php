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
            'product_slots' => 0,
        ];
    }

    public function premium(): static
    {
        return $this->withProductSlots(10);
    }

    public function expiredPremium(): static
    {
        return $this->state(fn (array $attributes): array => [
            'plan' => VendorPlan::Free,
            'premium_started_at' => now()->subDays(60),
            'premium_until' => now()->subDay(),
            'product_slots' => 0,
        ]);
    }

    public function withProductSlots(int $slots): static
    {
        return $this->state(fn (array $attributes): array => [
            'plan' => $slots > 0 ? VendorPlan::Premium : VendorPlan::Free,
            'premium_started_at' => $slots > 0 ? now() : null,
            'premium_until' => null,
            'product_slots' => $slots,
        ]);
    }
}
