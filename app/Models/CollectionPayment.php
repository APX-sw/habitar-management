<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CollectionPayment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['collection_id', 'amount', 'account_id', 'destination', 'notes', 'payment_date', 'transferred_to_owner'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    protected $casts = [
        'transferred_to_owner' => 'boolean',
    ];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function movement()
    {
        return $this->morphOne(CashRegisterMovement::class, 'related');
    }
}
