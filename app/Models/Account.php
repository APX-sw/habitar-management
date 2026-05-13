<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'initial_balance', 'is_active'];

    public function movements()
    {
        return $this->hasMany(CashRegisterMovement::class);
    }
    
    public function getCurrentBalanceAttribute()
    {
        $income = $this->movements()->where('type', 'income')->sum('amount');
        $expense = $this->movements()->where('type', 'expense')->sum('amount');
        return $this->initial_balance + $income - $expense;
    }
}
