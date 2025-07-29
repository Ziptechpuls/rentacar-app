<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CarPrice;
use App\Models\PriceSeason;
use Carbon\Carbon;

class StoreController extends Controller
{
    public function pricing()
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

        // 全シーズンを取得（料金倍率でソート）
        $seasons = PriceSeason::orderBy('rate', 'desc')->get();

        // シーズン区分を設定
        $seasonTypes = [
            'high' => $seasons->where('rate', '>', 100)->first() ?? null,    // ハイシーズン
            'normal' => $seasons->where('rate', 100)->first() ?? null,       // 通常シーズン
            'low' => $seasons->where('rate', '<', 100)->first() ?? null,     // ローシーズン
        ];

        return view('user.store.pricing', compact('carPrices', 'currentSeason', 'rate', 'seasonTypes'));
    }

    public function info()
    {
        $shop = \App\Models\Shop::first();
        return view('user.store.info', compact('shop'));
    }
} 