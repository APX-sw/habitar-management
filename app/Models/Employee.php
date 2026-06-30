<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Employee extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'document_number',
        'phone',
        'email',
        'hire_date',
        'job_title',
        'emergency_contact_name',
        'emergency_contact_phone',
        'bank_name',
        'cbu_alias',
        'base_salary',
        'update_type',
        'update_frequency_months',
        'increase_index_id',
        'increase_fixed_percentage',
        'last_increase_date',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'last_increase_date' => 'date',
        'base_salary' => 'decimal:2',
        'increase_fixed_percentage' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function increaseIndex()
    {
        return $this->belongsTo(IndexType::class, 'increase_index_id');
    }

    public function salarySettlements()
    {
        return $this->hasMany(EmployeeSalarySettlement::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    // Helper attribute for full name
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
