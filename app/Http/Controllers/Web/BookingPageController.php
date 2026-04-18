<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Venue;
use Illuminate\View\View;

class BookingPageController extends Controller
{
    public function index(): View
    {
        $venueIds = Venue::accessibleByCurrentUser()->pluck('id');

        $bookings = Booking::query()
            ->whereIn('venue_id', $venueIds)
            ->with(['venue.business', 'service', 'customer'])
            ->orderByDesc('starts_at')
            ->limit(50)
            ->get();

        return view('pages.bookings', compact('bookings'));
    }
}
