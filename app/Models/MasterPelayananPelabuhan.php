<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPelayananPelabuhan extends Model
{
    protected $fillable = [
        'nama_pelayanan',
        'deskripsi',
        'biaya',
        'satuan',
        'is_active',
    ];

    protected $casts = [
        'biaya' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
