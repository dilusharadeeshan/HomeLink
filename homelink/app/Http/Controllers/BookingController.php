<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\ServiceListing;
use App\Services\BookingService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate(['status' => ['nullable', Rule::enum(BookingStatus::class)]]);
        $query = Booking::with(['provider', 'customer', 'serviceListing.category']);
        $query->where($request->user()->role === 'provider' ? 'provider_id' : 'customer_id', $request->user()->id);
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return view('bookings', ['bookings' => $query->orderByDesc('starts_at')->paginate(10)->withQueryString()]);
    }

    public function show(Request $request, Booking $booking): View
    {
        Gate::authorize('view', $booking);

        return view('booking', ['booking' => $booking->load(['customer', 'provider', 'serviceListing.category', 'events.actor', 'review'])]);
    }

    public function store(Request $request, ServiceListing $listing, BookingService $service): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', 'before_or_equal:'.now()->addDays(90)->toDateString()],
            'time' => ['required', Rule::in(['08:00', '10:00', '12:00', '14:00', '16:00'])],
            'address' => ['required', 'string', 'min:10', 'max:255'],
            'phone' => ['required', 'regex:/^[+0-9 ()-]{9,20}$/'], 'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $booking = $service->create($request->user(), $listing, $data);

        return redirect()->route('bookings.show', $booking)->with('success', 'Your booking request is on its way.');
    }

    public function update(Request $request, Booking $booking, BookingService $service): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::enum(BookingStatus::class)]]);
        $service->transition($booking, $request->user(), BookingStatus::from($data['status']));

        return back()->with('success', 'Booking status updated.');
    }

    public function review(Request $request, Booking $booking): RedirectResponse
    {
        Gate::authorize('review', $booking);
        $data = $request->validate(['rating' => ['required', 'integer', 'between:1,5'], 'comment' => ['required', 'string', 'min:10', 'max:1000']]);
        if ($booking->review()->exists()) {
            return back()->withErrors(['rating' => 'You have already reviewed this booking.']);
        }
        try {
            $booking->review()->create([...$data, 'customer_id' => $request->user()->id]);
        } catch (UniqueConstraintViolationException $e) {
            return back()->withErrors(['rating' => 'You have already reviewed this booking.']);
        }

        return back()->with('success', 'Thank you. Your review is now visible on the service.');
    }

    public function cancel(Request $request, Booking $booking, BookingService $service): RedirectResponse
    {
        $service->cancel($booking, $request->user());

        return back()->with('success', 'Booking cancelled. Your provider can see the update.');
    }
}
