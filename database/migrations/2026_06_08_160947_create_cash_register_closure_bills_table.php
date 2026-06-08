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
        Schema::create('cash_register_closure_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_register_closure_id')->constrained('cash_register_closures')->onDelete('cascade');
            $table->integer('bill_value');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_register_closure_bills');
    }
};
