<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaVendorSupirCoaItem extends Model
{
    protected $table = 'pembayaran_pranota_vendor_supir_coa_items';

    protected $fillable = ['pembayaran_id', 'akun_coa_id', 'nominal', 'memo'];

    protected $casts = ['nominal' => 'decimal:2'];

    public function akunCoa()
    {
        return $this->belongsTo(Coa::class, 'akun_coa_id');
    }
}
