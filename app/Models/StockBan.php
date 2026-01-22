<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBan extends Model
{
    use HasFactory;

    protected $table = 'stock_bans';

    protected $fillable = [
        'nomor_seri',
        'merk',
        'ukuran',
        'kondisi',
        'status',
        'harga_beli',
        'tanggal_masuk',
        'lokasi',
        'keterangan',
        'mobil_id',
    ];

    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'tanggal_masuk' => 'date',
    ];
}
