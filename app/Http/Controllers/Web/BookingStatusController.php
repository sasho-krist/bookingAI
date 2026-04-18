<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;

class BookingStatusController extends Controller
{
    public function approve(Booking $booking): RedirectResponse
    {
        $this->authorizeBooking($booking);

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Само чакащи резервации могат да бъдат одобрени.');
        }

        $booking->update(['status' => 'confirmed']);

        return back()->with('status', 'Резервацията е одобрена (потвърдена).');
    }

    public function reject(Booking $booking): RedirectResponse
    {
        $this->authorizeBooking($booking);

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Само чакащи резервации могат да бъдат отказани.');
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('status', 'Резервацията е отказана.');
    }

    private function authorizeBooking(Booking $booking): void
    {
        $allowed = Venue::accessibleByCurrentUser()->whereKey($booking->venue_id)->exists();
        if (! $allowed) {
            abort(403);
        }
    }
}
