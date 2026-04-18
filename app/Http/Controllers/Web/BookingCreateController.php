<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingCreateController extends Controller
{
    public function create(): View
    {
        $businesses = Business::query()
            ->accessibleByCurrentUser()
            ->with([
                'venues' => fn ($q) => $q->orderBy('name')->with([
                    'services' => fn ($sq) => $sq->orderBy('name'),
                ]),
            ])
            ->orderBy('name')
            ->get();

        $orphanVenues = Venue::accessibleByCurrentUser()
            ->whereNull('business_id')
            ->with(['services' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        $mapVenueRow = static function (Venue $venue): array {
            return [
                'id' => $venue->id,
                'name' => $venue->name,
                'add_service_url' => route('venues.services.create', $venue),
                'services' => $venue->services->map(function (Service $service) {
                    return [
                        'id' => $service->id,
                        'label' => $service->name.' ('.$service->duration_minutes.' мин.)',
                    ];
                })->values()->all(),
            ];
        };

        $businessesTreePayload = $businesses->map(fn (Business $b) => [
            'id' => $b->id,
            'name' => $b->name,
            'venues' => $b->venues->map($mapVenueRow)->values()->all(),
        ])->values()->all();

        if ($orphanVenues->isNotEmpty()) {
            $businessesTreePayload[] = [
                'id' => '__orphan__',
                'name' => 'Локации без бизнес',
                'venues' => $orphanVenues->map($mapVenueRow)->values()->all(),
            ];
        }

        $allVenues = Venue::accessibleByCurrentUser()
            ->with(['services' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        $customers = Customer::query()->orderBy('name')->get();

        $prefillBusinessId = null;
        if (old('venue_id')) {
            $oldVenue = Venue::query()->find(old('venue_id'));
            if ($oldVenue !== null) {
                $prefillBusinessId = $oldVenue->business_id !== null ? (string) $oldVenue->business_id : '__orphan__';
            }
        }

        $aiAjaxConfig = [
            'slotsUrl' => route('ai.recommendations.slots'),
            'loadUrl' => route('ai.recommendations.load'),
            'venues' => $allVenues->map(fn (Venue $v) => [
                'id' => $v->id,
                'name' => $v->name,
                'timezone' => $v->timezone,
                'has_business_hours' => is_array($v->business_hours) && count($v->business_hours) > 0,
                'edit_business_hours_url' => route('venues.business-hours.edit', $v),
            ])->values()->all(),
        ];

        $firstBizForTpl = Business::query()->accessibleByCurrentUser()->orderBy('id')->first();
        $locationUrlTemplate = null;
        if ($firstBizForTpl !== null) {
            $locationUrlTemplate = preg_replace(
                '#/businesses/\d+/locations/create#',
                '/businesses/__BID__/locations/create',
                route('businesses.locations.create', $firstBizForTpl)
            );
        }

        $firstVenueForTpl = Venue::accessibleByCurrentUser()->orderBy('id')->first();
        $serviceUrlTemplate = null;
        if ($firstVenueForTpl !== null) {
            $serviceUrlTemplate = preg_replace(
                '#/locations/\d+/services/create#',
                '/locations/__VID__/services/create',
                route('venues.services.create', $firstVenueForTpl)
            );
        }

        $bookingFlowUrls = [
            'newBusinessUrl' => route('businesses.create', ['return' => 'booking']),
            'locationTemplate' => $locationUrlTemplate,
            'serviceTemplate' => $serviceUrlTemplate,
        ];

        return view('pages.bookings-create', [
            'businesses' => $businesses,
            'orphanVenues' => $orphanVenues,
            'businessesTreePayload' => $businessesTreePayload,
            'customers' => $customers,
            'prefillBusinessId' => $prefillBusinessId,
            'aiAjaxConfig' => $aiAjaxConfig,
            'bookingFlowUrls' => $bookingFlowUrls,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'venue_id' => ['required', 'integer', 'exists:venues,id'],
            'service_id' => [
                'required',
                'integer',
                Rule::exists('services', 'id')->where(fn ($q) => $q->where('venue_id', (int) $request->input('venue_id'))),
            ],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'starts_at' => ['required', 'date'],
            'status' => ['nullable', 'string', 'max:32'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $venue = Venue::query()->whereKey($data['venue_id'])->firstOrFail();
        $service = Service::query()
            ->whereKey($data['service_id'])
            ->where('venue_id', $venue->id)
            ->firstOrFail();

        if (! empty($data['customer_id'])) {
            Customer::query()->whereKey($data['customer_id'])->firstOrFail();
        }

        $startsAt = Carbon::parse($data['starts_at']);

        Booking::query()->create([
            'venue_id' => $venue->id,
            'service_id' => $service->id,
            'customer_id' => $data['customer_id'] ?? null,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addMinutes($service->duration_minutes),
            'status' => $data['status'] ?? 'confirmed',
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('bookings.index')
            ->with('status', 'Резервацията е създадена успешно.');
    }
}
