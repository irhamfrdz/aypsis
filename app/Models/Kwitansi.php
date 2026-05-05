<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kwitansi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelanggan_kode', 'pelanggan_nama', 'terima_dari', 'kirim_ke', 'no_po', 
        'kwt_no', 'tgl_inv', 'tgl_kirim', 'fob', 'syarat_pembayaran', 
        'pengirim', 'penjual', 'keterangan', 'akun_piutang', 'sub_total', 
        'discount_persen', 'discount_nominal', 'biaya_kirim', 'total_invoice',
        'kena_pajak', 'termasuk_pajak'
    ];

    protected $casts = [
        'tgl_inv' => 'date',
        'tgl_kirim' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(KwitansiDetail::class);
    }
}
