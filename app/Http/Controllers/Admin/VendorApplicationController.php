<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VendorApplicationStatus;
use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Controllers\Controller;
use App\Http\Requests\RejectVendorApplicationRequest;
use App\Mail\VendorApplicationApprovedMail;
use App\Mail\VendorApplicationRejectedMail;
use App\Models\VendorApplication;
use App\Services\VendorApplicationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class VendorApplicationController extends Controller
{
    use ProvidesStorefrontData;

    public function __construct(private readonly VendorApplicationService $applications) {}

    public function index(Request $request): View
    {
        $status = VendorApplicationStatus::tryFrom((string) $request->query('status', ''))
            ?? VendorApplicationStatus::Pending;

        $applications = VendorApplication::query()
            ->with(['user', 'reviewer'])
            ->where('status', $status)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.applications.index', [
            ...$this->sharedData(),
            'applications' => $applications,
            'status' => $status,
            'statuses' => VendorApplicationStatus::cases(),
        ]);
    }

    public function approve(Request $request, VendorApplication $vendorApplication): RedirectResponse
    {
        Gate::authorize('review', $vendorApplication);

        $vendor = $this->applications->approve($vendorApplication, $request->user());

        $vendorApplication->refresh()->load('user');

        Mail::to($vendorApplication->user)->queue(new VendorApplicationApprovedMail($vendorApplication, $vendor));

        return back()->with('status', "{$vendorApplication->store_name} is now a verified vendor.");
    }

    public function reject(RejectVendorApplicationRequest $request, VendorApplication $vendorApplication): RedirectResponse
    {
        $this->applications->reject(
            $vendorApplication,
            $request->user(),
            $request->validated('admin_note'),
        );

        $vendorApplication->refresh()->load('user');

        Mail::to($vendorApplication->user)->queue(new VendorApplicationRejectedMail($vendorApplication));

        return back()->with('status', "{$vendorApplication->store_name}'s application was rejected.");
    }
}
