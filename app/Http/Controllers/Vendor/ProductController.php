<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorProductRequest;
use App\Http\Requests\UpdateVendorProductRequest;
use App\Models\Product;
use App\Models\Vendor;
use App\Services\ProductCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use ProvidesStorefrontData;

    public function __construct(private readonly ProductCatalog $catalog) {}

    public function index(Request $request): View
    {
        $vendor = $this->vendor($request);

        return view('vendor.products.index', [
            ...$this->sharedData(),
            'vendor' => $vendor,
            'products' => $vendor->products()->latest()->paginate(15),
        ]);
    }

    public function create(Request $request): View
    {
        return view('vendor.products.create', [
            ...$this->sharedData(),
            'vendor' => $this->vendor($request),
            'categories' => $this->catalog->categories(),
        ]);
    }

    public function store(StoreVendorProductRequest $request): RedirectResponse
    {
        $vendor = $this->vendor($request);
        $data = $request->validated();
        $image = $request->file('image');

        unset($data['image']);

        $product = $vendor->products()->create([
            ...$data,
            'slug' => $this->uniqueSlug($data['name']),
            'image_path' => $image->storePublicly('vendor-products', $this->imageDisk()),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('vendor.products.index')
            ->with('status', "{$product->name} has been added to your storefront.");
    }

    public function edit(Request $request, Product $product): View
    {
        Gate::authorize('update', $product);

        return view('vendor.products.edit', [
            ...$this->sharedData(),
            'vendor' => $this->vendor($request),
            'product' => $product,
            'categories' => $this->catalog->categories(),
        ]);
    }

    public function update(UpdateVendorProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $image = $request->file('image');

        unset($data['image']);

        if ($image !== null) {
            Storage::disk($this->imageDisk())->delete($product->image_path);

            $data['image_path'] = $image->storePublicly('vendor-products', $this->imageDisk());
        }

        $product->update([
            ...$data,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('vendor.products.index')
            ->with('status', "{$product->name} has been updated.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        Gate::authorize('delete', $product);

        Storage::disk($this->imageDisk())->delete($product->image_path);
        $product->delete();

        return redirect()
            ->route('vendor.products.index')
            ->with('status', 'Product removed from your storefront.');
    }

    private function vendor(Request $request): Vendor
    {
        return $request->user()->vendor()->firstOrFail();
    }

    private function imageDisk(): string
    {
        return (string) config('filesystems.product_images_disk', 'public');
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        $suffix = 2;

        while (Product::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
