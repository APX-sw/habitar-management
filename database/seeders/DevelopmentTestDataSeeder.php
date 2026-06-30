<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Owner;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Lease;
use App\Models\Province;
use App\Models\City;
use App\Models\PropertyType;
use App\Models\IndexType;
use App\Models\IndexValue;
use App\Models\TransactionCategory;
use Carbon\Carbon;

class DevelopmentTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Desactivamos temporalmente las restricciones de claves foráneas para limpiar de manera segura
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        
        Lease::truncate();
        Owner::truncate();
        Property::truncate();
        Tenant::truncate();
        \Illuminate\Support\Facades\DB::table('fixed_charges')->truncate();
        \Illuminate\Support\Facades\DB::table('extra_charges')->truncate();
        \Illuminate\Support\Facades\DB::table('collections')->truncate();
        \Illuminate\Support\Facades\DB::table('collection_details')->truncate();
        \Illuminate\Support\Facades\DB::table('collection_payments')->truncate();
        \Illuminate\Support\Facades\DB::table('cash_register_movements')->truncate();
        \Illuminate\Support\Facades\DB::table('settlements')->truncate();
        \Illuminate\Support\Facades\DB::table('settlement_payments')->truncate();
        \Illuminate\Support\Facades\DB::table('index_values')->truncate();
        
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // Obtener datos auxiliares necesarios
        $province = Province::where('name', 'Santiago del Estero')->first() ?? Province::first();
        $cities = City::where('province_id', $province->id)->get();
        $propertyTypes = PropertyType::all();
        $indexTypes = IndexType::all();
        
        $emailComun = 'juanarcemitre@gmail.com';
        $phoneComun = '5493855200492';

        // ==========================================
        // 0. VALORES DE ÍNDICE (IPC e ICL para 2025 y 2026)
        // ==========================================
        $ipc = IndexType::where('name', 'IPC')->first();
        $icl = IndexType::where('name', 'ICL')->first();
        
        if ($ipc) {
            // Sembrar todos los meses de 2025 para el IPC
            for ($m = 1; $m <= 12; $m++) {
                IndexValue::create([
                    'index_type_id' => $ipc->id,
                    'year' => 2025,
                    'month' => $m,
                    'percentage' => round(4.0 + ($m % 3) * 0.5, 2) // Entre 4% y 5%
                ]);
            }
            // Sembrar todos los meses de 2026 para el IPC (Incluyendo de Enero a Diciembre de 2026)
            for ($m = 1; $m <= 12; $m++) {
                IndexValue::create([
                    'index_type_id' => $ipc->id,
                    'year' => 2026,
                    'month' => $m,
                    'percentage' => round(3.0 + ($m % 4) * 0.4, 2) // Entre 3% y 4.2%
                ]);
            }
        }

        if ($icl) {
            // Sembrar todos los meses de 2025 para el ICL
            for ($m = 1; $m <= 12; $m++) {
                IndexValue::create([
                    'index_type_id' => $icl->id,
                    'year' => 2025,
                    'month' => $m,
                    'percentage' => round(3.5 + ($m % 3) * 0.4, 2) // Entre 3.5% y 4.3%
                ]);
            }
            // Sembrar todos los meses de 2026 para el ICL
            for ($m = 1; $m <= 12; $m++) {
                IndexValue::create([
                    'index_type_id' => $icl->id,
                    'year' => 2026,
                    'month' => $m,
                    'percentage' => round(2.8 + ($m % 4) * 0.3, 2) // Entre 2.8% y 3.7%
                ]);
            }
        }

        // ==========================================
        // 1. PROPIETARIOS (3 Propietarios)
        // ==========================================
        $ownersData = [
            [
                'name' => 'Juan Carlos Mitre',
                'dni_cuit' => '20-11222333-9',
                'email' => $emailComun,
                'phone' => $phoneComun,
                'contact' => 'WhatsApp preferentemente por la mañana.',
                'commission_percentage' => 10.00
            ],
            [
                'name' => 'María Inés Alzaga',
                'dni_cuit' => '27-44555666-8',
                'email' => $emailComun,
                'phone' => $phoneComun,
                'contact' => 'Llamar después de las 17 hs.',
                'commission_percentage' => 8.50
            ],
            [
                'name' => 'Pedro Antonio Roca',
                'dni_cuit' => '30-77888999-5',
                'email' => $emailComun,
                'phone' => $phoneComun,
                'contact' => 'Contacto administrativo a través de su secretaria Liliana.',
                'commission_percentage' => 5.00
            ]
        ];

        $owners = [];
        foreach ($ownersData as $data) {
            $owners[] = Owner::create($data);
        }

        // ==========================================
        // 2. INQUILINOS (9 Inquilinos)
        // ==========================================
        $tenantsData = [
            ['name' => 'Sofía Lorenza', 'dni_cuit' => '27-99111222-3', 'email' => $emailComun, 'phone' => $phoneComun, 'contact' => 'Inquilino puntual, trabaja en el banco.'],
            ['name' => 'Federico Gutiérrez', 'dni_cuit' => '20-99333444-5', 'email' => $emailComun, 'phone' => $phoneComun, 'contact' => 'Ingeniero en sistemas, prefiere transferencias bancarias.'],
            ['name' => 'Lucía Belén', 'dni_cuit' => '27-99555666-7', 'email' => $emailComun, 'phone' => $phoneComun, 'contact' => 'Estudiante universitaria de medicina.'],
            ['name' => 'Santiago Ramón', 'dni_cuit' => '20-99777888-9', 'email' => $emailComun, 'phone' => $phoneComun, 'contact' => 'Comerciante independiente.'],
            ['name' => 'Clara Estela', 'dni_cuit' => '27-99888999-1', 'email' => $emailComun, 'phone' => $phoneComun, 'contact' => 'Jubilada, paga en efectivo.'],
            ['name' => 'Damián Andrés', 'dni_cuit' => '20-99000111-2', 'email' => $emailComun, 'phone' => $phoneComun, 'contact' => 'Profesor de secundaria.'],
            ['name' => 'Gabriela Paz', 'dni_cuit' => '27-99222333-4', 'email' => $emailComun, 'phone' => $phoneComun, 'contact' => 'Abogada, muy formal.'],
            ['name' => 'Mariano Joaquín', 'dni_cuit' => '20-99444555-6', 'email' => $emailComun, 'phone' => $phoneComun, 'contact' => 'Arquitecto.'],
            ['name' => 'Camila Agustina', 'dni_cuit' => '27-99666777-8', 'email' => $emailComun, 'phone' => $phoneComun, 'contact' => 'Médica del Hospital Regional. Horarios rotativos.']
        ];

        $tenants = [];
        foreach ($tenantsData as $data) {
            $tenants[] = Tenant::create($data);
        }

        // ==========================================
        // 3. PROPIEDADES (9 Propiedades distribuidas 3, 4 y 2)
        // ==========================================
        $propertiesData = [
            // Propietario 1 (Mitre) -> 3 Propiedades
            [
                'owner_id' => $owners[0]->id,
                'location' => 'Av. Belgrano (N) 450 - Piso 2 depto B',
                'description' => 'Hermoso departamento céntrico de 2 ambientes, luminoso y con aire acondicionado.',
                'province_id' => $province->id,
                'city_id' => $cities->where('name', 'Santiago del Estero (Capital)')->first()->id ?? $cities[0]->id,
                'property_type_id' => $propertyTypes->where('name', 'Departamento')->first()->id ?? $propertyTypes[0]->id,
                'rooms' => 2, 'bathrooms' => 1, 'has_garage' => 0, 'has_patio' => 0, 'has_balcony' => 1, 'pets_allowed' => 1, 'square_meters' => 45.00
            ],
            [
                'owner_id' => $owners[0]->id,
                'location' => 'Pellegrini 231',
                'description' => 'Local comercial amplio con vidriera a la calle en excelente zona transitable.',
                'province_id' => $province->id,
                'city_id' => $cities->where('name', 'Santiago del Estero (Capital)')->first()->id ?? $cities[0]->id,
                'property_type_id' => $propertyTypes->where('name', 'Local Comercial')->first()->id ?? $propertyTypes[0]->id,
                'rooms' => 3, 'bathrooms' => 2, 'has_garage' => 0, 'has_patio' => 0, 'has_balcony' => 0, 'pets_allowed' => 0, 'square_meters' => 90.00
            ],
            [
                'owner_id' => $owners[0]->id,
                'location' => 'Sarmiento 98 - Barrio Centro',
                'description' => 'Oficina moderna, equipada con divisiones de durlock y luminarias LED.',
                'province_id' => $province->id,
                'city_id' => $cities->where('name', 'Santiago del Estero (Capital)')->first()->id ?? $cities[0]->id,
                'property_type_id' => $propertyTypes->where('name', 'Oficina')->first()->id ?? $propertyTypes[0]->id,
                'rooms' => 2, 'bathrooms' => 1, 'has_garage' => 1, 'has_patio' => 0, 'has_balcony' => 0, 'pets_allowed' => 0, 'square_meters' => 50.00
            ],

            // Propietario 2 (Alzaga) -> 4 Propiedades
            [
                'owner_id' => $owners[1]->id,
                'location' => 'Calle 9 de Julio 567',
                'description' => 'Casa de familia amplia con 3 dormitorios, cochera techada y patio con asador.',
                'province_id' => $province->id,
                'city_id' => $cities->where('name', 'La Banda')->first()->id ?? $cities[0]->id,
                'property_type_id' => $propertyTypes->where('name', 'Casa')->first()->id ?? $propertyTypes[0]->id,
                'rooms' => 5, 'bathrooms' => 2, 'has_garage' => 1, 'has_patio' => 1, 'has_balcony' => 0, 'pets_allowed' => 1, 'square_meters' => 180.00
            ],
            [
                'owner_id' => $owners[1]->id,
                'location' => 'Av. Roca (S) 1200 - Torre 1 depto 4A',
                'description' => 'Monoambiente moderno en planta alta, ideal para estudiantes.',
                'province_id' => $province->id,
                'city_id' => $cities->where('name', 'Santiago del Estero (Capital)')->first()->id ?? $cities[0]->id,
                'property_type_id' => $propertyTypes->where('name', 'Departamento')->first()->id ?? $propertyTypes[0]->id,
                'rooms' => 1, 'bathrooms' => 1, 'has_garage' => 0, 'has_patio' => 0, 'has_balcony' => 0, 'pets_allowed' => 0, 'square_meters' => 30.00
            ],
            [
                'owner_id' => $owners[1]->id,
                'location' => 'Independencia 345 - Barrio Belgrano',
                'description' => 'Dúplex residencial con 2 habitaciones, cocina-comedor y patio pequeño.',
                'province_id' => $province->id,
                'city_id' => $cities->where('name', 'Santiago del Estero (Capital)')->first()->id ?? $cities[0]->id,
                'property_type_id' => $propertyTypes->where('name', 'Dúplex')->first()->id ?? $propertyTypes[0]->id,
                'rooms' => 3, 'bathrooms' => 2, 'has_garage' => 1, 'has_patio' => 1, 'has_balcony' => 1, 'pets_allowed' => 1, 'square_meters' => 110.00
            ],
            [
                'owner_id' => $owners[1]->id,
                'location' => 'España 140 - La Banda',
                'description' => 'Cochera de fácil acceso con portón automatizado.',
                'province_id' => $province->id,
                'city_id' => $cities->where('name', 'La Banda')->first()->id ?? $cities[0]->id,
                'property_type_id' => $propertyTypes->where('name', 'Cochera')->first()->id ?? $propertyTypes[0]->id,
                'rooms' => 1, 'bathrooms' => 0, 'has_garage' => 1, 'has_patio' => 0, 'has_balcony' => 0, 'pets_allowed' => 0, 'square_meters' => 15.00
            ],

            // Propietario 3 (Roca) -> 2 Propiedades
            [
                'owner_id' => $owners[2]->id,
                'location' => 'Av. Aguirre 1500 - Esquina Libertad',
                'description' => 'Galpón industrial de grandes dimensiones, tinglado de chapa de 6 metros de altura y entrada para camiones.',
                'province_id' => $province->id,
                'city_id' => $cities->where('name', 'Santiago del Estero (Capital)')->first()->id ?? $cities[0]->id,
                'property_type_id' => $propertyTypes->where('name', 'Galpón')->first()->id ?? $propertyTypes[0]->id,
                'rooms' => 2, 'bathrooms' => 1, 'has_garage' => 1, 'has_patio' => 1, 'has_balcony' => 0, 'pets_allowed' => 0, 'square_meters' => 450.00
            ],
            [
                'owner_id' => $owners[2]->id,
                'location' => 'Av. San Martín s/n - Termas de Río Hondo',
                'description' => 'Local comercial para fines turísticos o gastronómicos a pasos del Casino.',
                'province_id' => $province->id,
                'city_id' => $cities->where('name', 'Termas de Río Hondo')->first()->id ?? $cities[0]->id,
                'property_type_id' => $propertyTypes->where('name', 'Local Comercial')->first()->id ?? $propertyTypes[0]->id,
                'rooms' => 2, 'bathrooms' => 2, 'has_garage' => 0, 'has_patio' => 0, 'has_balcony' => 0, 'pets_allowed' => 0, 'square_meters' => 80.00
            ]
        ];

        $properties = [];
        foreach ($propertiesData as $data) {
            $properties[] = Property::create($data);
        }

        // ==========================================
        // 4. CONTRATOS DE ALQUILER (9 Contratos)
        // ==========================================
        
        // Catálogo de Conceptos Recurrentes
        $recurrentOptions = [
            'Servicio de Agua',
            'Servicio de Luz',
            'Servicio de Gas natural',
            'Expensas Comunes',
            'Tasa de Alumbrado y Limpieza Municipal'
        ];

        for ($i = 0; $i < 9; $i++) {
            $property = $properties[$i];
            $tenant = $tenants[$i];

            // Duración aleatoria entre 6 y 24 meses (requisito: mín 6 meses, máx 2 años)
            $monthsDuration = rand(6, 24);
            
            // Hacemos que algunos inicien en el pasado para tener historial contable, y otros sean actuales
            $startOffsetMonths = rand(-12, 1);
            $startDate = Carbon::today()->addMonths($startOffsetMonths)->startOfMonth();
            $endDate = $startDate->copy()->addMonths($monthsDuration)->subDay();

            // Precios base según tipo de inmueble
            $basePrice = 120000.00 + ($i * 45000.00); 

            // Montos de Garantía y Honorarios
            $depositAmount = $basePrice * 1.5; // El depósito es 1.5 meses de alquiler
            $agencyFeeAmount = $basePrice * 1.2; // Honorarios del 120% del alquiler

            // Cantidades de cuotas (mínimo 0 cuotas, máximo 3)
            $depositInstallments = rand(0, 3);
            if ($depositInstallments === 0) $depositInstallments = 1; // 0 cuotas equivale a un solo pago inicial
            
            $agencyFeeInstallments = rand(0, 3);
            if ($agencyFeeInstallments === 0) $agencyFeeInstallments = 1; // 0 cuotas equivale a un solo pago inicial

            // Métodos de actualización alternados
            $updateType = ($i % 2 === 0) ? 'indexed' : 'fixed';
            $updateFrequency = ($i % 3 === 0) ? 3 : (($i % 3 === 1) ? 4 : 6); // 3, 4 o 6 meses
            
            $indexTypeId = null;
            $updateValue = null;

            if ($updateType === 'indexed') {
                // IPC o ICL alternados
                $index = $indexTypes[$i % count($indexTypes)] ?? null;
                $indexTypeId = $index ? $index->id : null;
            } else {
                // Actualizaciones fijas: incrementos del 20%, 30% o 40%
                $updateValue = 20.00 + (($i % 3) * 10.00);
            }

            // Datos del garante completos (correo común obligatorio)
            $guarantorName = "Guarantor de " . $tenant->name;
            $guarantorDni = "G-" . (30000000 + $i * 123456);
            $guarantorAddress = "Calle Ficticia " . (100 + $i * 50) . ", Santiago del Estero";
            $guarantorPhone = $phoneComun;

            // Creamos el Contrato
            $lease = Lease::create([
                'property_id' => $property->id,
                'tenant_id' => $tenant->id,
                'guarantor_name' => $guarantorName,
                'guarantor_id_number' => $guarantorDni,
                'guarantor_email' => $emailComun, // Obligatorio mail personal del usuario
                'guarantor_address' => $guarantorAddress,
                'guarantor_phone' => $guarantorPhone,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'base_price' => $basePrice,
                'security_deposit_amount' => $depositAmount,
                'agency_fee_amount' => $agencyFeeAmount,
                'update_type' => $updateType,
                'update_frequency_months' => $updateFrequency,
                'update_value' => $updateValue,
                'index_type_id' => $indexTypeId,
                'is_active' => true,
                'renewal_status' => 'original'
            ]);

            // ==========================================
            // A. CONCEPTOS RECURRENTES (0 a 3 conceptos)
            // ==========================================
            $numFixedCharges = rand(0, 3);
            if ($numFixedCharges > 0) {
                // Mezclamos el array para tomar conceptos aleatorios y no repetidos
                $shuffledConcepts = $recurrentOptions;
                shuffle($shuffledConcepts);
                
                for ($j = 0; $j < $numFixedCharges; $j++) {
                    $chargeName = $shuffledConcepts[$j];
                    $chargeAmount = 2500.00 + ($j * 1500.00); // Entre $2500 y $5500

                    $lease->fixedCharges()->create([
                        'name' => $chargeName,
                        'amount' => $chargeAmount,
                        'is_paid_by_agency' => ($j % 2 === 0), // Alternamos pagos entre Inmobiliaria y Propietario
                        'transaction_category_id' => 8 // Categoría "Gastos Recurrentes"
                    ]);
                }
            }

            // ==========================================
            // B. DEPOSITOS EN GARANTÍA EN CUOTAS
            // ==========================================
            if ($depositAmount > 0) {
                $amountPerDepositInstallment = round($depositAmount / $depositInstallments, 2);
                
                for ($k = 1; $k <= $depositInstallments; $k++) {
                    $billingDate = $startDate->copy()->addMonths($k - 1);
                    
                    $lease->extraCharges()->create([
                        'description' => "Depósito en Garantía" . ($depositInstallments > 1 ? " (Cuota $k/$depositInstallments)" : ""),
                        'amount' => ($k == $depositInstallments) ? ($depositAmount - ($amountPerDepositInstallment * ($depositInstallments - 1))) : $amountPerDepositInstallment,
                        'billing_date' => $billingDate->toDateString(),
                        'installment_number' => $k,
                        'total_installments' => $depositInstallments,
                        'is_paid' => false,
                        'transaction_category_id' => 3 // Categoría Depósitos en Garantía (Sistema)
                    ]);
                }
            }

            // ==========================================
            // C. HONORARIOS INMOBILIARIOS EN CUOTAS
            // ==========================================
            if ($agencyFeeAmount > 0) {
                $amountPerFeeInstallment = round($agencyFeeAmount / $agencyFeeInstallments, 2);
                
                for ($k = 1; $k <= $agencyFeeInstallments; $k++) {
                    $billingDate = $startDate->copy()->addMonths($k - 1);
                    
                    $lease->extraCharges()->create([
                        'description' => "Honorarios Inmobiliaria" . ($agencyFeeInstallments > 1 ? " (Cuota $k/$agencyFeeInstallments)" : ""),
                        'amount' => ($k == $agencyFeeInstallments) ? ($agencyFeeAmount - ($amountPerFeeInstallment * ($agencyFeeInstallments - 1))) : $amountPerFeeInstallment,
                        'billing_date' => $billingDate->toDateString(),
                        'installment_number' => $k,
                        'total_installments' => $agencyFeeInstallments,
                        'is_paid' => false,
                        'transaction_category_id' => 2 // Categoría Honorarios Inmobiliarios (Sistema)
                    ]);
                }
            }
        }
    }
}
