<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockKontainer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nomor_kontainer',
        'ukuran',
        'tipe_kontainer',
        'status',
        'lokasi',
        'tanggal_masuk',
        'tanggal_keluar',
        'keterangan',
        'kondisi',
        'harga_sewa_per_hari',
        'harga_sewa_per_bulan',
        'pemilik',
        'nomor_seri',
        'tahun_pembuatan'
    ];

    /**
     * Cast date attributes to Carbon instances for safe formatting in views.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'harga_sewa_per_hari' => 'decimal:2',
        'harga_sewa_per_bulan' => 'decimal:2',
    ];

    /**
     * Get the status badge color for the kontainer.
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'available' => 'bg-green-100 text-green-800',
            'rented' => 'bg-blue-100 text-blue-800',
            'maintenance' => 'bg-yellow-100 text-yellow-800',
            'damaged' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get the kondisi badge color for the kontainer.
     */
    public function getKondisiBadgeAttribute()
    {
        return match($this->kondisi) {
            'baik' => 'bg-green-100 text-green-800',
            'rusak_ringan' => 'bg-yellow-100 text-yellow-800',
            'rusak_berat' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan kondisi
     */
    public function scopeByKondisi($query, $kondisi)
    {
        return $query->where('kondisi', $kondisi);
    }

    /**
     * Scope untuk kontainer yang tersedia
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
