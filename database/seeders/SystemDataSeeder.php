<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionCategory;
use App\Models\Account;
use App\Models\IndexType;
use App\Models\Province;
use App\Models\City;
use App\Models\PropertyType;

class SystemDataSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. CATEGORÍAS DE MOVIMIENTOS
        // ==========================================
        
        // A. Categorías de Sistema (Fundamentales para la lógica de cobros/pagos)
        $systemCategories = [
            ['id' => 1, 'name' => 'Alquileres', 'type' => 'income', 'group' => 'third_party', 'is_system' => true],
            ['id' => 2, 'name' => 'Honorarios Inmobiliarios', 'type' => 'income', 'group' => 'agency_profit', 'is_system' => true],
            ['id' => 3, 'name' => 'Depósitos en Garantía', 'type' => 'income', 'group' => 'third_party', 'is_system' => true],
            ['id' => 4, 'name' => 'Intereses y Recargos', 'type' => 'income', 'group' => 'agency_profit', 'is_system' => true],
            ['id' => 5, 'name' => 'Pago Rendición a Propietario', 'type' => 'expense', 'group' => 'third_party', 'is_system' => true],
            ['id' => 8, 'name' => 'Gastos Recurrentes', 'type' => 'income', 'group' => 'third_party', 'is_system' => true],
            ['id' => 9, 'name' => 'Transferencia entre cuentas', 'type' => 'both', 'group' => 'internal', 'is_system' => true],
            ['id' => 10, 'name' => 'Ajuste de Saldo', 'type' => 'both', 'group' => 'internal', 'is_system' => true],
        ];

        foreach ($systemCategories as $cat) {
            TransactionCategory::updateOrCreate(['id' => $cat['id']], $cat);
        }

        // B. Categorías Predeterminadas (Sugeridas, editables/borrables por el usuario)
        $defaultCategories = [
            ['name' => 'Expensas', 'type' => 'income', 'group' => 'third_party', 'is_system' => false],
            ['name' => 'Mantenimiento Propiedades', 'type' => 'expense', 'group' => 'third_party', 'is_system' => false],
            ['name' => 'Impuestos y Tasas', 'type' => 'expense', 'group' => 'third_party', 'is_system' => false],
            ['name' => 'Gastos de Oficina', 'type' => 'expense', 'group' => 'operational', 'is_system' => false],
        ];

        foreach ($defaultCategories as $cat) {
            TransactionCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // ==========================================
        // 2. CUENTAS INICIALES (CAJAS Y BANCO)
        // ==========================================
        Account::firstOrCreate(['name' => 'Caja Efectivo'], ['type' => 'cash', 'is_active' => true]);

        // Generar un CBU de 22 dígitos aleatorio
        $cbu = '';
        for ($j = 0; $j < 22; $j++) {
            $cbu .= rand(0, 9);
        }

        \App\Models\AgencyBankAccount::updateOrCreate(
            ['alias' => 'HABITAR.COBRO'],
            [
                'holder_name' => 'HABITAR SA',
                'cbu' => $cbu,
                'bank_entity' => 'Banco Galicia',
                'is_active' => true
            ]
        );

        // ==========================================
        // 3. TIPOS DE ÍNDICE DE AJUSTE Y SUS VALORES DE 2025 Y 2026 COMPLETOS
        // ==========================================
        $ipc = IndexType::updateOrCreate(['name' => 'IPC'], ['description' => 'Índice de Precios al Consumidor']);
        $icl = IndexType::updateOrCreate(['name' => 'ICL'], ['description' => 'Índice para Contratos de Locación']);

        // Sembrar valores de 2025 y 2026 para el IPC (Completo de Enero a Diciembre)
        foreach ([2025, 2026] as $y) {
            for ($m = 1; $m <= 12; $m++) {
                \App\Models\IndexValue::updateOrCreate(
                    ['index_type_id' => $ipc->id, 'year' => $y, 'month' => $m],
                    ['percentage' => round(3.0 + ($m % 4) * 0.4, 2)] // Entre 3% y 4.2%
                );
            }
        }

        // Sembrar valores de 2025 y 2026 para el ICL (Completo de Enero a Diciembre)
        foreach ([2025, 2026] as $y) {
            for ($m = 1; $m <= 12; $m++) {
                \App\Models\IndexValue::updateOrCreate(
                    ['index_type_id' => $icl->id, 'year' => $y, 'month' => $m],
                    ['percentage' => round(2.8 + ($m % 4) * 0.3, 2)] // Entre 2.8% y 3.7%
                );
            }
        }

        // ==========================================
        // 4. TIPOS DE INMUEBLES / PROPIEDADES
        // ==========================================
        $propertyTypes = [
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

        foreach ($propertyTypes as $type) {
            PropertyType::firstOrCreate(['name' => $type]);
        }

        // ==========================================
        // 5. GEOGRAFÍA (PROVINCIAS Y LOCALIDADES)
        // ==========================================
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

        // Localidades específicas de Santiago del Estero (para pruebas/desarrollo)
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

        // ==========================================
        // 6. CONFIGURACIONES DE CONTACTO / AGENCIA
        // ==========================================
        \App\Models\AgencySetting::updateOrCreate(['key' => 'whatsapp_number'], ['value' => '5493855200492']);
        \App\Models\AgencySetting::updateOrCreate(['key' => 'agency_email'], ['value' => 'info@habitar.com.ar']);
        \App\Models\AgencySetting::updateOrCreate(['key' => 'agency_address'], ['value' => 'Independencia 88 1er Piso F']);
    }
}
