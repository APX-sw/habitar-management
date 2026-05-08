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
        Schema::create('index_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('index_type_id')->constrained()->onDelete('cascade');
            $table->integer('year');
            $table->integer('month');
            $table->decimal('percentage', 8, 4); // Porcentaje de aumento para ese mes (ej: 4.5)
            $table->timestamps();
            
            $table->unique(['index_type_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('index_values');
    }
};
