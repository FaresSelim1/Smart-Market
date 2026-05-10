<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code'           => strtoupper(fake()->unique()->bothify('PROMO-####')),
            'type'           => fake()->randomElement(['fixed', 'percent']),
            'value'          => fake()->randomFloat(2, 5, 50),
            'max_uses'       => fake()->numberBetween(10, 500),
            'used_count'     => 0,
            'min_cart_value'  => fake()->randomFloat(2, 0, 100),
            'is_active'      => true,
            'expires_at'     => fake()->dateTimeBetween('+1 week', '+6 months'),
        ];
    }

    /**
     * Create an expired coupon.
     */
    public function expired(): static
    {
        return $this->state(fn () => [
            'expires_at' => fake()->dateTimeBetween('-3 months', '-1 day'),
        ]);
    }

    /**
     * Create a fully used coupon.
     */
    public function exhausted(): static
    {
        return $this->state(fn () => [
            'max_uses'   => 1,
            'used_count' => 1,
        ]);
    }
}
