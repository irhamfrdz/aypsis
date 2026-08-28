<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaBroadcast extends Model
{
    protected $fillable = [
        'nama_kapal',
        'no_voyage',
        'kategori_masalah',
        'deskripsi_masalah',
        'wa_template_id',
        'total_shipper',
    ];

    public function template()
    {
        return $this->belongsTo(WaTemplate::class, 'wa_template_id');
    }
}
