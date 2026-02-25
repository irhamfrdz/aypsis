<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalLabuhTambat extends Model
{
    protected $table = 'biaya_kapal_labuh_tambat';

    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'nomor_referensi',
        'vendor',
        'lokasi',
        'type_id',
        'type_keterangan',
        'is_lumpsum',
        'kuantitas',
        'harga',
        'sub_total',
        'ppn',
        'biaya_materai',
        'grand_total',
        'penerima',
        'nomor_rekening',
        'tanggal_invoice_vendor',
    ];

    protected $casts = [
        'kuantitas' => 'decimal:2',
        'harga' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'ppn' => 'decimal:2',
        'biaya_materai' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'tanggal_invoice_vendor' => 'date',
        'is_lumpsum' => 'boolean',
    ];

    /**
     * Relationship to BiayaKapal
     */
    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }

    /**
     * Accessor for formatted sub_total
     */
    public function getFormattedSubTotalAttribute()
    {
        return 'Rp ' . number_format($this->sub_total, 0, ',', '.');
    }

    /**
     * Accessor for formatted grand_total
     */
    public function getFormattedGrandTotalAttribute()
    {
        return 'Rp ' . number_format($this->grand_total, 0, ',', '.');
    }

    /**
     * Accessor for formatted ppn
     */
    public function getFormattedPpnAttribute()
    {
        return 'Rp ' . number_format($this->ppn, 0, ',', '.');
    }

    /**
     * Accessor for formatted biaya_materai
     */
    public function getFormattedBiayaMateraiAttribute()
    {
        return 'Rp ' . number_format($this->biaya_materai, 0, ',', '.');
    }
}
