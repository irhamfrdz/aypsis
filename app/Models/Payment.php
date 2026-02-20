<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'container_trip_id',
        'periode_bulan',
        'no_invoice',
    ];

    public function containerTrip()
    {
        return $this->belongsTo(ContainerTrip::class);
    }
}
