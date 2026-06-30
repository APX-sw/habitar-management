<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalarySettlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'base_amount',
        'advances_amount',
        'bonuses_amount',
        'net_amount',
        'status',
        'paid_amount',
        'payment_date',
        'account_id'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'base_amount' => 'decimal:2',
        'advances_amount' => 'decimal:2',
        'bonuses_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function bonuses()
    {
        return $this->hasMany(EmployeeSalaryBonus::class);
    }

    public function payments()
    {
        return $this->hasMany(EmployeeSalaryPayment::class);
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
