@extends('layouts.app')
@section('title', 'Manage services')
@section('eyebrow', 'Administration')
@section('content')
<div class="page-heading"><div><p class="eyebrow">CATALOG MODERATION</p><h1>Service listings</h1><p>Review provider listings, hide services, or remove them from the catalog.</p></div></div>
@include('admin.partials.nav')
<section class="panel">
    <form method="GET" class="admin-search">
        <label class="search-field"><x-icon name="search" /><span class="sr-only">Search services or providers</span><input name="q" type="search" maxlength="100" placeholder="Search title or provider" value="{{ request('q') }}"></label>
        <select name="category" aria-label="Filter by category"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select>
        <select name="status" aria-label="Filter by visibility"><option value="">All statuses</option><option value="active" @selected(request('status') === 'active')>Published</option><option value="inactive" @selected(request('status') === 'inactive')>Hidden</option></select>
        <button class="btn btn-dark" type="submit">Filter</button>
    </form>
    <div class="table-wrap"><table><thead><tr><th>Service</th><th>Provider</th><th>Category</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead><tbody>
    @forelse($listings as $listing)
        <tr>
            <td><strong>{{ $listing->title }}</strong><small class="admin-cell-note">{{ $listing->city }}</small></td>
            <td>{{ $listing->provider->name }}<small class="admin-cell-note">{{ $listing->provider->email }}</small></td>
            <td>{{ $listing->category->name }}</td>
            <td>LKR {{ number_format($listing->price) }}</td>
            <td><span class="status {{ $listing->is_active && $listing->provider->is_active ? 'status-accepted' : 'status-rejected' }}">{{ ! $listing->provider->is_active ? 'Provider suspended' : ($listing->is_active ? 'Published' : 'Hidden') }}</span></td>
            <td class="admin-action-cell"><a class="text-button teal" href="{{ route('admin.services.edit', $listing) }}">Edit</a><form method="POST" action="{{ route('admin.services.toggle', $listing) }}">@csrf @method('PATCH')<button class="text-button teal">{{ $listing->is_active ? 'Hide' : 'Publish' }}</button></form>
                <form method="POST" action="{{ route('admin.services.destroy', $listing) }}" data-confirm="Remove {{ $listing->title }} from the catalog? Existing bookings will be preserved.">@csrf @method('DELETE')<button class="text-button danger">Remove</button></form></td>
        </tr>
    @empty
        <tr><td colspan="6">No service listings match this filter.</td></tr>
    @endforelse
    </tbody></table></div>
    <div class="pagination">{{ $listings->links() }}</div>
</section>
@endsection
