<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPricelistBiayaTrucking extends Model
{
    use Auditable, HasFactory;

    protected $table = 'master_pricelist_biaya_trucking';

    protected $fillable = [
        'nama_vendor',
        'size',
        'biaya',
        'status',
    ];

    protected $casts = [
        'biaya' => 'decimal:2',
    ];

    /**
     * Accessor for formatted biaya
     */
    public function getFormattedBiayaAttribute()
    {
        return 'Rp '.number_format($this->biaya, 0, ',', '.');
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
        return $query->where('nama_vendor', 'like', '%'.$nama_vendor.'%');
    }
}
