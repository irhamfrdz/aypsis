<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembatalanSuratJalan extends Model
{
    protected $table = 'pembatalan_surat_jalans';

    protected $fillable = [
        'surat_jalan_id',
        'no_surat_jalan',
        'alasan_batal',
        'status',
        'created_by',
        'updated_by'
    ];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
