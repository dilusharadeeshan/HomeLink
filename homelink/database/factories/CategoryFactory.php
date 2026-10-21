<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => 'Cleaning', 'slug' => fake()->unique()->slug(2), 'icon' => 'sparkles', 'color' => 'teal'];
    }
}
