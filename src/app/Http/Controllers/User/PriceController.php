<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CarPrice;
use App\Models\PriceSeason;
use Carbon\Carbon;

class PriceController extends Controller
{
    public function index()
    {
        // 車種ごとの料金を取得
        $carPrices = CarPrice::with('carModel')->get();

        // 現在の日付に該当するシーズンを取得
        $today = Carbon::today();
        $currentSeason = PriceSeason::whereHas('periods', function ($query) use ($today) {
            $query->where('start_date', '<=', $today)
                  ->where('end_date', '>=', $today);
        })->first();

        // シーズン料金倍率（該当するシーズンがない場合は通常料金の100%）
        $rate = $currentSeason ? $currentSeason->rate : 100;

        // 今後3ヶ月分のシーズン期間を取得
        $futureSeasons = PriceSeason::with(['periods' => function ($query) use ($today) {
            $query->where('end_date', '>=', $today)
                  ->where('start_date', '<=', $today->copy()->addMonths(3));
        }])->get();

        return view('user.price.index', compact('carPrices', 'currentSeason', 'rate', 'futureSeasons'));
    }
} 