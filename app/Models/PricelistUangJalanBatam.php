<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricelistUangJalanBatam extends Model
{
    use HasFactory;

    protected $table = 'pricelist_uang_jalan_batam';

    protected $fillable = [
        'expedisi',
        'ring',
        'tarif_20ft_full',
        'tarif_20ft_full_base',
        'tarif_20ft_empty',
        'tarif_20ft_empty_base',
        'tarif_40ft_full',
        'tarif_40ft_full_base',
        'tarif_40ft_empty',
        'tarif_40ft_empty_base',
        'tarif_antar_lokasi',
        'status',
    ];

    protected $casts = [
        'tarif_20ft_full' => 'decimal:2',
        'tarif_20ft_full_base' => 'decimal:2',
        'tarif_20ft_empty' => 'decimal:2',
        'tarif_20ft_empty_base' => 'decimal:2',
        'tarif_40ft_full' => 'decimal:2',
        'tarif_40ft_full_base' => 'decimal:2',
        'tarif_40ft_empty' => 'decimal:2',
        'tarif_40ft_empty_base' => 'decimal:2',
        'tarif_antar_lokasi' => 'decimal:2',
    ];

    /**
     * Scope untuk filter berdasarkan expedisi
     */
    public function scopeByExpedisi($query, $expedisi)
    {
        return $query->where('expedisi', $expedisi);
    }

    /**
     * Scope untuk filter berdasarkan ring
     */
    public function scopeByRing($query, $ring)
    {
        return $query->where('ring', $ring);
    }





    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public static function getTarif($expedisi, $ring, $status = null)
    {
        return self::where('expedisi', $expedisi)
            ->where('ring', $ring)
            ->when($status, function($q) use ($status) {
                return $q->where('status', $status);
            })
            ->first();
    }
}
