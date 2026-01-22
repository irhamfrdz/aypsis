<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BelanjaAmprahan extends Model
{
    use HasFactory;

    protected $table = 'belanja_amprahans';

    protected $fillable = [
        'nomor',
        'tanggal',
        'supplier',
        'total',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total' => 'decimal:2',
    ];
}
