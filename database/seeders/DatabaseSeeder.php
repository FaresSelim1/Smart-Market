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

        // 4. Create Products and attach to branches
        $laptop = Product::firstOrCreate(
            ['sku' => 'HW-MB-001'],
            [
                'category_id' => $electronics->id,
                'name'        => 'MateBook B3',
                'description' => 'High performance laptop for professionals',
                'price'       => 1200.00,
            ]
        );
        $laptop->branches()->syncWithoutDetaching([
            $branch1->id => ['stock_level' => 50, 'low_stock_threshold' => 10],
            $branch2->id => ['stock_level' => 5, 'low_stock_threshold' => 10],
        ]);

        $camera = Product::firstOrCreate(
            ['sku' => 'SEC-CAM-001'],
            [
                'category_id' => $security->id,
                'name'        => 'HD Security Camera',
                'description' => '1080p weatherproof security camera with night vision',
                'price'       => 299.99,
            ]
        );
        $camera->branches()->syncWithoutDetaching([
            $branch1->id => ['stock_level' => 100, 'low_stock_threshold' => 20],
            $branch2->id => ['stock_level' => 75, 'low_stock_threshold' => 15],
        ]);

        $headphones = Product::firstOrCreate(
            ['sku' => 'ACC-HP-001'],
            [
                'category_id' => $accessories->id,
                'name'        => 'Wireless Headphones Pro',
                'description' => 'Noise-canceling wireless headphones with 30hr battery',
                'price'       => 189.00,
            ]
        );
        $headphones->branches()->syncWithoutDetaching([
            $branch1->id => ['stock_level' => 30, 'low_stock_threshold' => 5],
            $branch2->id => ['stock_level' => 8, 'low_stock_threshold' => 5],
        ]);

        // 5. Create Coupons (matching the enum: 'fixed' or 'percent')
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

        Coupon::updateOrCreate(
            ['code' => 'FLAT20'],
            [
                'type'           => 'fixed',
                'value'          => 20,
                'max_uses'       => 50,
                'used_count'     => 0,
                'min_cart_value'  => 50.00,
                'is_active'      => true,
                'expires_at'     => Carbon::now()->addMonths(3),
            ]
        );

        // 6. Create Flash Sale
        \App\Models\FlashSale::updateOrCreate(
            ['product_id' => $laptop->id],
            [
                'discount_price' => 899.99,
                'starts_at'      => Carbon::now()->subDay(),
                'ends_at'        => Carbon::now()->addDays(2),
                'is_active'      => true,
            ]
        );
    }
}