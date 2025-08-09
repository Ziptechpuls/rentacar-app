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
        Schema::create('car_type_prices', function (Blueprint $table) {
            $table->id();
            $table->string('car_type'); // 軽自動車、セダン、SUV、ミニバン、コンパクト、ステーションワゴン、その他
            $table->date('start_date')->nullable(); // 開始日
            $table->date('end_date')->nullable(); // 終了日
            $table->integer('price_3h')->default(0); // 3時間料金
            $table->integer('price_business')->default(0); // 営業時間料金
            $table->integer('price_24h')->default(0); // 24時間料金
            $table->integer('price_48h')->default(0); // 48時間料金
            $table->integer('price_72h')->default(0); // 72時間料金
            $table->integer('price_168h')->default(0); // 168時間料金
            $table->integer('price_extra_hour')->default(0); // 延長料金（1時間あたり）
            $table->string('period_name')->nullable(); // 期間名（例：通常期、繁忙期など）
            $table->text('notes')->nullable(); // 備考
            $table->string('season_type')->default('normal'); // シーズンタイプ
            $table->integer('price_per_day')->nullable(); // 1日あたりの料金
            $table->text('comment')->nullable(); // コメント
            $table->boolean('is_active')->default(true); // 有効/無効
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_type_prices');
    }
};
