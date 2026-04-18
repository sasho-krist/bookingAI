<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\View\View;

class VenuePageController extends Controller
{
    public function index(): View
    {
        $venues = Venue::accessibleByCurrentUser()
            ->with(['business'])
            ->withCount(['bookings', 'services'])
            ->orderBy('name')
            ->get();

        return view('pages.venues', compact('venues'));
    }
}
