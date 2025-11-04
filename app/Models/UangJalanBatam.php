<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UangJalanBatam extends Model
{
    use HasFactory;

    protected $table = 'uang_jalan_batam';

    protected $fillable = [
        'wilayah',
        'rute',
        'expedisi',
        'ring',
        'ft',
        'f_e',
        'tarif',
        'status',
        'tanggal_awal_berlaku',
        'tanggal_akhir_berlaku'
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
        'tanggal_awal_berlaku' => 'date',
        'tanggal_akhir_berlaku' => 'date'
    ];

    // Scope untuk filter berdasarkan wilayah
    public function scopeByWilayah($query, $wilayah)
    {
        return $query->where('wilayah', $wilayah);
    }

    // Scope untuk filter berdasarkan rute
    public function scopeByRute($query, $rute)
    {
        return $query->where('rute', $rute);
    }

    // Scope untuk filter berdasarkan expedisi
    public function scopeByExpedisi($query, $expedisi)
    {
        return $query->where('expedisi', $expedisi);
    }

    // Scope untuk tarif yang masih berlaku (dalam range tanggal)
    public function scopeAktif($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('tanggal_awal_berlaku', '<=', $today)
                    ->where('tanggal_akhir_berlaku', '>=', $today);
    }
}
