<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Requests\StoreVendorApplicationRequest;
use App\Models\Vendor;
use App\Services\ProductCatalog;
use App\Services\VendorApplicationService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    use ProvidesStorefrontData;

    public function __construct(
        private readonly VendorApplicationService $applications,
        private readonly ProductCatalog $catalog,
    ) {}

    public function index(): View
    {
        return view('vendors.index', [
            ...$this->sharedData(),
            'vendors' => Vendor::query()
                ->with('user')
                ->whereHas('user', fn (Builder $query) => $query->notSuspended())
                ->latest()
                ->paginate(12),
        ]);
    }

    public function apply(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->vendor()->exists()) {
            return redirect()->route('vendors.status');
        }

        $application = $user->vendorApplications()->latest()->first();

        if ($application?->isPending()) {
            return redirect()->route('vendors.status');
        }

        return view('vendors.apply', [
            ...$this->sharedData(),
            'application' => $application,
            'categories' => $this->catalog->categories(),
        ]);
    }

    public function store(StoreVendorApplicationRequest $request): RedirectResponse
    {
        $this->applications->apply($request->user(), $request->validated());

        return redirect()
            ->route('vendors.status')
            ->with('status', 'Your vendor application has been submitted. We will review it shortly.');
    }

    public function status(Request $request): View
    {
        $user = $request->user();

        return view('vendors.status', [
            ...$this->sharedData(),
            'application' => $user->vendorApplications()->with('reviewer')->latest()->first(),
            'vendor' => $user->vendor,
        ]);
    }
}
