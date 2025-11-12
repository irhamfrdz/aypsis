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
        'size',
        'f_e',
        'tarif',
        'tarif_base',
        'status',
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
        'tarif_base' => 'decimal:2',
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
     * Scope untuk filter berdasarkan size
     */
    public function scopeBySize($query, $size)
    {
        return $query->where('size', $size);
    }

    /**
     * Scope untuk filter berdasarkan F/E
     */
    public function scopeByFE($query, $fe)
    {
        return $query->where('f_e', $fe);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Mendapatkan tarif berdasarkan parameter
     */
    public static function getTarif($expedisi, $ring, $size, $fe, $status = null)
    {
        $query = self::where('expedisi', $expedisi)
            ->where('ring', $ring)
            ->where('size', $size)
            ->where('f_e', $fe);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->first();
    }
}
