<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 既存のreservationsテーブルが存在しない場合のみ作成
        if (!Schema::hasTable('reservations')) {
            Schema::create('reservations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('car_id')->constrained()->onDelete('cascade');
                $table->string('name_kanji')->nullable();
                $table->string('name_kana_sei')->nullable();
                $table->string('name_kana_mei')->nullable();
                $table->string('phone_main')->nullable();
                $table->string('phone_emergency')->nullable();
                $table->string('email')->nullable();
                $table->string('flight_number_arrival')->nullable();
                $table->string('flight_number_departure')->nullable();
                $table->dateTime('flight_departure')->nullable();
                $table->dateTime('flight_return')->nullable();
                $table->integer('number_of_adults')->default(1);
                $table->integer('number_of_children')->default(0);
                $table->dateTime('start_datetime');
                $table->dateTime('end_datetime');
                $table->json('options_json')->nullable();
                $table->integer('total_price');
                $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
}; 