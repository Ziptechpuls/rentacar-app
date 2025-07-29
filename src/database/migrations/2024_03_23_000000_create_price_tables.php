<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('price_seasons')) {
            Schema::create('price_seasons', function (Blueprint $table) {
                $table->id();
                $table->string('name');        // シーズン名
                $table->string('description'); // 説明（例：7月〜8月の夏季休暇期間）
                $table->integer('rate');       // 料金倍率（100 = 標準、80 = 20%引き、120 = 20%増）
                $table->timestamps();
            });
        }

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

        if (!Schema::hasTable('season_periods')) {
            Schema::create('season_periods', function (Blueprint $table) {
                $table->id();
                $table->foreignId('price_season_id')->constrained()->onDelete('cascade');
                $table->date('start_date');  // 開始日
                $table->date('end_date');    // 終了日
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('season_periods');
        Schema::dropIfExists('car_prices');
        Schema::dropIfExists('price_seasons');
    }
}; 