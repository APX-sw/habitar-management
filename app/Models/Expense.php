<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Expense extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'property_id', 
        'account_id', 
        'date', 
        'amount', 
        'description', 
        'attachment_path',
        'is_paid', 
        'transaction_category_id',
        'employee_id',
        'applies_to_settlement',
        'paid_with_habitar_funds'
    ];

    protected $casts = [
        'date' => 'date',
        'is_paid' => 'boolean',
        'applies_to_settlement' => 'boolean',
        'paid_with_habitar_funds' => 'boolean'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function movement()
    {
        return $this->morphOne(CashRegisterMovement::class, 'related');
    }

    public function transactionCategory()
    {
        return $this->belongsTo(TransactionCategory::class);
    }

    public function documents()
    {
        return $this->hasMany(ExpenseDocument::class);
    }
}
