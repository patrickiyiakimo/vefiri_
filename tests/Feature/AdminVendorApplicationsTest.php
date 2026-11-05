<?php

namespace Tests\Feature;

use App\Enums\VendorApplicationStatus;
use App\Models\User;
use App\Models\VendorApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminVendorApplicationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_admin_dashboard_renders_applications_from_deleted_accounts(): void
    {
        $admin = User::factory()->admin()->create();
        $applicant = User::factory()->create([
            'name' => 'Ada Okafor',
            'email' => 'ada@example.com',
        ]);

        $application = VendorApplication::factory()->create([
            'user_id' => $applicant->getKey(),
            'store_name' => 'Ada Home Store',
        ]);

        $applicant->delete();

        $this->assertSoftDeleted('users', ['id' => $applicant->getKey()]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('Ada Home Store')
            ->assertSeeText('ada@example.com')
            ->assertSeeText('Deleted account');
    }

    public function test_the_applications_page_marks_deleted_applicants_and_disables_approval(): void
    {
        $admin = User::factory()->admin()->create();
        $applicant = User::factory()->create([
            'name' => 'Ada Okafor',
            'email' => 'ada@example.com',
        ]);

        $application = VendorApplication::factory()->create([
            'user_id' => $applicant->getKey(),
            'store_name' => 'Ada Home Store',
        ]);

        $applicant->delete();

        $this->actingAs($admin)
            ->get(route('admin.applications.index'))
            ->assertOk()
            ->assertSeeText('Ada Home Store')
            ->assertSeeText('Ada Okafor')
            ->assertSeeText('ada@example.com')
            ->assertSeeText('Deleted account')
            ->assertSeeText('Applicant account deleted. Approval is disabled.')
            ->assertDontSee(route('admin.applications.approve', $application), false)
            ->assertSee(route('admin.applications.reject', $application), false);
    }

    public function test_an_application_from_a_deleted_applicant_cannot_be_approved(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();
        $applicant = User::factory()->create();
        $application = VendorApplication::factory()->create([
            'user_id' => $applicant->getKey(),
        ]);

        $applicant->delete();

        $this->actingAs($admin)
            ->patch(route('admin.applications.approve', $application))
            ->assertRedirect()
            ->assertSessionHasErrors('application');

        $this->assertSame(VendorApplicationStatus::Pending, $application->fresh()->status);
        $this->assertDatabaseMissing('vendors', ['user_id' => $applicant->getKey()]);
        Mail::assertNothingQueued();
    }

    public function test_an_application_from_a_deleted_applicant_can_be_rejected_without_sending_email(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();
        $applicant = User::factory()->create();
        $application = VendorApplication::factory()->create([
            'user_id' => $applicant->getKey(),
        ]);

        $applicant->delete();

        $this->actingAs($admin)
            ->patch(route('admin.applications.reject', $application), [
                'admin_note' => 'Applicant account no longer exists.',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $application->refresh();

        $this->assertSame(VendorApplicationStatus::Rejected, $application->status);
        $this->assertSame('Applicant account no longer exists.', $application->admin_note);
        Mail::assertNothingQueued();
    }
}
