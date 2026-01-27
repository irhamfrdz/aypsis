<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBan extends Model
{
    use HasFactory;

    protected $table = 'stock_bans';

    protected $fillable = [
        'nama_stock_ban_id',
        'nomor_seri',
        'nomor_bukti',
        'merk',
        'ukuran',
        'kondisi',
        'status',
        'harga_beli',
        'tanggal_masuk',
        'tanggal_keluar',
        'lokasi',
        'keterangan',
        'mobil_id',
        'penerima_id',
        'status_ban_luar',
    ];

    public function namaStockBan()
    {
        return $this->belongsTo(NamaStockBan::class, 'nama_stock_ban_id');
    }

    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }

    public function penerima()
    {
        return $this->belongsTo(Karyawan::class, 'penerima_id');
    }

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
    ];
}
