<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\AuthorizesBusinessAccess;
use App\Http\Controllers\Web\Concerns\AuthorizesVenueAccess;
use App\Models\Business;
use App\Models\BusinessType;
use App\Models\Venue;
use App\Services\VenueBusinessHoursScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SetupWizardController extends Controller
{
    use AuthorizesBusinessAccess;
    use AuthorizesVenueAccess;

    public function __construct(
        private readonly VenueBusinessHoursScheduleService $businessHoursSchedule,
    ) {}

    public function index(): RedirectResponse
    {
        if (! session()->has('setup.business_id')) {
            return redirect()->route('setup.business');
        }

        if (! session()->has('setup.venue_id')) {
            return redirect()->route('setup.location');
        }

        $venue = Venue::query()->find(session('setup.venue_id'));
        if ($venue === null) {
            session()->forget('setup.venue_id');

            return redirect()->route('setup.location');
        }

        if (! $venue->services()->exists()) {
            return redirect()->route('setup.service');
        }

        return redirect()->route('setup.hours');
    }

    public function business(): View
    {
        $businessTypes = BusinessType::query()->orderBy('name')->get();

        return view('pages.setup.business', [
            'step' => 1,
            'businessTypes' => $businessTypes,
        ]);
    }

    public function storeBusiness(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_type_id' => ['required', 'integer', 'exists:business_types,id'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
        ]);

        $business = Business::query()->create([
            'name' => $data['name'],
            'business_type_id' => $data['business_type_id'],
            'user_id' => $request->user()->id,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);

        session([
            'setup.business_id' => $business->id,
        ]);
        session()->forget('setup.venue_id');

        return redirect()
            ->route('setup.location')
            ->with('status', 'Стъпка 1 готова. Добавете локация.');
    }

    public function location(): View|RedirectResponse
    {
        $r = $this->redirectUnlessBusinessSession();
        if ($r instanceof RedirectResponse) {
            return $r;
        }

        $business = Business::query()->with('businessType')->findOrFail(session('setup.business_id'));
        $this->authorizeBusiness($business);

        return view('pages.setup.location', [
            'step' => 2,
            'business' => $business,
        ]);
    }

    public function storeLocation(Request $request): RedirectResponse
    {
        $r = $this->redirectUnlessBusinessSession();
        if ($r instanceof RedirectResponse) {
            return $r;
        }

        $business = Business::query()->findOrFail(session('setup.business_id'));
        $this->authorizeBusiness($business);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:64'],
            'timezone' => ['required', 'string', 'max:64'],
        ]);

        $venue = Venue::query()->create([
            'business_id' => $business->id,
            'name' => $data['name'],
            'type' => $data['type'] ?? 'generic',
            'timezone' => $data['timezone'],
            'business_hours' => null,
        ]);

        session(['setup.venue_id' => $venue->id]);

        return redirect()
            ->route('setup.service')
            ->with('status', 'Стъпка 2 готова. Добавете поне една услуга.');
    }

    public function service(): View|RedirectResponse
    {
        $r = $this->redirectUnlessVenueSession();
        if ($r instanceof RedirectResponse) {
            return $r;
        }

        $venue = $this->venueForSetup();

        $venue->load('business.businessType');

        return view('pages.setup.service', [
            'step' => 3,
            'venue' => $venue,
        ]);
    }

    public function storeService(Request $request): RedirectResponse
    {
        $r = $this->redirectUnlessVenueSession();
        if ($r instanceof RedirectResponse) {
            return $r;
        }

        $venue = $this->venueForSetup();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
        ]);

        $venue->services()->create($data);

        return redirect()
            ->route('setup.hours')
            ->with('status', 'Стъпка 3 готова. Задайте работно време.');
    }

    public function hours(): View|RedirectResponse
    {
        $r = $this->redirectUnlessVenueSession();
        if ($r instanceof RedirectResponse) {
            return $r;
        }

        $venue = $this->venueForSetup();

        if (! $venue->services()->exists()) {
            return redirect()
                ->route('setup.service')
                ->with('error', 'Първо добавете услуга.');
        }

        $venue->load('business.businessType');

        return view('pages.setup.hours', [
            'step' => 4,
            'venue' => $venue,
            'initialDays' => $this->businessHoursSchedule->initialDaysForVenue($venue),
        ]);
    }

    public function storeHours(Request $request): RedirectResponse
    {
        $r = $this->redirectUnlessVenueSession();
        if ($r instanceof RedirectResponse) {
            return $r;
        }

        $venue = $this->venueForSetup();

        if (! $venue->services()->exists()) {
            return redirect()
                ->route('setup.service')
                ->with('error', 'Първо добавете услуга.');
        }

        $businessHours = $this->businessHoursSchedule->validatedFromRequest($request);

        $venue->update(['business_hours' => $businessHours]);

        session()->forget(['setup.business_id', 'setup.venue_id']);

        return redirect()
            ->route('venues.show', $venue)
            ->with('status', 'Първоначалната настройка е завършена. Локацията е готова за резервации.');
    }

    public function reset(): RedirectResponse
    {
        session()->forget(['setup.business_id', 'setup.venue_id']);

        return redirect()
            ->route('setup.business')
            ->with('status', 'Започнахте отначало.');
    }

    private function redirectUnlessBusinessSession(): ?RedirectResponse
    {
        if (! session()->has('setup.business_id')) {
            return redirect()->route('setup.business')->with('error', 'Започнете от стъпка 1 — създайте бизнес.');
        }

        return null;
    }

    private function redirectUnlessVenueSession(): ?RedirectResponse
    {
        $b = $this->redirectUnlessBusinessSession();
        if ($b instanceof RedirectResponse) {
            return $b;
        }

        if (! session()->has('setup.venue_id')) {
            return redirect()->route('setup.location')->with('error', 'Първо добавете локация.');
        }

        return null;
    }

    private function venueForSetup(): Venue
    {
        $venue = Venue::query()->findOrFail(session('setup.venue_id'));
        $this->authorizeVenue($venue);

        return $venue;
    }
}
