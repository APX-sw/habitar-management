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
        Schema::table('cash_register_closures', function (Blueprint $table) {
            $table->decimal('initial_balance', 15, 2)->default(0)->after('id');
            $table->timestamp('opened_at')->nullable()->after('initial_balance');
            $table->enum('status', ['open', 'closed'])->default('open')->after('opened_at');
            
            // Hacer nullables los campos de cierre para cuando se abre la caja
            $table->date('closure_date')->nullable()->change();
            $table->decimal('system_balance', 15, 2)->nullable()->change();
            $table->decimal('physical_balance', 15, 2)->nullable()->change();
            $table->decimal('difference', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_register_closures', function (Blueprint $table) {
            $table->dropColumn(['initial_balance', 'opened_at', 'status']);
        });
    }
};
