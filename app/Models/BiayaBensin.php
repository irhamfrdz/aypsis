<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaBensin extends Model
{
    protected $table = 'biaya_bensin';

    protected $fillable = [
        'tanggal',
        'mobil_id',
        'karyawan_id',
        'km_awal',
        'km_akhir',
        'liter',
        'biaya',
        'harga_per_liter',
        'keterangan',
        'created_by',
        'bukti_beli',
        'status',
        'approved_by',
        'approved_at',
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
}
