<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaBensin extends Model
{
    protected $table = 'biaya_bensin';

    protected $fillable = [
        'tanggal',
        'mobil_id',
        'alat_berat_id',
        'nomor_kartu',
        'karyawan_id',
        'km_awal',
        'km_akhir',
        'liter',
        'biaya',
        'harga_per_liter',
        'keterangan',
        'nomor_rekening',
        'nama_bank',
        'penerima_rekening',
        'created_by',
        'bukti_beli',
        'status',
        'approved_by',
        'approved_at',
        'pranota_biaya_bensin_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'liter' => 'decimal:2',
        'biaya' => 'decimal:2',
        'harga_per_liter' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }

    public function alatBerat()
    {
        return $this->belongsTo(AlatBerat::class, 'alat_berat_id');
    }

    public function supir()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function pranotaBiayaBensin()
    {
        return $this->belongsTo(PranotaBiayaBensin::class, 'pranota_biaya_bensin_id');
    }
}
