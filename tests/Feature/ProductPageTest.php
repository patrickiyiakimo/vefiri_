<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_details_page_renders_product_and_related_items(): void
    {
        $response = $this->get(route('products.show', 'headphones'));

        $response->assertOk();
        $response->assertSee('data-product-purchase', false);
        $response->assertSee('data-quantity-select', false);
        $response->assertSee('More products for sale');
        $response->assertSee('data-more-item', false);
        $response->assertSee('images/products/headphones.webp', false);
        $response->assertDontSee('data-load-more-controls', false);
        $response->assertDontSee('.avif', false);
    }

    public function test_related_products_only_include_products_from_the_same_category(): void
    {
        $response = $this->get(route('products.show', 'men-fashion'));

        $response->assertOk()
            ->assertViewHas('category', "Men's Fashion")
            ->assertViewHas('relatedProducts', fn (array $relatedProducts): bool => collect($relatedProducts)->pluck('id')->all() === ['watch'])
            ->assertSee("Browse all Men's Fashion");
    }

    public function test_related_products_section_is_hidden_when_no_other_products_share_the_category(): void
    {
        $response = $this->get(route('products.show', 'sneakers'));

        $response->assertOk()
            ->assertViewHas('relatedProducts', []);

        $response->assertDontSee('More products for sale');
    }

    public function test_unknown_product_returns_not_found(): void
    {
        $this->get(route('products.show', 'missing-product'))->assertNotFound();
    }
}
