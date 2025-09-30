<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NomorTerakhir extends Model
{
    use HasFactory;

    protected $table = 'nomor_terakhir';

    protected $fillable = [
        'modul',
        'nomor_terakhir',
        'keterangan',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'nomor_terakhir' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
