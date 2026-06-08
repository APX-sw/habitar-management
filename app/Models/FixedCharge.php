<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class FixedCharge extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'lease_id',
        'recurrent_concept_id',
        'name',
        'amount',
        'transaction_category_id',
        'is_paid_by_agency'
    ];

    protected $casts = [
        'is_paid_by_agency' => 'boolean',
    ];

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function recurrentConcept()
    {
        return $this->belongsTo(RecurrentConcept::class);
    }

    public function transactionCategory()
    {
        return $this->belongsTo(TransactionCategory::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }
}
