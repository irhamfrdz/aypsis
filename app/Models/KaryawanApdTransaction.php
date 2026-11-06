<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaryawanApdTransaction extends Model
{
    protected $fillable = ['karyawan_id', 'asset_id', 'tanggal_serah_terima', 'nama_barang', 'ukuran', 'qty', 'keterangan', 'status', 'stock_deducted'];

    protected $casts = ['tanggal_serah_terima' => 'date', 'qty' => 'decimal:2', 'stock_deducted' => 'boolean'];

    public function getNomorTransaksiAttribute(): string
    {
        return 'TRX-APD-'.str_pad((string) $this->id, 3, '0', STR_PAD_LEFT);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
