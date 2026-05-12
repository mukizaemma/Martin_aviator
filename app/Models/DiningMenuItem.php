<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiningMenuItem extends Model
{
    protected $fillable = ['title', 'price_usd', 'image', 'sort_order'];

    protected $casts = [
        'price_usd' => 'decimal:2',
    ];
}
