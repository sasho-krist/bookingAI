<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'type',
        'timezone',
        'business_hours',
    ];

    protected function casts(): array
    {
        return [
            'business_hours' => 'array',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** Локации без бизнес или с бизнес (демо без собственик или ваш). */
    public static function accessibleByCurrentUser(): Builder
    {
        $userId = auth()->id();

        return static::query()->where(function (Builder $q) use ($userId) {
            $q->whereNull('business_id')
                ->orWhereHas('business', function (Builder $b) use ($userId) {
                    $b->whereNull('user_id')->orWhere('user_id', $userId);
                });
        });
    }
}
