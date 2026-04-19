<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Venue;
use App\Services\Ai\BookingAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AiBookingController extends Controller
{
    public function __construct(
        private readonly BookingAiService $ai,
    ) {}

    public function suggestSlots(Request $request): JsonResponse
    {
        $data = $request->validate([
            'venue_id' => ['required', 'integer', 'exists:venues,id'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'preferred_date' => ['nullable', 'date'],
            'preferences' => ['nullable', 'array'],
            'history_days' => ['nullable', 'integer', 'min:7', 'max:365'],
            'horizon_days' => ['nullable', 'integer', 'min:1', 'max:60'],
        ]);

        $venue = Venue::accessibleByCurrentUser()->whereKey((int) $data['venue_id'])->firstOrFail();
        if (! empty($data['preferred_date'])) {
            $picked = Carbon::parse($data['preferred_date'], $venue->timezone)->startOfDay();
            $todayLocal = Carbon::now($venue->timezone)->startOfDay();
            if ($picked->lt($todayLocal)) {
                return response()->json([
                    'message' => 'Датата за препоръки не може да е в миналото за часовата зона на локацията.',
                ], 422);
            }
        }

        $historyDays = $data['history_days'] ?? 60;
        $horizonDays = $data['horizon_days'] ?? 14;
        $from = now()->subDays($historyDays);
        $to = ($data['preferred_date'] ?? null)
            ? Carbon::parse($data['preferred_date'], $venue->timezone)->endOfDay()->addDays($horizonDays)
            : now()->addDays($horizonDays);

        $context = $this->ai->buildVenueContext((int) $data['venue_id'], $from, $to);
        $payload = [
            ...$context,
            ...$this->ai->schedulingConstraintsForVenue($venue),
            'task' => 'suggest_best_slots',
            'service_id' => $data['service_id'] ?? null,
            'preferred_date' => $data['preferred_date'] ?? null,
            'preferences' => $data['preferences'] ?? new \stdClass,
        ];

        $slots = $this->ai->suggestBestSlots($payload);
        $slots = $this->ai->filterSlotSuggestionsNotBefore($slots, Carbon::now($venue->timezone));

        return response()->json(['data' => $slots]);
    }

    public function forecastLoad(Request $request): JsonResponse
    {
        $data = $request->validate([
            'venue_id' => ['required', 'integer', 'exists:venues,id'],
            'history_days' => ['nullable', 'integer', 'min:14', 'max:730'],
            'future_days' => ['nullable', 'integer', 'min:1', 'max:60'],
        ]);

        Venue::accessibleByCurrentUser()->whereKey((int) $data['venue_id'])->firstOrFail();

        $historyDays = $data['history_days'] ?? 90;
        $futureDays = $data['future_days'] ?? 14;
        $from = now()->subDays($historyDays);
        $to = now()->addDays($futureDays);

        $context = $this->ai->buildVenueContext((int) $data['venue_id'], $from, $to);
        $payload = [
            ...$context,
            'task' => 'forecast_load',
        ];

        return response()->json(['data' => $this->ai->forecastLoad($payload)]);
    }

    public function reschedule(Request $request): JsonResponse
    {
        $data = $request->validate([
            'venue_id' => ['required', 'integer', 'exists:venues,id'],
            'problem' => ['required', 'string', 'max:2000'],
            'affected_booking_ids' => ['nullable', 'array'],
            'affected_booking_ids.*' => ['integer', 'exists:bookings,id'],
            'horizon_days' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        Venue::accessibleByCurrentUser()->whereKey((int) $data['venue_id'])->firstOrFail();

        $horizon = $data['horizon_days'] ?? 7;
        $context = $this->ai->buildVenueContext((int) $data['venue_id'], now()->startOfDay(), now()->addDays($horizon));

        $venueId = (int) $data['venue_id'];
        $affected = collect($data['affected_booking_ids'] ?? [])
            ->map(fn (int $id): ?Booking => Booking::query()->with(['service', 'customer'])->find($id))
            ->filter(fn (?Booking $b): bool => $b !== null && (int) $b->venue_id === $venueId)
            ->values();

        $payload = [
            ...$context,
            'task' => 'auto_reschedule',
            'problem' => $data['problem'],
            'affected_bookings' => $affected->map(fn (Booking $b): array => [
                'id' => $b->id,
                'starts_at' => $b->starts_at?->toIso8601String(),
                'ends_at' => $b->ends_at?->toIso8601String(),
                'status' => $b->status,
                'service' => $b->service?->only(['id', 'name', 'duration_minutes']),
                'customer' => $b->customer?->only(['id', 'name']),
            ])->all(),
        ];

        return response()->json(['data' => $this->ai->suggestReschedule($payload)]);
    }

    public function chat(Request $request): JsonResponse
    {
        $data = $request->validate([
            'venue_id' => ['required', 'integer', 'exists:venues,id'],
            'messages' => ['required', 'array', 'min:1'],
            'messages.*.role' => ['required', 'string', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string', 'max:8000'],
        ]);

        Venue::accessibleByCurrentUser()->whereKey((int) $data['venue_id'])->firstOrFail();

        $context = $this->ai->buildVenueContext((int) $data['venue_id'], now()->subDays(30), now()->addDays(14));
        $reply = $this->ai->bookingChatbot($data['messages'], $context);

        return response()->json([
            'data' => [
                'message' => [
                    'role' => 'assistant',
                    'content' => $reply,
                ],
            ],
        ]);
    }

    public function noShow(Request $request): JsonResponse
    {
        $data = $request->validate([
            'booking_id' => ['required', 'integer', 'exists:bookings,id'],
            'history_days' => ['nullable', 'integer', 'min:30', 'max:730'],
        ]);

        $booking = Booking::query()
            ->with(['venue', 'service', 'customer'])
            ->findOrFail($data['booking_id']);

        Venue::accessibleByCurrentUser()->whereKey($booking->venue_id)->firstOrFail();

        $days = $data['history_days'] ?? 180;
        $from = now()->subDays($days);

        $customerBookings = $booking->customer_id
            ? Booking::query()
                ->where('customer_id', $booking->customer_id)
                ->where('starts_at', '>=', $from)
                ->orderByDesc('starts_at')
                ->limit(50)
                ->get(['id', 'starts_at', 'ends_at', 'status', 'attended'])
            : collect();

        $payload = [
            'task' => 'no_show_risk',
            'booking' => [
                'id' => $booking->id,
                'starts_at' => $booking->starts_at?->toIso8601String(),
                'ends_at' => $booking->ends_at?->toIso8601String(),
                'status' => $booking->status,
                'attended' => $booking->attended,
                'venue' => $booking->venue?->only(['id', 'name', 'type']),
                'service' => $booking->service?->only(['id', 'name', 'duration_minutes']),
                'customer' => $booking->customer?->only(['id', 'name', 'email', 'phone']),
            ],
            'customer_recent_bookings' => $customerBookings->map(fn (Booking $b): array => [
                'id' => $b->id,
                'starts_at' => $b->starts_at?->toIso8601String(),
                'status' => $b->status,
                'attended' => $b->attended,
            ])->values()->all(),
        ];

        return response()->json(['data' => $this->ai->predictNoShow($payload)]);
    }
}
