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
            'vendor.product_slots.price' => 100_000,
            'vendor.product_slots.bundles' => [10, 20, 30, 40],
            'vendor.product_slots.currency' => 'NGN',
        ]);
    }

    public function test_guests_are_redirected_to_login_before_buying_product_slots(): void
    {
        $this->post(route('vendor.premium.store'), [
            'idempotency_key' => (string) Str::uuid(),
            'quantity' => 10,
        ])->assertRedirect(route('login'));
    }

    public function test_customers_cannot_view_the_product_slots_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('vendor.premium.show'))
            ->assertForbidden();
    }

    public function test_customers_cannot_buy_product_slots(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('vendor.premium.store'), [
                'idempotency_key' => (string) Str::uuid(),
                'quantity' => 10,
            ])
            ->assertForbidden();
    }

    public function test_the_product_slots_page_shows_the_configured_bundles_and_limits(): void
    {
        $vendor = Vendor::factory()->create();

        $this->actingAs($vendor->user)
            ->get(route('vendor.premium.show'))
            ->assertOk()
            ->assertSeeText('Product slots')
            ->assertSeeText('Free product limit')
            ->assertSeeText('10 products')
            ->assertSeeText('10 more products')
            ->assertSeeText('30 more products')
            ->assertSeeText('Buy product slots')
            ->assertSeeText('Paystack');
    }

    public function test_vendors_without_purchased_slots_are_shown_as_free_tier(): void
    {
        $vendor = Vendor::factory()->create();

        $this->actingAs($vendor->user)
            ->get(route('vendor.premium.show'))
            ->assertOk()
            ->assertSeeText('Free tier');

        $this->assertSame(VendorPlan::Free, $vendor->fresh()->currentPlan());
    }

    public function test_vendors_can_initialize_a_product_slots_payment(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/slots'],
            ]),
        ]);

        $vendor = Vendor::factory()->create([
            'business_email' => 'store@example.com',
        ]);
        $idempotencyKey = (string) Str::uuid();

        $this->actingAs($vendor->user)
            ->post(route('vendor.premium.store'), [
                'idempotency_key' => $idempotencyKey,
                'quantity' => 20,
            ])
            ->assertRedirect('https://checkout.paystack.com/slots');

        $payment = VendorPremiumPayment::query()->firstOrFail();

        $this->assertSame($vendor->getKey(), $payment->vendor_id);
        $this->assertSame(2_000_000, $payment->amount);
        $this->assertSame(20, $payment->quantity);
        $this->assertSame('NGN', $payment->currency);
        $this->assertSame(VendorPremiumPaymentStatus::Pending, $payment->status);
        $this->assertSame($idempotencyKey, $payment->idempotency_key);
        $this->assertSame('https://checkout.paystack.com/slots', $payment->authorization_url);
        $this->assertStringStartsWith('VEF_SLOT_', $payment->reference);

        Http::assertSent(function (ClientRequest $request) use ($vendor, $payment): bool {
            $data = $request->data();

            return $request->url() === 'https://api.paystack.co/transaction/initialize'
                && $request->method() === 'POST'
                && $request->hasHeader('Authorization', 'Bearer sk_test_premium')
                && $data['email'] === 'store@example.com'
                && $data['amount'] === 2_000_000
                && $data['currency'] === 'NGN'
                && $data['reference'] === $payment->reference
                && $data['callback_url'] === 'https://vefiri.test/vendor/premium/callback'
                && $data['metadata']['type'] === 'vendor_product_slots'
                && $data['metadata']['vendor_id'] === $vendor->getKey()
                && $data['metadata']['payment_id'] === $payment->getKey()
                && $data['metadata']['quantity'] === 20;
        });
    }

    public function test_a_quantity_outside_the_configured_bundles_is_rejected(): void
    {
        $vendor = Vendor::factory()->create();

        $this->actingAs($vendor->user)
            ->post(route('vendor.premium.store'), [
                'idempotency_key' => (string) Str::uuid(),
                'quantity' => 15,
            ])
            ->assertSessionHasErrors('quantity');

        $this->assertSame(0, VendorPremiumPayment::query()->count());
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
                'quantity' => 10,
            ])
            ->assertRedirect(route('vendor.premium.show'))
            ->assertSessionHasErrors('premium');

        $this->assertSame(0, VendorPremiumPayment::query()->count());
    }

    public function test_replaying_the_same_idempotency_key_returns_the_existing_product_slots_payment(): void
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
            ->post(route('vendor.premium.store'), ['idempotency_key' => $idempotencyKey, 'quantity' => 10])
            ->assertRedirect('https://checkout.paystack.com/once');

        $this->actingAs($vendor->user)
            ->post(route('vendor.premium.store'), ['idempotency_key' => $idempotencyKey, 'quantity' => 10])
            ->assertRedirect('https://checkout.paystack.com/once');

        $this->assertSame(1, VendorPremiumPayment::query()->count());
        Http::assertSentCount(1);
    }

    public function test_a_product_slots_idempotency_key_cannot_be_reused_by_another_vendor(): void
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
            ->post(route('vendor.premium.store'), ['idempotency_key' => $idempotencyKey, 'quantity' => 10])
            ->assertRedirect('https://checkout.paystack.com/owned');

        $this->actingAs($secondVendor->user)
            ->post(route('vendor.premium.store'), ['idempotency_key' => $idempotencyKey, 'quantity' => 10])
            ->assertSessionHasErrors('idempotency_key');

        $this->assertSame(1, VendorPremiumPayment::query()->count());
        Http::assertSentCount(1);
    }

    public function test_the_callback_page_verifies_and_adds_successful_product_slots(): void
    {
        $this->fakePremiumVerification('VEF_SLOT_CALLBACK_SUCCESS');

        $vendor = Vendor::factory()->create();
        $payment = VendorPremiumPayment::factory()->for($vendor)->create([
            'reference' => 'VEF_SLOT_CALLBACK_SUCCESS',
            'amount' => 1_000_000,
            'quantity' => 10,
            'currency' => 'NGN',
        ]);

        $this->actingAs($vendor->user)
            ->get(route('vendor.premium.callback', ['reference' => $payment->reference]))
            ->assertOk()
            ->assertSeeText('Product slots added');

        $this->assertSame(VendorPremiumPaymentStatus::Paid, $payment->fresh()->status);
        $this->assertSame(10, $vendor->fresh()->product_slots);
        $this->assertTrue($vendor->fresh()->isPremium());
    }

    public function test_the_callback_page_keeps_pending_payments_unconfirmed(): void
    {
        $this->fakePremiumVerification('VEF_SLOT_CALLBACK_PENDING', status: 'pending');

        $vendor = Vendor::factory()->create();
        $payment = VendorPremiumPayment::factory()->for($vendor)->create([
            'reference' => 'VEF_SLOT_CALLBACK_PENDING',
            'amount' => 1_000_000,
            'quantity' => 10,
            'currency' => 'NGN',
        ]);

        $this->actingAs($vendor->user)
            ->get(route('vendor.premium.callback', ['reference' => $payment->reference]))
            ->assertOk()
            ->assertSeeText('Payment is being confirmed');

        $this->assertSame(VendorPremiumPaymentStatus::Pending, $payment->fresh()->status);
        $this->assertSame(0, $vendor->fresh()->product_slots);
    }

    public function test_the_callback_page_shows_failed_payments(): void
    {
        $this->fakePremiumVerification('VEF_SLOT_CALLBACK_FAILED', status: 'failed');

        $vendor = Vendor::factory()->create();
        $payment = VendorPremiumPayment::factory()->for($vendor)->failed()->create([
            'reference' => 'VEF_SLOT_CALLBACK_FAILED',
            'amount' => 1_000_000,
            'quantity' => 10,
            'currency' => 'NGN',
        ]);

        $this->actingAs($vendor->user)
            ->get(route('vendor.premium.callback', ['reference' => $payment->reference]))
            ->assertOk()
            ->assertSeeText('Payment was not completed');

        $this->assertSame(VendorPremiumPaymentStatus::Failed, $payment->fresh()->status);
        $this->assertSame(0, $vendor->fresh()->product_slots);
    }

    public function test_the_callback_page_shows_a_confirmed_product_slots_payment(): void
    {
        $vendor = Vendor::factory()->withProductSlots(10)->create();
        $payment = VendorPremiumPayment::factory()->for($vendor)->paid()->create([
            'reference' => 'VEF_SLOT_CONFIRMED',
            'quantity' => 10,
            'amount' => 1_000_000,
        ]);

        $this->actingAs($vendor->user)
            ->get(route('vendor.premium.callback', ['reference' => $payment->reference]))
            ->assertOk()
            ->assertSeeText('Product slots added')
            ->assertSeeText($payment->reference);
    }

    private function fakePremiumVerification(
        string $reference,
        string $status = 'success',
        int $amount = 1_000_000,
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
