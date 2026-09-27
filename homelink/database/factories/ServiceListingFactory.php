<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceListingFactory extends Factory
{
    public function definition(): array
    {
        return ['provider_id' => User::factory()->state(['role' => 'provider', 'phone' => '0771234567', 'city' => 'Colombo']), 'category_id' => Category::factory(), 'title' => 'Careful home cleaning', 'slug' => fake()->unique()->slug(3), 'description' => 'A full home cleaning service with supplies and careful attention to every room.', 'price' => 4500, 'city' => 'Colombo', 'is_active' => true];
    }
}
