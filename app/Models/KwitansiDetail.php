<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KwitansiDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'kwitansi_id', 'item_kode', 'item_description', 'qty', 'unit_price', 
        'amount', 'no_bl', 'no_sj', 'dept', 'proyek', 'sn'
    ];

    public function kwitansi()
    {
        return $this->belongsTo(Kwitansi::class);
    }
}
