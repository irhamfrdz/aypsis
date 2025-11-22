<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UangJalan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'uang_jalans';

    protected $fillable = [
        'nomor_uang_jalan',
        'tanggal_uang_jalan',
        'surat_jalan_id',
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
     * Relationship dengan SuratJalan
     */
    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id');
    }

    /**
     * Relationship dengan User (creator)
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Alias untuk relationship user (backward compatibility)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship dengan PranotaUangJalan
     */
    public function pranotaUangJalan()
    {
        return $this->belongsToMany(PranotaUangJalan::class, 'pranota_uang_jalan_items', 'uang_jalan_id', 'pranota_uang_jalan_id')
                    ->withTimestamps();
    }

    /**
     * Scope untuk status tertentu
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Generate nomor uang jalan otomatis
     * Format: UJ + 2 digit bulan + 2 digit tahun + 6 digit running number
     * Running number tidak direset setiap bulan (kontinyu)
     */
    public static function generateNomorUangJalan()
    {
        $now = now();
        $month = $now->format('m'); // 2 digit bulan
        $year = $now->format('y');  // 2 digit tahun
        
        // Ambil nomor urut terakhir dari semua record (tidak filter berdasarkan bulan/tahun)
        // Urutkan berdasarkan nomor uang jalan untuk mendapatkan running number terbesar
        $lastRecord = static::whereNotNull('nomor_uang_jalan')
                           ->where('nomor_uang_jalan', 'LIKE', 'UJ%')
                           ->orderByRaw('CAST(SUBSTRING(nomor_uang_jalan, -6) AS UNSIGNED) DESC')
                           ->first();
        
        $runningNumber = 1;
        
        if ($lastRecord && $lastRecord->nomor_uang_jalan) {
            // Extract running number dari nomor terakhir (6 digit terakhir)
            $lastNumber = substr($lastRecord->nomor_uang_jalan, -6);
            $runningNumber = intval($lastNumber) + 1;
        }
        
        // Format: UJ + 2 digit bulan + 2 digit tahun + 6 digit running number
        return 'UJ' . $month . $year . str_pad($runningNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get next running number (untuk preview atau informasi)
     */
    public static function getNextRunningNumber()
    {
        $lastRecord = static::whereNotNull('nomor_uang_jalan')
                           ->where('nomor_uang_jalan', 'LIKE', 'UJ%')
                           ->orderByRaw('CAST(SUBSTRING(nomor_uang_jalan, -6) AS UNSIGNED) DESC')
                           ->first();
        
        if ($lastRecord && $lastRecord->nomor_uang_jalan) {
            $lastNumber = substr($lastRecord->nomor_uang_jalan, -6);
            return intval($lastNumber) + 1;
        }
        
        return 1;
    }

    /**
     * Accessor untuk format currency
     */
    public function getFormattedJumlahUangSupirAttribute()
    {
        return number_format($this->jumlah_uang_supir, 0, ',', '.');
    }

    public function getFormattedJumlahUangKenekAttribute()
    {
        return number_format($this->jumlah_uang_kenek, 0, ',', '.');
    }

    public function getFormattedTotalUangJalanAttribute()
    {
        return number_format($this->total_uang_jalan, 0, ',', '.');
    }
}
