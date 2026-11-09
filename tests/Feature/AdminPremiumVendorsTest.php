<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorPremiumPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPremiumVendorsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_admin_dashboard_lists_vendors_with_extra_slots_and_their_payments(): void
    {
        $admin = User::factory()->admin()->create();

        $vendor = Vendor::factory()->withProductSlots(10)->create([
            'store_name' => 'Ada Luxury',
            'business_email' => 'ada@example.com',
        ]);

        VendorPremiumPayment::factory()->for($vendor)->paid()->create([
            'reference' => 'VEF_SLOT_ADMIN_DASHBOARD',
            'amount' => 1_000_000,
            'quantity' => 10,
            'currency' => 'NGN',
            'paid_at' => now(),
        ]);

        Product::factory()->count(3)->for($vendor)->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('Vendors with extra product slots')
            ->assertSeeText('Ada Luxury')
            ->assertSeeText('ada@example.com')
            ->assertSeeText('20 limit')
            ->assertSeeText('3 uploaded')
            ->assertSeeText('NGN 10,000')
            ->assertSeeText('VEF_SLOT_ADMIN_DASHBOARD');
    }

    public function test_the_admin_dashboard_only_lists_vendors_with_purchased_slots(): void
    {
        $admin = User::factory()->admin()->create();

        $active = Vendor::factory()->withProductSlots(10)->create([
            'store_name' => 'Active Slots Store',
        ]);

        $expired = Vendor::factory()->expiredPremium()->create([
            'store_name' => 'Expired Premium Store',
        ]);

        $free = Vendor::factory()->create([
            'store_name' => 'Free Plan Store',
        ]);

        VendorPremiumPayment::factory()->for($free)->paid()->create([
            'reference' => 'VEF_SLOT_FREE_PLAN',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText($active->store_name)
            ->assertDontSeeText($expired->store_name)
            ->assertDontSeeText($free->store_name)
            ->assertDontSeeText('VEF_SLOT_FREE_PLAN');
    }

    public function test_the_admin_dashboard_uses_the_latest_paid_slots_payment(): void
    {
        $admin = User::factory()->admin()->create();
        $vendor = Vendor::factory()->withProductSlots(10)->create();

        VendorPremiumPayment::factory()->for($vendor)->paid()->create([
            'reference' => 'VEF_SLOT_OLD_PAYMENT',
            'paid_at' => now()->subDays(10),
        ]);

        VendorPremiumPayment::factory()->for($vendor)->paid()->create([
            'reference' => 'VEF_SLOT_NEWEST_PAYMENT',
            'paid_at' => now(),
        ]);

        VendorPremiumPayment::factory()->for($vendor)->create([
            'reference' => 'VEF_SLOT_UNPAID_PAYMENT',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('VEF_SLOT_NEWEST_PAYMENT')
            ->assertDontSeeText('VEF_SLOT_OLD_PAYMENT')
            ->assertDontSeeText('VEF_SLOT_UNPAID_PAYMENT');
    }
}
