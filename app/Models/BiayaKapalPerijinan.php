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
        'dari_tanggal',
        'sampai_tanggal',
        'nomor_referensi',
        'vendor',
        'lokasi',
        'sub_total',
        'grand_total',
        'penerima',
        'nomor_rekening',
        'bank_id',
        'tanggal_invoice_vendor',
        'keterangan',
        'jumlah_biaya',
    ];

    protected $casts = [
        'sub_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'jumlah_biaya' => 'decimal:2',
        'tanggal_invoice_vendor' => 'date',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function details()
    {
        return $this->hasMany(BiayaKapalPerijinanDetail::class, 'biaya_kapal_perijinan_id');
    }

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }
}
