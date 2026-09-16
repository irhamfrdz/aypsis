<?php

namespace App\Services;

use App\Models\Manifest;
use App\Models\MasterPengirimPenerima;
use App\Models\Pengirim;
use App\Models\ShipperConsignee;
use App\Models\WaPhoneOverride;
use Illuminate\Support\Collection;

class WaBroadcastRecipientService
{
    /**
     * Resolve the broadcast recipients.
     *
     * @param string $namaKapal
     * @param string $noVoyage
     * @param string $source 'manifest' or 'all_master_shippers'
     */
    public function recipients(string $namaKapal = '', string $noVoyage = '', string $source = 'manifest'): Collection
    {
        if ($source === 'all_master_shippers' || $source === 'master') {
            return $this->masterShippers();
        }

        // Load wa_phone_overrides for this call
        $phoneOverrides = $this->loadPhoneOverrides();

        $normalizedKapal = strtoupper(trim(str_replace('.', '', $namaKapal)));
        $normalizedKapal = preg_replace('/\s+/', ' ', $normalizedKapal);

        $manifests = Manifest::query()
            ->with('shipperConsignee:id,shipper,contact_person,telepon')
            ->whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->where('no_voyage', trim($noVoyage))
            ->orderBy('nomor_bl')
            ->get();

        if ($manifests->isEmpty()) {
            return collect();
        }

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

        return $groupedManifests->map(function (Collection $shipperManifests) use ($masterPengirims, $pengirims, $shippersByName, $phoneOverrides) {
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

            // Apply wa_phone_overrides (highest priority)
            $overridePhone = $phoneOverrides->get($namaTujuan);
            if ($overridePhone) {
                $nomorKontak = $overridePhone;
                $sumberTabel = $sumberTabel . ' (Override WA)';
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

    /**
     * Load all wa_phone_overrides keyed by shipper_name.
     */
    protected function loadPhoneOverrides(): Collection
    {
        try {
            return WaPhoneOverride::allKeyed();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * Resolve all master shippers from:
     * 1. master_pengirim_penerima
     * 2. pengirims
     * 3. shipper_consignees
     */
    public function masterShippers(): Collection
    {
        $all = collect();

        // 1. Dari master_pengirim_penerima
        try {
            MasterPengirimPenerima::whereNotNull('nama')
                ->where('nama', '!=', '')
                ->get()
                ->each(function ($item) use (&$all) {
                    $phone = $item->contact_person ?: $item->telepon;
                    $all->push([
                        'shipper_name' => trim($item->nama),
                        'telepon' => $phone ? trim($phone) : null,
                        'sumber_tabel' => 'Master Pengirim Penerima',
                        'jumlah_kontainer' => 0,
                        'daftar_kontainer' => [],
                        'daftar_resi' => [],
                    ]);
                });
        } catch (\Throwable $e) {}

        // 2. Dari pengirims
        try {
            Pengirim::whereNotNull('nama_pengirim')
                ->where('nama_pengirim', '!=', '')
                ->get()
                ->each(function ($item) use (&$all) {
                    $phone = $item->contact_person ?: $item->telepon;
                    $all->push([
                        'shipper_name' => trim($item->nama_pengirim),
                        'telepon' => $phone ? trim($phone) : null,
                        'sumber_tabel' => 'Pengirim',
                        'jumlah_kontainer' => 0,
                        'daftar_kontainer' => [],
                        'daftar_resi' => [],
                    ]);
                });
        } catch (\Throwable $e) {}

        // 3. Dari shipper_consignees
        try {
            ShipperConsignee::whereNotNull('shipper')
                ->where('shipper', '!=', '')
                ->get()
                ->each(function ($item) use (&$all) {
                    $phone = $item->contact_person ?: $item->telepon;
                    $all->push([
                        'shipper_name' => trim($item->shipper),
                        'telepon' => $phone ? trim($phone) : null,
                        'sumber_tabel' => 'Shipper Consignee',
                        'jumlah_kontainer' => 0,
                        'daftar_kontainer' => [],
                        'daftar_resi' => [],
                    ]);
                });
        } catch (\Throwable $e) {}

        // Deduplicate berdasarkan normalisasi nama shipper dan gabungkan nomor kontak & sumber tabel
        $groupedByName = $all->groupBy(function ($item) {
            return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $item['shipper_name']));
        });

        $phoneOverrides = $this->loadPhoneOverrides();

        $deduplicated = $groupedByName->map(function ($items) use ($phoneOverrides) {
            $withPhone = $items->first(fn ($i) => !empty($i['telepon']));
            $sources = $items->pluck('sumber_tabel')->unique()->implode(', ');

            if ($withPhone) {
                $withPhone['sumber_tabel'] = $sources;
                $base = $withPhone;
            } else {
                $first = $items->first();
                $first['sumber_tabel'] = $sources;
                $base = $first;
            }

            // Apply wa_phone_overrides (highest priority)
            $overridePhone = $phoneOverrides->get($base['shipper_name']);
            if ($overridePhone) {
                $base['telepon'] = $overridePhone;
                $base['sumber_tabel'] = $base['sumber_tabel'] . ' (Override WA)';
            }

            return $base;
        })->filter(fn ($i) => !empty($i['shipper_name']))->sortBy('shipper_name')->values();

        return $deduplicated;
    }
}
