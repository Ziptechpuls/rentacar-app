<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_model_id',
        'price_3h',
        'price_business',
        'price_24h',
        'price_48h',
        'price_72h',
        'price_168h',
        'price_extra_hour',
    ];

    public function carModel()
    {
        return $this->belongsTo(CarModel::class);
    }

    // 指定された時間に応じた料金を返す
    public function getPriceForHours($hours, $isBusinessHours = false)
    {
        if ($hours <= 3) return $this->price_3h;
        if ($isBusinessHours && $hours <= 10) return $this->price_business; // 営業時間内（最大10時間）
        if ($hours <= 24) return $this->price_24h;
        if ($hours <= 48) return $this->price_48h;
        if ($hours <= 72) return $this->price_72h;
        if ($hours <= 168) return $this->price_168h;

        // 168時間（1週間）を超える場合は、1週間料金 + 追加時間×延長料金
        $extraHours = $hours - 168;
        return $this->price_168h + ($extraHours * $this->price_extra_hour);
    }

    // シーズン料金を計算
    public function getPriceWithSeason($hours, $rate, $isBusinessHours = false)
    {
        $basePrice = $this->getPriceForHours($hours, $isBusinessHours);
        return (int)($basePrice * $rate / 100);
    }
} 