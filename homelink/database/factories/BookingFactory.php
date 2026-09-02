<?php

namespace Database\Factories;

use App\Models\ServiceListing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        return ['customer_id' => User::factory(), 'service_listing_id' => ServiceListing::factory(), 'provider_id' => fn (array $attrs) => ServiceListing::findOrFail($attrs['service_listing_id'])->provider_id, 'service_title' => 'Careful home cleaning', 'quoted_price' => 4500, 'starts_at' => now()->addDay()->setTime(10, 0), 'address' => '24, Garden Road, Colombo', 'phone' => '0771234567', 'status' => 'pending'];
    }
}
