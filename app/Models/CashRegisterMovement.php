<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CashRegisterMovement extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['account_id', 'type', 'amount', 'description', 'movement_date', 'related_type', 'related_id', 'transaction_category_id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function related()
    {
        return $this->morphTo();
    }

    public function transactionCategory()
    {
        return $this->belongsTo(TransactionCategory::class);
    }
}
