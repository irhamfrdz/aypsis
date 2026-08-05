<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GpsHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'mobil_id',
        'imei_gps',
        'lat',
        'lng',
        'speed',
        'status',
        'alamat',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
    ];

    public function mobil()
    {
        return $this->belongsTo(Mobil::class);
    }
}
