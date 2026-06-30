<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leases', function (Blueprint $blueprint) {
            $blueprint->text('termination_reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('leases', function (Blueprint $blueprint) {
            $blueprint->dropColumn('termination_reason');
        });
    }
};
