<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminBookingService
{
    public function updateStatus(Booking $booking, User $administrator, BookingStatus $next): void
    {
        if ($administrator->role !== 'admin') {
            abort(403);
        }

        DB::transaction(function () use ($booking, $administrator, $next): void {
            $booking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $isPending = $booking->status === BookingStatus::Pending;
            $isOpen = in_array($booking->status, [BookingStatus::Pending, BookingStatus::Accepted, BookingStatus::InProgress], true);
            $isAllowed = $next === BookingStatus::Rejected ? $isPending : $next === BookingStatus::Cancelled && $isOpen;

            if (! $isAllowed) {
                throw ValidationException::withMessages(['status' => 'Only pending bookings can be rejected; open bookings can be cancelled.']);
            }

            $booking->update(['status' => $next]);
            DB::table('booking_slots')->where('booking_id', $booking->id)->delete();
            $booking->events()->create([
                'actor_id' => $administrator->id,
                'status' => $next->value,
                'note' => 'Administrator marked this booking as '.strtolower($next->label()).'.',
            ]);
        }, 3);
    }
}
