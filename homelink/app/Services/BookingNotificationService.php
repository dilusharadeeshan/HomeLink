<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Mail\BookingWorkflowMail;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;

class BookingNotificationService
{
    public function requestCreated(Booking $booking): void
    {
        $booking->loadMissing(['customer', 'provider']);

        Mail::to($booking->customer)->queue(new BookingWorkflowMail($booking, 'request_received'));
        Mail::to($booking->provider)->queue(new BookingWorkflowMail($booking, 'provider_request'));
    }

    public function statusChanged(Booking $booking, BookingStatus $status): void
    {
        if (! in_array($status, [BookingStatus::Accepted, BookingStatus::Rejected, BookingStatus::InProgress, BookingStatus::Completed], true)) {
            return;
        }

        $booking->loadMissing(['customer', 'provider']);
        Mail::to($booking->customer)->queue(new BookingWorkflowMail($booking, $status->value));
    }

    public function cancelled(Booking $booking): void
    {
        $booking->loadMissing(['customer', 'provider']);
        Mail::to($booking->provider)->queue(new BookingWorkflowMail($booking, 'cancelled'));
    }
}
