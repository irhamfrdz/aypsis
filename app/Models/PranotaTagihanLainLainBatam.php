<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaTagihanLainLainBatam extends Model
{
    protected $fillable = [
        'nomor_pranota',
        'tanggal_pranota',
        'keterangan',
        'total_biaya',
        'created_by',
    ];

    protected $casts = [
        'tanggal_pranota' => 'date',
        'total_biaya' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(PranotaTagihanLainLainBatamItem::class, 'pranota_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
