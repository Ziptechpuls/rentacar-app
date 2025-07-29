<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_models', function (Blueprint $table) {
            if (!Schema::hasColumn('car_models', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('car_models', 'grade')) {
                $table->string('grade')->after('name'); // コンパクト、ミドル、ラグジュアリー
            }
            if (!Schema::hasColumn('car_models', 'manufacturer')) {
                $table->string('manufacturer')->after('grade'); // トヨタ、日産、ホンダなど
            }
            
            // car_modelカラムが存在する場合は削除
            if (Schema::hasColumn('car_models', 'car_model')) {
                $table->dropColumn('car_model');
            }
        });
    }

    public function down(): void
    {
        Schema::table('car_models', function (Blueprint $table) {
            if (Schema::hasColumn('car_models', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('car_models', 'grade')) {
                $table->dropColumn('grade');
            }
            if (Schema::hasColumn('car_models', 'manufacturer')) {
                $table->dropColumn('manufacturer');
            }
            
            if (!Schema::hasColumn('car_models', 'car_model')) {
                $table->string('car_model');
            }
        });
    }
}; 