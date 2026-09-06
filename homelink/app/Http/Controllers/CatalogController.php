<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ServiceListing;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'category' => ['nullable', 'string', 'max:50'], 'city' => ['nullable', 'string', 'max:80'], 'sort' => ['nullable', 'in:recommended,price_low,price_high,newest']]);
        $listings = ServiceListing::available()->with(['provider', 'category'])->withAvg('reviews', 'rating')->withCount('reviews');
        if ($request->filled('q')) {
            $listings->where(function ($q) use ($request) {
                $term = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $request->string('q')->trim()).'%';
                $q->where('title', 'like', $term)->orWhere('description', 'like', $term)->orWhereHas('provider', fn ($p) => $p->where('name', 'like', $term));
            });
        }
        if ($request->filled('category')) {
            $listings->whereHas('category', fn ($q) => $q->where('slug', $filters['category']));
        }
        if ($request->filled('city')) {
            $listings->where('city', $filters['city']);
        }
        match ($request->input('sort')) {
            'price_low' => $listings->orderBy('price'), 'price_high' => $listings->orderByDesc('price'),
            'newest' => $listings->latest(), default => $listings->orderByDesc('reviews_avg_rating')->orderBy('id'),
        };

        return view('catalog', ['listings' => $listings->paginate(9)->withQueryString(), 'categories' => Category::withCount(['listings' => fn ($q) => $q->available()])->get(), 'cities' => ServiceListing::available()->distinct()->orderBy('city')->pluck('city')]);
    }

    public function show(ServiceListing $listing): View
    {
        abort_unless($listing->is_active && $listing->provider->is_active, 404);
        $listing->load(['provider', 'category'])->loadAvg('reviews', 'rating')->loadCount('reviews');

        return view('service', ['listing' => $listing, 'reviews' => $listing->reviews()->with('customer')->latest()->limit(6)->get()]);
    }

    public function slots(Request $request, ServiceListing $listing): JsonResponse
    {
        abort_unless($listing->is_active && $listing->provider->is_active, 404);
        $data = $request->validate(['date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', 'before_or_equal:'.now()->addDays(90)->toDateString()]]);
        $busy = DB::table('booking_slots')->where('provider_id', $listing->provider_id)->whereDate('starts_at', $data['date'])->pluck('starts_at')->map(fn ($at) => Carbon::parse($at)->format('H:i'))->all();

        return response()->json(['unavailable' => $busy]);
    }
}
