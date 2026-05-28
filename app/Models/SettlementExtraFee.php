<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettlementExtraFee extends Model
{
    use HasFactory;

    protected $fillable = ['settlement_id', 'description', 'amount'];

    public function settlement()
    {
        return $this->belongsTo(Settlement::class);
    }
}
