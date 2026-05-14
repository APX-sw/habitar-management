<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FactoryResetSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->warn('🚨 ATENCIÓN: Iniciando RESET DE FÁBRICA total...');

        Schema::disableForeignKeyConstraints();

        $tables = [
            'settlement_payments',
            'settlements',
            'expenses',
            'cash_register_movements',
            'accounts',
            'collection_details',
            'collections',
            'lease_documents',
            'fixed_charges',
            'extra_charges',
            'leases',
            'properties',
            'tenants',
            'owners',
            'property_types',
            'index_values',
            'index_types',
            'transaction_categories',
            'cities',
            'provinces',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->command->info("Tabla vaciada: $table");
            }
        }

        Schema::enableForeignKeyConstraints();

        // Llamar a los seeders de datos iniciales
        $this->call(ArgentinaGeographySeeder::class);
        $this->call(SystemDataSeeder::class);

        $this->command->info('✨ El sistema ha sido reseteado a su estado inicial de fábrica.');
    }
}
