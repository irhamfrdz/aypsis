<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranBiayaKapalItem extends Model
{
    protected $table = 'pembayaran_biaya_kapal_items';

    protected $fillable = [
        'pembayaran_biaya_kapal_id',
        'biaya_kapal_id',
        'nominal',
        'payment_mode',
        'dp_item_id',
        'nilai_tagihan',
        'sisa_setelah_bayar',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'dp_item_id' => 'integer',
        'nilai_tagihan' => 'decimal:2',
        'sisa_setelah_bayar' => 'decimal:2',
    ];

    public function pembayaran()
    {
        return $this->belongsTo(PembayaranBiayaKapal::class, 'pembayaran_biaya_kapal_id');
    }

    public function dpItem()
    {
        return $this->belongsTo(self::class, 'dp_item_id');
    }

    public function pelunasanItems()
    {
        return $this->hasMany(self::class, 'dp_item_id');
    }

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }
}
