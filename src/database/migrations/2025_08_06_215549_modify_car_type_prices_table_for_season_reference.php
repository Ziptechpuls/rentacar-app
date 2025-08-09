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
            // 既存の料金カラムを削除
            $table->dropColumn([
                'price_high_season',
                'price_normal',
                'price_low_season'
            ]);
            
            // シーズン料金参照用のカラムを追加
            $table->string('season_type')->after('end_date'); // 適用するシーズンタイプ
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_type_prices', function (Blueprint $table) {
            // シーズン料金参照用のカラムを削除
            $table->dropColumn('season_type');
            
            // 元の料金カラムを復元
            $table->integer('price_high_season')->default(0);
            $table->integer('price_normal')->default(0);
            $table->integer('price_low_season')->default(0);
        });
    }
};
