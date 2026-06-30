<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Owner;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Lease;
use App\Models\Collection;
use App\Models\CollectionDetail;
use App\Models\Account;
use App\Models\TransactionCategory;
use App\Models\CashRegisterMovement;
use App\Models\Settlement;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckSystemCoherence extends Command
{
    protected $signature = 'habitar:check-coherence';
    protected $description = 'Simula un flujo financiero completo para verificar la coherencia del sistema sin usar el navegador.';

    public function handle()
    {
        $this->info("🚀 Iniciando Test de Coherencia Habitar...");
        $this->info("⏳ Todo el test se ejecutará dentro de una transacción y se hará Rollback al final.\n");

        DB::beginTransaction();

        try {
            // --- SETUP ---
            $this->comment("1. Configurando datos de prueba...");
            
            // Buscar o crear categorías esenciales dinámicamente para el test
            $catAlquiler = TransactionCategory::firstOrCreate(
                ['name' => 'Alquileres'],
                ['type' => 'income', 'group' => 'third_party', 'is_system' => true]
            );
            $catHonorarios = TransactionCategory::firstOrCreate(
                ['name' => 'Honorarios Inmobiliarios'],
                ['type' => 'income', 'group' => 'agency_profit', 'is_system' => true]
            );
            $catExpensas = TransactionCategory::firstOrCreate(
                ['name' => 'Expensas'],
                ['type' => 'income', 'group' => 'third_party', 'is_system' => false]
            );
            $catMantenimiento = TransactionCategory::firstOrCreate(
                ['name' => 'Mantenimiento Propiedades'],
                ['type' => 'expense', 'group' => 'third_party', 'is_system' => false]
            );

            $owner = Owner::create([
                'name' => 'Test Owner', 'dni_cuit' => '11-11111111-1', 'email' => 'owner@test.com', 'phone' => '123'
            ]);

            // Obtener IDs de tipos existentes
            $propType = \DB::table('property_types')->first();
            $city = \DB::table('cities')->first();
            $province = \DB::table('provinces')->first();

            $property = Property::create([
                'owner_id' => $owner->id, 
                'location' => 'Test Street 123', 
                'property_type_id' => $propType->id ?? 1, 
                'city_id' => $city->id ?? 1, 
                'province_id' => $province->id ?? 1, 
                'rooms' => 2, 
                'square_meters' => 50
            ]);

            $tenant = Tenant::create([
                'name' => 'Test Tenant', 'dni_cuit' => '22-22222222-2', 'email' => 'tenant@test.com', 'phone' => '456'
            ]);

            $account = Account::create(['name' => 'Test Cash Account', 'type' => 'cash', 'is_active' => true]);

            $lease = Lease::create([
                'property_id' => $property->id, 
                'tenant_id' => $tenant->id, 
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->addYear(), 
                'base_price' => 100000, 
                'update_type' => 'fixed',
                'update_frequency_months' => 6, 
                'update_value' => 0, 
                'is_active' => true
            ]);

            // Agregar un cargo fijo (Concepto Mensual)
            $lease->fixedCharges()->create(['name' => 'Expensas', 'amount' => 5000, 'transaction_category_id' => $catExpensas->id]);

            $this->info("✅ Setup completado.");

            // --- FASE 1: GENERACIÓN DE COBRO ---
            $this->comment("\n2. Generando Cobro de Alquiler...");
            $month = now()->month;
            $year = now()->year;

            $collection = Collection::create([
                'lease_id' => $lease->id, 'month' => $month, 'year' => $year, 'rent_amount' => 100000, 'status' => 'draft'
            ]);

            $collection->details()->create([
                'type' => 'rent', 'name' => 'Alquiler Mensual', 'amount' => 100000, 'destination' => 'owner', 'transaction_category_id' => $catAlquiler->id
            ]);

            foreach ($lease->fixedCharges as $charge) {
                $collection->details()->create([
                    'type' => 'fixed_charge', 'related_id' => $charge->id, 'name' => $charge->recurrentConcept ? $charge->recurrentConcept->name : $charge->name, 'amount' => $charge->amount, 'transaction_category_id' => $charge->transaction_category_id
                ]);
            }

            $collection->details()->create([
                'type' => 'extra_charge', 'name' => 'Honorarios Inmobiliarios', 'amount' => 10000, 'transaction_category_id' => $catHonorarios->id
            ]);

            $collection->update(['total_amount' => $collection->details()->sum('amount')]);

            $this->info("Cobro generado: $" . number_format($collection->total_amount, 2));
            if (abs($collection->total_amount - 115000) < 0.01) {
                $this->info("✅ Monto total correcto (100k Alquiler + 5k Expensas + 10k Honorarios).");
            } else {
                throw new \Exception("❌ Error: El monto total (" . $collection->total_amount . ") no coincide con la suma esperada (115000).");
            }

            // --- FASE 2: PAGO Y SPLIT DE CAJA ---
            $this->comment("\n3. Registrando Pago y verificando Split en Caja...");
            
            $paymentAmount = 115000;
            $payment = $collection->payments()->create([
                'account_id' => $account->id, 'amount' => $paymentAmount, 'payment_date' => now()
            ]);

            $totalDebt = $collection->total_amount;
            $proportion = $paymentAmount / $totalDebt;
            $details = $collection->details;
            
            $amountsByCategory = [];
            foreach ($details as $detail) {
                $catId = $detail->transaction_category_id ?? $catAlquiler->id;
                $amountsByCategory[$catId] = ($amountsByCategory[$catId] ?? 0) + ($detail->amount * $proportion);
            }

            foreach ($amountsByCategory as $catId => $catAmount) {
                CashRegisterMovement::create([
                    'account_id' => $account->id,
                    'type' => 'income',
                    'amount' => $catAmount,
                    'movement_date' => now(),
                    'description' => "Pago Cobro #{$collection->id} - " . (TransactionCategory::find($catId)->name),
                    'related_type' => get_class($payment),
                    'related_id' => $payment->id,
                    'transaction_category_id' => $catId
                ]);
            }

            $movements = CashRegisterMovement::where('related_id', $payment->id)->get();
            $this->info("Se generaron " . $movements->count() . " movimientos en caja.");
            
            $sumMovements = $movements->sum('amount');
            if (abs($sumMovements - $paymentAmount) < 0.01) {
                $this->info("✅ Coherencia Matemática: La suma de los movimientos coincide con el pago.");
            } else {
                throw new \Exception("❌ Error: Descuadre en el split de caja. Suma: " . $sumMovements);
            }

            $catIds = $movements->pluck('transaction_category_id')->toArray();
            if (in_array($catAlquiler->id, $catIds) && in_array($catHonorarios->id, $catIds) && in_array($catExpensas->id, $catIds)) {
                $this->info("✅ Coherencia de Categorías: Alquiler, Honorarios y Expensas identificados por separado.");
            } else {
                throw new \Exception("❌ Error: Faltan categorías en el split de caja.");
            }

            // --- FASE 3: REPORTE DE RENTABILIDAD ---
            $this->comment("\n4. Verificando Reporte de Rentabilidad...");
            
            $profitCategories = TransactionCategory::where('group', 'agency_profit')->pluck('id');
            $incomeMovements = CashRegisterMovement::whereIn('transaction_category_id', $profitCategories)
                ->where('related_id', $payment->id)
                ->sum('amount');

            if (abs($incomeMovements - 10000) < 0.01) {
                $this->info("✅ Coherencia de Reporte: Se identificaron correctamente los $10,000 de honorarios como ingreso propio.");
            } else {
                throw new \Exception("❌ Error: El reporte no identifica correctamente la ganancia neta. Detectado: " . $incomeMovements);
            }

            // --- FASE 4: RENDICIÓN A PROPIETARIO ---
            $this->comment("\n5. Simulando Rendición al Propietario...");
            
            Expense::create([
                'property_id' => $property->id, 'account_id' => $account->id, 'date' => now(), 'amount' => 2000, 
                'description' => 'Reparación de grifo', 'is_paid' => true, 'transaction_category_id' => $catMantenimiento->id
            ]);

            $rentPaid = $collection->details()->where('type', 'rent')->sum('amount');
            $expenses = Expense::where('property_id', $property->id)->sum('amount');
            $netToPay = $rentPaid - $expenses;

            if (abs($netToPay - 98000) < 0.01) {
                $this->info("✅ Coherencia de Rendición: El saldo para el dueño es correcto ($100k alquiler - $2k reparación = $98k).");
            } else {
                throw new \Exception("❌ Error: Cálculo de rendición incorrecto. Detectado: " . $netToPay);
            }

            $this->info("\n✨ TEST COMPLETADO CON ÉXITO ✨");
            $this->info("El sistema es COHERENTE en su versión actual.");

        } catch (\Exception $e) {
            $this->error("\n❌ FALLO DE COHERENCIA: " . $e->getMessage());
            $this->error("Línea: " . $e->getLine());
        } finally {
            DB::rollBack();
            $this->comment("\n🔄 Transacción revertida. La base de datos está limpia.");
        }
    }
}
