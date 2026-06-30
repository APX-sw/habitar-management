<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionDetail extends Model
{
    protected $fillable = ['collection_id', 'name', 'amount', 'type', 'related_id', 'destination', 'transaction_category_id'];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function transactionCategory()
    {
        return $this->belongsTo(TransactionCategory::class);
    }
}
