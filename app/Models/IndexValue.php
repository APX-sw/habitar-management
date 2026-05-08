<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndexValue extends Model
{
    use HasFactory;

    protected $fillable = ['index_type_id', 'year', 'month', 'percentage'];

    public function type()
    {
        return $this->belongsTo(IndexType::class, 'index_type_id');
    }
}
