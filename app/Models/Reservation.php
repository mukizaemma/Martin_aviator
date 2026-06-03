<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Reservation extends Model
{
    protected $table = 'reservations';

    protected $fillable = [
        'public_id',
        'room_id',
        'line_items',
        'checkin',
        'checkout',
        'adults',
        'children',
        'rooms',
        'nights',
        'total',
        'names',
        'phone',
        'email',
        'address',
        'status',
        'description',
        'booking_option',
        'payment_timing',
        'confirmation_channel',
        'airport_pickup',
        'airport_dropoff',
        'message_body',
    ];

    protected $casts = [
        'checkin' => 'date',
        'checkout' => 'date',
        'line_items' => 'array',
        'airport_pickup' => 'boolean',
        'airport_dropoff' => 'boolean',
        'total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Reservation $model): void {
            if (empty($model->public_id)) {
                $model->public_id = (string) Str::uuid();
            }
        });
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
