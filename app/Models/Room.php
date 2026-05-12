<?php

namespace App\Models;

use App\Models\roomImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    protected $fillable = ['roomName', 'category', 'image', 'slug', 'roomType', 'price', 'quantity', 'maxAdults', 'maxChildren', 'description'];

    public function reservation()
    {
        return $this->hasMany(Reservation::class);
    }

    public function images()
    {
        return $this->hasMany(roomImage::class);
    }

    public function amenityOptions(): BelongsToMany
    {
        return $this->belongsToMany(HotelAmenityOption::class, 'hotel_amenity_room', 'room_id', 'hotel_amenity_option_id')->withTimestamps();
    }
}
