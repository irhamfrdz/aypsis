<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockRingVelg extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_stock_ban_id',
        'nomor_bukti',
        'ukuran',
        'type',
        'qty',
        'harga_beli',
        'tanggal_masuk',
        'lokasi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'harga_beli' => 'decimal:2',
    ];

    public function namaStockBan()
    {
        return $this->belongsTo(NamaStockBan::class, 'nama_stock_ban_id');
    }
}
