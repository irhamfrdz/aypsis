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
        'keterangan',
        'created_by'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'liter' => 'decimal:2',
        'biaya' => 'decimal:2',
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
}
