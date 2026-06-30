<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegisterClosureBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_register_closure_id',
        'bill_value',
        'quantity'
    ];

    public function closure()
    {
        return $this->belongsTo(CashRegisterClosure::class, 'cash_register_closure_id');
    }
}
