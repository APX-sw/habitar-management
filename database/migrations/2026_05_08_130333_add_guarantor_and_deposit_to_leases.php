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
            $table->string('guarantor_name')->nullable()->after('tenant_id');
            $table->string('guarantor_id_number')->nullable()->after('guarantor_name');
            $table->string('guarantor_email')->nullable()->after('guarantor_id_number');
            $table->string('guarantor_address')->nullable()->after('guarantor_email');
            $table->string('guarantor_phone')->nullable()->after('guarantor_address');
            $table->decimal('security_deposit_amount', 15, 2)->default(0)->after('base_price');
        });
    }

    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->dropColumn([
                'guarantor_name',
                'guarantor_id_number',
                'guarantor_email',
                'guarantor_address',
                'guarantor_phone',
                'security_deposit_amount'
            ]);
        });
    }
};
