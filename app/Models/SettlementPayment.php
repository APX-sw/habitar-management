<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettlementPayment extends Model
{
    use HasFactory;

    protected $fillable = ['settlement_id', 'owner_bank_account_id', 'account_id', 'amount', 'date'];

    public function settlement()
    {
        return $this->belongsTo(Settlement::class);
    }

    public function ownerBankAccount()
    {
        return $this->belongsTo(OwnerBankAccount::class);
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
