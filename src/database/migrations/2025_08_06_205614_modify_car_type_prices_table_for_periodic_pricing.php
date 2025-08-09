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
            // 期間別料金設定用のカラムを追加
            $table->date('start_date')->after('car_type'); // 開始日
            $table->date('end_date')->after('start_date'); // 終了日
            $table->integer('price_3h')->default(0)->after('end_date'); // 3時間料金
            $table->integer('price_business')->default(0)->after('price_3h'); // 営業時間料金
            $table->integer('price_24h')->default(0)->after('price_business'); // 24時間料金
            $table->integer('price_48h')->default(0)->after('price_24h'); // 48時間料金
            $table->integer('price_72h')->default(0)->after('price_48h'); // 72時間料金
            $table->integer('price_168h')->default(0)->after('price_72h'); // 168時間料金
            $table->integer('price_extra_hour')->default(0)->after('price_168h'); // 延長料金（1時間あたり）
            $table->string('period_name')->nullable()->after('price_extra_hour'); // 期間名（例：通常期、繁忙期など）
            $table->text('notes')->nullable()->after('period_name'); // 備考
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_type_prices', function (Blueprint $table) {
            // 期間別料金設定用のカラムを削除
            $table->dropColumn([
                'start_date',
                'end_date',
                'price_3h',
                'price_business',
                'price_24h',
                'price_48h',
                'price_72h',
                'price_168h',
                'price_extra_hour',
                'period_name',
                'notes'
            ]);
        });
    }
};
