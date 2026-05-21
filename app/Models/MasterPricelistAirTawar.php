<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPricelistAirTawar extends Model
{
    use Auditable, HasFactory;

    protected $table = 'master_pricelist_air_tawar';

    protected $fillable = [
        'nama_agen',
        'harga',
        'keterangan',
        'lokasi',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    /**
     * Accessor for formatted harga
     */
    public function getFormattedHargaAttribute()
    {
        return 'Rp '.number_format($this->harga, 0, ',', '.');
    }
}
