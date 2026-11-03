<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranObAkunItem extends Model
{
    protected $fillable = ['pembayaran_ob_id', 'akun_coa_id', 'nominal', 'memo'];

    public function pembayaranOb()
    {
        return $this->belongsTo(PembayaranOb::class, 'pembayaran_ob_id');
    }

    public function akun()
    {
        return $this->belongsTo(Coa::class, 'akun_coa_id');
    }
}
