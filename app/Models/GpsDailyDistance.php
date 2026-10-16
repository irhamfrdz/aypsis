<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GpsDailyDistance extends Model
{
    protected $fillable = ['mobil_id', 'date', 'distance_km', 'point_count'];

    protected $casts = [
        'distance_km' => 'decimal:3',
        'point_count' => 'integer',
    ];

    public function mobil()
    {
        return $this->belongsTo(Mobil::class);
    }
}
