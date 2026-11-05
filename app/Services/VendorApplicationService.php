<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\VendorApplicationStatus;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VendorApplicationService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function apply(User $user, array $data): VendorApplication
    {
        return DB::transaction(function () use ($user, $data): VendorApplication {
            if ($user->vendor()->exists()) {
                throw ValidationException::withMessages([
                    'store_name' => 'You are already a verified vendor.',
                ]);
            }

            if ($user->vendorApplications()->pending()->lockForUpdate()->exists()) {
                throw ValidationException::withMessages([
                    'store_name' => 'You already have a pending vendor application.',
                ]);
            }

            return $user->vendorApplications()->create([
                ...$data,
                'status' => VendorApplicationStatus::Pending,
            ]);
        });
    }

    public function approve(VendorApplication $application, User $reviewer): Vendor
    {
        return DB::transaction(function () use ($application, $reviewer): Vendor {
            $application = VendorApplication::query()
                ->whereKey($application->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($application->status !== VendorApplicationStatus::Pending) {
                throw ValidationException::withMessages([
                    'application' => 'This vendor application has already been reviewed.',
                ]);
            }

            $applicant = $application->applicant;

            if ($applicant === null || $applicant->trashed()) {
                throw ValidationException::withMessages([
                    'application' => 'This applicant account has been deleted, so the application cannot be approved.',
                ]);
            }

            $application->update([
                'status' => VendorApplicationStatus::Approved,
                'admin_note' => null,
                'reviewed_by' => $reviewer->getKey(),
                'reviewed_at' => now(),
            ]);

            $vendor = Vendor::query()->updateOrCreate(
                ['user_id' => $application->user_id],
                [
                    'business_name' => $application->business_name,
                    'store_name' => $application->store_name,
                    'business_email' => $application->business_email,
                    'phone' => $application->phone,
                    'address' => $application->address,
                    'city' => $application->city,
                    'state' => $application->state,
                    'description' => $application->description,
                ],
            );

            $applicant->update(['role' => UserRole::Vendor]);

            return $vendor;
        });
    }

    public function reject(VendorApplication $application, User $reviewer, ?string $adminNote = null): VendorApplication
    {
        return DB::transaction(function () use ($application, $reviewer, $adminNote): VendorApplication {
            $application = VendorApplication::query()
                ->whereKey($application->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($application->status !== VendorApplicationStatus::Pending) {
                throw ValidationException::withMessages([
                    'application' => 'This vendor application has already been reviewed.',
                ]);
            }

            $application->update([
                'status' => VendorApplicationStatus::Rejected,
                'admin_note' => $adminNote,
                'reviewed_by' => $reviewer->getKey(),
                'reviewed_at' => now(),
            ]);

            return $application;
        });
    }
}
