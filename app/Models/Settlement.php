<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    use HasFactory;

    protected $fillable = ['owner_id', 'month', 'year', 'rent_total', 'total_income', 'total_expense', 'agency_commission', 'net_amount', 'status'];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function payments()
    {
        return $this->hasMany(SettlementPayment::class);
    }
}
