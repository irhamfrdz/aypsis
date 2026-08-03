<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UangLemburRule extends Model
{
    protected $fillable = [
        'uang_lembur_id',
        'tipe_hari',
        'jam_mulai',
        'jam_selesai',
        'is_sampai_selesai',
        'satuan',
        'nominal',
    ];

    public function uangLembur()
    {
        return $this->belongsTo(UangLembur::class, 'uang_lembur_id');
    }
}
