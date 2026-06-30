<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start_month',
        'start_year',
        'end_month',
        'end_year',
        'owner_ids'
    ];

    protected $casts = [
        'owner_ids' => 'array'
    ];

    /**
     * Obtiene los propietarios asociados a este reporte.
     */
    public function getOwnersAttribute()
    {
        return Owner::whereIn('id', $this->owner_ids)->get();
    }
}
