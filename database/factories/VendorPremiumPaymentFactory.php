<?php

namespace Database\Factories;

use App\Enums\VendorPremiumPaymentStatus;
use App\Models\Vendor;
use App\Models\VendorPremiumPayment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VendorPremiumPayment>
 */
class VendorPremiumPaymentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'reference' => 'VEF_PREM_'.Str::upper(Str::random(20)),
            'amount' => 500_000,
            'currency' => 'NGN',
            'status' => VendorPremiumPaymentStatus::Pending,
            'idempotency_key' => (string) Str::uuid(),
            'authorization_url' => null,
            'metadata' => null,
            'paid_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => VendorPremiumPaymentStatus::Paid,
            'paid_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => VendorPremiumPaymentStatus::Failed,
        ]);
    }
}
