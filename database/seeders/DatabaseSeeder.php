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
        // 1. Create Admin & Customer
        User::updateOrCreate(
            ['email' => 'admin@market.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@market.com'],
            [
                'name'     => 'Test Customer',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Create Branches
        $branch1 = Branch::firstOrCreate(
            ['slug' => 'cairo-main'],
            ['name' => 'Cairo Main', 'location' => 'Downtown']
        );
        $branch2 = Branch::firstOrCreate(
            ['slug' => 'alex-port'],
            ['name' => 'Alexandria Port', 'location' => 'Corniche']
        );

        // 3. Create Categories
        $electronics = Category::firstOrCreate(
            ['slug' => 'electronics'],
            ['name' => 'Electronics']
        );
        $accessories = Category::firstOrCreate(
            ['slug' => 'accessories'],
            ['name' => 'Accessories']
        );
        $security = Category::firstOrCreate(
            ['slug' => 'security'],
            ['name' => 'Security Equipment']
        );

        // 4. Clear existing product data
        \Schema::disableForeignKeyConstraints();
        \DB::table('product_images')->delete();
        \DB::table('branch_product')->delete();
        \DB::table('flash_sales')->delete();
        \DB::table('order_items')->delete(); // Clear order items too
        Product::query()->delete();
        \Schema::enableForeignKeyConstraints();

        // 5. Create Products and attach to branches
        $laptop = Product::create([
            'sku'         => 'HW-MB-001',
            'category_id' => $electronics->id,
            'name'        => 'MateBook B3 Pro',
            'description' => 'High performance workstation laptop with 4K display and titanium finish.',
            'price'       => 1499.00,
        ]);
        $laptop->images()->create(['path' => 'products/laptop.png', 'sort_order' => 0]);
        $laptop->branches()->sync([
            $branch1->id => ['stock_level' => 50, 'low_stock_threshold' => 10],
            $branch2->id => ['stock_level' => 5, 'low_stock_threshold' => 10],
        ]);

        $camera = Product::create([
            'sku'         => 'SEC-CAM-001',
            'category_id' => $security->id,
            'name'        => 'Onyx Smart Guard',
            'description' => '4K Night-vision AI-powered security camera with two-way audio.',
            'price'       => 349.99,
        ]);
        $camera->images()->create(['path' => 'products/camera.png', 'sort_order' => 0]);
        $camera->branches()->sync([
            $branch1->id => ['stock_level' => 100, 'low_stock_threshold' => 20],
            $branch2->id => ['stock_level' => 75, 'low_stock_threshold' => 15],
        ]);

        $headphones = Product::create([
            'sku'         => 'ACC-HP-001',
            'category_id' => $accessories->id,
            'name'        => 'AeroTune Pro',
            'description' => 'Studio-grade noise canceling headphones with 40-hour battery life.',
            'price'       => 249.00,
        ]);
        $headphones->images()->create(['path' => 'products/headphones.png', 'sort_order' => 0]);
        $headphones->branches()->sync([
            $branch1->id => ['stock_level' => 30, 'low_stock_threshold' => 5],
            $branch2->id => ['stock_level' => 8, 'low_stock_threshold' => 5],
        ]);

        // 6. Create Coupons (matching the enum: 'fixed' or 'percent')
        Coupon::updateOrCreate(
            ['code' => 'STORE2026'],
            [
                'type'           => 'percent',
                'value'          => 10,
                'max_uses'       => 100,
                'used_count'     => 0,
                'min_cart_value'  => 100.00,
                'is_active'      => true,
                'expires_at'     => Carbon::now()->addMonth(),
            ]
        );

        // 7. Create Flash Sale
        \App\Models\FlashSale::updateOrCreate(
            ['product_id' => $laptop->id],
            [
                'discount_price' => 1199.99,
                'starts_at'      => Carbon::now()->subDay(),
                'ends_at'        => Carbon::now()->addDays(2),
                'is_active'      => true,
            ]
        );
    }
}