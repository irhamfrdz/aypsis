<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PranotaObItem extends Model
{
    use HasFactory;

    protected $table = 'pranota_ob_items';

    protected $fillable = [
        'pranota_ob_id', 'item_type', 'item_id', 'nomor_kontainer', 'nama_barang', 'supir', 'size', 'biaya', 'status', 'created_by'
    ];

    public function pranotaOb()
    {
        return $this->belongsTo(PranotaOb::class, 'pranota_ob_id');
    }

    public function item()
    {
        return $this->morphTo();
    }
}
