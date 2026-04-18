<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessApiController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Business::query()
            ->accessibleByCurrentUser()
            ->with(['businessType'])
            ->withCount('venues')
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_type_id' => ['required', 'integer', 'exists:business_types,id'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
        ]);

        $business = Business::query()->create([
            'name' => $data['name'],
            'business_type_id' => $data['business_type_id'],
            'user_id' => $request->user()->id,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);

        $business->load('businessType');

        return response()->json(['data' => $business], 201);
    }

    public function show(Business $business): JsonResponse
    {
        $business->load(['businessType', 'venues' => fn ($q) => $q->orderBy('name')->withCount(['services', 'bookings'])]);

        return response()->json(['data' => $business]);
    }

    public function update(Request $request, Business $business): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_type_id' => ['required', 'integer', 'exists:business_types,id'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
        ]);

        $business->update([
            'name' => $data['name'],
            'business_type_id' => $data['business_type_id'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);

        $business->load('businessType');

        return response()->json(['data' => $business]);
    }

    public function destroy(Business $business): JsonResponse
    {
        $business->delete();

        return response()->json(['message' => 'Бизнесът е изтрит. Свързаните локации остават без избран бизнес.']);
    }
}
