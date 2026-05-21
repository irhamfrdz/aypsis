<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class JenisBarang extends Model
{
    use Auditable;

    protected $fillable = [
        'kode',
        'nama_barang',
        'catatan',
        'status',
    ];
}
