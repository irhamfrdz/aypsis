<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pranota extends Model
{
    protected $table = 'pranotalist';

    protected $fillable = [
        'nomor_pranota',
        'tanggal_pranota',
        'jenis_pranota',
        'tagihan_ids',
        'total_amount',
        'jumlah_tagihan',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tagihan_ids' => 'array',
        'tanggal_pranota' => 'date',
        'total_amount' => 'decimal:2',
    ];

    // Factory methods for creating specific types of pranota
    public static function forSuratJalan()
    {
        return new PranotaSuratJalan;
    }

    public static function forUangJalan()
    {
        return new PranotaUangJalan;
    }

    public function tagihanCatItems()
    {
        return $this->belongsToMany(
            \App\Models\TagihanCat::class,
            'pranota_tagihan_cat_items',
            'pranota_id',
            'tagihan_cat_id'
        )->withTimestamps();
    }

    public function tagihanCat()
    {
        return $this->belongsToMany(
            \App\Models\TagihanCat::class,
            'pranota_tagihan_cat_items',
            'pranota_id',
            'tagihan_cat_id'
        )->withTimestamps();
    }
}
