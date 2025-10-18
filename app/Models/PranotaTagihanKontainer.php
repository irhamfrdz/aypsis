<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use App\Traits\Auditable;
class PranotaTagihanKontainer extends Model
{
    use Auditable;

    protected $table = 'pranota_tagihan_kontainers';
    protected $fillable = ['nomor', 'tanggal', 'vendor', 'keterangan', 'total', 'periode', 'group_key'];
}
