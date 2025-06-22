<?php

namespace App\Models;

use App\Models\Car;
use Illuminate\Database\Eloquent\Model;

class CarImage extends Model
{
    protected $fillable = ['car_id', 'image_path'];

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}
