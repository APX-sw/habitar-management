<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Lease extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'property_id',
        'tenant_id',
        'guarantor_name',
        'guarantor_id_number',
        'guarantor_email',
        'guarantor_address',
        'guarantor_phone',
        'start_date',
        'end_date',
        'base_price',
        'security_deposit_amount',
        'agency_fee_amount',
        'update_type',
        'update_frequency_months',
        'update_value',
        'index_type_id',
        'is_active',
        'parent_lease_id',
        'property_review_status',
        'property_review_notes',
        'renewal_status',
        'termination_reason',
        'invoicing_enabled',
        'invoicing_percentage'
    ];

    public function parentLease()
    {
        return $this->belongsTo(Lease::class, 'parent_lease_id');
    }

    public function renewals()
    {
        return $this->hasMany(Lease::class, 'parent_lease_id');
    }

    public function indexType()
    {
        return $this->belongsTo(IndexType::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function fixedCharges()
    {
        return $this->hasMany(FixedCharge::class);
    }

    public function extraCharges()
    {
        return $this->hasMany(ExtraCharge::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function documents()
    {
        return $this->hasMany(LeaseDocument::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    /**
     * Calcula el monto del alquiler para un mes/año específico.
     */
    public function calculateRentForDate($month, $year)
    {
        $targetDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $startDate = \Carbon\Carbon::parse($this->start_date)->startOfMonth();
        
        if ($targetDate->lessThan($startDate)) return $this->base_price;

        $monthsDiff = $startDate->diffInMonths($targetDate);
        $periods = floor($monthsDiff / $this->update_frequency_months);

        if ($this->update_type === 'fixed') {
            return round($this->base_price * pow(1 + ($this->update_value / 100), $periods), 2);
        } else {
            $currentPrice = $this->base_price;
            for ($p = 1; $p <= $periods; $p++) {
                // Aplicamos un mes de desfase hacia atrás (lag) porque el índice del último mes
                // del período aún no está publicado al momento de iniciar el cobro (ej. al 1 de Abril no está Marzo, se usa Dic-Ene-Feb)
                $pStart = $startDate->copy()->addMonths(($p - 1) * $this->update_frequency_months)->subMonth();
                $pEnd = $pStart->copy()->addMonths($this->update_frequency_months - 1);
                
                // Verificar que existan TODOS los meses en el rango
                $requiredMonthsCount = $this->update_frequency_months;
                $existingValues = IndexValue::where('index_type_id', $this->index_type_id)
                    ->where(function($q) use ($pStart, $pEnd) {
                        $q->where(function($sq) use ($pStart, $pEnd) {
                            $sq->where('year', '>', $pStart->year)->orWhere(function($ssq) use ($pStart) {
                                $ssq->where('year', $pStart->year)->where('month', '>=', $pStart->month);
                            });
                        })->where(function($sq) use ($pEnd) {
                            $sq->where('year', '<', $pEnd->year)->orWhere(function($ssq) use ($pEnd) {
                                $ssq->where('year', $pEnd->year)->where('month', '<=', $pEnd->month);
                            });
                        });
                    });

                if ($existingValues->count() < $requiredMonthsCount) {
                    throw new \Exception("Faltan cargar porcentajes para el índice '{$this->indexType->name}' entre {$pStart->format('m/Y')} y {$pEnd->format('m/Y')} (considerando el mes de rezago por publicación). Por favor, cárgalos en Configuración antes de generar el cobro.");
                }

                $accumulatedFactor = 1.0;
                // Ordenar por año y mes para acumular correctamente en orden cronológico
                $values = $existingValues->orderBy('year', 'asc')->orderBy('month', 'asc')->get();
                foreach ($values as $val) {
                    $accumulatedFactor *= (1 + ($val->percentage / 100));
                }
                $currentPrice = $currentPrice * $accumulatedFactor;
            }
            return round($currentPrice, 2);
        }
    }

    /**
     * Determina si una fecha específica coincide con un periodo de actualización de alquiler.
     */
    public function isUpdateMonthForDate($month, $year)
    {
        $targetDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $startDate = \Carbon\Carbon::parse($this->start_date)->startOfMonth();
        
        if ($targetDate->lessThanOrEqualTo($startDate)) {
            return false;
        }

        $monthsDiff = $startDate->diffInMonths($targetDate);
        return ($monthsDiff > 0 && ($monthsDiff % $this->update_frequency_months === 0));
    }

    /**
     * Calcula el monto a facturar para un mes/año dado.
     * Retorna null si el contrato no tiene facturación habilitada.
     */
    public function getInvoiceAmountForDate($month, $year)
    {
        if (!$this->invoicing_enabled || !$this->invoicing_percentage) {
            return null;
        }
        $rent = $this->calculateRentForDate($month, $year);
        return round($rent * ($this->invoicing_percentage / 100), 2);
    }
}

