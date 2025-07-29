<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tel');
            $table->string('email');
            $table->string('address');
            $table->string('business_hours');
            $table->text('access');
            $table->string('map_url');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
}; 