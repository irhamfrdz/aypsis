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
        'status',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_service' => 'date',
        'biaya' => 'decimal:2',
    ];

    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }
}
