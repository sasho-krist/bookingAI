<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\AuthorizesVenueAccess;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VenueManageController extends Controller
{
    use AuthorizesVenueAccess;

    public function edit(Venue $venue): View
    {
        $this->authorizeVenue($venue);

        $venue->load('business.businessType');

        return view('pages.venues-edit', compact('venue'));
    }

    public function update(Request $request, Venue $venue): RedirectResponse
    {
        $this->authorizeVenue($venue);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:64'],
            'timezone' => ['required', 'string', 'max:64'],
        ]);

        $venue->update([
            'name' => $data['name'],
            'type' => $data['type'] ?? 'generic',
            'timezone' => $data['timezone'],
        ]);

        return redirect()
            ->route('venues.show', $venue)
            ->with('status', 'Локацията е обновена.');
    }

    public function destroy(Venue $venue): RedirectResponse
    {
        $this->authorizeVenue($venue);

        $businessId = $venue->business_id;
        $venue->delete();

        return ($businessId
            ? redirect()->route('businesses.show', $businessId)
            : redirect()->route('venues.index'))
            ->with('status', 'Локацията е изтрита заедно с услугите и резервациите към нея.');
    }
}
