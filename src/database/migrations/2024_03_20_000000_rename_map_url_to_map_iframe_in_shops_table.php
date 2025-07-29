<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->renameColumn('map_url', 'map_iframe');
            $table->text('map_iframe')->change();
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->renameColumn('map_iframe', 'map_url');
            $table->string('map_url')->change();
        });
    }
}; 