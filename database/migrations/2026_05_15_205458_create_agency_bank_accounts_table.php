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
        Schema::create('agency_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('alias')->nullable();
            $table->string('cbu')->nullable();
            $table->string('holder_name')->nullable();
            $table->string('bank_entity')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agency_bank_accounts');
    }
};
