<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Owner extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['name', 'dni_cuit', 'email', 'phone', 'contact', 'commission_percentage'];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(OwnerBankAccount::class);
    }

    public function settlements()
    {
        return $this->hasMany(Settlement::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }
}
