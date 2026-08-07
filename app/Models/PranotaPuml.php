<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaPuml extends Model
{
    protected $fillable = [
        'nomor_pranota',
        'tanggal_pranota',
        'periode_start',
        'periode_end',
        'total_uang_makan',
        'total_lembur',
        'grand_total',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_pranota' => 'date',
        'periode_start' => 'date',
        'periode_end' => 'date',
    ];

    public function uangMakans()
    {
        return $this->hasMany(PranotaUangMakan::class, 'pranota_puml_id');
    }

    public function lemburs()
    {
        return $this->hasMany(PranotaLemburKaryawanHeader::class, 'pranota_puml_id');
    }
}
