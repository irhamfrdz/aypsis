<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaObMuatTemasItem extends Model
{
    protected $table = 'pranota_ob_muat_temas_items';

    protected $fillable = ['pranota_ob_muat_temas_id', 'tagihan_ob_id', 'snapshot'];

    protected $casts = ['snapshot' => 'object'];

    public function pranota()
    {
        return $this->belongsTo(PranotaObMuatTemas::class, 'pranota_ob_muat_temas_id');
    }

    public function tagihanOb()
    {
        return $this->belongsTo(TagihanOb::class, 'tagihan_ob_id');
    }
}
