<?php

namespace Database\Seeders;

use App\Models\{
    User, Category, Brand, Attribute, AttributeValue, Tag, Tax,
    Product, ProductImage, Slider, Banner, Page, Testimonial, Faq,
    ShippingZone, Setting, Coupon
};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Roles & Permissions ----
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        $permissions = [
            'manage-products', 'manage-categories', 'manage-brands', 'manage-orders',
            'manage-customers', 'manage-coupons', 'manage-pages', 'manage-blogs',
            'manage-sliders', 'manage-banners', 'manage-settings', 'manage-users',
            'manage-roles', 'manage-reviews', 'manage-campaigns', 'manage-menus',
            'view-reports', 'manage-media', 'view-activity-logs',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
        $adminRole->givePermissionTo(Permission::all());

        // ---- Admin User ----
        $admin = User::firstOrCreate(
            ['email' => 'modestik@gmail.com'],
            [
                'name' => 'Super Admin',
                'phone' => '01700000000',
                'password' => Hash::make('123456'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // ---- Customer Users ----
        $customers = [];
        $customerData = [
            ['name' => 'Rahim Ahmed', 'email' => 'rahim@example.com', 'phone' => '01711111111'],
            ['name' => 'Karim Khan', 'email' => 'karim@example.com', 'phone' => '01722222222'],
            ['name' => 'Fatima Begum', 'email' => 'fatima@example.com', 'phone' => '01733333333'],
            ['name' => 'Shahid Islam', 'email' => 'shahid@example.com', 'phone' => '01744444444'],
            ['name' => 'Nusrat Jahan', 'email' => 'nusrat@example.com', 'phone' => '01755555555'],
        ];
        foreach ($customerData as $cd) {
            $customer = User::firstOrCreate(
                ['email' => $cd['email']],
                array_merge($cd, [
                    'password' => Hash::make('password'),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ])
            );
            $customer->assignRole('customer');
            $customers[] = $customer;
        }

        // ---- Categories ----
        $categories = [
            ['name' => 'Mehendi Cones (মেহেদি কোণ)', 'image' => 'categories/organic-mehdi-items.jpg', 'icon' => '<i class="fas fa-magic"></i>', 'children' => ['Organic Cones', 'Instant Cones', 'Bridal Cones']],
            ['name' => 'Stencils (স্টেনসিল স্টিকার)', 'image' => 'categories/henna-stencil-sticker-imported.jpg', 'icon' => '<i class="fas fa-paint-brush"></i>', 'children' => ['Hand Stencils', 'Leg Stencils', 'Kids Stencils']],
            ['name' => 'Instant Henna (ইনস্ট্যান্ট মেহেদি)', 'image' => 'categories/instant-henna-sticker-imported.jpg', 'icon' => '<i class="fas fa-bolt"></i>', 'children' => ['Instant Red', 'Instant Maroon']],
            ['name' => 'Nail Henna (নেইল মেহেদি)', 'image' => 'categories/nail-products.jpg', 'icon' => '<i class="fas fa-hand-holding-heart"></i>', 'children' => ['Henna Powder', 'Nail Paste']],
            ['name' => 'Accessories (অনুষঙ্গ)', 'image' => 'categories/accessories.jpg', 'icon' => '<i class="fas fa-gem"></i>', 'children' => ['Sticker Oil', 'Henna Oil', 'Sticker Packs']],
        ];

        foreach ($categories as $index => $catData) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($catData['name'])],
                [
                    'name' => $catData['name'],
                    'icon' => $catData['icon'],
                    'image' => $catData['image'],
                    'is_featured' => true,
                    'status' => true,
                    'order' => $index,
                ]
            );
            foreach ($catData['children'] as $childIndex => $childName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($childName)],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childName,
                        'status' => true,
                        'order' => $childIndex,
                    ]
                );
            }
        }

        // ---- Brands ----
        $brandNames = ['Samsung', 'Apple', 'Xiaomi', 'Sony', 'Philips', 'LG', 'Nike', 'Adidas', 'Puma', 'Asus', 'Dell', 'HP'];
        foreach ($brandNames as $index => $brandName) {
            Brand::firstOrCreate(
                ['slug' => Str::slug($brandName)],
                [
                    'name' => $brandName,
                    'is_featured' => true,
                    'status' => true,
                    'order' => $index,
                ]
            );
        }

        // ---- Attributes ----
        $colorAttr = Attribute::firstOrCreate(['slug' => 'color'], ['name' => 'Color', 'type' => 'color', 'status' => true]);
        $colors = [
            ['value' => 'Red', 'color_code' => '#ef4444'],
            ['value' => 'Blue', 'color_code' => '#3b82f6'],
            ['value' => 'Green', 'color_code' => '#22c55e'],
            ['value' => 'Black', 'color_code' => '#000000'],
            ['value' => 'White', 'color_code' => '#ffffff'],
            ['value' => 'Pink', 'color_code' => '#ec4899'],
        ];
        foreach ($colors as $i => $c) {
            AttributeValue::firstOrCreate(['attribute_id' => $colorAttr->id, 'value' => $c['value']], array_merge($c, ['order' => $i]));
        }

        $sizeAttr = Attribute::firstOrCreate(['slug' => 'size'], ['name' => 'Size', 'type' => 'size', 'status' => true]);
        foreach (['S', 'M', 'L', 'XL', 'XXL'] as $i => $s) {
            AttributeValue::firstOrCreate(['attribute_id' => $sizeAttr->id, 'value' => $s], ['order' => $i]);
        }

        // ---- Tags ----
        $tagNames = ['new-arrival', 'best-seller', 'trending', 'limited-edition', 'eco-friendly', 'premium', 'budget-friendly'];
        foreach ($tagNames as $t) {
            Tag::firstOrCreate(['slug' => $t], ['name' => ucwords(str_replace('-', ' ', $t))]);
        }

        // ---- Taxes ----
        Tax::firstOrCreate(['name' => 'VAT'], ['rate' => 7.50, 'type' => 'percentage', 'status' => true]);

        // ---- Shipping Zones ----
        ShippingZone::firstOrCreate(['name' => 'Inside Dhaka'], ['charge' => 80, 'min_days' => 1, 'max_days' => 2, 'free_shipping_min' => 2000, 'status' => true]);
        ShippingZone::firstOrCreate(['name' => 'Outside Dhaka'], ['charge' => 130, 'min_days' => 2, 'max_days' => 5, 'free_shipping_min' => 3000, 'status' => true]);
        ShippingZone::firstOrCreate(['name' => 'Suburban Areas'], ['charge' => 150, 'min_days' => 3, 'max_days' => 7, 'free_shipping_min' => 5000, 'status' => true]);

        // ---- Products ----
        $allCategories = Category::whereNotNull('parent_id')->get();
        $brands = Brand::all();
        $tags = Tag::all();

        $productData = [
            ['name' => 'Organic Mehendi Cone', 'price' => 150, 'sale_price' => 110, 'cat' => 'Organic Cones', 'featured' => true, 'trending' => true, 'flash' => true],
            ['name' => 'Premium Bridal Mehendi Cone', 'price' => 200, 'sale_price' => 150, 'cat' => 'Bridal Cones', 'featured' => true, 'trending' => true],
            ['name' => 'Instant Henna Maroon Cone', 'price' => 160, 'sale_price' => 130, 'cat' => 'Instant Maroon', 'featured' => true, 'flash' => true],
            ['name' => 'Organic Nail Henna Powder', 'price' => 150, 'sale_price' => 120, 'cat' => 'Henna Powder', 'featured' => true, 'trending' => true],
            ['name' => 'Designer Mehendi Stencil Sticker', 'price' => 220, 'sale_price' => 150, 'cat' => 'Hand Stencils', 'featured' => true, 'trending' => true],
            ['name' => 'Instant Red Mehendi Cone', 'price' => 150, 'sale_price' => 130, 'cat' => 'Instant Red', 'featured' => false],
            ['name' => 'Premium Henna Oil for Dark Stain', 'price' => 180, 'sale_price' => 120, 'cat' => 'Henna Oil', 'featured' => true],
            ['name' => 'Bridal Stencil Sticker Pack', 'price' => 300, 'sale_price' => 250, 'cat' => 'Hand Stencils', 'featured' => true],
            ['name' => 'Leg Mehendi Stencil Sticker', 'price' => 250, 'sale_price' => 180, 'cat' => 'Leg Stencils', 'featured' => false],
            ['name' => 'Kids Mehendi Stencil Sticker', 'price' => 120, 'sale_price' => 80, 'cat' => 'Kids Stencils', 'featured' => false],
            ['name' => 'Nail Henna Paste Tube', 'price' => 180, 'sale_price' => 140, 'cat' => 'Nail Paste', 'featured' => true],
            ['name' => 'Sticker Application Oil', 'price' => 150, 'sale_price' => 100, 'cat' => 'Sticker Oil', 'featured' => false],
        ];

        foreach ($productData as $pd) {
            $category = $allCategories->where('name', $pd['cat'])->first();
            if (!$category) $category = $allCategories->random();

            $product = Product::firstOrCreate(
                ['slug' => Str::slug($pd['name'])],
                [
                    'category_id' => $category->id,
                    'brand_id' => $brands->random()->id,
                    'name' => $pd['name'],
                    'sku' => 'SKU-' . strtoupper(Str::random(8)),
                    'barcode' => rand(1000000000000, 9999999999999),
                    'short_description' => 'High quality ' . strtolower($pd['name']) . ' with premium materials and excellent durability.',
                    'description' => '<h3>Product Description</h3><p>This is a high-quality ' . strtolower($pd['name']) . ' designed for everyday use. Made with premium materials for maximum durability and comfort.</p><h4>Key Features</h4><ul><li>Premium quality materials</li><li>Durable and long-lasting</li><li>Ergonomic design</li><li>Easy to use</li><li>Great value for money</li></ul><h4>Specifications</h4><p>Material: Premium Grade<br>Weight: Lightweight<br>Warranty: 6 months</p>',
                    'price' => $pd['price'],
                    'sale_price' => $pd['sale_price'],
                    'quantity' => rand(10, 200),
                    'is_featured' => $pd['featured'] ?? false,
                    'is_trending' => $pd['trending'] ?? false,
                    'is_flash_sale' => $pd['flash'] ?? false,
                    'flash_sale_price' => isset($pd['flash']) ? round($pd['sale_price'] * 0.85, 2) : null,
                    'flash_sale_start' => isset($pd['flash']) ? now() : null,
                    'flash_sale_end' => isset($pd['flash']) ? now()->addDays(3) : null,
                    'status' => 'active',
                    'views' => rand(50, 5000),
                ]
            );

            // Attach random tags if not attached
            if ($product->tags()->count() === 0) {
                $product->tags()->attach($tags->random(rand(1, 3))->pluck('id'));
            }

            // Create placeholder image entry
            ProductImage::firstOrCreate(
                ['product_id' => $product->id, 'is_primary' => true],
                [
                    'image' => 'products/placeholder.png',
                    'alt' => $pd['name'],
                    'order' => 0,
                ]
            );
        }

        // ---- Sliders ----
        $sliderData = [
            ['title' => 'Summer Collection 2024', 'subtitle' => 'Up to 50% Off', 'description' => 'Discover trending products at unbeatable prices', 'button_text' => 'Shop Now', 'button_url' => '/products', 'image' => 'sliders/slider.png'],
            ['title' => 'New Arrivals', 'subtitle' => 'Fresh & Exciting', 'description' => 'Check out our latest products just added', 'button_text' => 'Explore', 'button_url' => '/products?sort=newest', 'image' => 'sliders/banner_0.png'],
            ['title' => 'Flash Sale', 'subtitle' => 'Limited Time Only', 'description' => 'Grab the best deals before they\'re gone', 'button_text' => 'View Deals', 'button_url' => '/flash-sale', 'image' => 'sliders/banner_1.png'],
        ];
        foreach ($sliderData as $i => $sd) {
            Slider::updateOrCreate(
                ['title' => $sd['title']],
                array_merge($sd, [
                    'order' => $i,
                    'status' => true,
                ])
            );
        }

        // ---- Banners ----
        Banner::firstOrCreate(['title' => 'Free Delivery'], ['image' => 'banners/placeholder.png', 'url' => '/products', 'position' => 'home', 'status' => true]);
        Banner::firstOrCreate(['title' => 'Special Offer'], ['image' => 'banners/placeholder.png', 'url' => '/flash-sale', 'position' => 'home', 'status' => true]);

        // ---- Testimonials ----
        $testimonials = [
            ['name' => 'Mohammad Rahman', 'designation' => 'Regular Customer', 'content' => 'Great shopping experience! Fast delivery and quality products. I have been shopping here for months and never disappointed.', 'rating' => 5],
            ['name' => 'Ayesha Khatun', 'designation' => 'Verified Buyer', 'content' => 'Love the variety of products. Customer service is excellent and returns are hassle-free. Highly recommended!', 'rating' => 5],
            ['name' => 'Imran Hossain', 'designation' => 'Tech Enthusiast', 'content' => 'Best prices for gadgets in Bangladesh. The delivery was on time and packaging was perfect. Will shop again!', 'rating' => 4],
            ['name' => 'Tahmina Islam', 'designation' => 'Fashion Lover', 'content' => 'Amazing collection and the quality matches the description perfectly. Very satisfied with my purchase!', 'rating' => 5],
        ];
        foreach ($testimonials as $i => $t) {
            Testimonial::firstOrCreate(['name' => $t['name']], array_merge($t, ['status' => true, 'order' => $i]));
        }

        // ---- Pages ----
        $pages = [
            ['title' => 'About Us', 'slug' => 'about-us', 'content' => '<h2>About EcommerceBD</h2><p>EcommerceBD is Bangladesh\'s premier online shopping destination. We bring you a curated selection of quality products at the best prices, with fast delivery across the country.</p><h3>Our Mission</h3><p>To make online shopping accessible, affordable, and enjoyable for everyone in Bangladesh.</p><h3>Our Vision</h3><p>To be the most trusted e-commerce platform in Bangladesh, known for quality products and exceptional customer service.</p>'],
            ['title' => 'Privacy Policy', 'slug' => 'privacy-policy', 'content' => '<h2>Privacy Policy</h2><p>Your privacy is important to us. This policy describes how we collect, use, and protect your personal information.</p><h3>Information We Collect</h3><p>We collect information you provide directly, such as name, email, phone number, and shipping address when you create an account or place an order.</p><h3>How We Use Your Information</h3><p>We use your information to process orders, communicate with you, and improve our services.</p>'],
            ['title' => 'Terms & Conditions', 'slug' => 'terms-conditions', 'content' => '<h2>Terms & Conditions</h2><p>By using EcommerceBD, you agree to these terms and conditions.</p><h3>Account</h3><p>You must provide accurate information when creating an account.</p><h3>Orders</h3><p>All orders are subject to availability and confirmation of the order price.</p>'],
            ['title' => 'Return Policy', 'slug' => 'return-policy', 'content' => '<h2>Return Policy</h2><p>We want you to be completely satisfied with your purchase.</p><h3>Returns</h3><p>You can return most items within 7 days of delivery for a full refund.</p><h3>Conditions</h3><p>Items must be unused, in original packaging, and in the same condition you received them.</p>'],
        ];
        foreach ($pages as $p) {
            Page::firstOrCreate(['slug' => $p['slug']], array_merge($p, ['status' => true]));
        }

        // ---- FAQs ----
        $faqs = [
            ['question' => 'How do I place an order?', 'answer' => 'Browse products, add to cart, proceed to checkout, fill in shipping details and confirm your order.', 'category' => 'ordering'],
            ['question' => 'What payment methods do you accept?', 'answer' => 'We accept Cash on Delivery (COD), bKash, Nagad, and card payments.', 'category' => 'payment'],
            ['question' => 'How long does delivery take?', 'answer' => 'Inside Dhaka: 1-2 days. Outside Dhaka: 2-5 days. Suburban areas: 3-7 days.', 'category' => 'delivery'],
            ['question' => 'Can I return a product?', 'answer' => 'Yes, you can return most products within 7 days of delivery. Please check our return policy for details.', 'category' => 'returns'],
            ['question' => 'How do I track my order?', 'answer' => 'Go to Track Order page and enter your order number and phone number to see the status.', 'category' => 'ordering'],
        ];
        foreach ($faqs as $i => $f) {
            Faq::firstOrCreate(['question' => $f['question']], array_merge($f, ['status' => true, 'order' => $i]));
        }

        // ---- Coupons ----
        Coupon::firstOrCreate(
            ['code' => 'WELCOME10'],
            [
                'type' => 'percentage', 'value' => 10,
                'min_order' => 500, 'max_discount' => 200, 'usage_limit' => 100,
                'starts_at' => now(), 'expires_at' => now()->addMonths(3), 'status' => true,
            ]
        );
        Coupon::firstOrCreate(
            ['code' => 'FLAT100'],
            [
                'type' => 'fixed', 'value' => 100,
                'min_order' => 1000, 'usage_limit' => 50,
                'starts_at' => now(), 'expires_at' => now()->addMonths(1), 'status' => true,
            ]
        );

        // ---- Settings ----
        $settings = [
            ['key' => 'site_name', 'value' => 'Modestik', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Premium Organic Mehendi & Modest Beauty', 'group' => 'general'],
            ['key' => 'site_email', 'value' => 'admin@modestikbd.com', 'group' => 'general'],
            ['key' => 'site_phone', 'value' => '+8801810536303', 'group' => 'general'],
            ['key' => 'site_address', 'value' => 'Dhaka, Bangladesh', 'group' => 'general'],
            ['key' => 'currency', 'value' => 'BDT', 'group' => 'general'],
            ['key' => 'currency_symbol', 'value' => '৳', 'group' => 'general'],
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/ModestikBD', 'group' => 'social'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/modestikbd', 'group' => 'social'],
            ['key' => 'youtube_url', 'value' => 'https://youtube.com/ModestikBD', 'group' => 'social'],
            ['key' => 'meta_pixel_id', 'value' => '', 'group' => 'marketing'],
            ['key' => 'google_analytics_id', 'value' => '', 'group' => 'marketing'],
            ['key' => 'google_tag_manager_id', 'value' => '', 'group' => 'marketing'],
        ];
        foreach ($settings as $s) {
            Setting::firstOrCreate(['key' => $s['key']], $s);
        }

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('   Admin: modestik@gmail.com / 123456');
    }
}
