<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Review;
use App\Models\ServiceListing;
use App\Models\User;
use App\Services\AdminBookingService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminController extends Controller
{
    private const CATEGORY_ICONS = ['sparkles', 'droplets', 'zap', 'paint-roller', 'sprout', 'air-vent', 'shapes', 'wrench', 'hammer', 'plug', 'flower-2', 'wind', 'brush'];

    private const CATEGORY_COLORS = ['teal', 'blue', 'amber', 'purple', 'green', 'cyan'];

    public function index(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'role' => ['nullable', Rule::in(['customer', 'provider', 'admin'])]]);
        $query = User::query();

        if ($request->filled('q')) {
            $search = $filters['q'];
            $query->where(fn (Builder $builder) => $builder->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%'));
        }

        if ($request->filled('role')) {
            $query->where('role', $filters['role']);
        }

        return view('admin', [
            'users' => $query->latest()->paginate(12)->withQueryString(),
            'stats' => [
                'users' => User::count(),
                'services' => ServiceListing::available()->count(),
                'bookings' => Booking::count(),
                'pending' => Booking::where('status', BookingStatus::Pending->value)->count(),
            ],
            'recent' => Booking::with(['customer', 'provider'])->latest()->limit(5)->get(),
        ]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['role' => ['required', Rule::in(['customer', 'provider', 'admin'])]]);

        if ($request->user()->is($user)) {
            return back()->withErrors(['role' => 'You cannot change your own administrator role.']);
        }

        DB::transaction(function () use ($user, $data): void {
            $administratorCount = User::where('role', 'admin')->lockForUpdate()->get(['id'])->count();
            $target = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            if ($data['role'] === 'admin' && ! $target->is_active) {
                throw ValidationException::withMessages(['role' => 'Reactivate this account before granting administrator access.']);
            }

            if ($target->role === 'admin' && $data['role'] !== 'admin' && $administratorCount <= 1) {
                throw ValidationException::withMessages(['role' => 'The last administrator cannot be demoted.']);
            }

            $target->update(['role' => $data['role']]);
        });

        return back()->with('success', 'Account role updated.');
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        abort_if($user->role === 'admin' || $user->id === $request->user()->id, 403);
        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? 'Account reactivated.' : 'Account suspended. Their services are now hidden.');
    }

    public function categories(): View
    {
        return view('admin.categories', [
            'categories' => Category::withCount(['listings' => fn (Builder $query) => $query->withTrashed()])->orderBy('name')->paginate(20),
            'icons' => self::CATEGORY_ICONS,
            'colors' => self::CATEGORY_COLORS,
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $this->validatedCategory($request);
        $data['slug'] = $this->uniqueCategorySlug($data['name']);
        Category::create($data);

        return back()->with('success', 'Category created.');
    }

    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validatedCategory($request, $category);
        $data['slug'] = $this->uniqueCategorySlug($data['name'], $category);
        $category->update($data);

        return back()->with('success', 'Category updated.');
    }

    public function destroyCategory(Category $category): RedirectResponse
    {
        if ($category->listings()->withTrashed()->exists()) {
            return back()->withErrors(['category' => 'This category is in use. Reassign or remove its services before deleting it.']);
        }

        $category->delete();

        return back()->with('success', 'Category deleted.');
    }

    public function services(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);
        $query = ServiceListing::with(['provider', 'category']);

        if ($request->filled('q')) {
            $search = $filters['q'];
            $query->where(fn (Builder $builder) => $builder->where('title', 'like', '%'.$search.'%')
                ->orWhereHas('provider', fn (Builder $provider) => $provider->where('name', 'like', '%'.$search.'%')));
        }

        if ($request->filled('category')) {
            $query->where('category_id', $filters['category']);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $filters['status'] === 'active');
        }

        return view('admin.services', [
            'listings' => $query->latest()->paginate(15)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function editService(ServiceListing $listing): View
    {
        return view('admin.services-edit', [
            'listing' => $listing->load('provider', 'category'),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function updateService(Request $request, ServiceListing $listing): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'min:30', 'max:3000'],
            'price' => ['required', 'integer', 'between:100,1000000'],
            'pricing_note' => ['nullable', 'string', 'max:160'],
            'city' => ['required', 'string', 'max:80'],
            'is_active' => ['required', 'boolean'],
        ]);
        $listing->update($data);

        return redirect()->route('admin.services')->with('success', 'Service listing updated.');
    }

    public function toggleService(ServiceListing $listing): RedirectResponse
    {
        if (! $listing->is_active && ! $listing->provider()->where('is_active', true)->exists()) {
            return back()->withErrors(['service' => 'Reactivate the provider account before publishing this service.']);
        }

        $listing->update(['is_active' => ! $listing->is_active]);

        return back()->with('success', $listing->is_active ? 'Service published.' : 'Service hidden from the catalog.');
    }

    public function destroyService(ServiceListing $listing): RedirectResponse
    {
        $listing->delete();

        return back()->with('success', 'Service removed from the catalog. Existing bookings remain available.');
    }

    public function bookings(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(BookingStatus::class)],
        ]);
        $query = Booking::with(['customer', 'provider', 'serviceListing']);

        if ($request->filled('q')) {
            $search = $filters['q'];
            $reference = preg_match('/^HL-0*(\d+)$/i', $search, $matches) === 1 ? (int) $matches[1] : null;
            $query->where(function (Builder $builder) use ($reference, $search): void {
                if ($reference !== null) {
                    $builder->where('id', $reference);
                } else {
                    $builder->whereHas('customer', fn (Builder $customer) => $customer->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%'))
                        ->orWhereHas('provider', fn (Builder $provider) => $provider->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%'));
                }
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $filters['status']);
        }

        return view('admin.bookings', [
            'bookings' => $query->latest('starts_at')->paginate(15)->withQueryString(),
            'statuses' => BookingStatus::cases(),
        ]);
    }

    public function updateBookingStatus(Request $request, Booking $booking, AdminBookingService $service): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in([BookingStatus::Rejected->value, BookingStatus::Cancelled->value])]]);
        $service->updateStatus($booking, $request->user(), BookingStatus::from($data['status']));

        return back()->with('success', 'Booking status updated and recorded in its activity history.');
    }

    public function reviews(): View
    {
        return view('admin.reviews', [
            'reviews' => Review::with(['customer', 'booking.serviceListing', 'booking.provider'])->latest()->paginate(20),
        ]);
    }

    public function destroyReview(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Review removed.');
    }

    private function validatedCategory(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('categories', 'name')->ignore($category?->id)],
            'icon' => ['required', Rule::in(self::CATEGORY_ICONS)],
            'color' => ['required', Rule::in(self::CATEGORY_COLORS)],
        ]);
    }

    private function uniqueCategorySlug(string $name, ?Category $category = null): string
    {
        $base = Str::slug($name) ?: 'category';
        $slug = $base;
        $suffix = 2;

        while (Category::where('slug', $slug)->when($category, fn (Builder $query) => $query->where('id', '!=', $category->id))->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
