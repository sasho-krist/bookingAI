<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\AuthorizesVenueAccess;
use App\Models\Service;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use AuthorizesVenueAccess;

    public function index(Venue $venue): JsonResponse
    {
        $this->authorizeVenue($venue);

        return response()->json(['data' => $venue->services()->orderBy('name')->get()]);
    }

    public function store(Request $request, Venue $venue): JsonResponse
    {
        $this->authorizeVenue($venue);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
        ]);

        $service = $venue->services()->create($data);

        return response()->json(['data' => $service], 201);
    }

    public function show(Venue $venue, Service $service): JsonResponse
    {
        $this->authorizeVenue($venue);

        return response()->json(['data' => $service]);
    }

    public function update(Request $request, Venue $venue, Service $service): JsonResponse
    {
        $this->authorizeVenue($venue);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
        ]);

        $service->update($data);

        return response()->json(['data' => $service->fresh()]);
    }

    public function destroy(Venue $venue, Service $service): JsonResponse
    {
        $this->authorizeVenue($venue);

        $service->delete();

        return response()->json(['message' => 'Услугата е изтрита заедно с резервациите към нея.']);
    }
}
