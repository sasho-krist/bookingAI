<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessVenueApiController extends Controller
{
    public function store(Request $request, Business $business): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:64'],
            'timezone' => ['required', 'string', 'max:64'],
        ]);

        $venue = $business->venues()->create([
            'name' => $data['name'],
            'type' => $data['type'] ?? 'generic',
            'timezone' => $data['timezone'],
            'business_hours' => null,
        ]);

        return response()->json(['data' => $venue], 201);
    }
}
