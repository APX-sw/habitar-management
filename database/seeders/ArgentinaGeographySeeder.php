<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\City;
use Illuminate\Support\Facades\DB;

class ArgentinaGeographySeeder extends Seeder
{
    public function run()
    {
        $provinces = [
            'Buenos Aires',
            'Ciudad Autónoma de Buenos Aires',
            'Catamarca',
            'Chaco',
            'Chubut',
            'Córdoba',
            'Corrientes',
            'Entre Ríos',
            'Formosa',
            'Jujuy',
            'La Pampa',
            'La Rioja',
            'Mendoza',
            'Misiones',
            'Neuquén',
            'Río Negro',
            'Salta',
            'San Juan',
            'San Luis',
            'Santa Cruz',
            'Santa Fe',
            'Santiago del Estero',
            'Tierra del Fuego',
            'Tucumán'
        ];

        foreach ($provinces as $name) {
            Province::updateOrCreate(['name' => $name]);
        }

        $santiago = Province::where('name', 'Santiago del Estero')->first();

        if ($santiago) {
            $cities = [
                'Santiago del Estero (Capital)',
                'La Banda',
                'Termas de Río Hondo',
                'Frías',
                'Añatuya',
                'Quimilí',
                'Loreto',
                'Clodomira',
                'Suncho Corral',
                'Fernández',
                'Monte Quemado',
                'Beltrán',
                'Campo Gallo',
                'Tintina',
                'Bandera',
                'Nueva Esperanza',
                'Pozo Hondo',
                'Selva',
                'Colonia Dora',
                'Villa Atamisqui',
                'Pinto',
                'Los Juríes',
                'San Pedro de Guasayán',
                'Brea Pozo',
                'Garza',
                'Icaño',
                'Herrera',
                'Lavalle',
                'Lugones',
                'Malbrán',
                'Pampa de los Guanacos',
                'Sachayoj',
                'Taboada',
                'Tapso',
                'Vilmer',
                'Weisburd',
                'Villa La Punta',
                'El Charco',
                'Gramilla',
                'El Mojón',
                'Las Tinajas',
                'Real Sayana',
                'Villa Ojo de Agua',
                'Sumampa'
            ];

            foreach ($cities as $cityName) {
                City::updateOrCreate([
                    'province_id' => $santiago->id,
                    'name' => $cityName
                ]);
            }
        }
    }
}
