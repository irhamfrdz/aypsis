<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PranotaTagihanKontainer extends Model
{
    use Auditable;

    protected $table = 'pranota_tagihan_kontainers';

    protected $fillable = ['nomor', 'tanggal', 'vendor', 'keterangan', 'total', 'periode', 'group_key'];
}
