<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagihanPelindoItem extends Model
{
    protected $table = 'tagihan_pelindo_items';

    protected $fillable = [
        'tagihan_pelindo_id',
        'nomor_kontainer',
        'pricelist_pelindo_id',
        'kegiatan',
        'ukuran',
        'tarif',
        'jumlah',
        'total',
        'keterangan',
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
        'jumlah' => 'integer',
        'total' => 'decimal:2',
    ];

    // Relationships
    public function tagihanPelindo()
    {
        return $this->belongsTo(TagihanPelindo::class, 'tagihan_pelindo_id');
    }

    public function pricelistPelindo()
    {
        return $this->belongsTo(PricelistPelindo::class, 'pricelist_pelindo_id');
    }
}
