<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PriceSeason extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'rate',
    ];

    public function periods()
    {
        return $this->hasMany(SeasonPeriod::class);
    }
} 