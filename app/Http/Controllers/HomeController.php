<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Services\ProductCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use ProvidesStorefrontData;

    public function __construct(private readonly ProductCatalog $catalog) {}

    public function __invoke(): View
    {
        $products = $this->catalog->all();

        return view('home', [
            ...$this->sharedData(),
            'heroSlides' => $this->heroSlides($products),
            'quickCategories' => $this->quickCategories(),
            'dealTabs' => $this->dealTabs($products),
            'superDeals' => $this->selectProducts($products, [
                'car-vacuum',
                'gan-charger',
                'headphones',
                'led-strip',
                'watch',
                'sneakers',
                'skincare',
                'tools',
                'chair',
                'pets',
            ]),
            'recommendationTabs' => $this->recommendationTabs($products),
            'promoBanners' => $this->promoBanners(),
            'businessStats' => [
                ['value' => '3K+', 'label' => 'Factory-direct products'],
                ['value' => '2K+', 'label' => 'Items ready to ship'],
                ['value' => '20', 'label' => 'Local warehouses'],
                ['value' => '24H', 'label' => 'Sourcing support'],
            ],
        ]);
    }

    public function search(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $selectedCategory = trim((string) $request->query('category', ''));
        $terms = preg_split('/\s+/', mb_strtolower($query), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $products = collect($this->catalog->all())
            ->map(fn (array $product): array => [
                ...$product,
                'category' => $this->catalog->category($product['id']),
            ])
            ->filter(function (array $product) use ($terms, $selectedCategory): bool {
                $categoryMatches = $selectedCategory === ''
                    || $selectedCategory === 'All categories'
                    || $product['category'] === $selectedCategory;

                if (! $categoryMatches) {
                    return false;
                }

                if ($terms === []) {
                    return true;
                }

                $haystack = mb_strtolower(implode(' ', [
                    $product['title'],
                    $product['store'],
                    $product['category'],
                    $product['id'],
                ]));

                foreach ($terms as $term) {
                    if (! str_contains($haystack, $term)) {
                        return false;
                    }
                }

                return true;
            })
            ->values();

        return view('search', [
            ...$this->sharedData(),
            'products' => $products,
            'query' => $query,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function heroSlides(array $products): array
    {
        $slides = [
            [
                'eyebrow' => 'The biggest sale of the year',
                'title' => '11.11 Big Sale',
                'description' => 'Up to 70% off top picks, plus free shipping on all Choice items.',
                'cta' => 'Shop the sale',
                'code' => 'Code: BIG11',
                'theme' => 'from-[#ff1744] via-[#ff4d24] to-[#ff9f00]',
                'accent' => 'bg-[#2b1200] text-white',
                'product_keys' => ['smartphone', 'headphones', 'watch'],
            ],
            [
                'eyebrow' => 'Top-rated, always',
                'title' => 'Choice Day',
                'description' => 'Millions of quality finds with free shipping and buyer protection.',
                'cta' => 'Discover Choice',
                'code' => 'Free shipping',
                'theme' => 'from-[#003c2f] via-[#006f54] to-[#35c99b]',
                'accent' => 'bg-white text-[#005c45]',
                'product_keys' => ['skincare', 'sneakers', 'chair'],
            ],
            [
                'eyebrow' => 'New deals every day',
                'title' => 'Super Deals',
                'description' => 'Save up to 80% on trending tech, home and lifestyle picks.',
                'cta' => 'Shop SuperDeals',
                'code' => 'While stocks last',
                'theme' => 'from-[#24114d] via-[#5b21b6] to-[#db2777]',
                'accent' => 'bg-[#ffd60a] text-[#2c1358]',
                'product_keys' => ['camera', 'led-strip', 'bag'],
            ],
        ];

        foreach ($slides as $index => $slide) {
            $slides[$index]['products'] = $this->selectProducts($products, $slide['product_keys']);
            unset($slides[$index]['product_keys']);
        }

        return $slides;
    }

    /**
     * @return array<int, array{label: string, icon: string, image: string}>
     */
    private function quickCategories(): array
    {
        return [
            ['label' => 'Women', 'icon' => 'hanger', 'image' => 'images/products/women-fashion.jpg'],
            ['label' => 'Men', 'icon' => 'shirt', 'image' => 'images/products/men-fashion.jpg'],
            ['label' => 'Phones', 'icon' => 'smartphone', 'image' => 'images/products/smartphone.jpg'],
            ['label' => 'Electronics', 'icon' => 'headphones', 'image' => 'images/products/headphones.webp'],
            ['label' => 'Home', 'icon' => 'home', 'image' => 'images/products/chair.jpg'],
            ['label' => 'Beauty', 'icon' => 'sparkles', 'image' => 'images/products/cosmetics.jpg'],
            ['label' => 'Sports', 'icon' => 'trophy', 'image' => 'images/products/sneakers.jpg'],
            ['label' => 'Toys', 'icon' => 'puzzle', 'image' => 'images/products/toys.jpg'],
            ['label' => 'Pets', 'icon' => 'paw', 'image' => 'images/products/pets.jpg'],
            ['label' => 'Watches', 'icon' => 'watch', 'image' => 'images/products/watch.jpg'],
            ['label' => 'Bags', 'icon' => 'bag', 'image' => 'images/products/bag.jpg'],
            ['label' => 'Tools', 'icon' => 'wrench', 'image' => 'images/products/tools.jpg'],
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $products
     * @return array<int, array{id: string, label: string, products: array<int, array<string, mixed>>}>
     */
    private function dealTabs(array $products): array
    {
        return [
            [
                'id' => 'bundle',
                'label' => 'Bundle deals',
                'products' => $this->selectProducts($products, [
                    'face-massager',
                    'wrench-set',
                    'card-sticker',
                    'slippers',
                    'fanny-pack',
                    'headphones',
                ]),
            ],
            [
                'id' => 'daily',
                'label' => 'SuperDeals',
                'products' => $this->selectProducts($products, [
                    'car-vacuum',
                    'gan-charger',
                    'led-strip',
                    'watch',
                    'speaker',
                    'keyboard',
                ]),
            ],
            [
                'id' => 'big-save',
                'label' => 'Big Save',
                'products' => $this->selectProducts($products, [
                    'smartphone',
                    'laptop',
                    'drone',
                    'camera',
                    'sneakers',
                    'skincare',
                ]),
            ],
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $products
     * @return array<int, array{id: string, label: string, products: array<int, array<string, mixed>>}>
     */
    private function recommendationTabs(array $products): array
    {
        return [
            [
                'id' => 'recommended',
                'label' => 'Recommended',
                'products' => $this->selectProducts($products, [
                    'bag',
                    'perfume',
                    'sunglasses',
                    'heels',
                    'chair',
                    'toys',
                    'pets',
                    'jewelry',
                    'gaming',
                    'baby',
                    'garden',
                    'auto',
                    'women-fashion',
                    'men-fashion',
                    'appliances',
                    'cosmetics',
                    'laptop',
                    'speaker',
                ]),
            ],
            [
                'id' => 'new',
                'label' => 'New arrivals',
                'products' => $this->selectProducts($products, [
                    'women-fashion',
                    'men-fashion',
                    'appliances',
                    'cosmetics',
                    'laptop',
                    'speaker',
                    'keyboard',
                    'smartphone',
                    'camera',
                    'drone',
                    'headphones',
                    'watch',
                    'led-strip',
                    'sneakers',
                    'skincare',
                    'tools',
                    'chair',
                    'bag',
                ]),
            ],
            [
                'id' => 'best',
                'label' => 'Best sellers',
                'products' => $this->selectProducts($products, [
                    'headphones',
                    'car-vacuum',
                    'gan-charger',
                    'led-strip',
                    'sneakers',
                    'skincare',
                    'smartphone',
                    'watch',
                    'bag',
                    'tools',
                    'perfume',
                    'keyboard',
                    'gaming',
                    'camera',
                    'speaker',
                    'toys',
                    'jewelry',
                    'cosmetics',
                ]),
            ],
        ];
    }

    /**
     * @return array<int, array{eyebrow: string, title: string, description: string, cta: string, theme: string, icon: string}>
     */
    private function promoBanners(): array
    {
        return [
            [
                'eyebrow' => 'New here?',
                'title' => 'Get â‚¦5,000 off your first order',
                'description' => 'Use code NEW5 at checkout. Minimum spend â‚¦25,000.',
                'cta' => 'Claim coupon',
                'theme' => 'from-[#fff1e8] via-[#ffe4d1] to-[#ffd0ad]',
                'icon' => 'gift',
            ],
            [
                'eyebrow' => 'Vefiri Choice',
                'title' => 'Free shipping, every day',
                'description' => 'Top-rated products with free delivery and easy returns.',
                'cta' => 'Shop Choice',
                'theme' => 'from-[#e9fbf5] via-[#d2f7e9] to-[#a9eed8]',
                'icon' => 'truck',
            ],
            [
                'eyebrow' => 'Vefiri Business',
                'title' => 'Buy more, save more',
                'description' => 'Volume pricing, dedicated support and tax exemptions.',
                'cta' => 'Explore business',
                'theme' => 'from-[#eef2ff] via-[#dbe4ff] to-[#c7d2fe]',
                'icon' => 'briefcase',
            ],
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $products
     * @param  array<int, string>  $keys
     * @return array<int, array<string, mixed>>
     */
    private function selectProducts(array $products, array $keys): array
    {
        return array_values(array_intersect_key($products, array_flip($keys)));
    }
}
