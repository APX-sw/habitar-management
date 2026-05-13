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
        Schema::table('settlements', function (Blueprint $table) {
            $table->decimal('rent_total', 10, 2)->default(0)->after('year');
            $table->decimal('agency_commission', 10, 2)->default(0)->after('total_expense');
        });
    }

    public function down(): void
    {
        Schema::table('settlements', function (Blueprint $table) {
            $table->dropColumn(['rent_total', 'agency_commission']);
        });
    }
};
