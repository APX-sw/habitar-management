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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('dni_cuit')->unique()->after('name');
            $table->string('email')->nullable()->after('dni_cuit');
            $table->string('phone')->nullable()->after('email');
            $table->string('emergency_contact')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['dni_cuit', 'email', 'phone', 'emergency_contact']);
        });
    }
};
