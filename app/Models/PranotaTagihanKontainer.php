<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaTagihanKontainer extends Model
{
    protected $table = 'pranota_tagihan_kontainers';
    protected $fillable = ['nomor', 'tanggal', 'vendor', 'keterangan', 'total', 'periode', 'group_key'];
}
