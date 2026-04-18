<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Business;
use App\Models\Service;
use App\Models\Venue;
use Illuminate\Routing\Route as RouteContract;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Route::bind('venue', function (string $value): Venue {
            return Venue::accessibleByCurrentUser()->whereKey($value)->firstOrFail();
        });

        Route::bind('business', function (string $value): Business {
            return Business::accessibleByCurrentUser()->whereKey($value)->firstOrFail();
        });

        Route::bind('booking', function (string $value): Booking {
            $booking = Booking::query()->with(['venue.business'])->findOrFail($value);
            $venue = $booking->venue;
            if ($venue !== null) {
                Venue::accessibleByCurrentUser()->whereKey($venue->id)->firstOrFail();
            }

            return $booking;
        });

        Route::bind('service', function (string $value, RouteContract $route): Service {
            $venue = $route->parameter('venue');
            if (! $venue instanceof Venue) {
                abort(404);
            }

            return Service::query()->where('venue_id', $venue->id)->whereKey($value)->firstOrFail();
        });
    }
}
