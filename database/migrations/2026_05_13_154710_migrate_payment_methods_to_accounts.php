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
        // 1. Create the new accounts
        $cajaId = DB::table('accounts')->insertGetId([
            'name' => 'Caja',
            'type' => 'cash',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        DB::table('accounts')->insert([
            ['name' => 'Cuenta Benja', 'type' => 'bank', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cuenta Nacho', 'type' => 'bank', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cuenta Cami', 'type' => 'bank', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Add account_id to collection_payments and populate it
        Schema::table('collection_payments', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable()->after('collection_id')->constrained('accounts');
        });

        // 3. Assign all existing payments to "Caja" and create CashRegisterMovements
        $payments = DB::table('collection_payments')->get();
        foreach ($payments as $payment) {
            DB::table('collection_payments')
                ->where('id', $payment->id)
                ->update(['account_id' => $cajaId]);
                
            // Also generate the historic movement
            DB::table('cash_register_movements')->insert([
                'account_id' => $cajaId,
                'type' => 'income',
                'amount' => $payment->amount,
                'description' => 'Cobro de Alquiler Histórico',
                'movement_date' => $payment->payment_date ?? now(),
                'related_type' => 'App\Models\CollectionPayment',
                'related_id' => $payment->id,
                'created_at' => $payment->created_at ?? now(),
                'updated_at' => $payment->updated_at ?? now(),
            ]);
        }

        // Make account_id required now
        Schema::table('collection_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable(false)->change();
        });

        // 4. Drop the old payment_method_id and payment_methods table
        Schema::table('collection_payments', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn('payment_method_id');
        });

        Schema::dropIfExists('payment_methods');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No simple down migration due to data loss.
    }
};
