<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingWorkflowMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking, public string $event)
    {
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine());
    }

    public function content(): Content
    {
        $providerNotice = in_array($this->event, ['provider_request', 'cancelled'], true);

        return new Content(
            view: 'emails.booking-workflow',
            with: [
                'recipientName' => $providerNotice ? $this->booking->provider->name : $this->booking->customer->name,
                'heading' => $this->heading(),
                'introText' => $this->message(),
                'providerNotice' => $providerNotice,
                'event' => $this->event,
                'bookingUrl' => route('bookings.show', $this->booking),
            ],
        );
    }

    private function subjectLine(): string
    {
        return match ($this->event) {
            'provider_request' => 'New booking request · '.$this->booking->reference(),
            'request_received' => 'Booking request received · '.$this->booking->reference(),
            'accepted' => 'Your booking is confirmed · '.$this->booking->reference(),
            'rejected' => 'Booking request update · '.$this->booking->reference(),
            'in_progress' => 'Your service is underway · '.$this->booking->reference(),
            'completed' => 'Your service is complete · '.$this->booking->reference(),
            'cancelled' => 'Booking cancelled · '.$this->booking->reference(),
            default => 'Booking update · '.$this->booking->reference(),
        };
    }

    private function heading(): string
    {
        return match ($this->event) {
            'provider_request' => 'A new request needs your response',
            'request_received' => 'Your request is on its way',
            'accepted' => 'Your booking is confirmed',
            'rejected' => 'The provider declined this request',
            'in_progress' => 'Your service is underway',
            'completed' => 'Your service is marked complete',
            'cancelled' => 'A customer cancelled this booking',
            default => 'There is an update to your booking',
        };
    }

    private function message(): string
    {
        return match ($this->event) {
            'provider_request' => $this->booking->customer->name.' has requested '.$this->booking->service_title.'. Open the booking to review the details and respond.',
            'request_received' => 'We sent your request for '.$this->booking->service_title.' to '.$this->booking->provider->name.'. We’ll let you know when they respond.',
            'accepted' => $this->booking->provider->name.' accepted your request for '.$this->booking->service_title.'. Your appointment is confirmed.',
            'rejected' => $this->booking->provider->name.' could not accept your request for '.$this->booking->service_title.'. The provider’s note is included below.',
            'in_progress' => $this->booking->provider->name.' marked your '.$this->booking->service_title.' service as in progress.',
            'completed' => $this->booking->provider->name.' marked your '.$this->booking->service_title.' service as complete.',
            'cancelled' => $this->booking->customer->name.' cancelled the booking for '.$this->booking->service_title.'. The appointment time is available again.',
            default => 'There has been an update to your '.$this->booking->service_title.' booking.',
        };
    }
}
