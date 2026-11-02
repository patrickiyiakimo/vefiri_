<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductPageTest extends TestCase
{
    public function test_product_details_page_renders_product_and_related_items(): void
    {
        $response = $this->get(route('products.show', 'headphones'));

        $response->assertOk();
        $response->assertSee('data-product-purchase', false);
        $response->assertSee('data-quantity-select', false);
        $response->assertSee('More products for sale');
        $response->assertSee('data-load-more', false);
        $response->assertSee('data-more-item', false);
        $response->assertSee('images/products/headphones.webp', false);
        $response->assertDontSee('.avif', false);
    }

    public function test_unknown_product_returns_not_found(): void
    {
        $this->get(route('products.show', 'missing-product'))->assertNotFound();
    }
}
