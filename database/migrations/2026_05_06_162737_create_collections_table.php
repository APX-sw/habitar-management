<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained()->onDelete('cascade');
            $table->integer('month');
            $table->integer('year');
            $table->decimal('rent_amount', 15, 2);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status')->default('draft'); // draft, ready, paid
            $table->date('payment_date')->nullable();
            $table->timestamps();
            
            $table->unique(['lease_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
