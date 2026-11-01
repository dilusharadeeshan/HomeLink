@extends('layouts.app')
@section('title', 'Moderate reviews')
@section('eyebrow', 'Administration')
@section('content')
<div class="page-heading"><div><p class="eyebrow">COMMUNITY MODERATION</p><h1>Reviews</h1><p>Review customer feedback and remove content that violates your guidelines.</p></div></div>
@include('admin.partials.nav')
<section class="panel">
    <div class="section-heading"><h2>Customer reviews</h2><span class="muted">{{ $reviews->total() }} reviews</span></div>
    <div class="table-wrap"><table><thead><tr><th>Review</th><th>Rating</th><th>Customer</th><th>Service</th><th>Date</th><th>Action</th></tr></thead><tbody>
    @forelse($reviews as $review)
        <tr>
            <td class="admin-review-comment">{{ $review->comment }}</td>
            <td><span class="admin-rating" aria-label="{{ $review->rating }} out of 5 stars">{{ $review->rating }}/5</span></td>
            <td>{{ $review->customer->name }}</td>
            <td>{{ $review->booking->service_title }}<small class="admin-cell-note">{{ $review->booking->provider->name }}</small></td>
            <td>{{ $review->created_at->format('M j, Y') }}</td>
            <td><form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" data-confirm="Remove this review? This action cannot be undone.">@csrf @method('DELETE')<button class="text-button danger">Remove</button></form></td>
        </tr>
    @empty
        <tr><td colspan="6">No reviews have been submitted yet.</td></tr>
    @endforelse
    </tbody></table></div>
    <div class="pagination">{{ $reviews->links() }}</div>
</section>
@endsection
