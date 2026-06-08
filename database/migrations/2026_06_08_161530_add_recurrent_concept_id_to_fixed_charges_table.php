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
        Schema::table('fixed_charges', function (Blueprint $table) {
            $table->foreignId('recurrent_concept_id')->nullable()->after('lease_id')->constrained('recurrent_concepts')->onDelete('set null');
            $table->string('name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fixed_charges', function (Blueprint $table) {
            $table->dropForeign(['recurrent_concept_id']);
            $table->dropColumn('recurrent_concept_id');
            $table->string('name')->nullable(false)->change();
        });
    }
};
