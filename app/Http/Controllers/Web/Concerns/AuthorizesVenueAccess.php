<?php

namespace App\Http\Controllers\Web\Concerns;

use App\Models\Venue;

trait AuthorizesVenueAccess
{
    protected function authorizeVenue(Venue $venue): void
    {
        if ($venue->business_id === null) {
            return;
        }

        $venue->loadMissing('business');
        $biz = $venue->business;
        if ($biz === null) {
            return;
        }

        if ($biz->user_id !== null && (int) $biz->user_id !== (int) auth()->id()) {
            abort(403);
        }
    }
}
