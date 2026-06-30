<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyDocument extends Model
{
    protected $fillable = ['property_id', 'filename', 'path', 'size', 'mime_type'];
    
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
