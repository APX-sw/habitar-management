<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionCategory;
use App\Models\Account;
use App\Models\IndexType;
use App\Models\PropertyType;

class SystemDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categorías de Sistema (Fundamentales para la lógica del código)
        // Usamos IDs fijos para las categorías críticas de sistema para evitar errores de referencia
        $systemCategories = [
            ['id' => 1, 'name' => 'Alquileres', 'type' => 'income', 'group' => 'third_party', 'is_system' => true],
            ['id' => 2, 'name' => 'Honorarios Inmobiliarios', 'type' => 'income', 'group' => 'agency_profit', 'is_system' => true],
            ['id' => 3, 'name' => 'Depósitos en Garantía', 'type' => 'income', 'group' => 'third_party', 'is_system' => true],
            ['id' => 4, 'name' => 'Intereses y Recargos', 'type' => 'income', 'group' => 'agency_profit', 'is_system' => true],
            ['id' => 5, 'name' => 'Pago Rendición a Propietario', 'type' => 'expense', 'group' => 'third_party', 'is_system' => true],
            ['id' => 9, 'name' => 'Transferencia entre cuentas', 'type' => 'both', 'group' => 'internal', 'is_system' => true],
            ['id' => 10, 'name' => 'Ajuste de Saldo', 'type' => 'both', 'group' => 'internal', 'is_system' => true],
        ];

        foreach ($systemCategories as $cat) {
            TransactionCategory::updateOrCreate(['id' => $cat['id']], $cat);
        }

        // 2. Categorías Predeterminadas (Opcionales, el usuario puede borrarlas)
        $defaultCategories = [
            ['name' => 'Expensas', 'type' => 'income', 'group' => 'third_party', 'is_system' => false],
            ['name' => 'Mantenimiento Propiedades', 'type' => 'expense', 'group' => 'third_party', 'is_system' => false],
            ['name' => 'Impuestos y Tasas', 'type' => 'expense', 'group' => 'third_party', 'is_system' => false],
            ['name' => 'Gastos de Oficina', 'type' => 'expense', 'group' => 'operational', 'is_system' => false],
        ];

        foreach ($defaultCategories as $cat) {
            TransactionCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // 3. Cuentas Iniciales
        Account::firstOrCreate(['name' => 'Caja Efectivo'], ['type' => 'cash', 'is_active' => true]);

        // 4. Tipos de Índice
        IndexType::updateOrCreate(['name' => 'IPC'], ['description' => 'Índice de Precios al Consumidor']);
        IndexType::updateOrCreate(['name' => 'ICL'], ['description' => 'Índice para Contratos de Locación']);

        // 5. Tipos de Propiedad Básicos
        PropertyType::firstOrCreate(['name' => 'Casa']);
        PropertyType::firstOrCreate(['name' => 'Departamento']);
        PropertyType::firstOrCreate(['name' => 'Local Comercial']);
        PropertyType::firstOrCreate(['name' => 'Cochera']);
        PropertyType::firstOrCreate(['name' => 'Terreno']);
    }
}
