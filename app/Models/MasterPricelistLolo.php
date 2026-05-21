<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPricelistLolo extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $table = 'master_pricelist_lolos';

    protected $fillable = [
        'vendor',
        'nama_biaya',
        'lokasi',
        'size',
        'tarif',
        'status',
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
    ];

    /**
     * Accessor for formatted tarif
     */
    public function getFormattedTarifAttribute()
    {
        return 'Rp '.number_format($this->tarif, 0, ',', '.');
    }

    /**
     * Scope untuk filter status aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
