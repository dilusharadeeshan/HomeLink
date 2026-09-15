<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\ServiceListing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function create(User $customer, ServiceListing $listing, array $data): Booking
    {
        try {
            return DB::transaction(function () use ($customer, $listing, $data) {
                $listing = ServiceListing::whereKey($listing->id)->lockForUpdate()->firstOrFail();
                if (! $listing->is_active || ! $listing->provider->is_active) {
                    throw ValidationException::withMessages(['date' => 'This service is no longer available.']);
                }
                $at = Carbon::parse($data['date'].' '.$data['time']);
                if ($at->lte(now())) {
                    throw ValidationException::withMessages(['time' => 'Choose a time in the future.']);
                }
                $booking = Booking::create([
                    'customer_id' => $customer->id, 'provider_id' => $listing->provider_id, 'service_listing_id' => $listing->id,
                    'service_title' => $listing->title, 'quoted_price' => $listing->price, 'starts_at' => $at,
                    'address' => $data['address'], 'phone' => $data['phone'], 'notes' => $data['notes'] ?? null, 'status' => BookingStatus::Pending,
                ]);
                DB::table('booking_slots')->insert(['booking_id' => $booking->id, 'provider_id' => $listing->provider_id, 'starts_at' => $at]);
                $booking->events()->create(['actor_id' => $customer->id, 'status' => 'pending', 'note' => 'Booking requested. Awaiting provider confirmation.']);

                return $booking;
            }, 3);
        } catch (UniqueConstraintViolationException $e) {
            throw ValidationException::withMessages(['time' => 'This provider is already booked for that time. Please choose another slot.']);
        }
    }

    public function transition(Booking $booking, User $actor, BookingStatus $next): void
    {
        DB::transaction(function () use ($booking, $actor, $next) {
            $booking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            Gate::forUser($actor)->authorize('update', $booking);
            if (! $booking->status->canTransitionTo($next)) {
                throw ValidationException::withMessages(['status' => 'That status change is no longer available. Refresh and try again.']);
            }
            $booking->update(['status' => $next]);
            if ($next === BookingStatus::Rejected) {
                DB::table('booking_slots')->where('booking_id', $booking->id)->delete();
            }
            $booking->events()->create(['actor_id' => $actor->id, 'status' => $next->value, 'note' => 'Provider marked this booking as '.strtolower($next->label()).'.']);
        }, 3);
    }
}
