<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Services\ProductCatalog;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    use ProvidesStorefrontData;

    public function __construct(private readonly ProductCatalog $catalog) {}

    public function show(string $category): View
    {
        $categoryName = $this->catalog->categoryBySlug($category);

        abort_if($categoryName === null, 404);

        $products = collect($this->catalog->all())
            ->filter(fn (array $product): bool => $this->catalog->category($product['id']) === $categoryName)
            ->values();

        return view('categories.show', [
            ...$this->sharedData(),
            'category' => $categoryName,
            'products' => $products,
            'selectedCategory' => $categoryName,
        ]);
    }
}
