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
        Schema::create('deleted_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_movement_id')->nullable();
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->string('type'); // income or expense
            $table->decimal('amount', 12, 2);
            $table->string('description');
            $table->dateTime('movement_date');
            $table->unsignedBigInteger('transaction_category_id')->nullable();
            $table->foreignId('deleted_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deleted_movements');
    }
};
