<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PerbaikanKontainer extends Model
{
    use HasFactory;

    protected $table = 'perbaikan_kontainers';

    protected $fillable = [
        'nomor_tagihan',
        'nomor_kontainer',
        'tanggal_perbaikan',
        'estimasi_kerusakan_kontainer',
        'deskripsi_perbaikan',
        'realisasi_kerusakan',
        'estimasi_biaya_perbaikan',
        'realisasi_biaya_perbaikan',
        'vendor_bengkel_id',
        'vendor_bengkel',
        'status_perbaikan',
        'catatan',
        'jenis_catatan',
        'teknisi',
        'prioritas',
        'sparepart_dibutuhkan',
        'tanggal_catatan',
        'estimasi_waktu',
        'tanggal_selesai',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'tanggal_perbaikan',
        'tanggal_catatan',
        'tanggal_selesai',
    ];

    protected $casts = [
        'estimasi_biaya_perbaikan' => 'decimal:2',
        'realisasi_biaya_perbaikan' => 'decimal:2',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function vendorBengkel()
    {
        return $this->belongsTo(VendorBengkel::class, 'vendor_bengkel_id');
    }

    public function kontainer()
    {
        return $this->belongsTo(Kontainer::class, 'nomor_kontainer', 'nomor_seri_gabungan');
    }

    public function pranotaPerbaikanKontainers(): BelongsToMany
    {
        return $this->belongsToMany(PranotaPerbaikanKontainer::class, 'pranota_perbaikan_kontainer_items')
                    ->withPivot('biaya_item', 'catatan_item')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeBelumMasukPranota($query)
    {
        return $query->where('status_perbaikan', 'belum_masuk_pranota');
    }

    public function scopeSudahMasukPranota($query)
    {
        return $query->where('status_perbaikan', 'sudah_masuk_pranota');
    }

    public function scopeSudahDibayar($query)
    {
        return $query->where('status_perbaikan', 'sudah_dibayar');
    }

    // Accessors & Mutators
    public function getStatusColorAttribute()
    {
        return match($this->status_perbaikan) {
            'belum_masuk_pranota' => 'bg-yellow-100 text-yellow-800',
            'sudah_masuk_pranota' => 'bg-blue-100 text-blue-800',
            'sudah_dibayar' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status_perbaikan) {
            'belum_masuk_pranota' => 'Belum Masuk Pranota',
            'sudah_masuk_pranota' => 'Sudah Masuk Pranota',
            'sudah_dibayar' => 'Sudah Dibayar',
            default => 'Tidak Diketahui'
        };
    }

    // Helper methods
    public function isSudahDibayar()
    {
        return $this->status_perbaikan === 'sudah_dibayar';
    }

    public function isSudahMasukPranota()
    {
        return $this->status_perbaikan === 'sudah_masuk_pranota';
    }

    public function markAsSudahDibayar()
    {
        $this->update([
            'status_perbaikan' => 'sudah_dibayar',
            'tanggal_selesai' => now(),
        ]);
    }

    public function getEstimasiKerusakanKontainerOptions()
    {
        return [
            'maintenance' => 'Maintenance Rutin',
            'repair' => 'Perbaikan',
            'inspection' => 'Inspeksi',
            'replacement' => 'Penggantian Part',
            'cleaning' => 'Pembersihan',
            'other' => 'Lainnya'
        ];
    }

    public function getStatusOptions()
    {
        return [
            'belum_masuk_pranota' => 'Belum Masuk Pranota',
            'sudah_masuk_pranota' => 'Sudah Masuk Pranota',
            'sudah_dibayar' => 'Sudah Dibayar'
        ];
    }

    /**
     * Generate nomor tagihan
     * Format: TP + [1 digit cetakan] + [2 digit tahun] + [2 digit bulan] + [7 digit running number]
     * Example: TP12509240000001
     */
    public static function generateNomorTagihan()
    {
        $year = date('y'); // 2 digit year
        $month = date('m'); // 2 digit month
        $cetakan = '1'; // Default cetakan number

        // Get the last running number for current year and month
        $lastRecord = self::where('nomor_tagihan', 'like', "TP{$cetakan}{$year}{$month}%")
                         ->orderBy('nomor_tagihan', 'desc')
                         ->first();

        if ($lastRecord) {
            // Extract the running number from the last record
            $lastNumber = substr($lastRecord->nomor_tagihan, -7);
            $runningNumber = str_pad((int)$lastNumber + 1, 7, '0', STR_PAD_LEFT);
        } else {
            $runningNumber = '0000001';
        }

        return "TP{$cetakan}{$year}{$month}{$runningNumber}";
    }
}
