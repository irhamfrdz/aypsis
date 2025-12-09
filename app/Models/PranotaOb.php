<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaOb extends Model
{
    protected $fillable = [
        'nomor_pranota',
        'nama_kapal',
        'no_voyage',
        'items',
        'created_by',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
