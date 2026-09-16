<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_gudang',
        'lokasi',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'denah_layout' => 'array',
        'denah_version' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function positions()
    {
        return $this->hasMany(GudangPosition::class);
    }
}
