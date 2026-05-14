<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // En un DatabaseSeeder estándar solo llamamos a los datos del sistema
        // sin borrar nada, por seguridad.
        $this->call(ArgentinaGeographySeeder::class);
        $this->call(SystemDataSeeder::class);
    }
}
