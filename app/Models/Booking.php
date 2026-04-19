<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $venue_id
 * @property int|null $service_id
 * @property int|null $customer_id
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property string|null $status
 * @property bool|null $attended
 * @property-read Venue|null $venue
 * @property-read Service|null $service
 * @property-read Customer|null $customer
 */
class Booking extends Model
{
    protected $fillable = [
        'venue_id',
        'service_id',
        'customer_id',
        'starts_at',
        'ends_at',
        'status',
        'attended',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'attended' => 'boolean',
        ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
