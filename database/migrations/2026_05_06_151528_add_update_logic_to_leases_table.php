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
        Schema::table('leases', function (Blueprint $table) {
            $table->string('update_type')->default('fixed')->after('base_price'); // fixed, indexed
            $table->integer('update_frequency_months')->default(6)->after('update_type');
            $table->decimal('update_value', 10, 2)->nullable()->after('update_frequency_months'); // Porcentaje fijo o base de índice
            $table->string('update_index_name')->nullable()->after('update_value'); // IPC, ICL, etc.
            
            // Eliminamos la vieja si existe o la dejamos como fallback
            if (Schema::hasColumn('leases', 'update_formula')) {
                $table->dropColumn('update_formula');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->dropColumn(['update_type', 'update_frequency_months', 'update_value', 'update_index_name']);
            $table->string('update_formula')->nullable();
        });
    }
};
