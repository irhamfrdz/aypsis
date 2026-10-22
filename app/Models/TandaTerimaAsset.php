<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TandaTerimaAsset extends Model
{
    protected $table = 'tanda_terima_asset';

    protected $fillable = [
        'asset_id',
        'karyawan_id',
        'kode_asset',
        'nama_asset',
        'nama_merk',
        'kategori',
        'nomor_seri',
        'kondisi',
        'status',
        'lokasi',
        'nama_pemegang',
        'nik_pemegang',
        'departemen_pemegang',
        'tanggal_tanda_terima',
        'keterangan',
        'foto',
        'created_by',
    ];

    protected $casts = [
        'tanggal_tanda_terima' => 'date',
    ];

    public static function attributesFromAsset(Asset $asset): array
    {
        $karyawan = $asset->karyawan;

        return array_merge($asset->only([
            'karyawan_id', 'kode_asset', 'nama_asset', 'nama_merk', 'kategori',
            'nomor_seri', 'kondisi', 'status', 'lokasi', 'tanggal_tanda_terima',
            'keterangan', 'created_by',
        ]), [
            'nama_pemegang' => $karyawan?->nama_lengkap,
            'nik_pemegang' => $karyawan?->nik,
            'departemen_pemegang' => $karyawan?->departemen ?: $karyawan?->divisi,
        ]);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class)->withTrashed();
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? Storage::disk('tanda_terima_asset')->url($this->foto) : null;
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
