<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VendorApplicationStatus;
use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorApplication;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    use ProvidesStorefrontData;

    public function __invoke(): View
    {
        return view('admin.dashboard', [
            ...$this->sharedData(),
            'stats' => [
                'users' => User::query()->count(),
                'vendors' => Vendor::query()->count(),
                'pendingApplications' => VendorApplication::query()->pending()->count(),
                'approvedApplications' => VendorApplication::query()
                    ->where('status', VendorApplicationStatus::Approved)
                    ->count(),
            ],
            'recentApplications' => VendorApplication::query()
                ->with('user')
                ->latest()
                ->limit(6)
                ->get(),
            'recentUsers' => User::query()->latest()->limit(6)->get(),
        ]);
    }
}
