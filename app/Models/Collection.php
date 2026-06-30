<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Collection extends Model
{
    use LogsActivity;

    protected $fillable = ['lease_id', 'month', 'year', 'rent_amount', 'total_amount', 'status', 'payment_date'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function details()
    {
        return $this->hasMany(CollectionDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(CollectionPayment::class);
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return $this->total_amount - $this->total_paid;
    }
}
