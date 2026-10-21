@props(['listing'])
<article class="service-card">
    <a href="{{ route('services.show',$listing->slug) }}" class="service-image-link" aria-label="View {{ $listing->title }}">
        <img class="service-image" src="{{ asset('images/'.$listing->category->slug.'.jpg') }}" alt="{{ $listing->category->name }} home service" loading="lazy" width="640" height="420">
        <span class="image-category"><x-icon :name="$listing->category->icon" />{{ $listing->category->name }}</span>
        <span class="image-arrow"><x-icon name="arrow-up-right" /></span>
    </a>
    <div class="service-body">
        <div class="card-location"><x-icon name="map-pin" />{{ $listing->city }} <span class="rating"><x-icon name="star" />{{ $listing->reviews_count ? number_format($listing->reviews_avg_rating,1) : 'New' }}@if($listing->reviews_count)<small>({{ $listing->reviews_count }})</small>@endif</span></div>
        <h3><a href="{{ route('services.show',$listing->slug) }}">{{ $listing->title }}</a></h3>
        <p class="card-description">{{ Str::limit($listing->description,82) }}</p>
        <div class="provider-line"><span class="avatar avatar-small color-{{ $listing->category->color }}">{{ $listing->provider->initials() }}</span><span>{{ $listing->provider->name }}</span><x-icon name="user-round" class="teal" /></div>
        <div class="card-bottom"><div><small>Starting from</small><strong>LKR {{ number_format($listing->price) }}</strong></div><a class="card-cta" href="{{ route('services.show',$listing->slug) }}">View service <x-icon name="arrow-right" /></a></div>
    </div>
</article>
