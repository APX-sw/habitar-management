<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $blueprint) {
            $blueprint->string('description')->nullable()->change();
            $blueprint->string('attachment_path')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $blueprint) {
            $blueprint->string('description')->nullable(false)->change();
            $blueprint->dropColumn('attachment_path');
        });
    }
};
