<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PriceSeason;
use App\Models\SeasonPeriod;
use App\Models\CarPrice;
use App\Models\CarModel;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        // シーズン設定
        $seasons = [
            [
                'name' => 'ローシーズン',
                'description' => '1月〜3月の冬季期間',
                'rate' => 80,
                'periods' => [
                    ['start_date' => '2024-01-01', 'end_date' => '2024-03-31'],
                    ['start_date' => '2025-01-01', 'end_date' => '2025-03-31'],
                ]
            ],
            [
                'name' => '通常シーズン',
                'description' => '通常期間',
                'rate' => 100,
                'periods' => [
                    ['start_date' => '2024-04-01', 'end_date' => '2024-06-30'],
                    ['start_date' => '2024-09-01', 'end_date' => '2024-12-31'],
                ]
            ],
            [
                'name' => 'ハイシーズン',
                'description' => '7月〜8月の夏季休暇期間',
                'rate' => 120,
                'periods' => [
                    ['start_date' => '2024-07-01', 'end_date' => '2024-08-31'],
                    ['start_date' => '2025-07-01', 'end_date' => '2025-08-31'],
                ]
            ],
        ];

        foreach ($seasons as $seasonData) {
            $season = PriceSeason::create([
                'name' => $seasonData['name'],
                'description' => $seasonData['description'],
                'rate' => $seasonData['rate'],
            ]);

            foreach ($seasonData['periods'] as $period) {
                $season->periods()->create([
                    'start_date' => $period['start_date'],
                    'end_date' => $period['end_date'],
                ]);
            }
        }

        // 車種ごとの料金設定
        $carModels = CarModel::all();
        foreach ($carModels as $carModel) {
            // 基本料金は車種のグレードに応じて設定
            $basePrice = match ($carModel->grade) {
                'コンパクト' => 5000,
                'ミドル' => 7000,
                'ラグジュアリー' => 10000,
                default => 5000,
            };

            CarPrice::create([
                'car_model_id' => $carModel->id,
                'price_3h' => (int)($basePrice * 0.4),        // 3時間は1日料金の40%
                'price_business' => (int)($basePrice * 0.6),  // 営業時間内は1日料金の60%
                'price_24h' => $basePrice,                    // 1日料金（基準）
                'price_48h' => (int)($basePrice * 1.8),      // 2日は1日料金の90%×2
                'price_72h' => (int)($basePrice * 2.7),      // 3日は1日料金の90%×3
                'price_168h' => (int)($basePrice * 5.6),     // 1週間は1日料金の80%×7
                'price_extra_hour' => (int)($basePrice * 0.1), // 延長1時間は1日料金の10%
            ]);
        }
    }
} 