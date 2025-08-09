<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('season_prices', function (Blueprint $table) {
            $table->id();
            $table->string('car_type'); // 車両タイプ
            $table->string('season_type'); // シーズンタイプ（high_season, normal, low_season）
            $table->integer('price_per_day')->default(0); // 1日あたりの料金
            $table->string('season_name')->nullable(); // シーズン名（例：ハイシーズン、通常、ローシーズン）
            $table->text('description')->nullable(); // 説明
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // 車両タイプとシーズンタイプの組み合わせでユニーク
            $table->unique(['car_type', 'season_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('season_prices');
    }
};
