<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Settlement extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['owner_id', 'month', 'year', 'rent_total', 'total_income', 'total_expense', 'agency_commission', 'net_amount', 'status'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function payments()
    {
        return $this->hasMany(SettlementPayment::class);
    }
}
