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
        Schema::table('car_type_prices', function (Blueprint $table) {
            // price_per_dayカラムを追加
            $table->integer('price_per_day')->default(0)->after('end_date');
            
            // season_typeカラムを削除
            $table->dropColumn('season_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_type_prices', function (Blueprint $table) {
            // season_typeカラムを復元
            $table->string('season_type')->after('end_date');
            
            // price_per_dayカラムを削除
            $table->dropColumn('price_per_day');
        });
    }
};
