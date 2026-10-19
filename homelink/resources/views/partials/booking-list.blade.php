@forelse($bookings as $booking)
<a class="booking-row" href="{{ route('bookings.show',$booking) }}">
    <span class="booking-row-icon color-{{ $booking->serviceListing->category->color }}"><x-icon :name="$booking->serviceListing->category->icon" /></span>
    <div class="booking-row-main"><small>{{ $booking->reference() }}</small><h3>{{ $booking->service_title }}</h3><span>{{ auth()->user()->role==='provider' ? $booking->customer->name : $booking->provider->name }}</span></div>
    <div class="booking-row-date"><strong>{{ $booking->starts_at->format('d M Y') }}</strong><small>{{ $booking->starts_at->format('g:i A') }}</small></div>
    <x-status :status="$booking->status" /><x-icon name="chevron-right" class="row-arrow" />
</a>
@empty
<div class="empty-state"><span class="empty-icon"><x-icon name="calendar-plus" /></span><h3>A fresh start for your schedule</h3><p>{{ auth()->user()->role==='provider' ? 'Your customer requests will appear here. Add a service to get started.' : 'Your bookings will appear here once you find a helping hand.' }}</p><a class="btn btn-dark" href="{{ auth()->user()->role==='provider' ? route('provider.services') : route('home') }}">{{ auth()->user()->role==='provider' ? 'Manage services' : 'Explore services' }}</a></div>
@endforelse
