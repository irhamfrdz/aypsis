<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use App\Traits\Auditable;
class JenisBarang extends Model
{
    use Auditable;

    protected $fillable = [
        'kode',
        'nama_barang',
        'catatan',
        'status'
    ];
}
