<?php

namespace App\Http\Controllers\Web\Concerns;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait RedirectsAfterBookingFlow
{
    protected function intendedBookingCreateRedirect(Request $request, string $statusMessage, RedirectResponse $fallback): RedirectResponse
    {
        if ($this->wantsReturnToBookingCreate($request)) {
            return redirect()->route('bookings.create')->with('status', $statusMessage);
        }

        return $fallback;
    }

    protected function wantsReturnToBookingCreate(Request $request): bool
    {
        $v = $request->input('return') ?? $request->query('return');

        return $v === 'booking';
    }
}
