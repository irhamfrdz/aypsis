<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricelistPerijinan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tarif',
        'lokasi',
        'status',
    ];

    public function getFormattedTarifAttribute()
    {
        return 'Rp '.number_format($this->tarif, 0, ',', '.');
    }
}
