<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class KlasifikasiBiaya extends Model
{
    use Auditable;

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'is_active',
    ];
}
