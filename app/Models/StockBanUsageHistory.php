<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockBanUsageHistory extends Model
{
    protected $fillable = [
        'source_type', 'source_id', 'audit_log_id', 'mobil_id', 'alat_berat_id',
        'tanggal_digunakan', 'unit_name', 'nama_ban', 'merk', 'ukuran',
        'nomor_seri', 'nomor_bukti_pakai', 'penerima_id', 'penerima_name', 'keterangan',
    ];

    protected $casts = ['tanggal_digunakan' => 'date'];

    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }
}
