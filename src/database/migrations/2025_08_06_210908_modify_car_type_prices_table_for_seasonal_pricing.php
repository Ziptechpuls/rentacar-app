<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 既存データを削除
        DB::table('car_type_prices')->truncate();
        
        Schema::table('car_type_prices', function (Blueprint $table) {
            // シーズン別料金設定用のカラムを追加
            $table->integer('price_high_season')->default(0)->after('end_date'); // ハイシーズン料金（1日あたり）
            $table->integer('price_normal')->default(0)->after('price_high_season'); // 通常料金（1日あたり）
            $table->integer('price_low_season')->default(0)->after('price_normal'); // ローシーズン料金（1日あたり）
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_type_prices', function (Blueprint $table) {
            // シーズン別料金設定用のカラムを削除
            $table->dropColumn([
                'price_high_season',
                'price_normal',
                'price_low_season'
            ]);
        });
    }
};
