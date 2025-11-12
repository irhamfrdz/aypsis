<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelolaBbm extends Model
{
    protected $table = 'kelola_bbm';

    protected $fillable = [
        'bulan',
        'tahun',
        'bbm_per_liter',
        'persentase',
        'keterangan',
    ];

    protected $casts = [
        'bbm_per_liter' => 'decimal:2',
        'persentase' => 'decimal:2',
    ];

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByTanggal($query, $tanggal)
    {
        return $query->where('tanggal', $tanggal);
    }

    /**
     * Format harga BBM
     */
    public function getFormattedBbmAttribute()
    {
        return 'Rp ' . number_format($this->bbm_per_liter, 0, ',', '.');
    }

    /**
     * Get formatted bulan tahun
     */
    public function getFormattedBulanTahunAttribute()
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return ($bulanNames[$this->bulan] ?? '') . ' ' . $this->tahun;
    }
}
