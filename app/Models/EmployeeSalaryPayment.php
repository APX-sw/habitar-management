<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EmployeeSalaryPayment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'employee_salary_settlement_id',
        'amount',
        'account_id',
        'payment_date'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function settlement()
    {
        return $this->belongsTo(EmployeeSalarySettlement::class, 'employee_salary_settlement_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function movement()
    {
        return $this->morphOne(CashRegisterMovement::class, 'related');
    }
}
