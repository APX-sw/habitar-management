<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    public function payments()
    {
        return $this->hasMany(CollectionPayment::class, 'payment_method_id');
    }
}
