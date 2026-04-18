<?php

namespace App\Http\Controllers\Web\Concerns;

use App\Models\Business;

trait AuthorizesBusinessAccess
{
    protected function authorizeBusiness(Business $business): void
    {
        if ($business->user_id !== null && (int) $business->user_id !== (int) auth()->id()) {
            abort(403);
        }
    }
}
