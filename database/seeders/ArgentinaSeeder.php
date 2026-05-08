<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\City;

class ArgentinaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Buenos Aires' => ['La Plata', 'Mar del Plata', 'Bahía Blanca', 'Tandil', 'San Nicolás', 'Quilmes', 'Lanús', 'Lomas de Zamora'],
            'CABA' => ['Ciudad Autónoma de Buenos Aires'],
            'Catamarca' => ['San Fernando del Valle de Catamarca', 'Andalgalá', 'Belén', 'Tinogasta'],
            'Chaco' => ['Resistencia', 'Presidencia Roque Sáenz Peña', 'Villa Ángela', 'Charata'],
            'Chubut' => ['Rawson', 'Comodoro Rivadavia', 'Puerto Madryn', 'Esquel', 'Trelew'],
            'Córdoba' => ['Córdoba', 'Río Cuarto', 'Villa María', 'Carlos Paz', 'San Francisco', 'Alta Gracia'],
            'Corrientes' => ['Corrientes', 'Goya', 'Paso de los Libres', 'Curuzú Cuatiá', 'Mercedes'],
            'Entre Ríos' => ['Paraná', 'Concordia', 'Gualeguaychú', 'Concepción del Uruguay', 'Victoria'],
            'Formosa' => ['Formosa', 'Clorinda', 'Pirané', 'El Colorado'],
            'Jujuy' => ['San Salvador de Jujuy', 'Palpalá', 'San Pedro', 'Libertador General San Martín'],
            'La Pampa' => ['Santa Rosa', 'General Pico', 'Toay', 'Realicó'],
            'La Rioja' => ['La Rioja', 'Chilecito', 'Aimogasta', 'Chamical'],
            'Mendoza' => ['Mendoza', 'San Rafael', 'Godoy Cruz', 'Luján de Cuyo', 'Maipú', 'Guaymallén'],
            'Misiones' => ['Posadas', 'Eldorado', 'Oberá', 'Puerto Iguazú', 'Apóstoles'],
            'Neuquén' => ['Neuquén', 'Cutral Có', 'Centenario', 'San Martín de los Andes', 'Plottier'],
            'Río Negro' => ['Viedma', 'San Carlos de Bariloche', 'General Roca', 'Cipolletti', 'Villa Regina'],
            'Salta' => ['Salta', 'San Ramón de la Nueva Orán', 'Tartagal', 'General Güemes'],
            'San Juan' => ['San Juan', 'Rawson', 'Rivadavia', 'Chimbas', 'Santa Lucía'],
            'San Luis' => ['San Luis', 'Villa Mercedes', 'Merlo', 'Juana Koslay'],
            'Santa Cruz' => ['Río Gallegos', 'Caleta Olivia', 'El Calafate', 'Puerto Deseado'],
            'Santa Fe' => ['Santa Fe', 'Rosario', 'Rafaela', 'Venado Tuerto', 'Reconquista', 'Santo Tomé'],
            'Santiago del Estero' => ['Santiago del Estero', 'La Banda', 'Termas de Río Hondo', 'Añatuya', 'Frías', 'Quimilí'],
            'Tierra del Fuego' => ['Ushuaia', 'Río Grande', 'Tolhuin'],
            'Tucumán' => ['San Miguel de Tucumán', 'Yerba Buena', 'Tafí Viejo', 'Concepción', 'Aguilares']
        ];

        foreach ($data as $provinceName => $cities) {
            $province = Province::firstOrCreate(['name' => $provinceName]);
            foreach ($cities as $cityName) {
                City::firstOrCreate([
                    'province_id' => $province->id,
                    'name' => $cityName
                ]);
            }
        }
    }
}
