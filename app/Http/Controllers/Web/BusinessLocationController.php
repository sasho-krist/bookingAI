<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\RedirectsAfterBookingFlow;
use App\Models\Business;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessLocationController extends Controller
{
    use RedirectsAfterBookingFlow;

    public function create(Business $business): View
    {
        $this->authorizeBusiness($business);

        return view('pages.business-locations-create', compact('business'));
    }

    public function store(Request $request, Business $business): RedirectResponse
    {
        $this->authorizeBusiness($business);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:64'],
            'timezone' => ['required', 'string', 'max:64'],
            'return' => ['nullable', 'string', 'in:booking'],
        ]);

        $venue = $business->venues()->create([
            'name' => $data['name'],
            'type' => $data['type'] ?? 'generic',
            'timezone' => $data['timezone'],
            'business_hours' => null,
        ]);

        return $this->intendedBookingCreateRedirect(
            $request,
            'Локацията е създадена. Изберете я във формата за резервация.',
            redirect()->route('venues.show', $venue)->with('status', 'Локацията е създадена.'),
        );
    }

    private function authorizeBusiness(Business $business): void
    {
        if ($business->user_id !== null && (int) $business->user_id !== (int) auth()->id()) {
            abort(403);
        }
    }
}
