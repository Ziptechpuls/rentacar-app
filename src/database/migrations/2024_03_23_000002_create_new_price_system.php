<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 料金シーズン（ローシーズン、通常、ハイシーズン）
        if (!Schema::hasTable('price_seasons')) {
            Schema::create('price_seasons', function (Blueprint $table) {
                $table->id();
                $table->string('name');        // シーズン名
                $table->string('description'); // 説明（例：7月〜8月の夏季休暇期間）
                $table->integer('rate');       // 料金倍率（100 = 標準、80 = 20%引き、120 = 20%増）
                $table->timestamps();
            });
        }

        // 車種ごとの基本料金
        if (!Schema::hasTable('car_prices')) {
            Schema::create('car_prices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('car_model_id')->constrained()->onDelete('cascade');
                $table->integer('price_3h');         // 3時間料金
                $table->integer('price_business');   // 営業時間内料金（9:00-19:00）
                $table->integer('price_24h');        // 24時間料金
                $table->integer('price_48h');        // 48時間料金
                $table->integer('price_72h');        // 72時間料金
                $table->integer('price_168h');       // 1週間料金
                $table->integer('price_extra_hour'); // 延長料金（1時間あたり）
                $table->timestamps();
            });
        }

        // シーズン期間の設定
        if (!Schema::hasTable('season_periods')) {
            Schema::create('season_periods', function (Blueprint $table) {
                $table->id();
                $table->foreignId('price_season_id')->constrained()->onDelete('cascade');
                $table->date('start_date');  // 開始日
                $table->date('end_date');    // 終了日
                $table->timestamps();
            });
        }

        // 初期データの投入
        $this->seedInitialData();
    }

    private function seedInitialData()
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
            $season = DB::table('price_seasons')->insertGetId([
                'name' => $seasonData['name'],
                'description' => $seasonData['description'],
                'rate' => $seasonData['rate'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($seasonData['periods'] as $period) {
                DB::table('season_periods')->insert([
                    'price_season_id' => $season,
                    'start_date' => $period['start_date'],
                    'end_date' => $period['end_date'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 車種ごとの料金設定
        $carModels = DB::table('car_models')->get();
        foreach ($carModels as $carModel) {
            // 基本料金は車種のグレードに応じて設定
            $basePrice = match ($carModel->grade) {
                'コンパクト' => 5000,
                'ミドル' => 7000,
                'ラグジュアリー' => 10000,
                default => 5000,
            };

            DB::table('car_prices')->insert([
                'car_model_id' => $carModel->id,
                'price_3h' => (int)($basePrice * 0.4),        // 3時間は1日料金の40%
                'price_business' => (int)($basePrice * 0.6),  // 営業時間内は1日料金の60%
                'price_24h' => $basePrice,                    // 1日料金（基準）
                'price_48h' => (int)($basePrice * 1.8),      // 2日は1日料金の90%×2
                'price_72h' => (int)($basePrice * 2.7),      // 3日は1日料金の90%×3
                'price_168h' => (int)($basePrice * 5.6),     // 1週間は1日料金の80%×7
                'price_extra_hour' => (int)($basePrice * 0.1), // 延長1時間は1日料金の10%
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('season_periods');
        Schema::dropIfExists('car_prices');
        Schema::dropIfExists('price_seasons');
    }
}; 