<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VendorApplicationStatus;
use App\Enums\VendorPlan;
use App\Enums\VendorPremiumPaymentStatus;
use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DashboardController extends Controller
{
    use ProvidesStorefrontData;

    public function __invoke(): View
    {
        $premiumVendorsQuery = Vendor::query()
            ->where('plan', VendorPlan::Premium)
            ->where(function (Builder $query): void {
                $query->whereNull('premium_until')
                    ->orWhere('premium_until', '>', now());
            });

        return view('admin.dashboard', [
            ...$this->sharedData(),
            'stats' => [
                'users' => User::query()->count(),
                'vendors' => Vendor::query()->count(),
                'premiumVendors' => (clone $premiumVendorsQuery)->count(),
                'pendingApplications' => VendorApplication::query()->pending()->count(),
                'approvedApplications' => VendorApplication::query()
                    ->where('status', VendorApplicationStatus::Approved)
                    ->count(),
            ],
            'premiumVendors' => $premiumVendorsQuery
                ->with('user')
                ->withCount('products')
                ->with(['premiumPayments' => function (HasMany $query): void {
                    $query->where('status', VendorPremiumPaymentStatus::Paid)
                        ->latest('paid_at');
                }])
                ->latest('premium_started_at')
                ->get(),
            'recentApplications' => VendorApplication::query()
                ->with('applicant')
                ->latest()
                ->limit(6)
                ->get(),
            'recentUsers' => User::query()->latest()->limit(6)->get(),
        ]);
    }
}
