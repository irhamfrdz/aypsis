<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaObAntarGudang extends Model
{
    protected $table = 'pranota_ob_antar_gudangs';

    protected $fillable = [
        'nomor_pranota',
        'tanggal_pranota',
        'keterangan',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PranotaObAntarGudangItem::class, 'pranota_ob_antar_gudang_id');
    }
}
