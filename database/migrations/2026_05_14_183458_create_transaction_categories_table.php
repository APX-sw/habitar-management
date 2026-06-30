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
        Schema::create('transaction_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['income', 'expense', 'both'])->default('both');
            $table->string('group')->nullable(); // e.g., 'operational', 'third_party', 'agency_profit'
            $table->boolean('is_system')->default(false); // Protect core categories
            $table->timestamps();
        });

        // Insert initial system categories
        DB::table('transaction_categories')->insert([
            ['name' => 'Alquileres', 'type' => 'income', 'group' => 'third_party', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Honorarios Inmobiliarios', 'type' => 'income', 'group' => 'agency_profit', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Depósitos en Garantía', 'type' => 'income', 'group' => 'third_party', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Expensas / Impuestos', 'type' => 'income', 'group' => 'third_party', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pago Rendición a Propietario', 'type' => 'expense', 'group' => 'third_party', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mantenimiento de Inmuebles', 'type' => 'expense', 'group' => 'operational', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Multas por Atraso', 'type' => 'income', 'group' => 'agency_profit', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gastos de Oficina', 'type' => 'expense', 'group' => 'operational', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_categories');
    }
};
