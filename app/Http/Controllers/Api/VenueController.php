<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\AuthorizesVenueAccess;
use App\Models\Business;
use App\Models\Venue;
use App\Services\VenueBusinessHoursScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    use AuthorizesVenueAccess;

    public function __construct(
        private readonly VenueBusinessHoursScheduleService $businessHoursSchedule,
    ) {}

    public function index(): JsonResponse
    {
        $venues = Venue::accessibleByCurrentUser()
            ->with(['business.businessType'])
            ->withCount(['services', 'bookings'])
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $venues]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:64'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'business_hours' => ['nullable', 'array'],
            'business_id' => ['nullable', 'integer', 'exists:businesses,id'],
        ]);

        if (! empty($data['business_id'])) {
            Business::accessibleByCurrentUser()->whereKey($data['business_id'])->firstOrFail();
        }

        $venue = Venue::query()->create([
            'business_id' => $data['business_id'] ?? null,
            'name' => $data['name'],
            'type' => $data['type'] ?? 'generic',
            'timezone' => $data['timezone'] ?? 'UTC',
            'business_hours' => $data['business_hours'] ?? null,
        ]);

        return response()->json(['data' => $venue], 201);
    }

    public function show(Venue $venue): JsonResponse
    {
        $this->authorizeVenue($venue);

        $venue->load(['business.businessType', 'services']);

        return response()->json(['data' => $venue]);
    }

    public function update(Request $request, Venue $venue): JsonResponse
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

        return response()->json(['data' => $venue->fresh()]);
    }

    public function destroy(Venue $venue): JsonResponse
    {
        $this->authorizeVenue($venue);

        $venue->delete();

        return response()->json(['message' => 'Локацията е изтрита заедно с услугите и резервациите към нея.']);
    }

    public function updateBusinessHours(Request $request, Venue $venue): JsonResponse
    {
        $this->authorizeVenue($venue);

        $businessHours = $this->businessHoursSchedule->validatedFromRequest($request);
        $venue->update(['business_hours' => $businessHours]);

        return response()->json(['data' => $venue->fresh()]);
    }
}
