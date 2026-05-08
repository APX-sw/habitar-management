<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Tenant extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name', 'dni_cuit', 'email', 'phone', 'emergency_contact', 'contact', 'references'
    ];

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    public function activeLease()
    {
        return $this->hasOne(Lease::class)->where('is_active', true);
    }

    public function getTotalDebtAttribute()
    {
        return $this->getPendingCollections()->sum('pending_amount');
    }

    public function getPendingCollections()
    {
        $leaseIds = $this->leases()->pluck('id');
        return \App\Models\Collection::whereIn('lease_id', $leaseIds)
            ->where('status', '!=', 'paid')
            ->with(['payments', 'lease.property'])
            ->get()
            ->map(function($collection) {
                $collection->paid_amount = $collection->payments->sum('amount');
                $collection->pending_amount = $collection->total_amount - $collection->paid_amount;
                return $collection;
            })
            ->filter(function($collection) {
                return $collection->pending_amount > 0;
            });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }
}
