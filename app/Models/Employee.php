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
