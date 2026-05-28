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
        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('base_salary', 12, 2)->nullable();
            $table->enum('increase_frequency', ['mensual', 'trimestral', 'semestral', 'anual'])->nullable();
            $table->foreignId('increase_index_id')->nullable()->constrained('index_types')->nullOnDelete();
            $table->decimal('increase_fixed_percentage', 5, 2)->nullable();
            $table->date('last_increase_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['increase_index_id']);
            $table->dropColumn(['base_salary', 'increase_frequency', 'increase_index_id', 'increase_fixed_percentage', 'last_increase_date']);
        });
    }
};
