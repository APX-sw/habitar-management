<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseDocument extends Model
{
    use HasFactory;

    protected $fillable = ['expense_id', 'filename', 'path', 'size', 'mime_type'];
    
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
