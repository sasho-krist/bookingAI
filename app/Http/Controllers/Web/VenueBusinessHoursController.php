<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\AuthorizesVenueAccess;
use App\Models\Venue;
use App\Services\VenueBusinessHoursScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VenueBusinessHoursController extends Controller
{
    use AuthorizesVenueAccess;

    public function __construct(
        private readonly VenueBusinessHoursScheduleService $businessHoursSchedule,
    ) {}

    public function edit(Venue $venue): View
    {
        $this->authorizeVenue($venue);

        return view('pages.venue-business-hours-edit', [
            'venue' => $venue,
            'initialDays' => $this->businessHoursSchedule->initialDaysForVenue($venue),
        ]);
    }

    public function update(Request $request, Venue $venue): RedirectResponse
    {
        $this->authorizeVenue($venue);

        $businessHours = $this->businessHoursSchedule->validatedFromRequest($request);

        $venue->update(['business_hours' => $businessHours]);

        return redirect()
            ->route('venues.show', $venue)
            ->with('status', 'Работното време е записано.');
    }
}
