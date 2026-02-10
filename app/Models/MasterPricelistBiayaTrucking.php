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
        'nama_vendor',
        'size',
        'biaya',
        'status'
    ];

    protected $casts = [
        'biaya' => 'decimal:2',
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
     * Scope untuk filter berdasarkan nama_vendor
     */
    public function scopeNamaVendor($query, $nama_vendor)
    {
        return $query->where('nama_vendor', 'like', '%' . $nama_vendor . '%');
    }
}
