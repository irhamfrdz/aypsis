<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class MasterPricelistLolo extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'master_pricelist_lolos';

    protected $fillable = [
        'vendor',
        'lokasi',
        'size',
        'kategori',
        'tarif',
        'status'
    ];

    protected $casts = [
        'tarif' => 'decimal:2'
    ];

    /**
     * Accessor for formatted tarif
     */
    public function getFormattedTarifAttribute()
    {
        return 'Rp ' . number_format($this->tarif, 0, ',', '.');
    }

    /**
     * Scope untuk filter status aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
