<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->numerify('080########'),
            'delivery_address' => fake()->streetAddress(),
            'delivery_address_line_2' => null,
            'delivery_city' => fake()->city(),
            'delivery_state' => fake()->state(),
            'delivery_postal_code' => fake()->postcode(),
            'delivery_notes' => null,
            'total' => fake()->numberBetween(100_00, 5_000_00),
            'payment_reference' => 'VEF_'.Str::upper(Str::random(20)),
            'status' => OrderStatus::Pending,
            'idempotency_key' => (string) Str::uuid(),
            'checkout_url' => null,
            'metadata' => null,
            'paid_at' => null,
            'delivered_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => OrderStatus::Paid,
            'paid_at' => now(),
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => OrderStatus::Delivered,
            'paid_at' => now(),
            'delivered_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => OrderStatus::Failed,
        ]);
    }
}
