<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CarTypePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_type',
        'start_date',
        'end_date',
        'season_type',
        'price_per_day',
        'period_name',
        'notes',
        'comment',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // 指定された日数に応じた料金を返す
    public function getPriceForDays($days)
    {
        if ($this->season_type && $this->start_date && $this->end_date) {
            // 期間設定の場合、シーズン料金を取得
            $seasonPrice = self::where('car_type', $this->car_type)
                ->where('season_type', $this->season_type)
                ->whereNull('start_date')
                ->whereNull('end_date')
                ->first();
            return $seasonPrice ? $seasonPrice->price_per_day * $days : 0;
        }
        return $this->price_per_day * $days;
    }

    // 1日あたりの料金を取得
    public function getPricePerDay()
    {
        if ($this->season_type && $this->start_date && $this->end_date) {
            // 期間設定の場合、シーズン料金を取得
            $seasonPrice = self::where('car_type', $this->car_type)
                ->where('season_type', $this->season_type)
                ->whereNull('start_date')
                ->whereNull('end_date')
                ->first();
            return $seasonPrice ? $seasonPrice->price_per_day : 0;
        }
        return $this->price_per_day;
    }

    // 有効な料金設定のみを取得するスコープ
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // 指定日付の料金設定を取得するスコープ
    public function scopeForDate($query, $date)
    {
        return $query->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date);
    }

    // 車両タイプと日付で料金設定を取得する静的メソッド
    public static function getPriceByTypeAndDate($carType, $date = null)
    {
        if (!$date) {
            $date = Carbon::today();
        }

        // 指定日付に該当する料金設定を取得（複数ある場合は最新のものを優先）
        return self::where('car_type', $carType)
                  ->where('is_active', true)
                  ->where('start_date', '<=', $date)
                  ->where('end_date', '>=', $date)
                  ->orderBy('created_at', 'desc') // 最新の設定を優先
                  ->first();
    }

    // 車両タイプで料金設定を取得する静的メソッド（従来の互換性のため）
    public static function getPriceByType($carType)
    {
        return self::getPriceByTypeAndDate($carType);
    }

    // 車両タイプの通常料金を取得する静的メソッド
    public static function getNormalPriceByType($carType)
    {
        // まず通常料金（season_type = 'normal'、期間指定なし）を取得
        $normalPrice = self::where('car_type', $carType)
                          ->where('season_type', 'normal')
                          ->whereNull('start_date')
                          ->whereNull('end_date')
                          ->where('is_active', true)
                          ->orderBy('created_at', 'desc')
                          ->first();
        
        if ($normalPrice) {
            return $normalPrice;
        }
        
        // 通常料金が設定されていない場合は、該当車両タイプの最新の料金設定を取得
        return self::where('car_type', $carType)
                  ->where('is_active', true)
                  ->orderBy('created_at', 'desc')
                  ->first();
    }

    // 車両タイプと日付で適用される料金を取得（期間設定がなければ通常料金を返す）
    public static function getApplicablePriceByTypeAndDate($carType, $date = null)
    {
        if (!$date) {
            $date = Carbon::today();
        }

        // まず指定日付に該当する期間設定の料金を確認
        $periodPrice = self::getPriceByTypeAndDate($carType, $date);
        
        if ($periodPrice) {
            return $periodPrice;
        }
        
        // 期間設定がない場合は通常料金を取得
        return self::getNormalPriceByType($carType);
    }

    // 期間名を取得（期間名がない場合は期間を表示）
    public function getPeriodDisplayName()
    {
        if ($this->period_name) {
            return $this->period_name;
        }
        
        // シーズン料金の場合（start_dateとend_dateがnull）
        if (!$this->start_date || !$this->end_date) {
            return $this->getSeasonDisplayName() . '料金';
        }
        
        return $this->start_date->format('Y/m/d') . ' - ' . $this->end_date->format('Y/m/d');
    }

    // シーズン名を取得
    public function getSeasonDisplayName()
    {
        if (!$this->season_type) {
            return '設定なし';
        }
        
        $seasonNames = [
            'high_season' => 'ハイシーズン',
            'normal' => '通常',
            'low_season' => 'ローシーズン',
        ];
        
        return $seasonNames[$this->season_type] ?? $this->season_type;
    }

    // 料金の概要を取得
    public function getPriceSummary()
    {
        $pricePerDay = $this->getPricePerDay();
        return [
            'price_per_day' => $pricePerDay,
            'formatted_price' => '¥' . number_format($pricePerDay) . '/日',
        ];
    }
}
