<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Coupon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Clear existing data first to avoid stale IDs
        \Schema::disableForeignKeyConstraints();
        \Illuminate\Support\Facades\Cache::flush();
        \DB::table('product_images')->truncate();
        \DB::table('branch_product')->truncate();
        \DB::table('flash_sales')->truncate();
        \DB::table('order_items')->truncate();
        \DB::table('cart_items')->truncate();
        Product::truncate();
        Category::truncate();
        \Schema::enableForeignKeyConstraints();

        // 1. Create Admin & Customer
        User::updateOrCreate(
            ['email' => 'admin@market.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@market.com'],
            [
                'name'     => 'Test Customer',
                'password' => Hash::make('password'),
                'role'     => 'user',
            ]
        );

        // 2. Create Branches
        $branches = collect([
            Branch::firstOrCreate(['slug' => 'cairo-main'], ['name' => 'Cairo Main', 'location' => 'Downtown']),
            Branch::firstOrCreate(['slug' => 'alex-port'], ['name' => 'Alexandria Port', 'location' => 'Corniche']),
        ]);

        // 3. Create Categories
        $categoryNames = [
            'Laptops', 'Smartphones', 'Accessories', 'Gaming', 
            'Monitors', 'Audio Devices', 'Smart Home', 'Networking Devices',
            'Security Equipment'
        ];
        $categories = collect($categoryNames)->map(fn($name) => 
            Category::firstOrCreate(['name' => $name], ['slug' => \Illuminate\Support\Str::slug($name)])
        );

        // 5. Seed 100+ Products using Factory
        Product::factory(110)
            ->recycle($categories)
            ->create()
            ->each(function ($product) use ($branches) {
                // Attach to branches with random stock
                foreach ($branches as $branch) {
                    $product->branches()->attach($branch->id, [
                        'stock_level' => rand(5, 100),
                        'low_stock_threshold' => 10,
                    ]);
                }

                // Determine image based on category
                $imageMapping = [
                    'Laptops'            => 'products/laptop_premium.png',
                    'Smartphones'        => 'products/smartphone_premium.png',
                    'Audio Devices'      => 'products/headphones.png',
                    'Security Equipment' => 'products/camera.png',
                    'Accessories'        => 'products/headphones.png',
                    'Gaming'             => 'products/laptop_premium.png',
                    'Monitors'           => 'products/laptop_premium.png',
                    'Smart Home'         => 'products/camera.png',
                    'Networking Devices' => 'products/laptop_premium.png',
                ];

                $categoryName = $product->category->name;
                $imagePath = $imageMapping[$categoryName] ?? 'products/laptop.png';

                // Add primary image
                $product->images()->create([
                    'path' => $imagePath,
                    'sort_order' => 0
                ]);
            });

        // 6. Create some Coupons
        Coupon::firstOrCreate(
            ['code' => 'WELCOME20'],
            [
                'type'           => 'percent',
                'value'          => 20,
                'max_uses'       => 500,
                'used_count'     => 0,
                'min_cart_value'  => 50.00,
                'is_active'      => true,
                'expires_at'     => now()->addMonths(6),
            ]
        );

        // 7. Create some Flash Sales for variety
        Product::inRandomOrder()->take(15)->get()->each(function ($product) {
            $product->flashSales()->create([
                'discount_price' => $product->price * 0.75,
                'starts_at'      => now()->subDays(2),
                'ends_at'        => now()->addDays(5),
                'is_active'      => true,
            ]);
        });
    }
}