<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalAir extends Model
{
    protected $table = 'biaya_kapal_air';

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
        'jasa_air',
        'biaya_agen',
        'sub_total',
        'pph',
        'grand_total',
        'penerima',
        'nomor_rekening',
        'tanggal_invoice_vendor',
    ];

    protected $casts = [
        'kuantitas' => 'decimal:2',
        'harga' => 'decimal:2',
        'jasa_air' => 'decimal:2',
        'biaya_agen' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'pph' => 'decimal:2',
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
     * Accessor for formatted pph
     */
    public function getFormattedPphAttribute()
    {
        return 'Rp ' . number_format($this->pph, 0, ',', '.');
    }
}
