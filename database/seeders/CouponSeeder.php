<?php

namespace Database\Seeders;

use App\Enums\CouponType;
use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::query()->updateOrCreate(
            ['code' => 'SAVE500'],
            [
                'name' => 'Save ₦500',
                'type' => CouponType::Fixed,
                'amount' => 50_000,
                'percentage' => null,
                'free_shipping' => false,
                'vendor_id' => null,
                'is_active' => true,
                'starts_at' => null,
                'expires_at' => null,
            ],
        );
    }
}
