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
        // Consolidado en un único seeder de sistema para facilitar el mantenimiento.
        $this->call(SystemDataSeeder::class);
    }
}
