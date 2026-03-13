<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaKapalPerijinan extends Model
{
    use HasFactory;

    protected $table = 'biaya_kapal_perijinan';

    protected $fillable = [
        'biaya_kapal_id',
        'nama_kapal',
        'no_voyage',
        'nomor_referensi',
        'vendor',
        'lokasi',
        'biaya_insa',
        'biaya_pbni',
        'sub_total',
        'pph',
        'grand_total',
        'penerima',
        'nomor_rekening',
        'tanggal_invoice_vendor',
        'keterangan',
        'jumlah_biaya'
    ];

    protected $casts = [
        'biaya_insa' => 'decimal:2',
        'biaya_pbni' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'pph' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'jumlah_biaya' => 'decimal:2',
        'tanggal_invoice_vendor' => 'date',
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }
}
