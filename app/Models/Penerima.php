<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Penerima extends Model
{
    use Auditable;

    protected $fillable = [
        'nama_penerima',
        'contact_person',
        'alamat',
        'npwp',
        'nitku',
        'catatan',
        'status',
        'iu_bp_kawasan',
    ];
}
