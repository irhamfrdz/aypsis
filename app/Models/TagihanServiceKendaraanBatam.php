<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagihanServiceKendaraanBatam extends Model
{
    protected $table = 'tagihan_service_kendaraan_batams';

    protected $fillable = [
        'nomor_tagihan',
        'tanggal_service',
        'mobil_id',
        'nomor_polisi',
        'bengkel',
        'rincian_service',
        'biaya',
        'pph_rate',
        'total_biaya',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_service' => 'date',
        'biaya' => 'decimal:2',
        'pph_rate' => 'decimal:2',
        'total_biaya' => 'decimal:2',
    ];

    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }
}
