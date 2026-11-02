<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ProvidesStorefrontData;

    public function __invoke(Request $request): View
    {
        $vendor = $this->vendor($request);

        $products = $vendor->products();

        return view('vendor.dashboard', [
            ...$this->sharedData(),
            'vendor' => $vendor,
            'stats' => [
                'total' => (clone $products)->count(),
                'active' => (clone $products)->where('is_active', true)->count(),
                'outOfStock' => (clone $products)->where('stock', 0)->count(),
                'stock' => (int) (clone $products)->sum('stock'),
            ],
            'recentProducts' => $products->latest()->limit(5)->get(),
        ]);
    }

    private function vendor(Request $request): Vendor
    {
        return $request->user()->vendor()->firstOrFail();
    }
}
