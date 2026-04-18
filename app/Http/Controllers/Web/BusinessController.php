<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\RedirectsAfterBookingFlow;
use App\Models\Business;
use App\Models\BusinessType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessController extends Controller
{
    use RedirectsAfterBookingFlow;

    public function index(): View
    {
        $businesses = Business::query()
            ->accessibleByCurrentUser()
            ->with(['businessType'])
            ->withCount('venues')
            ->orderBy('name')
            ->get();

        return view('pages.businesses-index', compact('businesses'));
    }

    public function create(): View
    {
        $businessTypes = BusinessType::query()->orderBy('name')->get();

        return view('pages.businesses-create', compact('businessTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_type_id' => ['required', 'integer', 'exists:business_types,id'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'return' => ['nullable', 'string', 'in:booking'],
        ]);

        $business = Business::query()->create([
            'name' => $data['name'],
            'business_type_id' => $data['business_type_id'],
            'user_id' => $request->user()->id,
            'email' => isset($data['email']) && $data['email'] !== '' ? $data['email'] : null,
            'phone' => isset($data['phone']) && $data['phone'] !== '' ? $data['phone'] : null,
        ]);

        return $this->intendedBookingCreateRedirect(
            $request,
            'Бизнесът е създаден. Изберете го във формата за резервация.',
            redirect()->route('businesses.show', $business)->with('status', 'Бизнесът е създаден.'),
        );
    }

    public function show(Business $business): View
    {
        $this->authorizeBusiness($business);

        $business->load([
            'businessType',
            'venues' => fn ($q) => $q->orderBy('name')->with(['services' => fn ($s) => $s->orderBy('name')])->withCount(['bookings']),
        ]);

        return view('pages.businesses-show', compact('business'));
    }

    public function edit(Business $business): View
    {
        $this->authorizeBusiness($business);

        $businessTypes = BusinessType::query()->orderBy('name')->get();

        return view('pages.businesses-edit', compact('business', 'businessTypes'));
    }

    public function update(Request $request, Business $business): RedirectResponse
    {
        $this->authorizeBusiness($business);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_type_id' => ['required', 'integer', 'exists:business_types,id'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
        ]);

        $business->update([
            'name' => $data['name'],
            'business_type_id' => $data['business_type_id'],
            'email' => isset($data['email']) && $data['email'] !== '' ? $data['email'] : null,
            'phone' => isset($data['phone']) && $data['phone'] !== '' ? $data['phone'] : null,
        ]);

        return redirect()
            ->route('businesses.show', $business)
            ->with('status', 'Бизнесът е обновен.');
    }

    public function destroy(Business $business): RedirectResponse
    {
        $this->authorizeBusiness($business);

        $business->delete();

        return redirect()
            ->route('home')
            ->with('status', 'Бизнесът е изтрит. Свързаните локации остават без избран бизнес.');
    }

    private function authorizeBusiness(Business $business): void
    {
        if ($business->user_id !== null && (int) $business->user_id !== (int) auth()->id()) {
            abort(403);
        }
    }
}
