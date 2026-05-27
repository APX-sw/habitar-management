<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EmployeeDocument extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'employee_id',
        'document_type',
        'file_path',
        'original_name',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }
}
