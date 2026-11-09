<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VendorApplicationStatus;
use App\Enums\VendorPremiumPaymentStatus;
use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DashboardController extends Controller
{
    use ProvidesStorefrontData;

    public function __invoke(): View
    {
        $slotVendorsQuery = Vendor::query()->where('product_slots', '>', 0);

        return view('admin.dashboard', [
            ...$this->sharedData(),
            'stats' => [
                'users' => User::query()->count(),
                'vendors' => Vendor::query()->count(),
                'slotVendors' => (clone $slotVendorsQuery)->count(),
                'pendingApplications' => VendorApplication::query()->pending()->count(),
                'approvedApplications' => VendorApplication::query()
                    ->where('status', VendorApplicationStatus::Approved)
                    ->count(),
            ],
            'slotVendors' => $slotVendorsQuery
                ->with('user')
                ->withCount('products')
                ->with(['premiumPayments' => function (HasMany $query): void {
                    $query->where('status', VendorPremiumPaymentStatus::Paid)
                        ->latest('paid_at');
                }])
                ->latest('updated_at')
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
