<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ServiceListing;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate(['q' => ['nullable', 'string', 'max:100'], 'role' => ['nullable', Rule::in(['customer', 'provider', 'admin'])]]);
        $query = User::query();
        if ($request->filled('q')) {
            $query->where(fn ($q) => $q->where('name', 'like', '%'.$request->input('q').'%')->orWhere('email', 'like', '%'.$request->input('q').'%'));
        }
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        return view('admin', ['users' => $query->latest()->paginate(12)->withQueryString(), 'stats' => ['users' => User::count(), 'services' => ServiceListing::available()->count(), 'bookings' => Booking::count(), 'pending' => Booking::where('status', 'pending')->count()], 'recent' => Booking::with(['customer', 'provider'])->latest()->limit(5)->get()]);
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        abort_if($user->role === 'admin' || $user->id === $request->user()->id, 403);
        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? 'Account reactivated.' : 'Account suspended. Their services are now hidden.');
    }
}
