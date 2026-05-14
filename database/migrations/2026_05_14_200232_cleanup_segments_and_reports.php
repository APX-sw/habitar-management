<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('transaction_segment_id');
        });
        Schema::dropIfExists('transaction_segments');
    }

    public function down(): void
    {
        // No volveremos atrás fácilmente sin recrear los segmentos
    }
};
