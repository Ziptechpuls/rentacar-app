<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tel',
        'email',
        'address',
        'business_hours',
        'access',
        'map_iframe',
    ];
} 