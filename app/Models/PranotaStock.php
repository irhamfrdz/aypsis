<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PranotaStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pranota',
        'tanggal_pranota',
        'nomor_accurate',
        'vendor',
        'rekening',
        'penerima',
        'adjustment',
        'keterangan',
        'items',
        'status',
        'created_by',
    ];

    protected $casts = [
        'items' => 'array',
        'tanggal_pranota' => 'date',
        'adjustment' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
