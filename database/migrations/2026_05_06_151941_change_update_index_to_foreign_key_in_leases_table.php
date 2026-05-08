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
        Schema::table('leases', function (Blueprint $table) {
            $table->dropColumn('update_index_name');
            $table->foreignId('index_type_id')->nullable()->after('update_value')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->dropForeign(['index_type_id']);
            $table->dropColumn('index_type_id');
            $table->string('update_index_name')->nullable();
        });
    }
};
