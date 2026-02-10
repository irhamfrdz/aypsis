<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class MasterPricelistBiayaTrucking extends Model
{
    use HasFactory, Auditable;

    protected $table = 'master_pricelist_biaya_trucking';

    protected $fillable = [
        'rute',
        'tujuan',
        'jenis_kendaraan',
        'biaya',
        'satuan',
        'tanggal_berlaku',
        'tanggal_berakhir',
        'keterangan',
        'status'
    ];

    protected $casts = [
        'biaya' => 'decimal:2',
        'tanggal_berlaku' => 'date',
        'tanggal_berakhir' => 'date',
    ];

    /**
     * Accessor for formatted biaya
     */
    public function getFormattedBiayaAttribute()
    {
        return 'Rp ' . number_format($this->biaya, 0, ',', '.');
    }

    /**
     * Scope untuk filter status aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope untuk filter berdasarkan rute
     */
    public function scopeRute($query, $rute)
    {
        return $query->where('rute', 'like', '%' . $rute . '%');
    }
}
