@extends('layouts.app')
@section('title', 'Administration')
@section('eyebrow', 'Administration')
@section('content')
<div class="page-heading">
    <div><p class="eyebrow">KEEPING HOMELINK RUNNING WELL</p><h1>Administration</h1><p>Manage accounts and follow activity across your platform.</p></div>
    <span class="status status-accepted"><span class="status-dot"></span>Administrator</span>
</div>
@include('admin.partials.nav')
<div class="stats-grid" data-stagger>
    @foreach([['users', 'Registered accounts', 'users-round'], ['services', 'Active services', 'briefcase-business'], ['bookings', 'Total bookings', 'calendar-days'], ['pending', 'Awaiting response', 'clock-3']] as [$key, $label, $icon])
        <div class="stat-card"><div class="stat-label">{{ $label }}<x-icon :name="$icon" /></div><strong>{{ $stats[$key] }}</strong></div>
    @endforeach
</div>
<section class="panel">
    <div class="section-heading"><h2>People on HomeLink</h2><span class="muted">{{ $users->total() }} accounts</span></div>
    <form method="GET" class="admin-search">
        <label class="search-field"><x-icon name="search" /><span class="sr-only">Search accounts</span><input name="q" type="search" maxlength="100" placeholder="Search by name or email" value="{{ request('q') }}"></label>
        <select name="role" aria-label="Account role"><option value="">All roles</option>@foreach(['customer', 'provider', 'admin'] as $role)<option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>@endforeach</select>
        <button class="btn btn-dark" type="submit">Search</button>
    </form>
    <div class="table-wrap"><table><thead><tr><th>Account</th><th>Role</th><th>Location</th><th>Status</th><th>Manage</th></tr></thead><tbody>
    @forelse($users as $user)
        <tr>
            <td><div class="table-person"><span class="avatar avatar-small">{{ $user->initials() }}</span><span><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small></span></div></td>
            <td>
                @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.role', $user) }}" class="admin-inline-form">@csrf @method('PATCH')
                        <label class="sr-only" for="role-{{ $user->id }}">Role for {{ $user->name }}</label>
                        <select id="role-{{ $user->id }}" name="role" aria-label="Role for {{ $user->name }}">@foreach(['customer', 'provider', 'admin'] as $role)<option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst($role) }}</option>@endforeach</select>
                        <button class="text-button teal">Save role</button>
                    </form>
                @else
                    <span>{{ ucfirst($user->role) }} <small class="muted">(you)</small></span>
                @endif
            </td>
            <td>{{ $user->city }}</td>
            <td><span class="status {{ $user->is_active ? 'status-accepted' : 'status-rejected' }}">{{ $user->is_active ? 'Active' : 'Suspended' }}</span></td>
            <td>@if($user->role !== 'admin' && $user->id !== auth()->id())<form method="POST" action="{{ route('admin.users.toggle', $user) }}" data-confirm="{{ $user->is_active ? 'Suspend this account and hide its services?' : 'Reactivate this account?' }}">@csrf @method('PATCH')<button class="text-button {{ $user->is_active ? 'danger' : 'teal' }}">{{ $user->is_active ? 'Suspend' : 'Reactivate' }}</button></form>@else<span class="muted">Protected</span>@endif</td>
        </tr>
    @empty
        <tr><td colspan="5">No accounts match this search.</td></tr>
    @endforelse
    </tbody></table></div>
    <div class="pagination">{{ $users->links() }}</div>
</section>
<section class="panel"><div class="section-heading"><h2>Latest bookings</h2><a class="text-link" href="{{ route('admin.bookings') }}">Manage all bookings <x-icon name="arrow-right" /></a></div>
    @forelse($recent as $booking)
        <a class="admin-booking" href="{{ route('bookings.show', $booking) }}"><strong>{{ $booking->reference() }}</strong><span>{{ $booking->customer->name }} → {{ $booking->provider->name }}</span><x-status :status="$booking->status" /><x-icon name="arrow-up-right" /></a>
    @empty
        <p class="muted">No bookings have been created yet.</p>
    @endforelse
</section>
@endsection
