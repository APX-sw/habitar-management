<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertyType;

class PropertyTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Casa',
            'Departamento',
            'Dúplex',
            'Local Comercial',
            'Oficina',
            'Cochera',
            'Galpón',
            'Terreno / Lote',
            'Quinta',
            'Campo'
        ];

        foreach ($types as $type) {
            PropertyType::firstOrCreate(['name' => $type]);
        }
    }
}
