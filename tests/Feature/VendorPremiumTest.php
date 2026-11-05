<?php

namespace Tests\Feature;

use App\Enums\VendorPlan;
use App\Enums\VendorPremiumPaymentStatus;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorPremiumPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class VendorPremiumTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'paystack.secret_key' => 'sk_test_premium',
            'paystack.base_url' => 'https://api.paystack.co',
            'paystack.premium_callback_url' => 'https://vefiri.test/vendor/premium/callback',
            'vendor.free_product_limit' => 10,
            'vendor.premium_product_limit' => null,
            'vendor.premium.price' => 500_000,
            'vendor.premium.currency' => 'NGN',
            'vendor.premium.duration_days' => 30,
        ]);
    }

    public function test_guests_are_redirected_to_login_before_starting_a_premium_upgrade(): void
    {
        $this->post(route('vendor.premium.store'), [
            'idempotency_key' => (string) Str::uuid(),
        ])->assertRedirect(route('login'));
    }

    public function test_customers_cannot_view_the_premium_upgrade_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('vendor.premium.show'))
            ->assertForbidden();
    }

    public function test_customers_cannot_start_a_premium_upgrade(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('vendor.premium.store'), [
                'idempotency_key' => (string) Str::uuid(),
            ])
            ->assertForbidden();
    }

    public function test_the_premium_page_shows_the_configured_price_and_limits(): void
    {
        $vendor = Vendor::factory()->create();

        $this->actingAs($vendor->user)
            ->get(route('vendor.premium.show'))
            ->assertOk()
            ->assertSeeText('Premium plan')
            ->assertSeeText('10 products')
            ->assertSeeText('Unlimited')
            ->assertSeeText('Upgrade to Premium')
            ->assertSeeText('Paystack');
    }

    public function test_expired_premium_vendors_are_shown_as_free_tier(): void
    {
        $vendor = Vendor::factory()->expiredPremium()->create();

        $this->actingAs($vendor->user)
            ->get(route('vendor.premium.show'))
            ->assertOk()
            ->assertSeeTextInOrder(['Current plan', 'Free']);

        $this->assertSame(VendorPlan::Free, $vendor->fresh()->currentPlan());
    }

    public function test_vendors_can_initialize_a_premium_payment(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/premium'],
            ]),
        ]);

        $vendor = Vendor::factory()->create([
            'business_email' => 'store@example.com',
        ]);
        $idempotencyKey = (string) Str::uuid();

        $this->actingAs($vendor->user)
            ->post(route('vendor.premium.store'), [
                'idempotency_key' => $idempotencyKey,
            ])
            ->assertRedirect('https://checkout.paystack.com/premium');

        $payment = VendorPremiumPayment::query()->firstOrFail();

        $this->assertSame($vendor->getKey(), $payment->vendor_id);
        $this->assertSame(500_000, $payment->amount);
        $this->assertSame('NGN', $payment->currency);
        $this->assertSame(VendorPremiumPaymentStatus::Pending, $payment->status);
        $this->assertSame($idempotencyKey, $payment->idempotency_key);
        $this->assertSame('https://checkout.paystack.com/premium', $payment->authorization_url);
        $this->assertStringStartsWith('VEF_PREM_', $payment->reference);

        Http::assertSent(function (ClientRequest $request) use ($vendor, $payment): bool {
            $data = $request->data();

            return $request->url() === 'https://api.paystack.co/transaction/initialize'
                && $request->method() === 'POST'
                && $request->hasHeader('Authorization', 'Bearer sk_test_premium')
                && $data['email'] === 'store@example.com'
                && $data['amount'] === 500_000
                && $data['currency'] === 'NGN'
                && $data['reference'] === $payment->reference
                && $data['callback_url'] === 'https://vefiri.test/vendor/premium/callback'
                && $data['metadata']['type'] === 'vendor_premium'
                && $data['metadata']['vendor_id'] === $vendor->getKey()
                && $data['metadata']['payment_id'] === $payment->getKey();
        });
    }

    public function test_a_paystack_failure_during_initialization_does_not_create_a_payment(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => false,
                'message' => 'Service unavailable.',
            ], 503),
        ]);

        $vendor = Vendor::factory()->create();

        $this->actingAs($vendor->user)
            ->post(route('vendor.premium.store'), [
                'idempotency_key' => (string) Str::uuid(),
            ])
            ->assertRedirect(route('vendor.premium.show'))
            ->assertSessionHasErrors('premium');

        $this->assertSame(0, VendorPremiumPayment::query()->count());
    }

    public function test_replaying_the_same_idempotency_key_returns_the_existing_premium_payment(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/once'],
            ]),
        ]);

        $vendor = Vendor::factory()->create();
        $idempotencyKey = (string) Str::uuid();

        $this->actingAs($vendor->user)
            ->post(route('vendor.premium.store'), ['idempotency_key' => $idempotencyKey])
            ->assertRedirect('https://checkout.paystack.com/once');

        $this->actingAs($vendor->user)
            ->post(route('vendor.premium.store'), ['idempotency_key' => $idempotencyKey])
            ->assertRedirect('https://checkout.paystack.com/once');

        $this->assertSame(1, VendorPremiumPayment::query()->count());
        Http::assertSentCount(1);
    }

    public function test_a_premium_idempotency_key_cannot_be_reused_by_another_vendor(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/owned'],
            ]),
        ]);

        $firstVendor = Vendor::factory()->create();
        $secondVendor = Vendor::factory()->create();
        $idempotencyKey = (string) Str::uuid();

        $this->actingAs($firstVendor->user)
            ->post(route('vendor.premium.store'), ['idempotency_key' => $idempotencyKey])
            ->assertRedirect('https://checkout.paystack.com/owned');

        $this->actingAs($secondVendor->user)
            ->post(route('vendor.premium.store'), ['idempotency_key' => $idempotencyKey])
            ->assertSessionHasErrors('idempotency_key');

        $this->assertSame(1, VendorPremiumPayment::query()->count());
        Http::assertSentCount(1);
    }

    public function test_the_callback_page_verifies_and_upgrades_a_successful_payment(): void
    {
        $this->fakePremiumVerification('VEF_PREM_CALLBACK_SUCCESS');

        $vendor = Vendor::factory()->create();
        $payment = VendorPremiumPayment::factory()->for($vendor)->create([
            'reference' => 'VEF_PREM_CALLBACK_SUCCESS',
            'amount' => 500_000,
            'currency' => 'NGN',
        ]);

        $this->actingAs($vendor->user)
            ->get(route('vendor.premium.callback', ['reference' => $payment->reference]))
            ->assertOk()
            ->assertSeeText('Premium is active');

        $this->assertSame(VendorPremiumPaymentStatus::Paid, $payment->fresh()->status);
        $this->assertTrue($vendor->fresh()->isPremium());
    }

    public function test_the_callback_page_keeps_pending_payments_unconfirmed(): void
    {
        $this->fakePremiumVerification('VEF_PREM_CALLBACK_PENDING', status: 'pending');

        $vendor = Vendor::factory()->create();
        $payment = VendorPremiumPayment::factory()->for($vendor)->create([
            'reference' => 'VEF_PREM_CALLBACK_PENDING',
            'amount' => 500_000,
            'currency' => 'NGN',
        ]);

        $this->actingAs($vendor->user)
            ->get(route('vendor.premium.callback', ['reference' => $payment->reference]))
            ->assertOk()
            ->assertSeeText('Payment is being confirmed');

        $this->assertSame(VendorPremiumPaymentStatus::Pending, $payment->fresh()->status);
        $this->assertFalse($vendor->fresh()->isPremium());
    }

    public function test_the_callback_page_shows_failed_payments(): void
    {
        $this->fakePremiumVerification('VEF_PREM_CALLBACK_FAILED', status: 'failed');

        $vendor = Vendor::factory()->create();
        $payment = VendorPremiumPayment::factory()->for($vendor)->failed()->create([
            'reference' => 'VEF_PREM_CALLBACK_FAILED',
            'amount' => 500_000,
            'currency' => 'NGN',
        ]);

        $this->actingAs($vendor->user)
            ->get(route('vendor.premium.callback', ['reference' => $payment->reference]))
            ->assertOk()
            ->assertSeeText('Payment was not completed');

        $this->assertSame(VendorPremiumPaymentStatus::Failed, $payment->fresh()->status);
        $this->assertFalse($vendor->fresh()->isPremium());
    }

    public function test_the_callback_page_shows_a_confirmed_premium_payment(): void
    {
        $vendor = Vendor::factory()->premium()->create();
        $payment = VendorPremiumPayment::factory()->for($vendor)->paid()->create([
            'reference' => 'VEF_PREM_CONFIRMED',
        ]);

        $this->actingAs($vendor->user)
            ->get(route('vendor.premium.callback', ['reference' => $payment->reference]))
            ->assertOk()
            ->assertSeeText('Premium is active')
            ->assertSeeText($payment->reference);
    }

    private function fakePremiumVerification(
        string $reference,
        string $status = 'success',
        int $amount = 500_000,
        string $currency = 'NGN',
    ): void {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/verify/'.$reference => Http::response([
                'status' => true,
                'data' => [
                    'status' => $status,
                    'reference' => $reference,
                    'amount' => $amount,
                    'currency' => $currency,
                ],
            ]),
        ]);
    }
}
