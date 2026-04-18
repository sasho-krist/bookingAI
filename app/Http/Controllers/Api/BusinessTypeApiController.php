<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessTypeApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => BusinessType::query()->orderBy('name')->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $type = BusinessType::query()->create($data);

        return response()->json(['data' => $type], 201);
    }

    public function update(Request $request, BusinessType $business_type): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $business_type->update($data);

        return response()->json(['data' => $business_type->fresh()]);
    }

    public function destroy(BusinessType $business_type): JsonResponse
    {
        if ($business_type->businesses()->exists()) {
            return response()->json([
                'message' => 'Не може да изтриете тип с присвоени бизнеси.',
            ], 422);
        }

        $business_type->delete();

        return response()->json(['message' => 'Типът е изтрит.']);
    }
}
