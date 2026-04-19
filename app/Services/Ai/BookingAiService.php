<?php

namespace App\Services\Ai;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Venue;
use App\Services\OpenAi\OpenAiClient;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Throwable;

class BookingAiService
{
    public function __construct(
        private readonly OpenAiClient $openAi,
    ) {}

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function suggestBestSlots(array $input): array
    {
        $system = <<<'TXT'
You are an appointment scheduling optimizer for restaurants, salons, and clinics.
Given venue hours, service duration, existing bookings, and preferences, propose the best available time slots.

CRITICAL scheduling rules:
- ONLY propose NEW reservations from scheduling_constraints.minimum_new_booking_start_iso onward (same instant semantics as ISO times).
- NEVER suggest start_iso or end_iso in the past relative to that minimum. Ignore empty-looking gaps in booking history from past months/years — those periods are NOT bookable anymore.
- Use bookings only to infer busy patterns and conflicts for FUTURE scheduling within the suggestion horizon.
- If preferred_date is set, prefer slots on/near that date as long as it is still >= the minimum instant.

Return strict JSON with keys: recommended_slots (array of {start_iso, end_iso, score_0_to_100, reason}),
alternatives (array same shape, shorter list), assumptions (array of strings).
Use ISO-8601 only for start_iso and end_iso (venue local context; include offset or Z as appropriate).
Every human-readable string (reason, each assumptions item) MUST be written in Bulgarian.
TXT;

        $content = $this->openAi->chatCompletion(
            [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => json_encode($input, JSON_THROW_ON_ERROR)],
            ],
            ['type' => 'json_object'],
        );

        return $this->decodeJsonObject($content);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function forecastLoad(array $input): array
    {
        $system = <<<'TXT'
You forecast booking load / busyness from historical booking timestamps and statuses.
Return strict JSON with keys: hourly_forecast (array of {hour_local, expected_load_0_to_100, note}),
peak_windows (array of {from_iso, to_iso, intensity_0_to_100}),
quiet_windows (same shape), narrative (string).
ISO-8601 only in from_iso and to_iso.
All natural language (narrative, each note, hour_local labels if textual) MUST be Bulgarian.
TXT;

        $content = $this->openAi->chatCompletion(
            [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => json_encode($input, JSON_THROW_ON_ERROR)],
            ],
            ['type' => 'json_object'],
        );

        return $this->decodeJsonObject($content);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function suggestReschedule(array $input): array
    {
        $system = <<<'TXT'
You suggest minimal-disruption reschedules when conflicts, delays, or cancellations occur.
Return strict JSON with keys: proposals (array of {booking_id, old_start_iso, new_start_iso, new_end_iso, impact, reason}),
declined_if_any (array of strings).
ISO-8601 only for *_iso fields. impact and reason and each declined_if_any line MUST be Bulgarian.
TXT;

        $content = $this->openAi->chatCompletion(
            [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => json_encode($input, JSON_THROW_ON_ERROR)],
            ],
            ['type' => 'json_object'],
        );

        return $this->decodeJsonObject($content);
    }

    /**
     * @param  list<array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $context
     */
    public function bookingChatbot(array $messages, array $context): string
    {
        $system = <<<'TXT'
You are a concise booking assistant. Reply ONLY in Bulgarian.
Help users pick services, times, and confirm details.
If information is missing, ask at most two clarifying questions (in Bulgarian).
Never invent confirmed reservations unless the context explicitly contains them.
Context JSON will include venue, services, and optional upcoming availability summary.
TXT;

        $payload = [
            ['role' => 'system', 'content' => $system."\n\nContext:\n".json_encode($context, JSON_THROW_ON_ERROR)],
            ...$messages,
        ];

        return $this->openAi->chatCompletion($payload);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function predictNoShow(array $input): array
    {
        $system = <<<'TXT'
You estimate no-show / late-cancel risk from customer and booking history signals.
Return strict JSON with keys: risk_0_to_100 (number), label (one of ниско|средно|високо),
drivers (array of {signal, weight_0_to_100}), mitigations (array of strings).
signal and mitigations MUST be Bulgarian.
TXT;

        $content = $this->openAi->chatCompletion(
            [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => json_encode($input, JSON_THROW_ON_ERROR)],
            ],
            ['type' => 'json_object'],
        );

        return $this->decodeJsonObject($content);
    }

    /**
     * @return array<string, mixed>
     */
    public function buildVenueContext(int $venueId, ?CarbonInterface $from = null, ?CarbonInterface $to = null): array
    {
        $venue = Venue::query()->with(['services'])->findOrFail($venueId);
        $from ??= now()->subDays(30);
        $to ??= now()->addDays(14);

        $bookings = Booking::query()
            ->where('venue_id', $venueId)
            ->whereBetween('starts_at', [$from, $to])
            ->with(['service:id,name,duration_minutes', 'customer:id,name,email,phone'])
            ->orderBy('starts_at')
            ->get();

        return [
            'venue' => [
                'id' => $venue->id,
                'name' => $venue->name,
                'type' => $venue->type,
                'timezone' => $venue->timezone,
                'business_hours' => $venue->business_hours,
            ],
            'services' => $venue->services->map(fn (Service $s): array => [
                'id' => $s->id,
                'name' => $s->name,
                'duration_minutes' => $s->duration_minutes,
            ])->values()->all(),
            'bookings_window' => [
                'from' => $from->toIso8601String(),
                'to' => $to->toIso8601String(),
            ],
            'bookings_window_note_bg' => 'Прозорецът може да включва минали резервации (за натоварване) и бъдещи (заетост). Това не означава, че миналите дати са свободни за нови резервации.',
            'bookings' => $bookings->map(fn (Booking $b): array => [
                'id' => $b->id,
                'starts_at' => $b->starts_at?->toIso8601String(),
                'ends_at' => $b->ends_at?->toIso8601String(),
                'status' => $b->status,
                'attended' => $b->attended,
                'service' => $b->service ? [
                    'id' => $b->service->id,
                    'name' => $b->service->name,
                    'duration_minutes' => $b->service->duration_minutes,
                ] : null,
                'customer' => $b->customer ? [
                    'id' => $b->customer->id,
                    'name' => $b->customer->name,
                ] : null,
            ])->values()->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJsonObject(string $content): array
    {
        $decoded = json_decode($content, true, flags: JSON_THROW_ON_ERROR);
        if (! is_array($decoded)) {
            throw new \RuntimeException('Model returned non-object JSON.');
        }

        return $decoded;
    }

    /**
     * За локация: минимален момент, от който AI трябва да предлага нови часове (местно време на venue).
     *
     * @return array{scheduling_constraints: array<string, string>}
     */
    public function schedulingConstraintsForVenue(Venue $venue): array
    {
        $nowLocal = Carbon::now($venue->timezone);

        return [
            'scheduling_constraints' => [
                'minimum_new_booking_start_iso' => $nowLocal->toIso8601String(),
                'venue_timezone' => $venue->timezone,
                'instruction_bg' => 'Всички препоръчани start_iso/end_iso трябва да са >= minimum_new_booking_start_iso. Не предлагай часове в миналото (включително по-рано днес).',
            ],
        ];
    }

    /**
     * Премахва слотове, които започват преди допустимия момент (защита срещу халюцинации на модела).
     *
     * @param  array<string, mixed>  $decoded
     * @return array<string, mixed>
     */
    public function filterSlotSuggestionsNotBefore(array $decoded, CarbonInterface $earliestAllowedStart): array
    {
        foreach (['recommended_slots', 'alternatives'] as $key) {
            if (! isset($decoded[$key]) || ! is_array($decoded[$key])) {
                continue;
            }
            $decoded[$key] = array_values(array_filter($decoded[$key], function ($slot) use ($earliestAllowedStart) {
                if (! is_array($slot)) {
                    return false;
                }
                $start = $slot['start_iso'] ?? null;
                if (! is_string($start) || $start === '') {
                    return false;
                }
                try {
                    return Carbon::parse($start)->greaterThanOrEqualTo($earliestAllowedStart);
                } catch (Throwable) {
                    return false;
                }
            }));
        }

        return $decoded;
    }
}
