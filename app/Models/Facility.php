<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;
    protected $table = 'facilities';
    protected $fillable = ['title','category','description','image','slug'];

    public function facilityImages()
    {
        return $this->hasMany(FacilityImage::class, 'facility_id');
    }
}
