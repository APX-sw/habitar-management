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
            $table->decimal('security_deposit_amount', 15, 2)->nullable()->change();
            $table->decimal('agency_fee_amount', 15, 2)->nullable()->change();
            $table->decimal('update_value', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->decimal('security_deposit_amount', 15, 2)->nullable(false)->default(0)->change();
            $table->decimal('agency_fee_amount', 15, 2)->nullable(false)->default(0)->change();
            $table->decimal('update_value', 10, 2)->nullable(false)->change();
        });
    }
};
