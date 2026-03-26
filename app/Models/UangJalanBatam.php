<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UangJalanBatam extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'uang_jalan_batams';

    protected $fillable = [
        'nomor_uang_jalan',
        'tanggal_uang_jalan',
        'surat_jalan_batam_id',
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
        'jumlah_total' => 'decimal:2'
    ];

    /**
     * Relationship dengan SuratJalanBatam
     */
    public function suratJalanBatam()
    {
        return $this->belongsTo(SuratJalanBatam::class, 'surat_jalan_batam_id');
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
     * Generate nomor uang jalan batam otomatis
     * Format: UJBTM + 2 digit bulan + 2 digit tahun + 6 digit running number
     */
    public static function generateNomorUangJalan()
    {
        $now = now();
        $month = $now->format('m');
        $year = $now->format('y');
        
        $lastRecord = static::withTrashed()->whereNotNull('nomor_uang_jalan')
                           ->where('nomor_uang_jalan', 'LIKE', 'UJBTM%')
                           ->orderByRaw('CAST(SUBSTRING(nomor_uang_jalan, -6) AS UNSIGNED) DESC')
                           ->first();
        
        $runningNumber = 1;
        
        if ($lastRecord && $lastRecord->nomor_uang_jalan) {
            $lastNumber = substr($lastRecord->nomor_uang_jalan, -6);
            $runningNumber = intval($lastNumber) + 1;
        }
        
        return 'UJBTM' . $month . $year . str_pad($runningNumber, 6, '0', STR_PAD_LEFT);
    }
}
