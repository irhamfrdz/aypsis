<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Mobil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MobilAssetSyncService
{
    public function sync(Mobil $mobil, ?int $actor = null): ?Asset
    {
        return DB::transaction(function () use ($mobil, $actor) {
            // Lock the source too: two requests must never create two assets for one mobil.
            $mobil = Mobil::whereKey($mobil->getKey())->lockForUpdate()->firstOrFail();
            $asset = Asset::withTrashed()->where('source_table', $mobil->getTable())
                ->where('source_id', $mobil->id)->lockForUpdate()->first();

            if (! $mobil->is_asset_karyawan && (! $asset || $asset->trashed())) {
                return $asset;
            }

            $kode = $mobil->kode_no ?: 'MOBIL-'.$mobil->id;
            $collision = Asset::withTrashed()->where('kode_asset', $kode);
            if ($asset) {
                $collision->whereKeyNot($asset->id);
            }
            if ($collision->exists()) {
                throw ValidationException::withMessages([
                    'kode_no' => 'Kode kendaraan sudah digunakan oleh asset lain. Gunakan kode yang berbeda.',
                ]);
            }

            $isNew = ! $asset;
            $asset ??= new Asset;
            $active = (bool) $mobil->is_asset_karyawan;
            $status = $mobil->status_operasional ?: ($active ? 'Digunakan' : 'Tersedia');
            if (! $active && in_array($status, ['Digunakan', 'Dipinjam'], true)) {
                $status = 'Tersedia';
            }
            $lokasi = match (strtoupper(trim($mobil->lokasi ?? ''))) {
                'JKT', 'JAKARTA' => 'JKT',
                'BTM', 'BATAM' => 'BTM',
                'PNG' => 'PNG',
                default => null,
            };

            $asset->fill([
                'source_table' => $mobil->getTable(), 'source_id' => $mobil->id,
                'kode_asset' => $kode,
                'nama_asset' => Str::limit($mobil->catatan ?: ($mobil->nickname ?: ($mobil->jenis ?: $kode)), 255, ''),
                'nama_merk' => $mobil->merek,
                'kategori' => $mobil->kategori_asset ?: ($asset->kategori ?: 'Kendaraan'),
                'karyawan_id' => $active ? $mobil->karyawan_id : null,
                'tanggal_tanda_terima' => $active ? $mobil->tanggal_tanda_terima_asset : $asset->tanggal_tanda_terima,
                'kondisi' => $mobil->kondisi_fisik ?: ($asset->kondisi ?: 'Baik'),
                'status' => $status,
                'nomor_seri' => $mobil->no_mesin ?: $mobil->nomor_rangka,
                'lokasi' => $active ? 'Di bawa oleh karyawan' : match ($lokasi) {
                    'JKT' => 'Jakarta',
                    'BTM' => 'Batam',
                    default => null,
                },
                'lokasi_asset' => $lokasi,
                'kapal_id' => null,
                'keterangan' => collect([
                    $mobil->nomor_polisi ? 'Nomor Polisi: '.$mobil->nomor_polisi : null,
                    $mobil->jenis ? 'Jenis: '.$mobil->jenis : null,
                    $mobil->lokasi ? 'Lokasi kendaraan: '.$mobil->lokasi : null,
                    $mobil->catatan,
                ])->filter()->implode("\n"),
                'updated_by' => $actor,
            ]);
            if ($isNew) {
                $asset->created_by = $actor;
            }
            if ($active && $asset->trashed()) {
                $asset->deleted_at = null;
            }
            if ($isNew || $asset->isDirty()) {
                $asset->save();
            }

            return $asset;
        });
    }
}
