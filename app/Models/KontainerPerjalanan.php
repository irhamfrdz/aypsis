<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KontainerPerjalanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kontainer_perjalanans';

    protected $fillable = [
        'surat_jalan_id',
        'no_kontainer',
        'no_surat_jalan',
        'tipe_kontainer',
        'ukuran',
        'tujuan_pengiriman',
        'gudang_tujuan_id',
        'supir',
        'no_plat',
        'waktu_keluar',
        'estimasi_waktu_tiba',
        'waktu_tiba_aktual',
        'status',
        'catatan_keluar',
        'catatan_tiba',
        'lokasi_terakhir',
        'latitude',
        'longitude',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'waktu_keluar' => 'datetime',
        'estimasi_waktu_tiba' => 'datetime',
        'waktu_tiba_aktual' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Relationship to SuratJalan
     */
    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id');
    }

    /**
     * Relationship to Gudang (warehouse destination)
     */
    public function gudangTujuan()
    {
        return $this->belongsTo(Gudang::class, 'gudang_tujuan_id');
    }

    /**
     * Relationship to User who created the record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship to User who last updated the record
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope untuk kontainer yang masih dalam perjalanan
     */
    public function scopeDalamPerjalanan($query)
    {
        return $query->where('status', 'dalam_perjalanan');
    }

    /**
     * Scope untuk kontainer yang sudah sampai
     */
    public function scopeSampaiTujuan($query)
    {
        return $query->where('status', 'sampai_tujuan');
    }

    /**
     * Check if container is still in transit
     */
    public function isDalamPerjalanan()
    {
        return $this->status === 'dalam_perjalanan';
    }

    /**
     * Check if container has arrived
     */
    public function isSampaiTujuan()
    {
        return $this->status === 'sampai_tujuan';
    }

    /**
     * Calculate duration of transit in hours
     */
    public function getDurasiPerjalananAttribute()
    {
        if (!$this->waktu_keluar) {
            return null;
        }

        $waktuAkhir = $this->waktu_tiba_aktual ?? now();
        return $this->waktu_keluar->diffInHours($waktuAkhir);
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'dalam_perjalanan' => 'blue',
            'sampai_tujuan' => 'green',
            'dibatalkan' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status label in Indonesian
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'dalam_perjalanan' => 'Dalam Perjalanan',
            'sampai_tujuan' => 'Sampai Tujuan',
            'dibatalkan' => 'Dibatalkan',
            default => 'Tidak Diketahui',
        };
    }
}
