<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\AuthorizesVenueAccess;
use App\Models\Booking;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class VenueShowController extends Controller
{
    use AuthorizesVenueAccess;

    public function show(Request $request, Venue $venue): View
    {
        $this->authorizeVenue($venue);

        $monthInput = $request->query('month');
        try {
            $month = $monthInput
                ? Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth()
                : now()->startOfMonth();
        } catch (\Throwable) {
            $month = now()->startOfMonth();
        }

        $monthStart = $month->copy()->startOfMonth()->startOfDay();
        $monthEnd = $month->copy()->endOfMonth()->endOfDay();

        $bookings = Booking::query()
            ->where('venue_id', $venue->id)
            ->whereBetween('starts_at', [$monthStart, $monthEnd])
            ->with(['service', 'customer'])
            ->orderBy('starts_at')
            ->get();

        $bookingsByDay = $bookings->groupBy(function (Booking $b) use ($venue) {
            return $b->starts_at->timezone($venue->timezone)->format('Y-m-d');
        });

        $calendarWeeks = $this->buildCalendarWeeks($month, $venue->timezone, $bookingsByDay);

        $prevMonth = $month->copy()->subMonth()->format('Y-m');
        $nextMonth = $month->copy()->addMonth()->format('Y-m');

        $venue->load(['business.businessType', 'services']);

        return view('pages.venues-show', [
            'venue' => $venue,
            'monthLabel' => $month->locale('bg')->translatedFormat('F Y'),
            'monthParam' => $month->format('Y-m'),
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
            'bookings' => $bookings,
            'bookingsByDay' => $bookingsByDay,
            'calendarWeeks' => $calendarWeeks,
        ]);
    }

    /**
     * @param  \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, Booking>>  $bookingsByDay
     * @return list<list<array{date: string, inMonth: bool, count: int, dayNum: int}>>
     */
    private function buildCalendarWeeks(Carbon $month, string $tz, $bookingsByDay): array
    {
        $first = $month->copy()->timezone($tz)->startOfMonth();
        $last = $month->copy()->timezone($tz)->endOfMonth();
        $cursor = $first->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $last->copy()->endOfWeek(Carbon::SUNDAY);

        $weeks = [];
        while ($cursor->lte($gridEnd)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $inMonth = $cursor->gte($first) && $cursor->lte($last);
                $dateKey = $cursor->format('Y-m-d');
                $count = $inMonth && $bookingsByDay->has($dateKey)
                    ? $bookingsByDay->get($dateKey)->count()
                    : 0;

                $week[] = [
                    'date' => $dateKey,
                    'inMonth' => $inMonth,
                    'count' => $count,
                    'dayNum' => (int) $cursor->format('j'),
                ];
                $cursor->addDay();
            }
            $weeks[] = $week;
        }

        return $weeks;
    }
}
