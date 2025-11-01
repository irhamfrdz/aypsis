<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaUangKenekSummary extends Model
{
    protected $table = 'pranota_uang_kenek_summary';

    protected $fillable = [
        'pranota_uang_kenek_id',
        'kenek_nama',
        'jumlah_surat_jalan',
        'total_uang_kenek',
        'hutang',
        'tabungan',
        'grand_total_kenek'
    ];

    protected $casts = [
        'jumlah_surat_jalan' => 'integer',
        'total_uang_kenek' => 'decimal:2',
        'hutang' => 'decimal:2',
        'tabungan' => 'decimal:2',
        'grand_total_kenek' => 'decimal:2',
    ];

    // Relationships
    public function pranotaUangKenek()
    {
        return $this->belongsTo(PranotaUangKenek::class);
    }
}
