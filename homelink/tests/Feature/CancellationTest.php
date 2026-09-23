<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\ServiceListing;
use App\Models\User;
use App\Services\BookingService;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CancellationTest extends TestCase
{
    private function booking(int $hours = 24, string $status = 'pending'): Booking
    {
        $this->travelTo(Carbon::parse('2026-09-15 10:00:00'));
        $at = now()->addHours($hours);
        $booking = app(BookingService::class)->create(User::factory()->create(), ServiceListing::factory()->create(), ['date' => $at->toDateString(), 'time' => $at->format('H:i'), 'address' => '24, Sample Garden Road, Colombo', 'phone' => '0771234567']);
        $booking->update(['status' => $status]);

        return $booking;
    }

    public function test_customer_cancellation_releases_slot_and_records_history(): void
    {
        $booking = $this->booking();
        $this->actingAs($booking->customer)->post('/bookings/'.$booking->id.'/cancel')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'cancelled']);
        $this->assertDatabaseMissing('booking_slots', ['booking_id' => $booking->id]);
        $this->assertDatabaseHas('booking_events', ['booking_id' => $booking->id, 'actor_id' => $booking->customer_id, 'status' => 'cancelled']);
    }

    public function test_exact_two_hour_cutoff_allows_cancellation(): void
    {
        $booking = $this->booking(2, 'accepted');
        $this->actingAs($booking->customer)->post('/bookings/'.$booking->id.'/cancel')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'cancelled']);
    }

    public function test_late_cancellation_is_rejected_without_releasing_slot(): void
    {
        $booking = $this->booking(1, 'accepted');
        $this->actingAs($booking->customer)->post('/bookings/'.$booking->id.'/cancel')->assertSessionHasErrors('status');
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'accepted']);
        $this->assertDatabaseCount('booking_slots', 1);
    }

    public function test_other_customer_cannot_cancel_booking(): void
    {
        $booking = $this->booking();
        $this->actingAs(User::factory()->create())->post('/bookings/'.$booking->id.'/cancel')->assertForbidden();
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'pending']);
        $this->assertDatabaseCount('booking_slots', 1);
    }

    public static function terminalStates(): array
    {
        return [['in_progress'], ['completed'], ['rejected'], ['cancelled']];
    }

    #[DataProvider('terminalStates')]
    public function test_ineligible_states_cannot_be_cancelled(string $status): void
    {
        $booking = $this->booking(24, $status);
        $this->actingAs($booking->customer)->post('/bookings/'.$booking->id.'/cancel')->assertSessionHasErrors('status');
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => $status]);
    }

    public function test_cancelled_slot_can_be_booked_again(): void
    {
        $booking = $this->booking();
        app(BookingService::class)->cancel($booking, $booking->customer);
        $this->actingAs(User::factory()->create())->post('/services/'.$booking->serviceListing->slug.'/book', ['date' => '2026-09-16', 'time' => '10:00', 'address' => '25, Another Sample Road, Colombo', 'phone' => '0771234567'])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('bookings', 2);
        $this->assertDatabaseCount('booking_slots',1);
    }
}
