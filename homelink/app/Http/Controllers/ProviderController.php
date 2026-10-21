<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ServiceListing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProviderController extends Controller
{
    public function index(Request $request): View
    {
        return view('provider.services', ['listings' => $request->user()->listings()->with('category')->latest()->paginate(9)]);
    }

    public function create(): View
    {
        return view('provider.form', ['listing' => new ServiceListing(['city' => auth()->user()->city, 'is_active' => true]), 'categories' => Category::all()]);
    }

    public function edit(Request $request, ServiceListing $listing): View
    {
        Gate::authorize('update', $listing);

        return view('provider.form', ['listing' => $listing, 'categories' => Category::all()]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate(['category_id' => ['required', 'exists:categories,id'], 'title' => ['required', 'string', 'max:120'], 'description' => ['required', 'string', 'min:30', 'max:3000'], 'price' => ['required', 'integer', 'between:100,1000000'], 'pricing_note' => ['nullable', 'string', 'max:160'], 'city' => ['required', 'string', 'max:80'], 'is_active' => ['nullable', 'boolean']]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $request->user()->listings()->create([...$data, 'slug' => Str::slug($data['title']).'-'.Str::lower(Str::random(8))]);

        return redirect()->route('provider.services')->with('success', 'Your service has been created.');
    }

    public function update(Request $request, ServiceListing $listing): RedirectResponse
    {
        Gate::authorize('update', $listing);
        $listing->update($this->validated($request));

        return redirect()->route('provider.services')->with('success', 'Service updated.');
    }

    public function destroy(Request $request, ServiceListing $listing): RedirectResponse
    {
        Gate::authorize('update', $listing);
        $listing->delete();

        return redirect()->route('provider.services')->with('success', 'Service removed from the catalog. Existing bookings remain available.');
    }
}
