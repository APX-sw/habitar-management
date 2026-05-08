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
        Schema::table('properties', function (Blueprint $table) {
            $table->integer('rooms')->default(1);
            $table->integer('bathrooms')->default(1);
            $table->boolean('has_garage')->default(false);
            $table->boolean('has_patio')->default(false);
            $table->boolean('has_balcony')->default(false);
            $table->boolean('pets_allowed')->default(false);
            $table->decimal('square_meters', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            //
        });
    }
};
