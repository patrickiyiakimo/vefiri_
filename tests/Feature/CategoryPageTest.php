<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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

    public function test_sidebar_and_mega_menu_categories_link_to_category_pages(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();

        $sidebar = Str::between($response->getContent(), 'aria-label="Categories"', '</nav>');
        $megaMenu = Str::between($response->getContent(), 'data-dropdown-panel', 'View all categories');

        $this->assertStringContainsString(route('categories.show', 'mens-fashion'), $sidebar);
        $this->assertStringContainsString(route('categories.show', 'phones-accessories'), $sidebar);
        $this->assertStringContainsString(route('categories.show', 'beauty-health'), $sidebar);
        $this->assertStringContainsString(route('categories.show', 'tools'), $sidebar);
        $this->assertStringContainsString(route('categories.show', 'womens-fashion'), $megaMenu);
    }

    public function test_hero_and_top_category_links_point_to_category_pages(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();

        $heroCategories = Str::between($response->getContent(), 'aria-label="Product categories"', '</nav>');
        $topCategories = Str::between($response->getContent(), 'id="quick-categories-heading"', '</section>');

        $this->assertStringContainsString(route('categories.show', 'mens-fashion'), $heroCategories);
        $this->assertStringContainsString(route('categories.show', 'phones-accessories'), $heroCategories);
        $this->assertStringContainsString(route('categories.show', 'beauty-health'), $heroCategories);
        $this->assertStringContainsString(route('categories.show', 'womens-fashion'), $topCategories);
        $this->assertStringContainsString(route('categories.show', 'consumer-electronics'), $topCategories);
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
