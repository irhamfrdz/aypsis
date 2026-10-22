<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'created_by',
    ];

    protected $casts = [
        'tanggal_tanda_terima' => 'date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class)->withTrashed();
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
