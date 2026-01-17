<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class MasterPricelistAirTawar extends Model
{
    use HasFactory, Auditable;

    protected $table = 'master_pricelist_air_tawar';

    protected $fillable = [
        'nama_agen',
        'harga',
        'keterangan'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    /**
     * Accessor for formatted harga
     */
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}
