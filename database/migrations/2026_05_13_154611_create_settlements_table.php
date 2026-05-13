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
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->integer('month');
            $table->integer('year');
            $table->decimal('total_income', 10, 2)->default(0); // Suma de cobros del mes
            $table->decimal('total_expense', 10, 2)->default(0); // Suma de gastos del mes
            $table->decimal('net_amount', 10, 2)->default(0); // income - expense
            $table->string('status')->default('draft'); // draft, ready, paid
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};
