<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PranotaUangRitBatamItem extends Model
{
    use HasFactory;

    protected $table = 'pranota_uang_rit_batam_items';

    protected $fillable = [
        'pranota_uang_rit_batam_id',
        'surat_jalan_batam_id',
        'uang_rit'
    ];

    public function pranota()
    {
        return $this->belongsTo(PranotaUangRitBatam::class, 'pranota_uang_rit_batam_id');
    }

    public function suratJalanBatam()
    {
        return $this->belongsTo(SuratJalanBatam::class, 'surat_jalan_batam_id');
    }
}
