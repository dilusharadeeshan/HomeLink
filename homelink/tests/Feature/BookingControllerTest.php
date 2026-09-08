<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\ServiceListing;
use App\Models\User;
use App\Services\BookingService;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    private function payload(array $overrides = []): array
    {
        $this->travelTo(Carbon::parse('2026-09-15 09:00:00'));

        return array_replace(['date' => '2026-09-17', 'time' => '10:00', 'address' => '24, Sample Garden Road, Colombo', 'phone' => '0771234567', 'notes' => 'Please call on arrival.'], $overrides);
    }

    private function reserved(): Booking
    {
        $payload = $this->payload();

        return app(BookingService::class)->create(User::factory()->create(), ServiceListing::factory()->create(), $payload);
    }

    public function test_customer_creates_pending_booking_with_a_price_snapshot(): void
    {
        $listing = ServiceListing::factory()->create(['price' => 4500]);
        $customer = User::factory()->create();
        $this->actingAs($customer)->post('/services/'.$listing->slug.'/book', $this->payload(['quoted_price' => 1, 'status' => 'completed', 'customer_id' => 999]))->assertRedirectToRoute('bookings.show', 1);
        $this->assertDatabaseHas('bookings', ['customer_id' => $customer->id, 'provider_id' => $listing->provider_id, 'quoted_price' => 4500, 'status' => 'pending', 'starts_at' => '2026-09-17 10:00:00']);
        $this->assertDatabaseHas('booking_slots', ['booking_id' => 1, 'provider_id' => $listing->provider_id]);
        $this->assertDatabaseHas('booking_events', ['booking_id' => 1, 'actor_id' => $customer->id, 'status' => 'pending']);
        $listing->update(['price' => 7000]);
        $this->assertSame(4500, Booking::findOrFail(1)->quoted_price);
    }

    public function test_guest_must_sign_in_to_book(): void
    {
        $listing = ServiceListing::factory()->create();
        $this->post('/services/'.$listing->slug.'/book', $this->payload())->assertRedirectToRoute('login');
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_provider_cannot_book_as_customer(): void
    {
        $listing = ServiceListing::factory()->create();
        $this->actingAs($listing->provider)->post('/services/'.$listing->slug.'/book', $this->payload())->assertForbidden();
        $this->assertDatabaseCount('bookings', 0);
    }

    public static function invalidBookings(): array
    {
        return ['past date' => [['date' => '2026-09-14'], 'date'], 'beyond horizon' => [['date' => '2027-09-15'], 'date'], 'non-slot time' => [['time' => '09:30'], 'time'], 'past same-day slot' => [['date' => '2026-09-15', 'time' => '08:00'], 'time'], 'short address' => [['address' => 'abc'], 'address'], 'bad phone' => [['phone' => 'abc'], 'phone'], 'oversized notes' => [['notes' => str_repeat('a', 2001)], 'notes']];
    }

    #[DataProvider('invalidBookings')]
    public function test_invalid_booking_does_not_reserve_a_slot(array $overrides, string $field): void
    {
        $listing = ServiceListing::factory()->create();
        $this->actingAs(User::factory()->create())->post('/services/'.$listing->slug.'/book', $this->payload($overrides))->assertSessionHasErrors($field);
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('booking_slots', 0);
    }

    public function test_hidden_service_cannot_be_booked(): void
    {
        $listing = ServiceListing::factory()->create(['is_active' => false]);
        $this->actingAs(User::factory()->create())->post('/services/'.$listing->slug.'/book', $this->payload())->assertSessionHasErrors('date');
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_conflicting_request_across_services_is_rejected_atomically(): void
    {
        $booking = $this->reserved();
        $otherListing = ServiceListing::factory()->create(['provider_id' => $booking->provider_id]);
        $this->actingAs(User::factory()->create())->post('/services/'.$otherListing->slug.'/book', $this->payload())->assertSessionHasErrors(['time' => 'This provider is already booked for that time. Please choose another slot.']);
        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseCount('booking_slots', 1);
        $this->assertDatabaseCount('booking_events', 1);
    }

    public function test_slot_endpoint_exposes_only_unavailable_times(): void
    {
        $booking = $this->reserved();
        $this->getJson('/services/'.$booking->serviceListing->slug.'/slots?date=2026-09-17')->assertExactJson(['unavailable' => ['10:00']]);
    }

    public function test_provider_acceptance_updates_status_and_timeline(): void
    {
        $booking = $this->reserved();
        $this->actingAs($booking->provider)->patch('/bookings/'.$booking->id.'/status', ['status' => 'accepted'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'accepted']);
        $this->assertDatabaseHas('booking_events', ['booking_id' => $booking->id, 'status' => 'accepted']);
        $this->assertDatabaseCount('booking_slots', 1);
    }

    public function test_rejection_releases_the_provider_slot(): void
    {
        $booking = $this->reserved();
        $this->actingAs($booking->provider)->patch('/bookings/'.$booking->id.'/status', ['status' => 'rejected'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'rejected']);
        $this->assertDatabaseCount('booking_slots', 0);
    }

    public function test_provider_cannot_skip_from_pending_to_completed(): void
    {
        $booking = $this->reserved();
        $this->actingAs($booking->provider)->patch('/bookings/'.$booking->id.'/status', ['status' => 'completed'])->assertSessionHasErrors('status');
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'pending']);
        $this->assertDatabaseCount('booking_events', 1);
    }

    public function test_other_provider_cannot_update_booking(): void
    {
        $booking = $this->reserved();
        $other = User::factory()->create(['role' => 'provider']);
        $this->actingAs($other)->patch('/bookings/'.$booking->id.'/status', ['status' => 'accepted'])->assertForbidden();
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'pending']);
    }

    public function test_other_customer_cannot_view_booking_contact_details(): void
    {
        $booking = $this->reserved();
        $this->actingAs(User::factory()->create())->get('/bookings/'.$booking->id)->assertForbidden()->assertDontSee($booking->address);
    }

    public function test_customers_see_only_their_own_booking_history(): void
    {
        $booking = $this->reserved();
        $other = Booking::factory()->create(['service_title' => 'Another customer private booking']);
        $this->actingAs($booking->customer)->get('/bookings')->assertSee($booking->service_title)->assertDontSee($other->service_title);
    }

    public function test_customer_can_review_a_completed_booking_once(): void
    {
        $booking = Booking::factory()->create(['status' => 'completed']);
        $this->actingAs($booking->customer)->post('/bookings/'.$booking->id.'/review', ['rating' => 5, 'comment' => 'Careful work and clear communication.'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('reviews', ['booking_id' => $booking->id, 'rating' => 5]);
        $this->post('/bookings/'.$booking->id.'/review', ['rating' => 1, 'comment' => 'A repeated review should not replace the first.'])->assertSessionHasErrors('rating');
        $this->assertDatabaseCount('reviews', 1);
    }

    public function test_unfinished_booking_cannot_be_reviewed(): void
    {
        $booking = $this->reserved();
        $this->actingAs($booking->customer)->post('/bookings/'.$booking->id.'/review', ['rating' => 5, 'comment' => 'This has not happened yet.'])->assertForbidden();
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_wrong_customer_cannot_review_completed_booking(): void
    {
        $booking = Booking::factory()->create(['status' => 'completed']);
        $this->actingAs(User::factory()->create())->post('/bookings/'.$booking->id.'/review', ['rating' => 5, 'comment' => 'Not my booking to review.'])->assertForbidden();
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_invalid_review_rating_is_rejected(): void
    {
        $booking = Booking::factory()->create(['status' => 'completed']);
        $this->actingAs($booking->customer)->post('/bookings/'.$booking->id.'/review',['rating' => 6, 'comment' => 'Careful work and clear communication.'])->assertSessionHasErrors('rating');
        $this->assertDatabaseCount('reviews',0);
    }
}
