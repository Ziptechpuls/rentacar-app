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
        Schema::create('expense_amounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('expense_category_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('effective_date')->default(now());
            $table->timestamps();
            
            $table->unique(['company_id', 'expense_category_id', 'effective_date'], 'expense_amounts_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_amounts');
    }
};
