<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = ['lease_id', 'month', 'year', 'rent_amount', 'total_amount', 'status', 'payment_date'];

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
