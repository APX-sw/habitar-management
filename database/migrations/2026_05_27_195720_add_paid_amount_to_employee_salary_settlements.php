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
        Schema::table('employee_salary_settlements', function (Blueprint $table) {
            $table->decimal('paid_amount', 12, 2)->default(0)->after('net_amount');
        });
        
        DB::statement("ALTER TABLE employee_salary_settlements MODIFY COLUMN status ENUM('draft', 'ready', 'partial', 'paid') NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_salary_settlements', function (Blueprint $table) {
            $table->dropColumn('paid_amount');
        });
        
        DB::statement("ALTER TABLE employee_salary_settlements MODIFY COLUMN status ENUM('draft', 'ready', 'paid') NOT NULL DEFAULT 'draft'");
    }
};
