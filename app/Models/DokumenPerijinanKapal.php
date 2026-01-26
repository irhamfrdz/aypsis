<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenPerijinanKapal extends Model
{
    protected $fillable = [
        'nama_dokumen',
        'keterangan',
        'status_aktif'
    ];
}
