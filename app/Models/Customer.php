<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string|null $name
 */
class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
