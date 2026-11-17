<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Services\ProductCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use ProvidesStorefrontData;

    public function __construct(private readonly ProductCatalog $catalog) {}

    public function show(string $product): View
    {
        $item = $this->catalog->find($product);

        abort_if($item === null, 404);

        $category = $this->catalog->category($product);
        $item['category'] = $category;

        $relatedProducts = collect($this->catalog->all())
            ->except($product)
            ->filter(fn (array $candidate): bool => $this->catalog->category($candidate['id']) === $category)
            ->take(18)
            ->values()
            ->all();

        return view('products.show', [
            ...$this->sharedData(),
            'product' => $item,
            'category' => $category,
            'categoryUrl' => route('categories.show', Str::slug($category)),
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
