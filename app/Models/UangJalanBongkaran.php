<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UangJalanBongkaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'uang_jalan_bongkarans';

    protected $fillable = [
        'nomor_uang_jalan',
        'tanggal_uang_jalan',
        'surat_jalan_bongkaran_id',
        'kegiatan_bongkar_muat',
        'jumlah_uang_jalan',
        'jumlah_mel',
        'jumlah_pelancar',
        'jumlah_kawalan',
        'jumlah_parkir',
        'subtotal',
        'alasan_penyesuaian',
        'jumlah_penyesuaian',
        'jumlah_total',
        'memo',
        'status',
        'created_by'
    ];

    protected $casts = [
        'tanggal_uang_jalan' => 'date',
        'jumlah_uang_jalan' => 'decimal:2',
        'jumlah_mel' => 'decimal:2',
        'jumlah_pelancar' => 'decimal:2',
        'jumlah_kawalan' => 'decimal:2',
        'jumlah_parkir' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'jumlah_penyesuaian' => 'decimal:2',
        'jumlah_total' => 'decimal:2',
        'jumlah_uang_supir' => 'decimal:2',
        'jumlah_uang_kenek' => 'decimal:2',
        'total_uang_jalan' => 'decimal:2'
    ];

    /**
     * Relationship dengan SuratJalanBongkaran
     */
    public function suratJalanBongkaran()
    {
        return $this->belongsTo(SuratJalanBongkaran::class, 'surat_jalan_bongkaran_id');
    }

    /**
     * Relationship dengan User yang membuat
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDateRange($query, $tanggalDari, $tanggalSampai)
    {
        if ($tanggalDari) {
            $query->whereDate('tanggal_uang_jalan', '>=', $tanggalDari);
        }
        if ($tanggalSampai) {
            $query->whereDate('tanggal_uang_jalan', '<=', $tanggalSampai);
        }
        return $query;
    }

    /**
     * Get status options untuk dropdown
     */
    public static function getStatusOptions()
    {
        return [
            'belum_dibayar' => 'Belum Dibayar',
            'belum_masuk_pranota' => 'Belum Masuk Pranota',
            'sudah_masuk_pranota' => 'Sudah Masuk Pranota',
            'lunas' => 'Lunas',
            'dibatalkan' => 'Dibatalkan'
        ];
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        $statusClasses = [
            'belum_dibayar' => 'bg-yellow-100 text-yellow-800',
            'belum_masuk_pranota' => 'bg-orange-100 text-orange-800',
            'sudah_masuk_pranota' => 'bg-blue-100 text-blue-800',
            'lunas' => 'bg-green-100 text-green-800',
            'dibatalkan' => 'bg-red-100 text-red-800'
        ];

        return $statusClasses[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        $statusLabels = [
            'belum_dibayar' => 'Belum Bayar',
            'belum_masuk_pranota' => 'Belum Pranota',
            'sudah_masuk_pranota' => 'Sudah Pranota',
            'lunas' => 'Lunas',
            'dibatalkan' => 'Batal'
        ];

        return $statusLabels[$this->status] ?? ucfirst($this->status);
    }
}