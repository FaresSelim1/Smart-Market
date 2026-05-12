<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $brands = ['Apple', 'Samsung', 'Sony', 'Dell', 'HP', 'Lenovo', 'Asus', 'Logitech', 'Bose', 'Sennheiser', 'Netgear', 'TP-Link'];
        $techAdjectives = ['Pro', 'Elite', 'Ultra', 'Smart', 'Wireless', 'Noise-Cancelling', 'High-Speed', 'Next-Gen'];
        $productNames = ['Station', 'Hub', 'Pad', 'Link', 'Gate', 'Core', 'Flow', 'X-1', 'Z-Prime'];

        $name = fake()->randomElement($brands) . ' ' . fake()->randomElement($techAdjectives) . ' ' . fake()->randomElement($productNames);

        return [
            'category_id' => Category::factory(),
            'name'        => $name,
            'description' => fake()->sentences(3, true),
            'price'       => fake()->randomFloat(2, 49, 2999),
            'sku'         => strtoupper(fake()->unique()->bothify('??-####-??')),
        ];
    }
}
