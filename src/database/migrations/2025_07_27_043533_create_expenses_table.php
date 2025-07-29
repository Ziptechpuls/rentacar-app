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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 経費名
            $table->text('description')->nullable(); // 説明
            $table->decimal('amount', 10, 2); // 金額
            $table->enum('type', ['fixed', 'variable']); // 固定費 or 変動費
            $table->enum('category', ['rent', 'utilities', 'insurance', 'maintenance', 'fuel', 'other']); // カテゴリ
            $table->date('date'); // 適用日
            $table->boolean('is_recurring')->default(false); // 毎月繰り返しかどうか
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
