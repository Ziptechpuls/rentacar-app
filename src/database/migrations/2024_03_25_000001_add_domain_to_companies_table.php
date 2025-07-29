<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('subdomain')->unique()->after('name');
            $table->string('logo_path')->nullable()->after('subdomain');
            $table->string('theme_color')->default('#000000')->after('logo_path');
            $table->boolean('is_active')->default(true)->after('theme_color');
            $table->timestamp('contract_start_date')->nullable()->after('is_active');
            $table->timestamp('contract_end_date')->nullable()->after('contract_start_date');
            $table->string('contract_plan')->nullable()->after('contract_end_date');
            $table->text('contract_memo')->nullable()->after('contract_plan');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'subdomain',
                'logo_path',
                'theme_color',
                'is_active',
                'contract_start_date',
                'contract_end_date',
                'contract_plan',
                'contract_memo'
            ]);
        });
    }
}; 