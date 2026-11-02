<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPranotaObAkunItem extends Model
{
    protected $table = 'pembayaran_pranota_ob_akun_items';

    protected $fillable = ['akun_coa_id', 'nominal'];

    public function akunCoa()
    {
        return $this->belongsTo(Coa::class, 'akun_coa_id');
    }
}
