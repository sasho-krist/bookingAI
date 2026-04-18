<?php

use App\Http\Controllers\Api\AiBookingController;
use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\BusinessApiController;
use App\Http\Controllers\Api\BusinessTypeApiController;
use App\Http\Controllers\Api\BusinessVenueApiController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\VenueController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/auth/token', [AuthTokenController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/auth/logout', [AuthTokenController::class, 'destroy']);

        Route::get('/business-types', [BusinessTypeApiController::class, 'index']);
        Route::post('/business-types', [BusinessTypeApiController::class, 'store']);
        Route::put('/business-types/{business_type}', [BusinessTypeApiController::class, 'update']);
        Route::delete('/business-types/{business_type}', [BusinessTypeApiController::class, 'destroy']);

        Route::get('/businesses', [BusinessApiController::class, 'index']);
        Route::post('/businesses', [BusinessApiController::class, 'store']);
        Route::get('/businesses/{business}', [BusinessApiController::class, 'show']);
        Route::put('/businesses/{business}', [BusinessApiController::class, 'update']);
        Route::delete('/businesses/{business}', [BusinessApiController::class, 'destroy']);
        Route::post('/businesses/{business}/venues', [BusinessVenueApiController::class, 'store']);

        Route::get('/venues', [VenueController::class, 'index']);
        Route::post('/venues', [VenueController::class, 'store']);
        Route::get('/venues/{venue}', [VenueController::class, 'show']);
        Route::put('/venues/{venue}', [VenueController::class, 'update']);
        Route::delete('/venues/{venue}', [VenueController::class, 'destroy']);
        Route::put('/venues/{venue}/business-hours', [VenueController::class, 'updateBusinessHours']);

        Route::get('/venues/{venue}/services', [ServiceController::class, 'index']);
        Route::post('/venues/{venue}/services', [ServiceController::class, 'store']);
        Route::get('/venues/{venue}/services/{service}', [ServiceController::class, 'show']);
        Route::put('/venues/{venue}/services/{service}', [ServiceController::class, 'update']);
        Route::delete('/venues/{venue}/services/{service}', [ServiceController::class, 'destroy']);

        Route::get('/customers', [CustomerController::class, 'index']);
        Route::post('/customers', [CustomerController::class, 'store']);
        Route::put('/customers/{customer}', [CustomerController::class, 'update']);

        Route::get('/bookings', [BookingController::class, 'index']);
        Route::get('/venues/{venue}/bookings', [BookingController::class, 'venueIndex']);
        Route::post('/venues/{venue}/bookings', [BookingController::class, 'store']);
        Route::patch('/bookings/{booking}', [BookingController::class, 'update']);
        Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);

        Route::post('/ai/slots', [AiBookingController::class, 'suggestSlots']);
        Route::post('/ai/load-forecast', [AiBookingController::class, 'forecastLoad']);
        Route::post('/ai/reschedule', [AiBookingController::class, 'reschedule']);
        Route::post('/ai/chat', [AiBookingController::class, 'chat']);
        Route::post('/ai/no-show', [AiBookingController::class, 'noShow']);
    });
});
