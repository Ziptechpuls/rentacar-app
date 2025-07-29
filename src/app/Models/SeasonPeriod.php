<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeasonPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'price_season_id',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function season()
    {
        return $this->belongsTo(PriceSeason::class, 'price_season_id');
    }
} 