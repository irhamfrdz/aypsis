<?php

namespace App\Observers;

use App\Models\Asset;

class AssetObserver
{
    public function created(Asset $asset): void
    {
        $karyawan = $asset->karyawan;

        $asset->tandaTerima()->create(array_merge($asset->only([
            'karyawan_id',
            'kode_asset',
            'nama_asset',
            'nama_merk',
            'kategori',
            'nomor_seri',
            'kondisi',
            'status',
            'lokasi',
            'tanggal_tanda_terima',
            'keterangan',
            'created_by',
        ]), [
            'nama_pemegang' => $karyawan?->nama_lengkap,
            'nik_pemegang' => $karyawan?->nik,
            'departemen_pemegang' => $karyawan?->departemen ?: $karyawan?->divisi,
        ]));
    }
}
