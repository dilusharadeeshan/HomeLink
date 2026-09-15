<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return ['booking_id' => Booking::factory()->state(['status' => 'completed']), 'customer_id' => fn (array $attrs) => Booking::findOrFail($attrs['booking_id'])->customer_id, 'rating' => 5, 'comment' => 'A careful and helpful service from start to finish.'];
    }
}
