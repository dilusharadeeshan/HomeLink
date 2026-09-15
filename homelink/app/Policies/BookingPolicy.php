<?php

namespace App\Policies;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return in_array($user->id, [$booking->customer_id, $booking->provider_id]) || $user->role === 'admin';
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->role === 'provider' && $user->id === $booking->provider_id;
    }

    public function review(User $user, Booking $booking): bool
    {
        return $user->role === 'customer' && $user->id === $booking->customer_id && $booking->status === BookingStatus::Completed;
    }
}
