<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaTemplate extends Model
{
    protected $fillable = [
        'nama_template',
        'isi_template',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
