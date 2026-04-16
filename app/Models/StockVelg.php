<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockVelg extends Model
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
        'created_by',
        'updated_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected $casts = [
        'tanggal_masuk' => 'date',
        'harga_beli' => 'float',
    ];

    public function namaStockBan()
    {
        return $this->belongsTo(NamaStockBan::class, 'nama_stock_ban_id');
    }
}
