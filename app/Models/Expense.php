<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'account_id', 'date', 'amount', 'description', 'is_paid'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function movement()
    {
        return $this->morphOne(CashRegisterMovement::class, 'related');
    }
}
