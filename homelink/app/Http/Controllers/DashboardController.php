<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        if ($request->user()->role === 'admin') {
            return redirect()->route('admin.index');
        }
        $column = $request->user()->role === 'provider' ? 'provider_id' : 'customer_id';
        $base = Booking::where($column, $request->user()->id);

        return view('dashboard', [
            'counts' => ['total' => (clone $base)->count(), 'upcoming' => (clone $base)->whereIn('status', ['pending', 'accepted', 'in_progress'])->count(), 'completed' => (clone $base)->where('status', 'completed')->count(), 'value' => (clone $base)->where('status', 'completed')->sum('quoted_price')],
            'bookings' => (clone $base)->with(['customer', 'provider', 'serviceListing.category'])->orderByDesc('created_at')->limit(5)->get(),
        ]);
    }
}
