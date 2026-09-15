<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['status' => BookingStatus::class, 'starts_at' => 'datetime'];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function serviceListing(): BelongsTo
    {
        return $this->belongsTo(ServiceListing::class)->withTrashed();
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(BookingEvent::class)->orderBy('id');
    }

    public function reference(): string
    {
        return 'HL-'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }
}
