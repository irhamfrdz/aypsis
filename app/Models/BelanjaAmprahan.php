<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BelanjaAmprahan extends Model
{
    use HasFactory;

    protected $table = 'belanja_amprahans';

    protected $fillable = [
        'nomor',
        'tanggal',
        'supplier',
        'nama_barang',
        'total',
        'keterangan',
        'penerima_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total' => 'decimal:2',
    ];

    public function penerima()
    {
        return $this->belongsTo(Karyawan::class, 'penerima_id');
    }
}
