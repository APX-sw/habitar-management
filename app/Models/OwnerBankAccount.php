<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class OwnerBankAccount extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['owner_id', 'cbu_alias', 'holder_name', 'holder_cuit'];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }
}
