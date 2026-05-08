<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\City;
use App\Models\PropertyType;
use App\Models\IndexType;
use App\Models\IndexValue;
use App\Models\Owner;
use App\Models\Tenant;
use App\Models\Property;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Provincias y Ciudades
        $santaFe = Province::firstOrCreate(['name' => 'Santa Fe']);
        $rosario = City::firstOrCreate(['province_id' => $santaFe->id, 'name' => 'Rosario']);
        $sfCity = City::firstOrCreate(['province_id' => $santaFe->id, 'name' => 'Santa Fe']);

        // 2. Tipos de Propiedad
        $casa = PropertyType::firstOrCreate(['name' => 'Casa']);
        $depto = PropertyType::firstOrCreate(['name' => 'Departamento']);
        $local = PropertyType::firstOrCreate(['name' => 'Local Comercial']);

        // 3. Tipos de Índice y Valores Históricos
        $ipc = IndexType::firstOrCreate(['name' => 'IPC', 'description' => 'Índice de Precios al Consumidor']);
        $icl = IndexType::firstOrCreate(['name' => 'ICL', 'description' => 'Índice para Contratos de Locación']);

        // Cargar algunos valores para que el motor de cálculo funcione
        $months = [
            ['month' => 1, 'year' => 2026, 'percentage' => 4.2],
            ['month' => 2, 'year' => 2026, 'percentage' => 3.8],
            ['month' => 3, 'year' => 2026, 'percentage' => 5.1],
            ['month' => 4, 'year' => 2026, 'percentage' => 4.5],
            ['month' => 5, 'year' => 2026, 'percentage' => 4.0],
        ];

        foreach ($months as $m) {
            IndexValue::create(array_merge($m, ['index_type_id' => $ipc->id]));
            IndexValue::create(array_merge($m, ['index_type_id' => $icl->id, 'percentage' => $m['percentage'] - 0.5]));
        }

        // 4. Propietarios COMPLETOS
        $owner1 = Owner::create([
            'name' => 'Juan Ignacio Pérez',
            'dni_cuit' => '20-30444555-9',
            'email' => 'juan.perez@habitar.com',
            'phone' => '341 588 9900',
            'contact' => 'Atender después de las 16hs. Tiene cuenta en Banco Santa Fe.'
        ]);

        $owner2 = Owner::create([
            'name' => 'María Elena García',
            'dni_cuit' => '27-25666777-1',
            'email' => 'm.garcia@gmail.com',
            'phone' => '341 422 3344',
            'contact' => 'Preferiblemente contactar por WhatsApp.'
        ]);

        // 5. Inquilinos COMPLETOS
        $tenant1 = Tenant::create([
            'name' => 'Carlos Alberto Rodríguez',
            'dni_cuit' => '20-33444555-2',
            'email' => 'carlos.rod@outlook.com',
            'phone' => '341 611 2233',
            'emergency_contact' => 'Esposa: Ana (341 999 8877)',
            'references' => 'Anterior alquiler en Inmobiliaria R&R sin deudas.',
            'contact' => 'Trabaja en el Hospital Privado de Rosario.'
        ]);

        // 6. Propiedades COMPLETAS
        Property::create([
            'owner_id' => $owner1->id,
            'province_id' => $santaFe->id,
            'city_id' => $rosario->id,
            'property_type_id' => $depto->id,
            'location' => 'Bv. Oroño 1234, 4° B',
            'description' => 'Departamento luminoso frente al parque, recién pintado.',
            'rooms' => 2,
            'bathrooms' => 1,
            'square_meters' => 48.5,
            'has_garage' => false,
            'has_patio' => false,
            'has_balcony' => true,
            'pets_allowed' => true,
        ]);

        Property::create([
            'owner_id' => $owner1->id,
            'province_id' => $santaFe->id,
            'city_id' => $rosario->id,
            'property_type_id' => $casa->id,
            'location' => 'Zeballos 3200',
            'description' => 'Casa antigua reciclada, techos altos.',
            'rooms' => 4,
            'bathrooms' => 2,
            'square_meters' => 120,
            'has_garage' => true,
            'has_patio' => true,
            'has_balcony' => false,
            'pets_allowed' => true,
        ]);

        Property::create([
            'owner_id' => $owner2->id,
            'province_id' => $santaFe->id,
            'city_id' => $sfCity->id,
            'property_type_id' => $local->id,
            'location' => 'San Martín 2400',
            'description' => 'Local comercial en excelente zona.',
            'rooms' => 1,
            'bathrooms' => 1,
            'square_meters' => 60,
            'has_garage' => false,
            'has_patio' => false,
            'has_balcony' => false,
            'pets_allowed' => false,
        ]);
    }
}
