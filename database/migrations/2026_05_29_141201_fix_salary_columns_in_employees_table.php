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
        if (Schema::hasColumn('employees', 'increase_frequency')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('increase_frequency');
            });
        }

        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'update_type')) {
                $table->enum('update_type', ['fixed', 'indexed'])->nullable()->after('base_salary');
            }
            if (!Schema::hasColumn('employees', 'update_frequency_months')) {
                $table->integer('update_frequency_months')->nullable()->after('update_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'update_frequency_months')) {
                $table->dropColumn('update_frequency_months');
            }
            if (Schema::hasColumn('employees', 'update_type')) {
                $table->dropColumn('update_type');
            }
            if (!Schema::hasColumn('employees', 'increase_frequency')) {
                $table->enum('increase_frequency', ['mensual', 'trimestral', 'semestral', 'anual'])->nullable();
            }
        });
    }
};
