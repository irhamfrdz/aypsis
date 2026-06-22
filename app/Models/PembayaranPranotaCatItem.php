<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaCatItem extends Model
{
    use Auditable;

    protected $table = 'pembayaran_pranota_cat_items';

    protected $fillable = [
        'pembayaran_pranota_cat_id',
        'pranota_tagihan_cat_id',
        'pranota_perbaikan_kontainer_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Relationship to PembayaranPranotaCat
     */
    public function pembayaran()
    {
        return $this->belongsTo(PembayaranPranotaCat::class, 'pembayaran_pranota_cat_id');
    }

    /**
     * Relationship to PranotaTagihanCat
     */
    public function pranotaTagihanCat()
    {
        return $this->belongsTo(PranotaTagihanCat::class, 'pranota_tagihan_cat_id');
    }

    /**
     * Relationship to PranotaPerbaikanKontainer
     */
    public function pranotaPerbaikanKontainer()
    {
        return $this->belongsTo(PranotaPerbaikanKontainer::class, 'pranota_perbaikan_kontainer_id');
    }
}
