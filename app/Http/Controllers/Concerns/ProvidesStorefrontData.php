<?php

namespace App\Http\Controllers\Concerns;

trait ProvidesStorefrontData
{
    private function sharedData(): array
    {
        return [
            'menuCategories' => $this->menuCategories(),
            'navLinks' => $this->navLinks(),
            'searchCategories' => $this->searchCategories(),
            'searchSuggestions' => $this->searchSuggestions(),
            'footerColumns' => $this->footerColumns(),
            'paymentMethods' => ['Visa', 'Mastercard', 'Verve', 'Paystack', 'Flutterwave', 'Bank Transfer'],
        ];
    }

    /**
     * @return array<int, array{label: string, icon: string, children: array<int, string>}>
     */
    private function menuCategories(): array
    {
        return [
            [
                'label' => "Women's Fashion",
                'icon' => 'hanger',
                'children' => ['Dresses', 'Tops & Tees', 'Shoes', 'Bags', 'Jewelry', 'Activewear'],
            ],
            [
                'label' => "Men's Fashion",
                'icon' => 'shirt',
                'children' => ['T-Shirts', 'Shirts', 'Hoodies', 'Jeans', 'Sneakers', 'Watches'],
            ],
            [
                'label' => 'Phones & Accessories',
                'icon' => 'smartphone',
                'children' => ['Smartphones', 'Cases', 'Chargers', 'Earphones', 'Smart Watches', 'Holders'],
            ],
            [
                'label' => 'Consumer Electronics',
                'icon' => 'headphones',
                'children' => ['Headphones', 'Speakers', 'Cameras', 'Keyboards', 'Gaming', 'Drones'],
            ],
            [
                'label' => 'Home & Garden',
                'icon' => 'home',
                'children' => ['Furniture', 'Lighting', 'Kitchen', 'Decor', 'Garden', 'Storage'],
            ],
            [
                'label' => 'Beauty & Health',
                'icon' => 'sparkles',
                'children' => ['Skincare', 'Makeup', 'Hair Care', 'Fragrance', 'Tools', 'Wellness'],
            ],
            [
                'label' => 'Sports & Outdoors',
                'icon' => 'trophy',
                'children' => ['Sneakers', 'Fitness', 'Camping', 'Cycling', 'Swimming', 'Outdoor'],
            ],
            [
                'label' => 'Toys & Games',
                'icon' => 'puzzle',
                'children' => ['Action Figures', 'Learning', 'Board Games', 'Outdoor Play', 'Collectibles', 'Baby Toys'],
            ],
            [
                'label' => 'Tools & Home Improvement',
                'icon' => 'wrench',
                'children' => ['Power Tools', 'Hand Tools', 'Hardware', 'Lighting', 'Plumbing', 'Safety'],
            ],
            [
                'label' => 'Automobiles',
                'icon' => 'car',
                'children' => ['Car Care', 'Interior', 'Exterior', 'Electronics', 'Motorcycle', 'Accessories'],
            ],
        ];
    }

    /**
     * @return array<int, array{label: string, icon: string, accent: string|null}>
     */
    private function navLinks(): array
    {
        return [
            ['label' => 'Choice', 'icon' => 'badge-check', 'accent' => 'choice'],
            ['label' => 'SuperDeals', 'icon' => 'flame', 'accent' => 'sale'],
            ['label' => 'Bundle deals', 'icon' => 'gift', 'accent' => null],
            ['label' => 'Automotive', 'icon' => 'car', 'accent' => null],
            ['label' => 'Appliances', 'icon' => 'plug', 'accent' => null],
            ['label' => "Women's Clothing", 'icon' => 'hanger', 'accent' => null],
            ['label' => "Men's Clothing", 'icon' => 'shirt', 'accent' => null],
            ['label' => 'Toys & Games', 'icon' => 'puzzle', 'accent' => null],
            ['label' => 'Beauty & Health', 'icon' => 'sparkles', 'accent' => null],
            ['label' => 'Home & Living', 'icon' => 'home', 'accent' => null],
            ['label' => 'Tools', 'icon' => 'wrench', 'accent' => null],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function searchCategories(): array
    {
        return [
            'All categories',
            "Women's Fashion",
            "Men's Fashion",
            'Phones & Accessories',
            'Consumer Electronics',
            'Home & Garden',
            'Beauty & Health',
            'Sports & Outdoors',
            'Toys & Games',
            'Tools',
            'Automobiles',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function searchSuggestions(): array
    {
        return [
            'wireless earbuds',
            'phone case',
            'led strip lights',
            'summer dress',
            'smart watch',
            'car accessories',
            'kitchen gadgets',
            'makeup brushes',
        ];
    }

    /**
     * @return array<int, array{title: string, links: array<int, string>}>
     */
    private function footerColumns(): array
    {
        return [
            [
                'title' => 'Customer service',
                'links' => ['Help Center', 'Returns & refunds', 'Shipping information', 'Dispute resolution', 'Accessibility'],
            ],
            [
                'title' => 'Shopping with us',
                'links' => ['Making payments', 'Delivery options', 'Buyer protection', 'Track your order', 'Gift cards'],
            ],
            [
                'title' => 'Collaborate with us',
                'links' => ['Vefiri Business', 'Become a verified vendor', 'Affiliate program', 'Partnerships', 'Press office'],
            ],
            [
                'title' => 'Stay connected',
                'links' => ['Facebook', 'Instagram', 'X (Twitter)', 'YouTube', 'TikTok'],
            ],
        ];
    }
}
