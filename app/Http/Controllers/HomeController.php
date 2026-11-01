<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $products = $this->products();

        return view('home', [
            'menuCategories' => $this->menuCategories(),
            'navLinks' => $this->navLinks(),
            'searchCategories' => $this->searchCategories(),
            'searchSuggestions' => $this->searchSuggestions(),
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
                ['value' => '5M+', 'label' => 'Factory-direct products'],
                ['value' => '20M+', 'label' => 'Items ready to ship'],
                ['value' => '10', 'label' => 'Local warehouses'],
                ['value' => '24H', 'label' => 'Sourcing support'],
            ],
            'footerColumns' => $this->footerColumns(),
            'paymentMethods' => ['Visa', 'Mastercard', 'PayPal', 'Apple Pay', 'Google Pay', 'Klarna'],
        ]);
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
     * @param  array<string, array<string, mixed>>  $products
     * @return array<int, array<string, mixed>>
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
            ['label' => 'Electronics', 'icon' => 'headphones', 'image' => 'images/products/headphones.avif'],
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
                'title' => 'Get $3 off your first order',
                'description' => 'Use code NEW3 at checkout. Minimum spend $15.',
                'cta' => 'Claim coupon',
                'theme' => 'from-[#fff1e8] via-[#ffe4d1] to-[#ffd0ad]',
                'icon' => 'gift',
            ],
            [
                'eyebrow' => 'AliExpress Choice',
                'title' => 'Free shipping, every day',
                'description' => 'Top-rated products with free delivery and easy returns.',
                'cta' => 'Shop Choice',
                'theme' => 'from-[#e9fbf5] via-[#d2f7e9] to-[#a9eed8]',
                'icon' => 'truck',
            ],
            [
                'eyebrow' => 'AliExpress Business',
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

    /**
     * @return array<string, array<string, mixed>>
     */
    private function products(): array
    {
        return [
            'face-massager' => [
                'id' => 'face-massager',
                'title' => '1-5pcs Silicone Face Lifting Massager V-Line Muscle Trainer Double Chin Reducer Facial Yoga Tool',
                'image' => 'images/products/face-massager.avif',
                'price' => 1.94,
                'old_price' => 6.39,
                'discount' => 70,
                'rating' => 4.5,
                'sold' => '3,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'Beauty Care Store',
                'badge' => 'Best seller',
            ],
            'wrench-set' => [
                'id' => 'wrench-set',
                'title' => '16-in-1 Socket Wrench Set, Non-Slip Rotating Head, Durable Stainless Steel for Home & Car Repairs',
                'image' => 'images/products/wrench-set.avif',
                'price' => 10.38,
                'old_price' => 21.79,
                'discount' => 52,
                'rating' => 4.6,
                'sold' => '10,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'ToolMaster Official Store',
                'badge' => 'Top rated',
            ],
            'card-sticker' => [
                'id' => 'card-sticker',
                'title' => 'Funny Credit Card Sticker 2D Flat Skin PVC Waterproof Debit Card Cover Decal Bank Card Protector',
                'image' => 'images/products/card-sticker.avif',
                'price' => 1.94,
                'old_price' => 5.64,
                'discount' => 66,
                'rating' => 4.6,
                'sold' => '3,000+',
                'choice' => false,
                'free_shipping' => true,
                'store' => 'Sticker Lab',
                'badge' => 'Trending',
            ],
            'slippers' => [
                'id' => 'slippers',
                'title' => 'Arch Support Anti-Slip Soft Sole Slippers, Thick Breathable Summer Hole Shoes for Men and Women',
                'image' => 'images/products/slippers.avif',
                'price' => 9.17,
                'old_price' => 18.70,
                'discount' => 51,
                'rating' => 4.5,
                'sold' => '5,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'Comfort Step Store',
                'badge' => null,
            ],
            'fanny-pack' => [
                'id' => 'fanny-pack',
                'title' => "Men's Waterproof Large Capacity Fanny Pack, Adjustable Strap for Travel, Running and Hiking",
                'image' => 'images/products/fanny-pack.avif',
                'price' => 1.52,
                'old_price' => 10.45,
                'discount' => 85,
                'rating' => 4.4,
                'sold' => '5,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'Outdoor Gear Store',
                'badge' => 'Crazy deal',
            ],
            'headphones' => [
                'id' => 'headphones',
                'title' => 'Earphone Clip Wireless Bluetooth Headphone Bone Conduction Headset TWS Sports with Noise Canceling',
                'image' => 'images/products/headphones.avif',
                'price' => 4.13,
                'old_price' => 9.38,
                'discount' => 55,
                'rating' => 4.7,
                'sold' => '10,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'SoundPeak Official Store',
                'badge' => 'Best seller',
            ],
            'car-vacuum' => [
                'id' => 'car-vacuum',
                'title' => '9000Pa Wireless Car Vacuum Cleaner, USB Charging Portable Wet and Dry Mini Vacuum for Home',
                'image' => 'images/products/car-vacuum.avif',
                'price' => 6.74,
                'old_price' => 27.05,
                'discount' => 75,
                'rating' => 4.8,
                'sold' => '20,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'CleanTech Store',
                'badge' => 'Best seller',
            ],
            'gan-charger' => [
                'id' => 'gan-charger',
                'title' => '65W GaN USB C Charger, 2-Port Fast Charging Power Adapter for iPhone, Samsung and Xiaomi',
                'image' => 'images/products/gan-charger.avif',
                'price' => 1.52,
                'old_price' => 5.98,
                'discount' => 74,
                'rating' => 4.6,
                'sold' => '10,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'VoltEdge Store',
                'badge' => 'Hot',
            ],
            'led-strip' => [
                'id' => 'led-strip',
                'title' => 'RGB LED Strip Lights 5m-30m with App Control, Flexible Ribbon Tape for Bedroom Decoration',
                'image' => 'images/products/led-strip.avif',
                'price' => 1.52,
                'old_price' => 4.15,
                'discount' => 63,
                'rating' => 4.6,
                'sold' => '50,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'Glow Home Store',
                'badge' => 'Best seller',
            ],
            'watch' => [
                'id' => 'watch',
                'title' => 'Classic Minimalist Quartz Watch, Stainless Steel Mesh Strap Waterproof Wristwatch for Men and Women',
                'image' => 'images/products/watch.jpg',
                'price' => 17.99,
                'old_price' => 45.00,
                'discount' => 60,
                'rating' => 4.6,
                'sold' => '8,000+',
                'choice' => false,
                'free_shipping' => true,
                'store' => 'Timeless Watch Store',
                'badge' => null,
            ],
            'speaker' => [
                'id' => 'speaker',
                'title' => 'Portable Bluetooth Speaker with Rich Bass, IPX7 Waterproof, 24-Hour Playtime for Outdoor',
                'image' => 'images/products/speaker.jpg',
                'price' => 9.99,
                'old_price' => 29.99,
                'discount' => 67,
                'rating' => 4.7,
                'sold' => '6,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'SoundPeak Official Store',
                'badge' => 'Top rated',
            ],
            'keyboard' => [
                'id' => 'keyboard',
                'title' => 'Mechanical Gaming Keyboard, Hot-Swappable RGB Backlight, Compact 68-Key Layout for PC and Mac',
                'image' => 'images/products/keyboard.jpg',
                'price' => 14.50,
                'old_price' => 38.99,
                'discount' => 63,
                'rating' => 4.8,
                'sold' => '4,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'KeyForge Store',
                'badge' => 'Top rated',
            ],
            'sneakers' => [
                'id' => 'sneakers',
                'title' => 'Lightweight Breathable Running Sneakers, Non-Slip Cushioned Sports Shoes for Men and Women',
                'image' => 'images/products/sneakers.jpg',
                'price' => 19.99,
                'old_price' => 49.99,
                'discount' => 60,
                'rating' => 4.7,
                'sold' => '12,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'Motion Sports Store',
                'badge' => 'Best seller',
            ],
            'skincare' => [
                'id' => 'skincare',
                'title' => 'Hydrating Skincare Set with Hyaluronic Acid, Gentle Cleanser, Serum and Moisturizer for Daily Care',
                'image' => 'images/products/skincare.jpg',
                'price' => 12.40,
                'old_price' => 34.90,
                'discount' => 64,
                'rating' => 4.8,
                'sold' => '9,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'Glow Ritual Store',
                'badge' => 'Top rated',
            ],
            'tools' => [
                'id' => 'tools',
                'title' => 'Cordless Drill and Home Tool Kit, 108-Piece Hand Tool Set with Storage Case for Repairs',
                'image' => 'images/products/tools.jpg',
                'price' => 24.99,
                'old_price' => 59.99,
                'discount' => 58,
                'rating' => 4.7,
                'sold' => '7,000+',
                'choice' => false,
                'free_shipping' => true,
                'store' => 'ToolMaster Official Store',
                'badge' => null,
            ],
            'chair' => [
                'id' => 'chair',
                'title' => 'Modern Ergonomic Accent Chair with Solid Wood Legs, Soft Upholstered Lounge Chair for Living Room',
                'image' => 'images/products/chair.jpg',
                'price' => 89.00,
                'old_price' => 199.00,
                'discount' => 55,
                'rating' => 4.5,
                'sold' => '1,000+',
                'choice' => false,
                'free_shipping' => false,
                'store' => 'Nordic Home Store',
                'badge' => null,
            ],
            'pets' => [
                'id' => 'pets',
                'title' => 'Cozy Pet Bed for Small and Medium Dogs, Washable Calming Donut Cushion with Anti-Slip Base',
                'image' => 'images/products/pets.jpg',
                'price' => 8.75,
                'old_price' => 24.99,
                'discount' => 65,
                'rating' => 4.7,
                'sold' => '6,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'Happy Paws Store',
                'badge' => 'Top rated',
            ],
            'smartphone' => [
                'id' => 'smartphone',
                'title' => '5G Unlocked Smartphone with 6.7-inch AMOLED Display, 256GB Storage and 50MP Triple Camera',
                'image' => 'images/products/smartphone.jpg',
                'price' => 89.99,
                'old_price' => 159.99,
                'discount' => 44,
                'rating' => 4.6,
                'sold' => '3,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'Nova Mobile Store',
                'badge' => 'Hot',
            ],
            'laptop' => [
                'id' => 'laptop',
                'title' => '14-inch Ultra-Slim Laptop, 16GB RAM, 512GB SSD, Full HD IPS Display for Work and Study',
                'image' => 'images/products/laptop.jpg',
                'price' => 249.00,
                'old_price' => 399.00,
                'discount' => 38,
                'rating' => 4.5,
                'sold' => '2,000+',
                'choice' => false,
                'free_shipping' => true,
                'store' => 'TechNova Store',
                'badge' => null,
            ],
            'drone' => [
                'id' => 'drone',
                'title' => 'Foldable Camera Drone with 4K HD Video, GPS Return Home, Follow Me and 30-Minute Flight Time',
                'image' => 'images/products/drone.jpg',
                'price' => 39.99,
                'old_price' => 79.99,
                'discount' => 50,
                'rating' => 4.4,
                'sold' => '2,000+',
                'choice' => false,
                'free_shipping' => true,
                'store' => 'SkyView Store',
                'badge' => null,
            ],
            'camera' => [
                'id' => 'camera',
                'title' => 'Mirrorless Digital Camera with 24MP Sensor, 4K Video, Vlog Kit and 16-50mm Zoom Lens',
                'image' => 'images/products/camera.jpg',
                'price' => 59.99,
                'old_price' => 119.99,
                'discount' => 50,
                'rating' => 4.8,
                'sold' => '1,500+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'LensCraft Store',
                'badge' => 'Top rated',
            ],
            'bag' => [
                'id' => 'bag',
                'title' => 'Minimalist Leather Crossbody Bag, Adjustable Strap with Multiple Compartments for Everyday Use',
                'image' => 'images/products/bag.jpg',
                'price' => 16.80,
                'old_price' => 42.00,
                'discount' => 60,
                'rating' => 4.6,
                'sold' => '5,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'Urban Carry Store',
                'badge' => null,
            ],
            'perfume' => [
                'id' => 'perfume',
                'title' => 'Long-Lasting Floral Eau de Parfum, Fresh Citrus and Jasmine Notes, 50ml Unisex Fragrance',
                'image' => 'images/products/perfume.jpg',
                'price' => 11.20,
                'old_price' => 39.00,
                'discount' => 71,
                'rating' => 4.5,
                'sold' => '4,000+',
                'choice' => false,
                'free_shipping' => true,
                'store' => 'Essence House',
                'badge' => null,
            ],
            'sunglasses' => [
                'id' => 'sunglasses',
                'title' => 'Retro Square Sunglasses with UV400 Protection, Lightweight Frame for Men and Women',
                'image' => 'images/products/sunglasses.jpg',
                'price' => 3.90,
                'old_price' => 12.99,
                'discount' => 70,
                'rating' => 4.4,
                'sold' => '8,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'Sunny Side Store',
                'badge' => null,
            ],
            'heels' => [
                'id' => 'heels',
                'title' => 'Elegant Pointed Toe High Heels, Comfortable Block Heel Pumps for Office and Evening Wear',
                'image' => 'images/products/heels.jpg',
                'price' => 22.50,
                'old_price' => 59.99,
                'discount' => 62,
                'rating' => 4.5,
                'sold' => '2,000+',
                'choice' => false,
                'free_shipping' => true,
                'store' => 'Elegance Steps Store',
                'badge' => null,
            ],
            'toys' => [
                'id' => 'toys',
                'title' => 'Building Block Set with 500 Pieces, Creative STEM Toy for Kids Ages 6 and Up',
                'image' => 'images/products/toys.jpg',
                'price' => 14.90,
                'old_price' => 36.00,
                'discount' => 59,
                'rating' => 4.8,
                'sold' => '3,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'Bright Minds Store',
                'badge' => 'Top rated',
            ],
            'jewelry' => [
                'id' => 'jewelry',
                'title' => 'Sterling Silver Moissanite Ring, Brilliant Cut Promise Ring with Gift Box for Women',
                'image' => 'images/products/jewelry.jpg',
                'price' => 18.60,
                'old_price' => 49.00,
                'discount' => 62,
                'rating' => 4.7,
                'sold' => '4,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'Luna Fine Jewelry',
                'badge' => null,
            ],
            'gaming' => [
                'id' => 'gaming',
                'title' => 'Wireless Gaming Controller with Hall Effect Joysticks, RGB Lighting and Charging Dock',
                'image' => 'images/products/gaming.jpg',
                'price' => 21.99,
                'old_price' => 54.99,
                'discount' => 60,
                'rating' => 4.7,
                'sold' => '6,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'PlayForge Store',
                'badge' => 'Best seller',
            ],
            'baby' => [
                'id' => 'baby',
                'title' => 'Soft Cotton Baby Clothing Set, 5-Piece Newborn Gift Box with Hat, Bodysuit and Mittens',
                'image' => 'images/products/baby.jpg',
                'price' => 7.80,
                'old_price' => 19.90,
                'discount' => 61,
                'rating' => 4.8,
                'sold' => '3,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'Little Sprout Store',
                'badge' => null,
            ],
            'garden' => [
                'id' => 'garden',
                'title' => 'Indoor Herb Garden Kit with LED Grow Light, Self-Watering Planter and Seed Starter Pods',
                'image' => 'images/products/garden.jpg',
                'price' => 23.40,
                'old_price' => 58.00,
                'discount' => 60,
                'rating' => 4.5,
                'sold' => '1,000+',
                'choice' => false,
                'free_shipping' => true,
                'store' => 'Green Space Store',
                'badge' => null,
            ],
            'auto' => [
                'id' => 'auto',
                'title' => 'Car Interior Cleaning Kit, Detailing Brush Set with Microfiber Towels and Storage Bag',
                'image' => 'images/products/auto.jpg',
                'price' => 6.50,
                'old_price' => 18.00,
                'discount' => 64,
                'rating' => 4.6,
                'sold' => '9,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'AutoCare Store',
                'badge' => null,
            ],
            'women-fashion' => [
                'id' => 'women-fashion',
                'title' => 'Floral Print Summer Midi Dress, Flowy A-Line Silhouette with Adjustable Straps',
                'image' => 'images/products/women-fashion.jpg',
                'price' => 13.90,
                'old_price' => 34.00,
                'discount' => 59,
                'rating' => 4.5,
                'sold' => '5,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'Bloom Fashion Store',
                'badge' => null,
            ],
            'men-fashion' => [
                'id' => 'men-fashion',
                'title' => 'Premium Cotton Crew Neck T-Shirt, Breathable Relaxed Fit Tee for Everyday Wear',
                'image' => 'images/products/men-fashion.jpg',
                'price' => 5.90,
                'old_price' => 15.00,
                'discount' => 61,
                'rating' => 4.6,
                'sold' => '10,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'Basics Studio',
                'badge' => null,
            ],
            'appliances' => [
                'id' => 'appliances',
                'title' => 'Compact Electric Food Processor, 8-Cup Multifunctional Blender for Kitchen Prep',
                'image' => 'images/products/appliances.jpg',
                'price' => 34.90,
                'old_price' => 79.00,
                'discount' => 56,
                'rating' => 4.5,
                'sold' => '2,000+',
                'choice' => false,
                'free_shipping' => true,
                'store' => 'KitchenPro Store',
                'badge' => null,
            ],
            'cosmetics' => [
                'id' => 'cosmetics',
                'title' => 'Professional Makeup Brush Set, 12-Piece Vegan Brushes with Travel Case',
                'image' => 'images/products/cosmetics.jpg',
                'price' => 8.90,
                'old_price' => 24.00,
                'discount' => 63,
                'rating' => 4.7,
                'sold' => '7,000+',
                'choice' => true,
                'free_shipping' => true,
                'store' => 'Glow Ritual Store',
                'badge' => 'Best seller',
            ],
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
                'links' => ['AliExpress Business', 'Seller center', 'Affiliate program', 'Partnerships', 'Press office'],
            ],
            [
                'title' => 'Stay connected',
                'links' => ['Facebook', 'Instagram', 'X (Twitter)', 'YouTube', 'TikTok'],
            ],
        ];
    }
}
