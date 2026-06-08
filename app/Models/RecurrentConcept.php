<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurrentConcept extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'transaction_category_id'];

    public function transactionCategory()
    {
        return $this->belongsTo(TransactionCategory::class);
    }

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'property_recurrent_concepts')
                    ->withPivot('id', 'payment_code', 'notes')
                    ->withTimestamps();
    }
}
