<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Category;
use App\Models\ServiceListing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('Demo seeding is allowed only in local or testing environments.');
        }
        if (User::where('email', 'admin@homelink.test')->exists()) {
            $this->command?->info('Demo data already exists; nothing changed.');

            return;
        }
        DB::transaction(function (): void {
            $password = Hash::make('HomeLink@123');
            $admin = User::create(['name' => 'HomeLink Admin', 'email' => 'admin@homelink.test', 'password' => $password, 'role' => 'admin', 'phone' => '0770000001', 'city' => 'Colombo']);
            $customer = User::create(['name' => 'Amaya Fernando', 'email' => 'customer@homelink.test', 'password' => $password, 'role' => 'customer', 'phone' => '0771234567', 'city' => 'Colombo']);
            $reviewers = collect([$customer]);
            foreach (['Dinuka Silva', 'Tharushi Jayasekara', 'Kavindu Fernando'] as $i => $name) {
                $reviewers->push(User::create(['name' => $name, 'email' => 'customer'.($i + 2).'@homelink.test', 'password' => $password, 'role' => 'customer', 'phone' => '077100000'.($i + 1), 'city' => 'Colombo']));
            }
            $categories = [];
            foreach ([['Cleaning', 'cleaning', 'sparkles', 'teal'], ['Plumbing', 'plumbing', 'droplets', 'blue'], ['Electrical', 'electrical', 'zap', 'amber'], ['Painting', 'painting', 'paint-roller', 'purple'], ['Gardening', 'gardening', 'sprout', 'green'], ['AC services', 'air-conditioning', 'air-vent', 'cyan']] as [$name,$slug,$icon,$color]) {
                $categories[$slug] = Category::create(compact('name', 'slug', 'icon', 'color'));
            }
            $providers = [];
            foreach ([
                ['Nimal Perera', 'provider@homelink.test', 'Colombo', 'Electrical repairs and careful home maintenance. I explain the work before I begin and keep your space tidy.'],
                ['Sewwandi Silva', 'cleaning@homelink.test', 'Colombo', 'Thoughtful cleaning for busy homes. My team focuses on the little details that make a home feel fresh again.'],
                ['Ravindu Jayasinghe', 'plumbing@homelink.test', 'Colombo', 'Practical plumbing help for leaks, taps, and everyday water problems. Clear explanations and tidy work.'],
                ['Kasun Wickramasinghe', 'painting@homelink.test', 'Galle', 'Interior painting and touch-ups with careful preparation, clean edges, and a finish that feels right at home.'],
                ['Tharanga De Silva', 'gardening@homelink.test', 'Matara', 'Garden care for greener corners, from pruning and planting to a seasonal tidy-up.'],
                ['Dinesh Kumara', 'ac@homelink.test', 'Colombo', 'Air-conditioner servicing, filter cleaning, and cooling-system inspections for a more comfortable home.'],
            ] as $i => [$name,$email,$city,$bio]) {
                $providers[$i] = User::create(['name' => $name, 'email' => $email, 'password' => $password, 'role' => 'provider', 'phone' => '077200000'.($i + 1), 'city' => $city, 'bio' => $bio, 'created_at' => now()->subMonths(3)]);
            }
            $services = [
                [1, 'cleaning', 'A fresh start for your home', 4500, 'Colombo', 'A thorough clean for living spaces, bedrooms, bathrooms, and the kitchen. We dust surfaces, vacuum and mop floors, and leave the everyday spaces feeling cared for. Please let us know your home size and any priorities when booking.', 'Starting price for a small home. Cleaning supplies included.'],
                [2, 'plumbing', 'Little leaks, expertly fixed', 2500, 'Colombo', 'Help with dripping taps, leaking pipe joints, and simple sink blockages. We inspect the problem, explain the repair, and test the water flow before we leave. Replacement parts are agreed with you separately.', 'Inspection and basic labour included. Parts priced separately.'],
                [0, 'electrical', 'Safe connections. Peace of mind.', 3000, 'Colombo', 'Troubleshooting for faulty switches, sockets, lights, and small household electrical issues. Includes an initial inspection and safe basic repairs. Please describe the fault and keep the affected area accessible.', 'Basic inspection and labour. Replacement parts cost extra.'],
                [3, 'painting', 'A new mood for your walls', 6500, 'Galle', 'Bring a room back to life with careful surface preparation and interior paint touch-ups. We cover furniture, protect floors, and help you plan the finish. Large rooms or full repainting may require a separate quote.', 'Starting labour price. Paint and larger repairs quoted separately.'],
                [4, 'gardening', 'Make room for a little green', 3500, 'Matara', 'A garden refresh that covers light pruning, weeding, sweeping, and basic plant care. Ideal for small front gardens, terraces, and green corners. Share photos or describe your garden when arranging the work.', 'Small garden visit. Plants and waste hauling are additional.'],
                [5, 'air-conditioning', 'A cooler, more comfortable home', 4000, 'Colombo', 'Routine air-conditioner cleaning and performance checks. Includes filter cleaning, indoor-unit inspection, and airflow checks. Refrigerant, repairs, and replacement parts are quoted before any additional work.', 'Price for one domestic wall-mounted AC unit.'],
                [1, 'cleaning', 'A kitchen that feels like new', 5500, 'Colombo', 'A focused kitchen refresh: clean worktops, reachable cabinets, splashbacks, sinks, and exterior appliance surfaces. Tell us about grease buildup or priority areas so we can plan the visit.', 'Interior oven cleaning and specialist treatments quoted separately.'],
                [0, 'electrical', 'A brighter corner of your home', 2800, 'Colombo', 'Installation and replacement of standard indoor light fittings in accessible locations. We check the connection and make sure everything works safely. Provide your chosen fixture and tell us about the ceiling height.', 'Basic installation labour for one standard light fitting.'],
                [2, 'plumbing', 'Bathroom care, down to the details', 3200, 'Colombo', 'A practical bathroom plumbing check, including taps, visible pipework, and straightforward drainage issues. We explain any repairs needed and agree the scope before making changes.', 'Initial inspection and small adjustments. Materials cost extra.'],
            ];
            foreach ($services as $index => [$pi,$category,$title,$price,$city,$description,$pricingNote]) {
                $listing = ServiceListing::create(['provider_id' => $providers[$pi]->id, 'category_id' => $categories[$category]->id, 'title' => $title, 'slug' => Str::slug($title), 'price' => $price, 'city' => $city, 'description' => $description, 'pricing_note' => $pricingNote]);
                for ($r = 0; $r < 3; $r++) {
                    $reviewer = $reviewers[($index + $r) % 4];
                    $start = now()->subDays(30 - $index * 2 - $r)->setTime(8 + $r * 2, 0);
                    $booking = Booking::create(['customer_id' => $reviewer->id, 'provider_id' => $listing->provider_id, 'service_listing_id' => $listing->id, 'service_title' => $listing->title, 'quoted_price' => $listing->price, 'starts_at' => $start, 'address' => '24, Sample Garden Road, '.$city, 'phone' => $reviewer->phone, 'notes' => 'Fictional demonstration booking.', 'status' => 'completed', 'created_at' => $start->copy()->subDay()]);
                    foreach (['pending', 'accepted', 'in_progress', 'completed'] as $n => $status) {
                        $booking->events()->create(['actor_id' => $n === 0 ? $reviewer->id : $listing->provider_id, 'status' => $status, 'note' => ['Booking requested.', 'Provider accepted the request.', 'Service work started.', 'Service marked complete.'][$n], 'created_at' => $start->copy()->addMinutes($n * 25)]);
                    }
                    $booking->review()->create(['customer_id' => $reviewer->id, 'rating' => $r === 2 && $index % 2 ? 4 : 5, 'comment' => ['Easy to arrange, and the work was completed with care. Everything was left tidy.', 'Clear communication and a helpful explanation of the work. A smooth experience from start to finish.', 'Arrived at the agreed time and took care with the details. The home feels better already.'][$r], 'created_at' => $start->copy()->addHours(3)]);
                }
            }
            foreach ([['safe-connections-peace-of-mind', 1, 'pending'], ['a-fresh-start-for-your-home', 3, 'accepted']] as [$slug,$days,$status]) {
                $listing = ServiceListing::where('slug', $slug)->firstOrFail();
                $booking = Booking::create(['customer_id' => $customer->id, 'provider_id' => $listing->provider_id, 'service_listing_id' => $listing->id, 'service_title' => $listing->title, 'quoted_price' => $listing->price, 'starts_at' => now()->addDays($days)->setTime(10, 0), 'address' => '24, Sample Garden Road, Colombo', 'phone' => $customer->phone, 'notes' => 'Please call when you arrive. Fictional demonstration booking.', 'status' => $status]);
                DB::table('booking_slots')->insert(['booking_id' => $booking->id, 'provider_id' => $listing->provider_id, 'starts_at' => $booking->starts_at]);
                $booking->events()->create(['actor_id' => $customer->id, 'status' => 'pending', 'note' => 'Booking requested. Awaiting provider confirmation.']);
                if ($status === 'accepted') {
                    $booking->events()->create(['actor_id' => $listing->provider_id, 'status' => 'accepted', 'note' => 'Provider accepted this booking.']);
                }
            }
        });
    }
}
