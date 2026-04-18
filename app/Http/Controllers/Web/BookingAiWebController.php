<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Services\Ai\BookingAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Throwable;

class BookingAiWebController extends Controller
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

        $venue = Venue::query()->findOrFail((int) $data['venue_id']);
        if (! empty($data['preferred_date'])) {
            $picked = Carbon::parse($data['preferred_date'], $venue->timezone)->startOfDay();
            $todayLocal = Carbon::now($venue->timezone)->startOfDay();
            if ($picked->lt($todayLocal)) {
                return response()->json([
                    'message' => 'Датата за препоръки не може да е в миналото за часовата зона на локацията.',
                ], 422);
            }
        }

        try {
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
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'AI заявката не успя. Проверете OPENAI_API_KEY и мрежата.',
            ], 502);
        }
    }

    public function forecastLoad(Request $request): JsonResponse
    {
        $data = $request->validate([
            'venue_id' => ['required', 'integer', 'exists:venues,id'],
            'history_days' => ['nullable', 'integer', 'min:14', 'max:730'],
            'future_days' => ['nullable', 'integer', 'min:1', 'max:60'],
        ]);

        try {
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
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'AI заявката не успя. Проверете OPENAI_API_KEY и мрежата.',
            ], 502);
        }
    }
}
