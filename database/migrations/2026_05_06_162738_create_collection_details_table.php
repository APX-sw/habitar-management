<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('amount', 15, 2);
            $table->string('type'); // rent, fixed_charge, extra_charge
            $table->unsignedBigInteger('related_id')->nullable(); // ID of fixed_charge or extra_charge if applicable
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_details');
    }
};
