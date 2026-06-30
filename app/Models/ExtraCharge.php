<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ExtraCharge extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['lease_id', 'description', 'amount', 'billing_date', 'installment_number', 'total_installments', 'is_paid', 'transaction_category_id'];

    public function lease()
    {
        return $this->belongsTo(Lease::class);
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
