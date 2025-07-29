<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('price');
            $table->boolean('is_quantity')->default(false);
            $table->string('image_path')->nullable();
            $table->enum('price_type', ['per_piece', 'per_day'])->default('per_day');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('options');
    }
}; 