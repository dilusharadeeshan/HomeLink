@extends('layouts.app')
@section('title',$listing->title)
@section('eyebrow','Service details')
@section('content')
<a href="{{ route('home') }}#services" class="back-link"><x-icon name="arrow-left" />Back to services</a>
<div class="detail-grid">
<div>
    <div class="detail-cover"><img src="{{ asset('images/'.$listing->category->slug.'.jpg') }}" alt="{{ $listing->category->name }} service" width="1000" height="600"><span class="image-category"><x-icon :name="$listing->category->icon" />{{ $listing->category->name }}</span></div>
    <div class="detail-heading"><p class="eyebrow">{{ $listing->category->name }} · {{ $listing->city }}</p><h1>{{ $listing->title }}</h1><div class="detail-meta"><span><x-icon name="map-pin" />{{ $listing->city }}</span><span class="rating"><x-icon name="star" />{{ $listing->reviews_count ? number_format($listing->reviews_avg_rating,1).' · '.$listing->reviews_count.' reviews' : 'New on HomeLink' }}</span><span><x-icon name="clock-3" />2-hour appointment window</span></div></div>
    <section class="panel"><h2>About this service</h2><p class="prose preserve-lines">{{ $listing->description }}</p><div class="info-box"><x-icon name="info" /><span>{{ $listing->pricing_note ?: 'Starting price covers the listed service. Agree any additional work or material costs directly with your provider before work begins.' }}</span></div></section>
    <section class="panel provider-panel"><span class="avatar avatar-large color-{{ $listing->category->color }}">{{ $listing->provider->initials() }}</span><div><p class="eyebrow">MEET YOUR PROVIDER</p><h2>{{ $listing->provider->name }}</h2><p>{{ $listing->provider->bio ?: 'A local home-service provider ready to help with your next project.' }}</p><span class="muted">Based in {{ $listing->provider->city }} · Member since {{ $listing->provider->created_at->format('M Y') }}</span></div></section>
    <section class="panel"><div class="section-heading"><h2>From homes like yours</h2><span class="rating"><x-icon name="star" />{{ $listing->reviews_count ? number_format($listing->reviews_avg_rating,1) : 'No reviews yet' }}</span></div>
    @forelse($reviews as $review)<div class="review"><span class="avatar avatar-small">{{ $review->customer->initials() }}</span><div><strong>{{ $review->customer->name }}</strong><small>{{ $review->created_at->format('M Y') }} · Completed booking</small><div class="review-stars" aria-label="{{ $review->rating }} out of 5 stars">@for($i=0;$i<$review->rating;$i++)<x-icon name="star" />@endfor</div><p>{{ $review->comment }}</p></div></div>@empty<p class="muted">Reviews appear after customers complete a booking. Be the first to share your experience.</p>@endforelse
    </section>
</div>
<aside class="booking-panel panel">
    <p class="eyebrow">LET’S GET IT SORTED</p><h2>Book a helping hand</h2><div class="booking-price"><strong>LKR {{ number_format($listing->price) }}</strong><span>starting price / visit</span></div><div class="soft-divider"></div>
    @if(auth()->check() && auth()->user()->role==='customer')
    <form method="POST" action="{{ route('bookings.store',$listing->slug) }}" data-submit x-data="bookingForm(@js(route('services.slots',$listing->slug)), @js(old('date',now()->addDay()->toDateString())), @js(old('time','')))" x-init="loadSlots()">
        @csrf
        <label class="field">Preferred date<input required type="date" name="date" x-model="date" @change="loadSlots()" min="{{ now()->toDateString() }}" max="{{ now()->addDays(90)->toDateString() }}"></label>
        <fieldset class="field"><legend>Choose a start time <small>Asia/Colombo</small></legend><div class="time-slots">@foreach(['08:00'=>'8:00 AM','10:00'=>'10:00 AM','12:00'=>'12:00 PM','14:00'=>'2:00 PM','16:00'=>'4:00 PM'] as $value=>$label)<label class="time-slot" :class="{'chosen':time==='{{ $value }}','unavailable':unavailable.includes('{{ $value }}')}"><input type="radio" name="time" value="{{ $value }}" x-model="time" :disabled="loading || unavailable.includes('{{ $value }}')" required><span>{{ $label }}</span></label>@endforeach</div><span x-show="loading" class="muted" role="status">Checking availability…</span><span x-show="slotError" x-text="slotError" class="text-error" role="alert"></span></fieldset>
        <label class="field">Service address<textarea name="address" rows="2" required minlength="10" maxlength="255" placeholder="House number, street, and area">{{ old('address') }}</textarea></label>
        <label class="field">Contact number<input name="phone" type="tel" required value="{{ old('phone',auth()->user()->phone) }}" placeholder="077 123 4567" pattern="[+0-9 ()\-]{9,20}"></label>
        <label class="field">Anything we should know? <small>Optional</small><textarea name="notes" rows="3" maxlength="2000" placeholder="Tell your provider a little about the job…">{{ old('notes') }}</textarea></label>
        <button class="btn btn-teal btn-full" type="submit" :disabled="loading || !time">Request booking <x-icon name="arrow-right" /></button>
        <p class="form-note"><x-icon name="shield-check" />No payment now. Your provider confirms the request.</p>
    </form>
    @elseif(auth()->guest())<p class="prose">Choose your service, find a time, and leave the rest to your local provider.</p><a class="btn btn-teal btn-full" href="{{ route('login') }}">Log in to book <x-icon name="arrow-right" /></a><p class="form-note">New here? <a href="{{ route('register') }}">Create a customer account</a></p>
    @else<div class="info-box"><x-icon name="info" /><span>Bookings are available through customer accounts. You are signed in as {{ auth()->user()->role }}.</span></div>@endif
    <div class="booking-reassurance"><div><x-icon name="calendar-check" /><span>Clear booking updates</span></div><div><x-icon name="hand-coins" /><span>Pay your provider directly</span></div></div>
</aside>
</div>
@endsection
