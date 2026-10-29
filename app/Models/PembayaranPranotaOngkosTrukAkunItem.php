<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaOngkosTrukAkunItem extends Model
{
    protected $table = 'pembayaran_pranota_ongkos_truk_akun_items';

    protected $fillable = ['akun_coa_id', 'nominal', 'memo'];

    protected $casts = ['nominal' => 'decimal:2'];

    public function akunCoa()
    {
        return $this->belongsTo(Coa::class, 'akun_coa_id');
    }
}
