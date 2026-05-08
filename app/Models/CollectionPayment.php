<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionPayment extends Model
{
    use HasFactory;

    protected $fillable = ['collection_id', 'amount', 'payment_method_id', 'destination', 'notes', 'payment_date'];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
