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
        Schema::table('cash_register_movements', function (Blueprint $table) {
            $table->foreignId('transaction_category_id')->nullable()->constrained()->nullOnDelete();
        });
        Schema::table('fixed_charges', function (Blueprint $table) {
            $table->foreignId('transaction_category_id')->nullable()->constrained()->nullOnDelete();
        });
        Schema::table('extra_charges', function (Blueprint $table) {
            $table->foreignId('transaction_category_id')->nullable()->constrained()->nullOnDelete();
        });
        Schema::table('collection_details', function (Blueprint $table) {
            $table->foreignId('transaction_category_id')->nullable()->constrained()->nullOnDelete();
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('transaction_category_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_register_movements', function (Blueprint $table) {
            $table->dropForeign(['transaction_category_id']);
            $table->dropColumn('transaction_category_id');
        });
        Schema::table('fixed_charges', function (Blueprint $table) {
            $table->dropForeign(['transaction_category_id']);
            $table->dropColumn('transaction_category_id');
        });
        Schema::table('extra_charges', function (Blueprint $table) {
            $table->dropForeign(['transaction_category_id']);
            $table->dropColumn('transaction_category_id');
        });
        Schema::table('collection_details', function (Blueprint $table) {
            $table->dropForeign(['transaction_category_id']);
            $table->dropColumn('transaction_category_id');
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['transaction_category_id']);
            $table->dropColumn('transaction_category_id');
        });
    }
};
