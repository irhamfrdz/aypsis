<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenerimaBuktiPotongPph extends Model
{
    use SoftDeletes;

    protected $table = 'master_penerima_bukti_potong_pph';

    protected $fillable = [
        'nama',
        'npwp',
        'nik',
        'alamat',
        'email',
        'telepon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
