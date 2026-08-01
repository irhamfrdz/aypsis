<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaUangMakan extends Model
{
    protected $table = 'pranota_uang_makans';

    protected $fillable = [
        'nomor_pranota',
        'tanggal_pranota',
        'total_nominal',
        'status',
    ];

    protected $casts = [
        'tanggal_pranota' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(PranotaUangMakanDetail::class, 'pranota_uang_makan_id');
    }
}
