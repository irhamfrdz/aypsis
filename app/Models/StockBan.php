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
        'tempat_beli',
        'tanggal_masuk',
        'tanggal_keluar',
        'lokasi',
        'keterangan',
        'mobil_id',
        'penerima_id',
        'status_ban_luar',
        'status_masak',
        'jumlah_masak',
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

    public static function generateNextInvoice()
    {
        $yearMonth = date('Ym');
        $prefix = 'INV-KS-' . $yearMonth . '-';
        
        $lastInvoice = self::where('nomor_bukti', 'like', $prefix . '%')
            ->orderBy('nomor_bukti', 'desc')
            ->first();

        if (!$lastInvoice) {
            return $prefix . '001';
        }

        $lastNumber = intval(substr($lastInvoice->nomor_bukti, -3));
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $nextNumber;
    }
}
