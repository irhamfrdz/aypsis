<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaObAntarGudangItem extends Model
{
    protected $table = 'pranota_ob_antar_gudang_items';

    protected $fillable = [
        'pranota_ob_antar_gudang_id',
        'tagihan_ob_id',
        'created_by',
    ];

    public function pranota()
    {
        return $this->belongsTo(PranotaObAntarGudang::class, 'pranota_ob_antar_gudang_id');
    }

    public function tagihanOb()
    {
        return $this->belongsTo(TagihanOb::class, 'tagihan_ob_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
