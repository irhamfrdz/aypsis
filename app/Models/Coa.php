<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    protected $table = 'akun_coa';

    protected $fillable = [
        'nomor_akun',
        'nama_akun',
        'tipe_akun',
        'saldo'
    ];
}
