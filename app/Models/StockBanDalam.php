<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockBanDalam extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stock_ban_dalams';

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
        'harga_beli' => 'decimal:2',
        'qty' => 'integer',
        'tanggal_masuk' => 'date',
    ];

    public function namaStockBan()
    {
        return $this->belongsTo(NamaStockBan::class, 'nama_stock_ban_id');
    }

    public function usages()
    {
        return $this->hasMany(StockBanDalamUsage::class);
    }
}
