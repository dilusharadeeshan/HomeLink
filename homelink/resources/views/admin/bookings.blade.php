@extends('layouts.app')
@section('title', 'Manage bookings')
@section('eyebrow', 'Administration')
@section('content')
<div class="page-heading"><div><p class="eyebrow">PLATFORM ACTIVITY</p><h1>Bookings</h1><p>Search bookings and resolve open requests when needed.</p></div></div>
@include('admin.partials.nav')
<section class="panel">
    <form method="GET" class="admin-search">
        <label class="search-field"><x-icon name="search" /><span class="sr-only">Search bookings, customers, or providers</span><input name="q" type="search" maxlength="100" placeholder="Booking reference, customer, or provider" value="{{ request('q') }}"></label>
        <select name="status" aria-label="Filter by booking status"><option value="">All statuses</option>@foreach($statuses as $status)<option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>@endforeach</select>
        <button class="btn btn-dark" type="submit">Filter</button>
    </form>
    <div class="table-wrap"><table><thead><tr><th>Reference</th><th>Service</th><th>Customer</th><th>Provider</th><th>Appointment</th><th>Status</th><th>Resolve</th></tr></thead><tbody>
    @forelse($bookings as $booking)
        <tr>
            <td><a class="text-link" href="{{ route('bookings.show', $booking) }}">{{ $booking->reference() }}</a></td>
            <td>{{ $booking->service_title }}</td>
            <td>{{ $booking->customer->name }}<small class="admin-cell-note">{{ $booking->customer->email }}</small></td>
            <td>{{ $booking->provider->name }}<small class="admin-cell-note">{{ $booking->provider->email }}</small></td>
            <td>{{ $booking->starts_at->format('M j, Y · g:i A') }}</td>
            <td><x-status :status="$booking->status" /></td>
            <td>
                @if(in_array($booking->status, [\App\Enums\BookingStatus::Pending, \App\Enums\BookingStatus::Accepted, \App\Enums\BookingStatus::InProgress], true))
                    <div class="admin-action-cell">
                        @if($booking->status === \App\Enums\BookingStatus::Pending)
                            <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" data-confirm="Reject booking {{ $booking->reference() }}? The reserved time will be released.">@csrf @method('PATCH')<input type="hidden" name="status" value="rejected"><button class="text-button danger">Reject</button></form>
                        @endif
                        <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" data-confirm="Cancel booking {{ $booking->reference() }}? The reserved time will be released.">@csrf @method('PATCH')<input type="hidden" name="status" value="cancelled"><button class="text-button danger">Cancel</button></form>
                    </div>
                @else
                    <span class="muted">No action</span>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="7">No bookings match this filter.</td></tr>
    @endforelse
    </tbody></table></div>
    <div class="pagination">{{ $bookings->links() }}</div>
</section>
@endsection
