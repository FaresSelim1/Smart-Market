<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'branch_id'        => Branch::factory(),
            'order_number'     => 'ORD-' . strtoupper(uniqid()),
            'total_amount'     => fake()->randomFloat(2, 50, 5000),
            'discount'         => 0,
            'status'           => 'pending',
            'payment_status'   => 'unpaid',
            'shipping_address' => fake()->address(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'payment_status' => 'paid',
            'status'         => 'processing',
        ]);
    }
}
