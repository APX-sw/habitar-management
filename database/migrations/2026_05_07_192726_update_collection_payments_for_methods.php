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
        Schema::table('collection_payments', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->nullable()->constrained()->after('amount');
            $table->dropColumn('method');
        });
    }

    public function down(): void
    {
        Schema::table('collection_payments', function (Blueprint $table) {
            $table->string('method')->after('amount');
            $table->dropConstrainedForeignId('payment_method_id');
        });
    }
};
