<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaStockAkunItem extends Model
{
    protected $table = 'pembayaran_pranota_stock_akun_items';

    protected $fillable = ['akun_coa_id', 'nominal', 'keterangan'];

    protected $casts = ['nominal' => 'decimal:2'];

    public function akunCoa()
    {
        return $this->belongsTo(Coa::class, 'akun_coa_id');
    }
}
