<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_movement_id',
        'account_id',
        'type',
        'amount',
        'description',
        'movement_date',
        'transaction_category_id',
        'deleted_by_user_id',
        'reason',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'deleted_by_user_id');
    }

    public function transactionCategory()
    {
        return $this->belongsTo(TransactionCategory::class);
    }
}
