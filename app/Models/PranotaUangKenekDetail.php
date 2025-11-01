<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaUangKenekDetail extends Model
{
    protected $fillable = [
        'pranota_uang_kenek_id',
        'surat_jalan_id',
        'no_surat_jalan',
        'supir_nama',
        'kenek_nama',
        'no_plat',
        'uang_rit_kenek'
    ];

    protected $casts = [
        'uang_rit_kenek' => 'decimal:2',
    ];

    // Relationships
    public function pranotaUangKenek()
    {
        return $this->belongsTo(PranotaUangKenek::class);
    }

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class);
    }
}
