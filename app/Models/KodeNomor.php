<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeNomor extends Model
{
    use HasFactory;

    protected $table = 'kode_nomor';

    protected $fillable = [
        'kode',
        'nomor_akun',
        'nama_akun',
        'tipe_akun',
        'saldo',
        'catatan',
    ];
}
