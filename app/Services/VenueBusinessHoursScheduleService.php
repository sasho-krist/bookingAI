<?php

namespace App\Services;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class VenueBusinessHoursScheduleService
{
    /** @var list<string> */
    private const DAY_KEYS = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

    /**
     * @return array<string, array{active: bool, open: string, close: string}>
     */
    public function initialDaysForVenue(Venue $venue): array
    {
        $defaults = [];
        foreach (self::DAY_KEYS as $k) {
            $defaults[$k] = [
                'active' => false,
                'open' => '09:00',
                'close' => '18:00',
            ];
        }

        $bh = $venue->business_hours;
        if (is_array($bh) && $bh !== []) {
            foreach ($bh as $day => $pair) {
                if (! is_string($day) || ! isset($defaults[$day])) {
                    continue;
                }
                if (is_array($pair) && count($pair) >= 2 && is_string($pair[0]) && is_string($pair[1])) {
                    $defaults[$day] = [
                        'active' => true,
                        'open' => substr($pair[0], 0, 5),
                        'close' => substr($pair[1], 0, 5),
                    ];
                }
            }

            return $defaults;
        }

        foreach (['mon', 'tue', 'wed', 'thu', 'fri'] as $d) {
            $defaults[$d]['active'] = true;
        }

        return $defaults;
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public function validatedFromRequest(Request $request): array
    {
        $rules = [];
        foreach (self::DAY_KEYS as $day) {
            $rules["days.$day.active"] = ['nullable'];
            $rules["days.$day.open"] = ['nullable', 'date_format:H:i'];
            $rules["days.$day.close"] = ['nullable', 'date_format:H:i'];
        }

        $request->validate($rules);

        $out = [];

        foreach (self::DAY_KEYS as $day) {
            if (! $request->boolean("days.$day.active")) {
                continue;
            }

            $open = $request->input("days.$day.open");
            $close = $request->input("days.$day.close");

            if (! is_string($open) || $open === '' || ! is_string($close) || $close === '') {
                throw ValidationException::withMessages([
                    "days.$day.open" => 'Посочете начало и край за избраните работни дни.',
                ]);
            }

            $tOpen = strtotime($open);
            $tClose = strtotime($close);
            if ($tOpen === false || $tClose === false || $tClose <= $tOpen) {
                throw ValidationException::withMessages([
                    "days.$day.close" => 'Краят трябва да е след началото (смени без преминаване през полунощ не се поддържат).',
                ]);
            }

            $out[$day] = [$open, $close];
        }

        if ($out === []) {
            throw ValidationException::withMessages([
                'days' => 'Маркирайте поне един работен ден с часове.',
            ]);
        }

        return $out;
    }
}
