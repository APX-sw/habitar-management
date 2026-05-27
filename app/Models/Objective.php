<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objective extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'creator_id',
        'title',
        'description',
        'due_date',
        'period',
        'status',
        'employee_notes',
        'admin_comment',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
