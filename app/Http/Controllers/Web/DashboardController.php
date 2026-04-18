<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $venues = Venue::accessibleByCurrentUser()
            ->with([
                'business.businessType',
                'services' => fn ($q) => $q->orderBy('name'),
            ])
            ->withCount('bookings')
            ->orderBy('name')
            ->get();

        return view('pages.dashboard', compact('venues'));
    }
}
