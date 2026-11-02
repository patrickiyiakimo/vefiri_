<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    use ProvidesStorefrontData;

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $vendors = Vendor::query()
            ->with('user')
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
}
