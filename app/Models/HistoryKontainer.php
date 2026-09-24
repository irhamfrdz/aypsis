<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryKontainer extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'nomor_kontainer',
        'tipe_kontainer',
        'jenis_kegiatan',
        'tanggal_kegiatan',
        'asal_gudang_id',
        'gudang_id',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
    ];

    protected static function booted(): void
    {
        static::created(function (HistoryKontainer $history) {
            self::syncCurrentLocation($history->nomor_kontainer);
        });

        static::updated(function (HistoryKontainer $history) {
            self::syncCurrentLocation($history->nomor_kontainer);
        });

        static::deleted(function (HistoryKontainer $history) {
            self::syncCurrentLocation($history->nomor_kontainer);
        });
    }

    /**
     * Synchronize both master tables with the latest movement for a container.
     */
    public static function syncCurrentLocation(string $nomorKontainer): void
    {
        $latest = self::where('nomor_kontainer', $nomorKontainer)
            ->orderByDesc('tanggal_kegiatan')
            ->orderByDesc('id')
            ->first();

        $gudangId = $latest?->gudang_id;

        Kontainer::where('nomor_seri_gabungan', $nomorKontainer)
            ->update(['gudangs_id' => $gudangId]);

        StockKontainer::where('nomor_seri_gabungan', $nomorKontainer)
            ->update(['gudangs_id' => $gudangId]);
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
