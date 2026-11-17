<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class CategoryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_page_lists_only_products_in_the_selected_category(): void
    {
        $response = $this->get(route('categories.show', 'mens-fashion'));

        $response->assertOk()
            ->assertViewHas('category', "Men's Fashion")
            ->assertViewHas('products', fn (Collection $products): bool => $products->pluck('id')->sort()->values()->all() === ['men-fashion', 'watch'])
            ->assertSee(route('products.show', 'men-fashion'), false)
            ->assertSee(route('products.show', 'watch'), false)
            ->assertDontSee(route('products.show', 'headphones'), false);
    }

    public function test_unknown_category_returns_not_found(): void
    {
        $this->get(route('categories.show', 'not-a-category'))->assertNotFound();
    }

    public function test_search_input_links_to_category_pages(): void
    {
        $response = $this->get(route('search'));

        $response->assertOk()
            ->assertSee('data-search-category-select', false)
            ->assertSee('data-search-category-link', false)
            ->assertSee(route('categories.show', 'mens-fashion'), false)
            ->assertSee(route('categories.show', 'phones-accessories'), false)
            ->assertSee(route('categories.show', 'beauty-health'), false);
    }
}
