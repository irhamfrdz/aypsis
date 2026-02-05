<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Penerima extends Model
{
    use Auditable;

    protected $fillable = [
        'nama_penerima',
        'alamat',
        'npwp',
        'nitku',
        'catatan',
        'status',
        'iu_bp_kawasan'
    ];
}
