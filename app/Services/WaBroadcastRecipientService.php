<?php

namespace App\Services;

use App\Models\Manifest;
use App\Models\MasterPengirimPenerima;
use App\Models\Pengirim;
use App\Models\ShipperConsignee;
use Illuminate\Support\Collection;

class WaBroadcastRecipientService
{
    /**
     * Resolve the broadcast recipients for a ship and voyage from its manifests.
     */
    public function recipients(string $namaKapal, string $noVoyage): Collection
    {
        $normalizedKapal = strtoupper(trim(str_replace('.', '', $namaKapal)));
        $normalizedKapal = preg_replace('/\s+/', ' ', $normalizedKapal);

        $manifests = Manifest::query()
            ->with('shipperConsignee:id,shipper,contact_person,telepon')
            ->whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->where('no_voyage', trim($noVoyage))
            ->orderBy('nomor_bl')
            ->get();

        $groupedManifests = $manifests
            ->filter(fn (Manifest $manifest) => $manifest->shipper_id || filled($manifest->pengirim))
            ->groupBy(fn (Manifest $manifest) => $manifest->shipper_id
                ? 'shipper_'.$manifest->shipper_id
                : 'pengirim_'.trim($manifest->pengirim));

        $pengirimNames = $groupedManifests
            ->map(fn (Collection $shipperManifests) => trim((string) $shipperManifests->first()->pengirim))
            ->filter()
            ->unique()
            ->values();

        $masterPengirims = MasterPengirimPenerima::whereIn('nama', $pengirimNames)
            ->get()
            ->keyBy('nama');
        $pengirims = Pengirim::whereIn('nama_pengirim', $pengirimNames)
            ->get()
            ->keyBy('nama_pengirim');
        $shippersByName = ShipperConsignee::whereIn('shipper', $pengirimNames)
            ->get()
            ->keyBy('shipper');

        return $groupedManifests->map(function (Collection $shipperManifests) use ($masterPengirims, $pengirims, $shippersByName) {
            $firstManifest = $shipperManifests->first();
            $pengirimName = trim((string) $firstManifest->pengirim);
            $namaTujuan = $pengirimName ?: 'Shipper';
            $nomorKontak = null;
            $sumberTabel = '-';

            $masterPengirim = $masterPengirims->get($pengirimName);
            if ($masterPengirim) {
                $sumberTabel = 'Master Pengirim Penerima';
                if ($masterPengirim->contact_person || $masterPengirim->telepon) {
                    $namaTujuan = $masterPengirim->nama;
                    $nomorKontak = $masterPengirim->contact_person ?: $masterPengirim->telepon;
                }
            }

            if (! $nomorKontak) {
                $pengirim = $pengirims->get($pengirimName);
                if ($pengirim) {
                    $sumberTabel = 'Pengirim';
                    if ($pengirim->contact_person || $pengirim->telepon) {
                        $namaTujuan = $pengirim->nama_pengirim;
                        $nomorKontak = $pengirim->contact_person ?: $pengirim->telepon;
                    }
                }
            }

            if (! $nomorKontak) {
                $shipper = $firstManifest->shipperConsignee ?: $shippersByName->get($pengirimName);
                if ($shipper) {
                    $sumberTabel = 'Shipper Consignee';
                    $namaTujuan = $shipper->shipper ?: $namaTujuan;
                    $nomorKontak = $shipper->contact_person ?: $shipper->telepon;
                }
            }

            if (! $nomorKontak && filled($firstManifest->contact_person)) {
                $sumberTabel = 'Manifest';
                $nomorKontak = $firstManifest->contact_person;
            }

            return [
                'shipper_name' => $namaTujuan,
                'telepon' => $nomorKontak,
                'sumber_tabel' => $sumberTabel,
                'jumlah_kontainer' => $shipperManifests->count(),
                'daftar_kontainer' => $shipperManifests->pluck('nomor_kontainer')->filter()->unique()->values()->all(),
                'daftar_resi' => $shipperManifests->map(fn (Manifest $manifest) => [
                    'nomor_bl' => $manifest->nomor_bl,
                    'nomor_kontainer' => $manifest->nomor_kontainer,
                ])->all(),
            ];
        })->values();
    }
}
