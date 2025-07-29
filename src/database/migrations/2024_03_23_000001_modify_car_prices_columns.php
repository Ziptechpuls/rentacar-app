<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_prices', function (Blueprint $table) {
            // 既存のカラムが存在する場合のみ削除
            if (Schema::hasColumn('car_prices', 'price_6h')) {
                $table->dropColumn('price_6h');
            }
            if (Schema::hasColumn('car_prices', 'price_12h')) {
                $table->dropColumn('price_12h');
            }

            // 新しいカラムが存在しない場合のみ追加
            if (!Schema::hasColumn('car_prices', 'price_3h')) {
                $table->integer('price_3h')->after('car_model_id');
            }
            if (!Schema::hasColumn('car_prices', 'price_business')) {
                $table->integer('price_business')->after('price_3h');
            }
        });
    }

    public function down(): void
    {
        Schema::table('car_prices', function (Blueprint $table) {
            // 新しいカラムが存在する場合のみ削除
            if (Schema::hasColumn('car_prices', 'price_3h')) {
                $table->dropColumn('price_3h');
            }
            if (Schema::hasColumn('car_prices', 'price_business')) {
                $table->dropColumn('price_business');
            }

            // 元のカラムが存在しない場合のみ追加
            if (!Schema::hasColumn('car_prices', 'price_6h')) {
                $table->integer('price_6h')->after('car_model_id');
            }
            if (!Schema::hasColumn('car_prices', 'price_12h')) {
                $table->integer('price_12h')->after('price_6h');
            }
        });
    }
}; 