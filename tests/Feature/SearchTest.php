<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_can_be_searched_by_keyword(): void
    {
        $response = $this->get(route('search', ['q' => 'headphones']));

        $response->assertOk();
        $response->assertSeeText('Results for');
        $response->assertSeeText('Earphone Clip Wireless Bluetooth Headphone');
        $response->assertDontSeeText('RGB LED Strip Lights');
    }

    public function test_search_results_can_be_filtered_by_category(): void
    {
        $response = $this->get(route('search', ['category' => 'Beauty & Health']));

        $response->assertOk();
        $response->assertSeeText('Hydrating Skincare Set');
        $response->assertSeeText('in Beauty & Health');
        $response->assertDontSeeText('Earphone Clip Wireless Bluetooth Headphone');
    }

    public function test_search_shows_an_empty_state_when_nothing_matches(): void
    {
        $response = $this->get(route('search', ['q' => 'zzzz-no-product']));

        $response->assertOk();
        $response->assertSeeText('No products found');
        $response->assertSeeText('Browse all products');
    }
}
