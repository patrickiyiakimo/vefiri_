<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Services\ProductCatalog;
use Illuminate\Contracts\View\View;

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
            ->sortBy(fn (array $candidate): int => $this->catalog->category($candidate['id']) === $category ? 0 : 1)
            ->take(18)
            ->values()
            ->all();

        return view('products.show', [
            ...$this->sharedData(),
            'product' => $item,
            'category' => $category,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
