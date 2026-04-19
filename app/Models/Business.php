<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $user_id
 * @property-read Collection<int, Venue> $venues
 */
class Business extends Model
{
    protected $fillable = [
        'business_type_id',
        'user_id',
        'name',
        'email',
        'phone',
    ];

    public function businessType(): BelongsTo
    {
        return $this->belongsTo(BusinessType::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    /** Бизнеси без собственик (демо) или притежавани от текущия потребител. */
    public function scopeAccessibleByCurrentUser(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->whereNull('user_id')->orWhere('user_id', auth()->id());
        });
    }
}
