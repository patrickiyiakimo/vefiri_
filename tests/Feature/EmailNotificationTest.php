<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Mail\DeliveryConfirmationMail;
use App\Mail\LoginNotificationMail;
use App\Mail\VendorApplicationApprovedMail;
use App\Mail\VendorApplicationReceivedMail;
use App\Mail\VendorApplicationRejectedMail;
use App\Mail\WelcomeMail;
use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_welcome_email_is_queued_when_a_user_registers(): void
    {
        Mail::fake();

        $this->postJson(route('register.store'), [
            'name' => 'Ada Okafor',
            'email' => 'ada@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertOk();

        Mail::assertQueued(WelcomeMail::class, function (WelcomeMail $mail): bool {
            return $mail->hasTo('ada@example.com')
                && $mail->user->email === 'ada@example.com';
        });
    }

    public function test_a_login_notification_is_queued_when_a_user_signs_in(): void
    {
        Mail::fake();

        User::factory()->create([
            'email' => 'ada@example.com',
            'password' => 'password123',
        ]);

        $this->postJson(route('login.store'), [
            'email' => 'ada@example.com',
            'password' => 'password123',
        ])->assertOk();

        Mail::assertQueued(LoginNotificationMail::class, function (LoginNotificationMail $mail): bool {
            return $mail->hasTo('ada@example.com')
                && $mail->signedInAt !== ''
                && $mail->ipAddress !== null;
        });
    }

    public function test_a_vendor_application_received_email_is_queued(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'vendor@example.com']);

        $this->actingAs($user)
            ->post(route('vendors.apply.store'), $this->vendorApplicationPayload())
            ->assertRedirect(route('vendors.status'));

        Mail::assertQueued(VendorApplicationReceivedMail::class, function (VendorApplicationReceivedMail $mail) use ($user): bool {
            return $mail->hasTo($user->email)
                && $mail->application->store_name === 'Ada Home Store';
        });
    }

    public function test_a_vendor_approval_email_is_queued(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['email' => 'vendor@example.com']);
        $application = VendorApplication::factory()->create([
            'user_id' => $user->getKey(),
            'store_name' => 'Ada Home Store',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.applications.approve', $application))
            ->assertRedirect();

        $this->assertSame(UserRole::Vendor, $user->fresh()->role);

        Mail::assertQueued(VendorApplicationApprovedMail::class, function (VendorApplicationApprovedMail $mail) use ($user): bool {
            return $mail->hasTo($user->email)
                && $mail->vendor->store_name === 'Ada Home Store';
        });
    }

    public function test_a_vendor_rejection_email_is_queued(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['email' => 'vendor@example.com']);
        $application = VendorApplication::factory()->create([
            'user_id' => $user->getKey(),
            'store_name' => 'Ada Home Store',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.applications.reject', $application), [
                'admin_note' => 'Please provide a valid business registration number.',
            ])
            ->assertRedirect();

        Mail::assertQueued(VendorApplicationRejectedMail::class, function (VendorApplicationRejectedMail $mail) use ($user): bool {
            return $mail->hasTo($user->email)
                && $mail->application->admin_note === 'Please provide a valid business registration number.';
        });
    }

    public function test_all_notification_emails_render_with_the_brand_signature_and_automated_notice(): void
    {
        $user = User::factory()->create([
            'name' => 'Ada Okafor',
            'email' => 'ada@example.com',
        ]);
        $application = VendorApplication::factory()->approved()->create([
            'user_id' => $user->getKey(),
        ]);
        $vendor = Vendor::factory()->create([
            'user_id' => $user->getKey(),
            'store_name' => 'Ada Home Store',
        ]);
        $order = Order::factory()->delivered()->create([
            'customer_name' => 'Ada Okafor',
            'customer_email' => 'ada@example.com',
        ]);

        $mailables = [
            new WelcomeMail($user),
            new LoginNotificationMail($user, now()->format('M j, Y \a\t g:i A T'), '127.0.0.1', 'PHPUnit'),
            new DeliveryConfirmationMail($order),
            new VendorApplicationReceivedMail($application),
            new VendorApplicationApprovedMail($application, $vendor),
            new VendorApplicationRejectedMail($application),
        ];

        foreach ($mailables as $mailable) {
            $html = $mailable->render();

            $this->assertStringContainsString('Vefiri', $html);
            $this->assertStringContainsString('Please do not reply to this email.', $html);
            $this->assertStringContainsString('Automated message', $html);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function vendorApplicationPayload(): array
    {
        return [
            'business_name' => 'Ada Retail Limited',
            'store_name' => 'Ada Home Store',
            'business_email' => 'store@example.com',
            'phone' => '08012345678',
            'address' => '12 Admiralty Way',
            'city' => 'Lekki',
            'state' => 'Lagos',
            'business_registration_number' => 'RC123456',
            'description' => 'We sell carefully selected home and lifestyle products for modern Nigerian homes.',
            'product_categories' => ['Home & Garden'],
        ];
    }
}
