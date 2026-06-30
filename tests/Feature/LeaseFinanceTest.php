<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Owner;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Lease;
use App\Models\Collection;
use App\Models\ExtraCharge;
use App\Models\TransactionCategory;
use App\Models\IndexType;
use App\Models\IndexValue;
use App\Models\FixedCharge;
use App\Models\PropertyType;
use App\Models\Province;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LeaseFinanceTest extends TestCase
{
    use DatabaseTransactions;

    protected $owner;
    protected $tenant;
    protected $property;
    protected $catAlquiler;
    protected $catHonorarios;
    protected $catExpensas;
    protected $catGarantia;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear/Buscar Categorías
        $this->catAlquiler = TransactionCategory::firstOrCreate(
            ['name' => 'Alquileres'],
            ['type' => 'income', 'group' => 'third_party', 'is_system' => true]
        );
        $this->catHonorarios = TransactionCategory::firstOrCreate(
            ['name' => 'Honorarios Inmobiliarios'],
            ['type' => 'income', 'group' => 'agency_profit', 'is_system' => true]
        );
        $this->catExpensas = TransactionCategory::firstOrCreate(
            ['name' => 'Expensas'],
            ['type' => 'income', 'group' => 'third_party', 'is_system' => false]
        );
        $this->catGarantia = TransactionCategory::firstOrCreate(
            ['name' => 'Depósitos en Garantía'],
            ['type' => 'income', 'group' => 'third_party', 'is_system' => true]
        );

        // Geografía & Tipos
        $province = Province::firstOrCreate(['name' => 'Test Province']);
        $city = City::firstOrCreate(['name' => 'Test City', 'province_id' => $province->id]);
        $propType = PropertyType::firstOrCreate(['name' => 'Test Department']);

        // Entidades
        $this->owner = Owner::create([
            'name' => 'Test Owner', 'dni_cuit' => '11-11111111-1', 'email' => 'owner@test.com', 'phone' => '123'
        ]);
        $this->tenant = Tenant::create([
            'name' => 'Test Tenant', 'dni_cuit' => '22-22222222-2', 'email' => 'tenant@test.com', 'phone' => '456'
        ]);
        $this->property = Property::create([
            'owner_id' => $this->owner->id,
            'location' => 'Finance Street 456',
            'property_type_id' => $propType->id,
            'city_id' => $city->id,
            'province_id' => $province->id,
            'rooms' => 3,
            'square_meters' => 60
        ]);
    }

    /**
     * Test que valida la generación de honorarios en cuotas y sus fechas correspondientes.
     */
    public function test_agency_fees_installments_are_generated_with_correct_months_and_amounts()
    {
        $startDate = Carbon::create(2026, 2, 1);
        $endDate = $startDate->copy()->addYear();

        $leaseData = [
            'property_id' => $this->property->id,
            'tenant_id' => $this->tenant->id,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'base_price' => 150000,
            'security_deposit_amount' => 150000,
            'agency_fee_amount' => 90000, // Honorarios totales
            'update_type' => 'fixed',
            'update_frequency_months' => 6,
            'update_value' => 0,
            'initial_fee_installments' => 3, // Pagado en 3 cuotas
        ];

        // Crear contrato llamando al controlador store
        $response = $this->post(route('leases.store'), $leaseData);
        $response->assertRedirect();

        // Verificar el contrato creado
        $lease = Lease::where('property_id', $this->property->id)->where('is_active', true)->first();
        $this->assertNotNull($lease);
        $this->assertEquals(90000, $lease->agency_fee_amount);

        // Verificar cargos extra generados
        $extraCharges = ExtraCharge::where('lease_id', $lease->id)->get();
        
        // Debe tener 1 cargo de Depósito en Garantía + 3 cuotas de Honorarios = 4 cargos
        $this->assertCount(4, $extraCharges);

        $depositCharge = $extraCharges->where('description', 'Depósito en Garantía')->first();
        $this->assertNotNull($depositCharge);
        $this->assertEquals(150000, $depositCharge->amount);
        $this->assertEquals($startDate->format('Y-m-d'), Carbon::parse($depositCharge->billing_date)->format('Y-m-d'));

        $installmentCharges = $extraCharges->where('description', '!=', 'Depósito en Garantía')->sortBy('installment_number');
        $this->assertCount(3, $installmentCharges);

        $expectedBillingDates = [
            '2026-02-01',
            '2026-03-01',
            '2026-04-01'
        ];

        $i = 0;
        foreach ($installmentCharges as $charge) {
            $expectedInstNumber = $i + 1;
            $this->assertEquals($expectedInstNumber, $charge->installment_number);
            $this->assertEquals(3, $charge->total_installments);
            $this->assertEquals(30000, $charge->amount); // 90000 / 3
            $this->assertEquals($expectedBillingDates[$i], Carbon::parse($charge->billing_date)->format('Y-m-d'));
            $this->assertStringContainsString("Honorarios Inmobiliaria (Cuota $expectedInstNumber/3)", $charge->description);
            $i++;
        }
    }

    /**
     * Test que valida que la facturación mensual (Collections) asocie correctamente
     * las cuotas correspondientes a cada mes.
     */
    public function test_monthly_collections_correctly_pulls_the_installment_for_the_corresponding_month()
    {
        $startDate = Carbon::create(2026, 2, 1);
        $endDate = $startDate->copy()->addYear();

        $lease = Lease::create([
            'property_id' => $this->property->id,
            'tenant_id' => $this->tenant->id,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'base_price' => 100000,
            'security_deposit_amount' => 0,
            'agency_fee_amount' => 60000,
            'update_type' => 'fixed',
            'update_frequency_months' => 6,
            'update_value' => 0,
            'is_active' => true
        ]);

        // Crear 3 cuotas manualmente para asegurar fechas exactas
        for ($i = 1; $i <= 3; $i++) {
            $lease->extraCharges()->create([
                'description' => "Honorarios Inmobiliaria (Cuota $i/3)",
                'amount' => 20000,
                'billing_date' => $startDate->copy()->addMonths($i - 1),
                'installment_number' => $i,
                'total_installments' => 3,
                'is_paid' => false,
            ]);
        }

        // Simular la generación de cobro para Febrero 2026 (Debe traer la Cuota 1)
        $response1 = $this->post(route('collections.store'), [
            'month' => 2,
            'year' => 2026,
            'lease_ids' => [$lease->id]
        ]);
        $response1->assertRedirect();

        $collectionFeb = Collection::where('lease_id', $lease->id)->where('month', 2)->where('year', 2026)->first();
        $this->assertNotNull($collectionFeb);
        
        // Debe tener 2 detalles: 1 Alquiler (100k) + 1 Cargo Extra Cuota 1 (20k)
        $detailsFeb = $collectionFeb->details;
        $this->assertCount(2, $detailsFeb);
        $this->assertEquals(120000, $collectionFeb->total_amount);

        $extraDetailFeb = $detailsFeb->where('type', 'extra_charge')->first();
        $this->assertNotNull($extraDetailFeb);
        $this->assertEquals(20000, $extraDetailFeb->amount);
        $this->assertStringContainsString("Cuota 1/3", $extraDetailFeb->name);

        // Simular la generación de cobro para Marzo 2026 (Debe traer la Cuota 2)
        $response2 = $this->post(route('collections.store'), [
            'month' => 3,
            'year' => 2026,
            'lease_ids' => [$lease->id]
        ]);
        $response2->assertRedirect();

        $collectionMar = Collection::where('lease_id', $lease->id)->where('month', 3)->where('year', 2026)->first();
        $this->assertNotNull($collectionMar);
        
        $detailsMar = $collectionMar->details;
        $this->assertCount(2, $detailsMar);
        $this->assertEquals(120000, $collectionMar->total_amount);

        $extraDetailMar = $detailsMar->where('type', 'extra_charge')->first();
        $this->assertNotNull($extraDetailMar);
        $this->assertEquals(20000, $extraDetailMar->amount);
        $this->assertStringContainsString("Cuota 2/3", $extraDetailMar->name);
    }

    /**
     * Test que valida los cálculos de actualizaciones fijas por períodos.
     */
    public function test_fixed_rent_price_updates_correctly_over_periods()
    {
        $startDate = Carbon::create(2026, 1, 1);
        $endDate = $startDate->copy()->addYears(2);

        $lease = Lease::create([
            'property_id' => $this->property->id,
            'tenant_id' => $this->tenant->id,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'base_price' => 100000,
            'update_type' => 'fixed',
            'update_frequency_months' => 6,
            'update_value' => 10, // 10% incremento fijo cada 6 meses
            'is_active' => true
        ]);

        // Meses 1 a 6: Precio base ($100k)
        $this->assertEquals(100000, $lease->calculateRentForDate(1, 2026));
        $this->assertEquals(100000, $lease->calculateRentForDate(6, 2026));

        // Meses 7 a 12: Primer incremento ($110k)
        $this->assertEquals(110000, $lease->calculateRentForDate(7, 2026));
        $this->assertEquals(110000, $lease->calculateRentForDate(12, 2026));

        // Meses 13 a 18: Segundo incremento ($121k)
        $this->assertEquals(121000, $lease->calculateRentForDate(1, 2027));
        $this->assertEquals(121000, $lease->calculateRentForDate(6, 2027));

        // Meses 19 a 24: Tercer incremento ($133.1k)
        $this->assertEquals(133100, $lease->calculateRentForDate(7, 2027));
    }

    /**
     * Test que valida los cálculos de actualizaciones indexadas,
     * acumulando correctamente los índices con el mes de rezago.
     */
    public function test_indexed_rent_price_updates_correctly_accumulating_index_values()
    {
        $startDate = Carbon::create(2026, 2, 1); // Febrero 2026
        $endDate = $startDate->copy()->addYear();

        $indexType = IndexType::create(['name' => 'TEST_IPC', 'description' => 'Test IPC Index']);

        $lease = Lease::create([
            'property_id' => $this->property->id,
            'tenant_id' => $this->tenant->id,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'base_price' => 100000,
            'update_type' => 'indexed',
            'update_frequency_months' => 3, // Cada 3 meses
            'index_type_id' => $indexType->id,
            'is_active' => true
        ]);

        // Sembrar valores de índice para el período de rezago
        // Para el primer incremento (mes 4, Mayo 2026) con frecuencia de 3 meses:
        // PStart = Febrero - 1 mes = Enero 2026.
        // PEnd = Enero + 2 meses = Marzo 2026.
        // Meses requeridos: Enero, Febrero, Marzo 2026.
        
        IndexValue::create(['index_type_id' => $indexType->id, 'year' => 2026, 'month' => 1, 'percentage' => 3.0]); // Enero: 3%
        IndexValue::create(['index_type_id' => $indexType->id, 'year' => 2026, 'month' => 2, 'percentage' => 4.0]); // Febrero: 4%
        IndexValue::create(['index_type_id' => $indexType->id, 'year' => 2026, 'month' => 3, 'percentage' => 3.0]); // Marzo: 3%

        // Antes del mes 4, debe costar el precio base
        $this->assertEquals(100000, $lease->calculateRentForDate(2, 2026));
        $this->assertEquals(100000, $lease->calculateRentForDate(4, 2026)); // Mes 3 es Abril, Mayo es Mes 4

        // En Mayo 2026 (Mes 4 / 3 meses diff desde Feb) debe aplicarse el ajuste indexado acumulado:
        // factor = 1.03 * 1.04 * 1.03 = 1.103336
        // precio = 100000 * 1.103336 = 110333.60
        $expectedAdjustedPrice = round(100000 * (1.03 * 1.04 * 1.03), 2);
        $this->assertEquals($expectedAdjustedPrice, $lease->calculateRentForDate(5, 2026));
    }

    /**
     * Test que valida que si faltan cargar porcentajes de índice,
     * se dispare la excepción correspondiente.
     */
    public function test_missing_index_values_throws_exception()
    {
        $startDate = Carbon::create(2026, 2, 1);
        $endDate = $startDate->copy()->addYear();

        $indexType = IndexType::create(['name' => 'TEST_IPC_MISSING', 'description' => 'Test IPC Index']);

        $lease = Lease::create([
            'property_id' => $this->property->id,
            'tenant_id' => $this->tenant->id,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'base_price' => 100000,
            'update_type' => 'indexed',
            'update_frequency_months' => 3,
            'index_type_id' => $indexType->id,
            'is_active' => true
        ]);

        // Solo sembramos Enero y Febrero (falta Marzo)
        IndexValue::create(['index_type_id' => $indexType->id, 'year' => 2026, 'month' => 1, 'percentage' => 3.0]);
        IndexValue::create(['index_type_id' => $indexType->id, 'year' => 2026, 'month' => 2, 'percentage' => 4.0]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Faltan cargar porcentajes para el índice");

        // Intentar calcular para Mayo 2026 (Mes 4)
        $lease->calculateRentForDate(5, 2026);
    }
}
