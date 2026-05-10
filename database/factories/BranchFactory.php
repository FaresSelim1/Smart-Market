<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'name'      => fake()->city() . ' Branch',
            'location'  => fake()->address(),
            'slug'      => fake()->unique()->slug(2),
            'is_active' => true,
        ];
    }
}
