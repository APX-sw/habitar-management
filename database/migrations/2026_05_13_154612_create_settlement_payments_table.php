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
        Schema::create('settlement_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('settlement_id')->constrained('settlements')->cascadeOnDelete();
            $table->foreignId('owner_bank_account_id')->constrained('owner_bank_accounts'); // A qué cuenta del dueño va
            $table->foreignId('account_id')->constrained('accounts'); // De qué cuenta de la inmobiliaria sale
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlement_payments');
    }
};
