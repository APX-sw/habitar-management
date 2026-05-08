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
        'renewal_status'
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
                $pStart = $startDate->copy()->addMonths(($p - 1) * $this->update_frequency_months);
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
                    throw new \Exception("Faltan cargar porcentajes para el índice '{$this->indexType->name}' entre {$pStart->format('m/Y')} y {$pEnd->format('m/Y')}. Por favor, cárgalos en Configuración antes de generar el cobro.");
                }

                $accumulatedPercentage = $existingValues->sum('percentage');
                $currentPrice = $currentPrice * (1 + ($accumulatedPercentage / 100));
            }
            return round($currentPrice, 2);
        }
    }
}
