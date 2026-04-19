<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\AuthorizesVenueAccess;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{
    use AuthorizesVenueAccess;

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'venue_id' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $venueIds = Venue::accessibleByCurrentUser()->pluck('id');

        $q = Booking::query()
            ->whereIn('venue_id', $venueIds)
            ->with(['venue', 'service', 'customer'])
            ->orderByDesc('starts_at');

        if ($request->filled('venue_id')) {
            $vid = (int) $request->input('venue_id');
            Venue::accessibleByCurrentUser()->whereKey($vid)->firstOrFail();
            $q->where('venue_id', $vid);
        }

        if ($request->filled('from')) {
            $q->where('starts_at', '>=', Carbon::parse($request->string('from')));
        }
        if ($request->filled('to')) {
            $q->where('starts_at', '<=', Carbon::parse($request->string('to')));
        }

        $limit = (int) ($request->input('limit') ?? 100);

        return response()->json(['data' => $q->limit($limit)->get()]);
    }

    public function venueIndex(Request $request, Venue $venue): JsonResponse
    {
        $this->authorizeVenue($venue);

        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $q = $venue->bookings()->with(['service', 'customer'])->orderBy('starts_at');

        if ($request->filled('from')) {
            $q->where('starts_at', '>=', Carbon::parse($request->string('from')));
        }
        if ($request->filled('to')) {
            $q->where('starts_at', '<=', Carbon::parse($request->string('to')));
        }

        return response()->json(['data' => $q->get()]);
    }

    public function store(Request $request, Venue $venue): JsonResponse
    {
        $this->authorizeVenue($venue);

        $data = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'starts_at' => ['required', 'date'],
            'status' => ['nullable', 'string', 'max:32'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $service = Service::query()->whereKey($data['service_id'])->where('venue_id', $venue->id)->firstOrFail();
        $startsAt = Carbon::parse($data['starts_at']);
        $endsAt = $startsAt->copy()->addMinutes($service->duration_minutes);

        if (! empty($data['customer_id'])) {
            Customer::query()->whereKey($data['customer_id'])->firstOrFail();
        }

        $booking = Booking::query()->create([
            'venue_id' => $venue->id,
            'service_id' => $service->id,
            'customer_id' => $data['customer_id'] ?? null,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => $data['status'] ?? 'confirmed',
            'notes' => $data['notes'] ?? null,
        ]);

        $booking->load(['service', 'customer']);

        return response()->json(['data' => $booking], 201);
    }

    public function update(Request $request, Booking $booking): JsonResponse
    {
        $booking->loadMissing('service');

        $data = $request->validate([
            'starts_at' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:32'],
            'attended' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if (array_key_exists('starts_at', $data) && $data['starts_at'] !== null) {
            $startsAt = Carbon::parse($data['starts_at']);
            $booking->starts_at = $startsAt;
            $duration = $booking->service !== null ? $booking->service->duration_minutes : 30;
            $booking->ends_at = $startsAt->copy()->addMinutes($duration);
        }

        if (array_key_exists('status', $data)) {
            $booking->status = $data['status'];
        }
        if (array_key_exists('attended', $data)) {
            $booking->attended = $data['attended'];
        }
        if (array_key_exists('notes', $data)) {
            $booking->notes = $data['notes'];
        }

        $booking->save();
        $booking->load(['venue', 'service', 'customer']);

        return response()->json(['data' => $booking]);
    }

    public function destroy(Booking $booking): JsonResponse
    {
        $booking->delete();

        return response()->json(['message' => 'Резервацията е изтрита.']);
    }
}
