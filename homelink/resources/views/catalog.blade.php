@extends('layouts.app')
@section('title','Explore home services')
@section('content')
<div class="page-heading"><div><p class="eyebrow">LET’S TAKE CARE OF HOME</p><h1>Your home. Our helping hands.</h1><p>Find the right people for the jobs on your list.</p></div><span class="date-chip"><x-icon name="sun" />A good day to get things done</span></div>
<section class="hero">
    <div class="hero-copy"><span class="hero-kicker"><span></span>LOCAL EXPERTISE, PERSONAL CARE</span><h2>A little help.<br>A happier home<span>.</span></h2><p>From the quick fixes to the big refresh.<br>Find a service that feels right for you.</p><a href="#services" class="hero-link">Find your helping hand <x-icon name="arrow-down-right" /></a></div>
    <div class="hero-image"><img src="{{ asset('images/home.jpg') }}" alt="A bright, thoughtfully cared-for living room" width="900" height="700"><div class="hero-image-shade"></div><div class="floating-note"><span class="floating-icon"><x-icon name="house-plus" /></span><span><strong>Less to do. More to enjoy.</strong><small>Make room for what matters.</small></span></div></div>
</section>
<form action="{{ route('home') }}#services" method="GET" class="search-bar" role="search">
    <label class="search-field"><x-icon name="search" /><span class="sr-only">Search services or providers</span><input name="q" type="search" placeholder="What does your home need?" value="{{ request('q') }}" maxlength="100"></label>
    <label class="location-field"><x-icon name="map-pin" /><span class="sr-only">Service city</span><select name="city"><option value="">All locations</option>@foreach($cities as $city)<option @selected(request('city')===$city)>{{ $city }}</option>@endforeach</select></label>
    @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
    <button class="btn btn-teal" type="submit">Find services <x-icon name="arrow-right" /></button>
</form>
<section class="category-section" aria-label="Service categories">
    <a href="{{ route('home',request()->except('category','page')) }}#services" class="category-card {{ !request('category') ? 'selected' : '' }}"><span class="category-icon color-teal"><x-icon name="shapes" /></span><span>All services</span></a>
    @foreach($categories as $category)<a href="{{ route('home',array_merge(request()->except('page'),['category'=>$category->slug])) }}#services" class="category-card {{ request('category')===$category->slug ? 'selected' : '' }}"><span class="category-icon color-{{ $category->color }}"><x-icon :name="$category->icon" /></span><span>{{ $category->name }}</span></a>@endforeach
</section>
<section id="services" class="catalog-section">
    <div class="section-heading"><div><div class="section-overline">A LITTLE EXPERTISE GOES A LONG WAY</div><h2>{{ request()->filled('q') || request()->filled('category') || request()->filled('city') ? 'Your search results' : 'Find your next helping hand' }}<span class="count-badge">{{ $listings->total() }}</span></h2></div>
    <form method="GET" action="{{ route('home') }}#services" class="sort-form">@foreach(request()->except('sort','page') as $key=>$value)@if(is_string($value))<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif @endforeach<label for="sort">Sort by</label><select id="sort" name="sort" x-data @change="$el.form.submit()"><option value="recommended" @selected(request('sort','recommended')==='recommended')>Recommended</option><option value="price_low" @selected(request('sort')==='price_low')>Price: low to high</option><option value="price_high" @selected(request('sort')==='price_high')>Price: high to low</option><option value="newest" @selected(request('sort')==='newest')>Newest first</option></select><noscript><button>Sort</button></noscript></form></div>
    @if(request()->filled('q') || request()->filled('category') || request()->filled('city'))<div class="filter-summary">Showing {{ $listings->total() }} matching services <a href="{{ route('home') }}#services">Clear filters <x-icon name="x" /></a></div>@endif
    <div class="service-grid" data-stagger>@foreach($listings as $listing)<x-service-card :listing="$listing" />@endforeach</div>
    @if($listings->isEmpty())<div class="empty-state"><span class="empty-icon"><x-icon name="search-x" /></span><h3>No services found just yet</h3><p>Try a different service, category, or nearby location.</p><a class="btn btn-dark" href="{{ route('home') }}#services">Explore all services</a></div>@endif
    <div class="pagination">{{ $listings->links() }}</div>
</section>
<div class="promise-strip" data-stagger><div><x-icon name="calendar-check-2" /><span><strong>Plans made simple</strong><small>Choose a time that works for you.</small></span></div><div><x-icon name="message-circle-heart" /><span><strong>People, not guesswork</strong><small>Read reviews from completed bookings.</small></span></div><div><x-icon name="list-checks" /><span><strong>Every detail, together</strong><small>Follow your booking from start to finish.</small></span></div></div>
@endsection
