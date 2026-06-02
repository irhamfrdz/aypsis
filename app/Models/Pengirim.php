<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Pengirim extends Model
{
    use Auditable;

    protected $fillable = [
        'kode',
        'nama_pengirim',
        'nickname1',
        'alamat',
        'catatan',
        'status',
    ];
}
