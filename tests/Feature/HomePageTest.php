<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_the_storefront_home_page_renders(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSeeText("Today's deals");
        $response->assertSeeText('More to love');
        $response->assertSeeText('Free shipping on eligible Choice items');
        $response->assertSeeText('United States');
        $response->assertSee('data-carousel-track', false);
        $response->assertSee('data-add-to-cart', false);
        $response->assertSee('data-cart-item-template', false);
    }
}
