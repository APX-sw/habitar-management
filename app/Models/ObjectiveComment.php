<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ObjectiveComment extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    protected $fillable = [
        'objective_id',
        'user_id',
        'comment',
        'file_path',
        'file_name',
    ];

    public function objective()
    {
        return $this->belongsTo(Objective::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
