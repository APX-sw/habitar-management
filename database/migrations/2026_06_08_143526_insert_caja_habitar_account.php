<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('accounts')->insertOrIgnore([
            'name' => 'Caja Habitar',
            'type' => 'habitar_fund', // Special type
            'initial_balance' => 0,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('accounts')->where('type', 'habitar_fund')->delete();
    }
};
