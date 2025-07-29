<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 既存のcarsテーブルが存在しない場合のみ作成
        if (!Schema::hasTable('cars')) {
            Schema::create('cars', function (Blueprint $table) {
                $table->id();
                $table->foreignId('car_model_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('type');
                $table->integer('capacity');
                $table->integer('price');
                $table->string('transmission');
                $table->string('smoking_preference')->default('non-smoking');
                $table->boolean('has_bluetooth')->default(false);
                $table->boolean('has_back_monitor')->default(false);
                $table->boolean('has_navigation')->default(false);
                $table->boolean('has_etc')->default(false);
                $table->text('description')->nullable();
                $table->boolean('is_public')->default(true);
                $table->string('car_number')->nullable();
                $table->string('color')->nullable();
                $table->string('car_vin')->nullable();
                $table->integer('passenger')->default(5);
                $table->foreignId('store_id')->nullable()->constrained('shops')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
}; 