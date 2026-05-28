<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryBonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_salary_settlement_id',
        'description',
        'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function settlement()
    {
        return $this->belongsTo(EmployeeSalarySettlement::class, 'employee_salary_settlement_id');
    }
}
