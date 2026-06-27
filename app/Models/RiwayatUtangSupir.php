<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatUtangSupir extends Model
{
    use HasFactory;

    protected $table = 'riwayat_utang_supirs';

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'tipe',
        'nominal',
        'referensi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
