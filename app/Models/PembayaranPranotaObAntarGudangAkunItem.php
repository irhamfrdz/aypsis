<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaObAntarGudangAkunItem extends Model
{
    protected $table = 'pembayaran_pranota_ob_antar_gudang_akun_items';

    protected $fillable = ['akun_coa_id', 'nominal', 'memo'];

    public function akunCoa()
    {
        return $this->belongsTo(Coa::class, 'akun_coa_id');
    }
}
