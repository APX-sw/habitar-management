<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaseDocument extends Model
{
    protected $fillable = ['lease_id', 'filename', 'path', 'size', 'mime_type'];

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }
}
