<?php

namespace Database\Factories;

use App\Enums\CouponType;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => Str::upper(fake()->unique()->bothify('SAVE####')),
            'name' => fake()->words(3, true),
            'type' => CouponType::Fixed,
            'amount' => 50_000,
            'percentage' => null,
            'free_shipping' => false,
            'vendor_id' => null,
            'is_active' => true,
            'starts_at' => null,
            'expires_at' => null,
        ];
    }

    public function fixed(int $amount = 50_000): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => CouponType::Fixed,
            'amount' => $amount,
            'percentage' => null,
            'free_shipping' => false,
        ]);
    }

    public function percentage(float $percentage = 10): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => CouponType::Percentage,
            'amount' => null,
            'percentage' => $percentage,
            'free_shipping' => false,
        ]);
    }

    public function freeShipping(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => CouponType::FreeShipping,
            'amount' => null,
            'percentage' => null,
            'free_shipping' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes): array => [
            'starts_at' => now()->subMonth(),
            'expires_at' => now()->subDay(),
        ]);
    }
}
