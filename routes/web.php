<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Web\ApiDocumentationController;
use App\Http\Controllers\Web\BookingAiWebController;
use App\Http\Controllers\Web\BookingCreateController;
use App\Http\Controllers\Web\BookingPageController;
use App\Http\Controllers\Web\BookingStatusController;
use App\Http\Controllers\Web\BusinessController;
use App\Http\Controllers\Web\BusinessLocationController;
use App\Http\Controllers\Web\BusinessTypeController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\SetupWizardController;
use App\Http\Controllers\Web\VenueBusinessHoursController;
use App\Http\Controllers\Web\VenueManageController;
use App\Http\Controllers\Web\VenuePageController;
use App\Http\Controllers\Web\VenueServiceController;
use App\Http\Controllers\Web\VenueShowController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('landing');
Route::view('/privacy', 'pages.privacy')->name('legal.privacy');
Route::view('/terms', 'pages.terms')->name('legal.terms');
Route::view('/faq', 'pages.faq')->name('legal.faq');

Route::middleware('guest')->group(function (): void {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/home', DashboardController::class)->name('home');
    Route::redirect('/dashboard', '/home')->name('dashboard');

    Route::get('/api-docs', ApiDocumentationController::class)->name('api.docs');

    Route::get('/setup', [SetupWizardController::class, 'index'])->name('setup.index');
    Route::get('/setup/business', [SetupWizardController::class, 'business'])->name('setup.business');
    Route::post('/setup/business', [SetupWizardController::class, 'storeBusiness'])->name('setup.storeBusiness');
    Route::get('/setup/location', [SetupWizardController::class, 'location'])->name('setup.location');
    Route::post('/setup/location', [SetupWizardController::class, 'storeLocation'])->name('setup.storeLocation');
    Route::get('/setup/service', [SetupWizardController::class, 'service'])->name('setup.service');
    Route::post('/setup/service', [SetupWizardController::class, 'storeService'])->name('setup.storeService');
    Route::get('/setup/hours', [SetupWizardController::class, 'hours'])->name('setup.hours');
    Route::post('/setup/hours', [SetupWizardController::class, 'storeHours'])->name('setup.storeHours');
    Route::post('/setup/reset', [SetupWizardController::class, 'reset'])->name('setup.reset');

    Route::get('/business-types', [BusinessTypeController::class, 'index'])->name('business-types.index');
    Route::get('/business-types/create', [BusinessTypeController::class, 'create'])->name('business-types.create');
    Route::post('/business-types', [BusinessTypeController::class, 'store'])->name('business-types.store');
    Route::get('/business-types/{business_type}/edit', [BusinessTypeController::class, 'edit'])->name('business-types.edit');
    Route::put('/business-types/{business_type}', [BusinessTypeController::class, 'update'])->name('business-types.update');
    Route::delete('/business-types/{business_type}', [BusinessTypeController::class, 'destroy'])->name('business-types.destroy');

    Route::get('/businesses/create', [BusinessController::class, 'create'])->name('businesses.create');
    Route::post('/businesses', [BusinessController::class, 'store'])->name('businesses.store');
    Route::get('/businesses', [BusinessController::class, 'index'])->name('businesses.index');
    Route::get('/businesses/{business}', [BusinessController::class, 'show'])->name('businesses.show');
    Route::get('/businesses/{business}/edit', [BusinessController::class, 'edit'])->name('businesses.edit');
    Route::put('/businesses/{business}', [BusinessController::class, 'update'])->name('businesses.update');
    Route::delete('/businesses/{business}', [BusinessController::class, 'destroy'])->name('businesses.destroy');

    Route::get('/businesses/{business}/locations/create', [BusinessLocationController::class, 'create'])->name('businesses.locations.create');
    Route::post('/businesses/{business}/locations', [BusinessLocationController::class, 'store'])->name('businesses.locations.store');

    Route::get('/locations/{venue}/services/{service}/edit', [VenueServiceController::class, 'edit'])->name('venues.services.edit');
    Route::put('/locations/{venue}/services/{service}', [VenueServiceController::class, 'update'])->name('venues.services.update');
    Route::delete('/locations/{venue}/services/{service}', [VenueServiceController::class, 'destroy'])->name('venues.services.destroy');

    Route::get('/locations/{venue}/services/create', [VenueServiceController::class, 'create'])->name('venues.services.create');
    Route::post('/locations/{venue}/services', [VenueServiceController::class, 'store'])->name('venues.services.store');

    Route::get('/locations/{venue}/edit', [VenueManageController::class, 'edit'])->name('venues.edit');
    Route::put('/locations/{venue}', [VenueManageController::class, 'update'])->name('venues.update');
    Route::delete('/locations/{venue}', [VenueManageController::class, 'destroy'])->name('venues.destroy');

    Route::get('/locations/{venue}/business-hours/edit', [VenueBusinessHoursController::class, 'edit'])->name('venues.business-hours.edit');
    Route::put('/locations/{venue}/business-hours', [VenueBusinessHoursController::class, 'update'])->name('venues.business-hours.update');

    Route::get('/locations/{venue}', [VenueShowController::class, 'show'])->name('venues.show');

    Route::get('/venues', [VenuePageController::class, 'index'])->name('venues.index');
    Route::get('/bookings/create', [BookingCreateController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingCreateController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/{booking}/approve', [BookingStatusController::class, 'approve'])->name('bookings.approve');
    Route::post('/bookings/{booking}/reject', [BookingStatusController::class, 'reject'])->name('bookings.reject');
    Route::get('/bookings', [BookingPageController::class, 'index'])->name('bookings.index');

    Route::post('/ai/recommendations/slots', [BookingAiWebController::class, 'suggestSlots'])->name('ai.recommendations.slots');
    Route::post('/ai/recommendations/load', [BookingAiWebController::class, 'forecastLoad'])->name('ai.recommendations.load');
});
