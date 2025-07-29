<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');        // 車種名
            $table->string('grade');       // グレード（コンパクト、ミドル、ラグジュアリー）
            $table->text('description')->nullable(); // 説明
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_models');
    }
}; 