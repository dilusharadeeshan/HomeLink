<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingEventFactory extends Factory
{
    public function definition(): array
    {
        return ['booking_id' => Booking::factory(), 'actor_id' => User::factory(), 'status' => 'pending', 'note' => 'Booking requested.'];
    }
}
