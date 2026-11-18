<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Controllers\Controller;
use App\Models\ProductPhotoInquiry;
use Illuminate\Contracts\View\View;

class ProductPhotoInquiryController extends Controller
{
    use ProvidesStorefrontData;

    public function index(): View
    {
        return view('admin.product-photo-inquiries.index', [
            ...$this->sharedData(),
            'inquiries' => ProductPhotoInquiry::query()
                ->with('customer')
                ->latest()
                ->paginate(20),
        ]);
    }
}
