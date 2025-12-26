<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class KlasifikasiBiaya extends Model
{
    use Auditable;

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'is_active'
    ];
}
