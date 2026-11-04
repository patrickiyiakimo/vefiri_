<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Services\AccountModerator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    use ProvidesStorefrontData;

    public function __construct(private readonly AccountModerator $accounts) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $vendors = Vendor::query()
            ->with('user')
            ->whereHas('user')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('store_name', 'like', "%{$search}%")
                        ->orWhere('business_name', 'like', "%{$search}%")
                        ->orWhere('business_email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.vendors.index', [
            ...$this->sharedData(),
            'vendors' => $vendors,
            'search' => $search,
        ]);
    }

    public function suspend(Request $request, Vendor $vendor): RedirectResponse
    {
        $this->accounts->suspend($this->owner($vendor), $request->user());

        return back()->with('status', "{$vendor->store_name}'s account has been suspended.");
    }

    public function reactivate(Request $request, Vendor $vendor): RedirectResponse
    {
        $this->accounts->reactivate($this->owner($vendor), $request->user());

        return back()->with('status', "{$vendor->store_name}'s account has been reactivated.");
    }

    public function destroy(Request $request, Vendor $vendor): RedirectResponse
    {
        $this->accounts->delete($this->owner($vendor), $request->user());

        return back()->with('status', "{$vendor->store_name}'s account has been deleted.");
    }

    private function owner(Vendor $vendor): User
    {
        $owner = $vendor->user;

        abort_if($owner === null, 404);

        return $owner;
    }
}
