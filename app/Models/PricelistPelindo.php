<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricelistPelindo extends Model
{
    use Auditable, HasFactory;

    protected $table = 'pricelist_pelindos';

    protected $fillable = [
        'kegiatan',
        'ukuran',
        'tarif',
        'keterangan',
        'status',
        'status_kontainer',
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
    ];

    /**
     * Scope to only include active pricelists
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope to only include inactive pricelists
     */
    public function scopeNonaktif($query)
    {
        return $query->where('status', 'nonaktif');
    }

    /**
     * Accessor for formatted tarif in Indonesian Rupiah
     */
    public function getFormattedTarifAttribute()
    {
        return 'Rp '.number_format((float) $this->tarif, 0, ',', '.');
    }
}
