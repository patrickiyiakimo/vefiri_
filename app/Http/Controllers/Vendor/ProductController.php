<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorProductRequest;
use App\Http\Requests\UpdateVendorProductRequest;
use App\Models\Product;
use App\Models\Vendor;
use App\Services\CloudinaryProductImages;
use App\Services\ProductCatalog;
use App\Services\VendorProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProductController extends Controller
{
    use ProvidesStorefrontData;

    public function __construct(
        private readonly ProductCatalog $catalog,
        private readonly CloudinaryProductImages $images,
    ) {}

    public function index(Request $request): View
    {
        $vendor = $this->vendor($request);

        return view('vendor.products.index', [
            ...$this->sharedData(),
            'vendor' => $vendor,
            'products' => $vendor->products()->latest()->paginate(15),
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $vendor = $this->vendor($request);

        if ($vendor->hasReachedProductLimit()) {
            return $this->redirectToPremium();
        }

        return view('vendor.products.create', [
            ...$this->sharedData(),
            'vendor' => $vendor,
            'categories' => $this->catalog->categories(),
        ]);
    }

    public function store(StoreVendorProductRequest $request, VendorProductService $products): RedirectResponse
    {
        $vendor = $this->vendor($request);

        if ($vendor->hasReachedProductLimit()) {
            return $this->redirectToPremium();
        }

        $data = $request->validated();
        $imagePath = (string) $request->attributes->get('product_image_public_id');

        unset($data['image_token'], $data['image_version'], $data['image_signature']);

        try {
            $product = $products->create($vendor, [
                ...$data,
                'slug' => $this->uniqueSlug($data['name']),
                'image_path' => $imagePath,
                'is_active' => $request->boolean('is_active'),
            ]);
        } catch (Throwable $exception) {
            $this->images->delete($imagePath);

            if ($exception instanceof ValidationException && array_key_exists('limit', $exception->errors())) {
                return $this->redirectToPremium();
            }

            throw $exception;
        }

        return redirect()
            ->route('vendor.products.index')
            ->with('toast', "{$product->name} has been added to your storefront.");
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
        $oldImagePath = $product->image_path;
        $newImagePath = $request->attributes->get('product_image_public_id');

        unset($data['image_token'], $data['image_version'], $data['image_signature']);

        if (is_string($newImagePath) && $newImagePath !== '') {
            $data['image_path'] = $newImagePath;
        }

        try {
            $product->update([
                ...$data,
                'is_active' => $request->boolean('is_active'),
            ]);
        } catch (Throwable $exception) {
            $this->images->delete(is_string($newImagePath) ? $newImagePath : null);

            throw $exception;
        }

        if (is_string($newImagePath) && $newImagePath !== '' && $oldImagePath !== $newImagePath) {
            $this->images->delete($oldImagePath);
        }

        return redirect()
            ->route('vendor.products.index')
            ->with('toast', "{$product->name} has been updated.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        Gate::authorize('delete', $product);

        $imagePath = $product->image_path;

        $product->delete();

        $this->images->delete($imagePath);

        return redirect()
            ->route('vendor.products.index')
            ->with('toast', 'Product removed from your storefront.');
    }

    private function redirectToPremium(): RedirectResponse
    {
        return redirect()
            ->route('vendor.premium.show')
            ->with([
                'status' => 'You have reached your product limit. Buy more product slots to keep uploading products.',
                'limit_reached' => true,
            ]);
    }

    private function vendor(Request $request): Vendor
    {
        return $request->user()->vendor()->firstOrFail();
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
