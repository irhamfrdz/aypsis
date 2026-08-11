<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaPumlPotongan extends Model
{
    protected $fillable = [
        'pranota_puml_id',
        'tipe_karyawan',
        'karyawan_id',
        'pot_utang',
        'pot_bpjs',
        'pot_pph',
        'pot_terlambat',
    ];

    public function pranotaPuml()
    {
        return $this->belongsTo(PranotaPuml::class, 'pranota_puml_id');
    }

    public function karyawan()
    {
        return $this->morphTo('karyawan', 'tipe_karyawan', 'karyawan_id');
    }
}