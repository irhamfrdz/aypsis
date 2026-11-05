<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaGajiSupirBatamItem extends Model
{
    protected $fillable = ['pranota_id', 'gaji_supir_batam_id', 'total_gaji', 'pph', 'bpjs', 'bpjs_detail_id', 'bpjs_nomor_pranota'];

    protected $casts = ['total_gaji' => 'decimal:2', 'pph' => 'decimal:2', 'bpjs' => 'decimal:2'];

    public function gaji()
    {
        return $this->belongsTo(GajiSupirBatam::class, 'gaji_supir_batam_id');
    }
}
