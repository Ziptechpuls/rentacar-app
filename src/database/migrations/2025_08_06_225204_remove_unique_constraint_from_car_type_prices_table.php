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
            // car_typeカラムのユニーク制約を削除
            $table->dropUnique(['car_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_type_prices', function (Blueprint $table) {
            // car_typeカラムのユニーク制約を復元
            $table->unique('car_type');
        });
    }
};
