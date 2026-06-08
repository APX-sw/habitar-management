<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegisterClosure extends Model
{
    use HasFactory;

    protected $fillable = [
        'closure_date',
        'system_balance',
        'physical_balance',
        'difference',
        'notes',
        'user_id',
        'initial_balance',
        'opened_at',
        'status'
    ];

    public function bills()
    {
        return $this->hasMany(CashRegisterClosureBill::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
