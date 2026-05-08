<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leases', function (Blueprint $blueprint) {
            $blueprint->foreignId('parent_lease_id')->nullable()->constrained('leases')->nullOnDelete();
            $blueprint->boolean('property_review_status')->default(false);
            $blueprint->text('property_review_notes')->nullable();
            $blueprint->string('renewal_status')->default('original'); // original, renewed, pending_renewal
        });
    }

    public function down(): void
    {
        Schema::table('leases', function (Blueprint $blueprint) {
            $blueprint->dropForeign(['parent_lease_id']);
            $blueprint->dropColumn(['parent_lease_id', 'property_review_status', 'property_review_notes', 'renewal_status']);
        });
    }
};
