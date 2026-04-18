<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\RedirectsAfterBookingFlow;
use App\Http\Controllers\Web\Concerns\AuthorizesVenueAccess;
use App\Models\Service;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VenueServiceController extends Controller
{
    use AuthorizesVenueAccess;
    use RedirectsAfterBookingFlow;

    public function create(Venue $venue): View
    {
        $this->authorizeVenue($venue);

        $venue->load('business.businessType');

        return view('pages.venue-services-create', compact('venue'));
    }

    public function store(Request $request, Venue $venue): RedirectResponse
    {
        $this->authorizeVenue($venue);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
            'return' => ['nullable', 'string', 'in:booking'],
        ]);

        $venue->services()->create([
            'name' => $data['name'],
            'duration_minutes' => $data['duration_minutes'],
        ]);

        return $this->intendedBookingCreateRedirect(
            $request,
            'Услугата е добавена. Изберете я във формата за резервация.',
            redirect()->route('venues.show', $venue)->with('status', 'Услугата е добавена.'),
        );
    }

    public function edit(Venue $venue, Service $service): View
    {
        abort_if((int) $service->venue_id !== (int) $venue->id, 404);
        $this->authorizeVenue($venue);

        $venue->load('business.businessType');

        return view('pages.venue-services-edit', compact('venue', 'service'));
    }

    public function update(Request $request, Venue $venue, Service $service): RedirectResponse
    {
        abort_if((int) $service->venue_id !== (int) $venue->id, 404);
        $this->authorizeVenue($venue);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
        ]);

        $service->update($data);

        return redirect()
            ->route('venues.show', $venue)
            ->with('status', 'Услугата е обновена.');
    }

    public function destroy(Venue $venue, Service $service): RedirectResponse
    {
        abort_if((int) $service->venue_id !== (int) $venue->id, 404);
        $this->authorizeVenue($venue);

        $service->delete();

        return redirect()
            ->route('venues.show', $venue)
            ->with('status', 'Услугата е изтрита заедно със свързаните резервации към нея.');
    }
}
