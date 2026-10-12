<?php

namespace App\Http\Controllers;

use App\Models\BiayaKapal;
use App\Models\Gudang;
use App\Models\PranotaObMuatTemas;
use App\Services\RekapBlService;
use App\Services\RekapUangRitService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RekapBiayaKapalController extends Controller
{
    private function tkbmSections($details): Collection
    {
        return $details->groupBy(fn ($detail) => implode('|', [
            $detail->kapal, $detail->voyage, $detail->no_referensi, $detail->tanggal_invoice_vendor,
        ]));
    }

    private function allocateTkbmRows(BiayaKapal $record, Collection $containers, string $kapal, string $voyage): Collection
    {
        $details = $record->tkbmDetails->filter(fn ($detail) => strtolower(trim($detail->kapal ?? '')) === strtolower(trim($kapal))
            && strtolower(trim($detail->voyage ?? '')) === strtolower(trim($voyage)));
        if ($details->isEmpty()) {
            return collect();
        }

        // Adjustment is repeated on every item in a section, so count it once.
        $adjustment = $this->tkbmSections($details)->sum(fn ($section) => (float) $section->first()->adjustment);
        $fullTotal = (float) $this->getApportionedCostForRecord($record, $kapal, $voyage)['total_biaya'];
        $selectedTotal = (float) $record->apportioned['total_biaya'];
        $selectedRatio = $fullTotal != 0 ? $selectedTotal / $fullTotal : 0;
        $adjustment *= $selectedRatio;
        $pph = (float) $record->apportioned['pph'];
        $totalCents = (int) round($selectedTotal * 100);
        $adjustmentCents = (int) round($adjustment * 100);
        $pphCents = (int) round($pph * 100);
        $baseCents = $totalCents - $adjustmentCents + $pphCents;
        $tariffGroups = [];
        $itemRows = collect();
        foreach ($details as $detail) {
            $name = strtoupper((string) ($detail->pricelistTkbm?->nama_barang ?? ''));
            $size = str_contains($name, '40') ? '40' : (str_contains($name, '20') ? '20' : null);
            $status = str_contains($name, 'EMPTY') || str_contains($name, 'KOSONG') ? 'EMPTY'
                : (str_contains($name, 'FULL') ? 'FULL' : null);
            if ($size === null || $status === null || (float) $detail->jumlah <= 0) {
                $itemRows->push([
                    'jenis' => 'barang', 'label' => $name ?: 'Barang lainnya', 'size' => '-', 'status' => '-',
                    'jumlah' => (float) $detail->jumlah * $selectedRatio,
                    'tarif' => (float) $detail->tarif,
                    'biaya_cents' => (int) round((float) $detail->subtotal * $selectedRatio * 100),
                    'adjustment_cents' => 0,
                ]);
                continue;
            }
            $key = $size.'|'.$status;
            $tariffGroups[$key]['subtotal'] = ($tariffGroups[$key]['subtotal'] ?? 0) + (float) $detail->subtotal;
            $tariffGroups[$key]['jumlah'] = ($tariffGroups[$key]['jumlah'] ?? 0) + (float) $detail->jumlah;
        }
        $rates = [];
        foreach ($tariffGroups as $key => $group) {
            $rates[$key] = (int) round($group['subtotal'] / $group['jumlah'] * 100);
        }

        $rows = collect();
        $containerCount = $containers->count();
        foreach ($containers as $index => $container) {
            $rateCents = $rates[$container['size'].'|'.$container['status']] ?? 0;
            $share = $containerCount > 0 ? intdiv($adjustmentCents, $containerCount) : 0;
            $rows->push([
                'jenis' => 'kontainer', 'label' => $container['nomor_kontainer'],
                'size' => $container['size'],
                'status' => $container['status'],
                'jumlah' => 1, 'tarif' => $rateCents / 100,
                'biaya_cents' => $rateCents,
                'adjustment_cents' => $index === $containerCount - 1
                    ? $adjustmentCents - $share * $index : $share,
            ]);
        }
        $rows = $rows->concat($itemRows)->values();

        // Keep any unmatched invoice amount visible instead of hiding it inside container tariffs.
        $unallocatedCents = $baseCents - $rows->sum('biaya_cents');
        if ($containerCount > 0 && $rows->where('jenis', 'kontainer')->sum('biaya_cents') === 0) {
            $perContainer = intdiv($unallocatedCents, $containerCount);
            foreach ($rows as $index => $row) {
                if ($row['jenis'] !== 'kontainer') {
                    continue;
                }
                $row['biaya_cents'] = $index === $containerCount - 1
                    ? $unallocatedCents - $perContainer * $index : $perContainer;
                $row['tarif'] = $row['biaya_cents'] / 100;
                $rows[$index] = $row;
            }
        } elseif ($unallocatedCents !== 0) {
            $rows->push([
                'jenis' => 'selisih', 'label' => 'Selisih alokasi invoice', 'size' => '-', 'status' => '-',
                'jumlah' => null, 'tarif' => null, 'biaya_cents' => $unallocatedCents,
                'adjustment_cents' => 0,
            ]);
        }
        if ($containerCount === 0 && $adjustmentCents !== 0) {
            $rows->push([
                'jenis' => 'adjustment', 'label' => 'Adjustment invoice', 'size' => '-', 'status' => '-',
                'jumlah' => null, 'tarif' => null, 'biaya_cents' => 0,
                'adjustment_cents' => $adjustmentCents,
            ]);
        }

        $taxBasis = $rows->sum(fn ($row) => max(0, $row['biaya_cents'] + $row['adjustment_cents']));
        $allocatedPph = 0;
        foreach ($rows as $index => $row) {
            $pphShare = $index === $rows->count() - 1
                ? $pphCents - $allocatedPph
                : ($taxBasis > 0 ? (int) round($pphCents * max(0, $row['biaya_cents'] + $row['adjustment_cents']) / $taxBasis) : 0);
            $allocatedPph += $pphShare;
            $row['biaya'] = $row['biaya_cents'] / 100;
            $row['adjustment'] = $row['adjustment_cents'] / 100;
            $row['pph'] = $pphShare / 100;
            $row['total'] = ($row['biaya_cents'] + $row['adjustment_cents'] - $pphShare) / 100;
            unset($row['biaya_cents'], $row['adjustment_cents']);
            $rows[$index] = $row;
        }

        return $rows;
    }

    private function allocateOppOptRows(BiayaKapal $record, Collection $voyageContainers, string $kapal, string $voyage): Collection
    {
        $rows = collect();
        foreach ($record->oppOptDetails as $detail) {
            if (strcasecmp(trim($detail->kapal ?? ''), trim($kapal)) !== 0
                || strcasecmp(trim($detail->voyage ?? ''), trim($voyage)) !== 0) {
                continue;
            }

            $manifests = $detail->manifests->unique('id')->values();
            $totalCents = (int) round((float) ($detail->rekap_opp_opt_total ?? $detail->subtotal) * 100);
            if ($manifests->isEmpty() && strtolower(trim((string) $detail->klasifikasi)) === 'opslag') {
                $type = strtoupper(trim((string) $detail->jenis_ukuran));
                $size = str_contains($type, '40') ? '40' : (str_contains($type, '20') ? '20' : null);
                $isLcl = str_contains($type, 'LCL');
                $status = preg_match('/\bEMPTY\b|\bKOSONG\b/', $type) ? 'EMPTY'
                    : (preg_match('/\bFULL\b/', $type) ? 'FULL' : null);
                $matches = $size && ($isLcl || ($status && ! str_contains($type, 'BOOKING')))
                    ? $voyageContainers->filter(fn ($container) => $container['size'] === $size
                        && ($isLcl ? ($container['category'] ?? 'standard') === 'LCL'
                            : ($container['category'] ?? 'standard') === 'standard' && $container['status'] === $status))->values()
                    : collect();
                // Opslag stores category totals, while voyage records may identify
                // only some of the billed containers. Show known numbers and leave
                // any unmatched quantity visible as a separate row.
                if ($matches->isNotEmpty() && $matches->count() <= (int) $detail->jumlah
                    && (float) $detail->jumlah === (float) (int) $detail->jumlah) {
                    $unitCount = (int) $detail->jumlah;
                    $baseUnitCents = intdiv($totalCents, $unitCount);
                    $extraCents = $totalCents % $unitCount;
                    $allocated = 0;
                    foreach ($matches as $index => $container) {
                        $amount = $baseUnitCents + ($index < $extraCents ? 1 : 0);
                        $allocated += $amount;
                        $rows->push([
                            'kontainer' => $container['nomor_kontainer'],
                            'bl' => $container['nomor_bl'] ?? '-',
                            'jenis' => $detail->klasifikasi ?: 'OPP/OPT',
                            'ukuran' => $detail->jenis_ukuran ?: '-',
                            'vendor' => $detail->vendor ?: '-', 'jumlah' => 1,
                            'per_kontainer' => true,
                            'tarif' => (float) $detail->tarif, 'biaya' => $amount / 100,
                        ]);
                    }
                    $unmatchedCount = $unitCount - $matches->count();
                    if ($unmatchedCount > 0) {
                        $rows->push([
                            'kontainer' => 'Sisa jumlah '.$detail->jenis_ukuran.' tanpa nomor kontainer',
                            'bl' => '-', 'jenis' => $detail->klasifikasi ?: 'OPP/OPT',
                            'ukuran' => $detail->jenis_ukuran ?: '-', 'vendor' => $detail->vendor ?: '-',
                            'jumlah' => $unmatchedCount, 'per_kontainer' => false,
                            'tarif' => (float) $detail->tarif,
                            'biaya' => ($totalCents - $allocated) / 100,
                        ]);
                    }
                    continue;
                }
            }
            $weights = $manifests->map(fn ($manifest) => (float) ($manifest->rekap_bl_share ?? 1));
            $weightTotal = $weights->sum();
            $allocated = 0;
            foreach ($manifests as $index => $manifest) {
                $amount = $index === $manifests->count() - 1
                    ? $totalCents - $allocated
                    : ($weightTotal > 0 ? (int) round($totalCents * $weights[$index] / $weightTotal) : 0);
                $allocated += $amount;
                $rows->push([
                    'kontainer' => $manifest->nomor_kontainer ?: '-',
                    'bl' => $manifest->nomor_bl ?: '-',
                    'jenis' => $detail->klasifikasi ?: 'OPP/OPT',
                    'ukuran' => $manifest->size_kontainer ?: ($detail->jenis_ukuran ?: '-'),
                    'vendor' => $detail->vendor ?: '-',
                    'jumlah' => 1,
                    'per_kontainer' => true,
                    'tarif' => (float) $detail->tarif,
                    'biaya' => $amount / 100,
                ]);
            }
            if ($manifests->isEmpty() && $totalCents !== 0) {
                $rows->push([
                    'kontainer' => $detail->jenis_ukuran ?: 'Biaya OPP/OPT', 'bl' => '-',
                    'jenis' => $detail->klasifikasi ?: 'OPP/OPT',
                    'ukuran' => $detail->jenis_ukuran ?: '-',
                    'vendor' => $detail->vendor ?: '-',
                    'jumlah' => (float) $detail->jumlah,
                    'per_kontainer' => false,
                    'tarif' => (float) $detail->tarif,
                    'biaya' => $totalCents / 100,
                ]);
            }
        }

        return $rows;
    }

    private $relations = [
        'barangDetails',
        'airDetails',
        'tkbmDetails',
        'truckingDetails',
        'stuffingDetails',
        'perlengkapanDetails',
        'labuhTambatDetails',
        'oppOptDetails',
        'thcDetails',
        'loloDetails',
        'storageDetails',
        'freightDetails',
        'perijinanDetails',
        'meratusDetails',
        'temasDetails',
        'tantoDetails',
        'demurrageDetails',
        'notaReturDetails',
        'tenagaKerjaDetails',
        'buruhBatamDetails',
        'buruhBongkarDetails',
        'klaimDetails',
        'umumDetails',
        'operasionalDetails',
        'dokumens',
    ];

    /**
     * Check if a BiayaKapal record matches a specific ship and voyage pairing.
     */
    private function recordHasShipAndVoyage($record, $kapal, $voyage)
    {
        $kapalLower = strtolower(trim($kapal));
        $voyageLower = strtolower(trim($voyage));

        $hasAnyDetails = false;

        // Check details relations first
        foreach ($this->relations as $relation) {
            if ($record->relationLoaded($relation) && $record->{$relation}->count() > 0) {
                $hasAnyDetails = true;
                foreach ($record->{$relation} as $detail) {
                    $dKapal = isset($detail->kapal) ? strtolower(trim($detail->kapal)) : '';
                    $dVoyage = isset($detail->voyage) ? strtolower(trim($detail->voyage)) : '';
                    if ($dKapal === $kapalLower && $dVoyage === $voyageLower) {
                        return true;
                    }
                }
            }
        }

        // If details exist but no matching detail was found, then this ship/voyage pairing doesn't match
        if ($hasAnyDetails) {
            return false;
        }

        // Fallback to parent table if no details exist
        $parentShips = is_array($record->nama_kapal) ? $record->nama_kapal : ($record->nama_kapal ? [$record->nama_kapal] : []);
        $parentVoyages = is_array($record->no_voyage) ? $record->no_voyage : ($record->no_voyage ? [$record->no_voyage] : []);

        $parentShipsLower = array_map(fn ($s) => strtolower(trim($s)), $parentShips);
        $parentVoyagesLower = array_map(fn ($v) => strtolower(trim($v)), $parentVoyages);

        $indices = array_keys($parentShipsLower, $kapalLower);
        if (! empty($indices)) {
            foreach ($indices as $idx) {
                if (isset($parentVoyagesLower[$idx])) {
                    if ($parentVoyagesLower[$idx] === $voyageLower) {
                        return true;
                    }
                } elseif (count($parentVoyagesLower) === 1) {
                    if ($parentVoyagesLower[0] === $voyageLower) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get ships and voyages associated with a BiayaKapal record (including detail relations).
     */
    private function getShipsAndVoyagesForRecord($record)
    {
        $ships = [];
        $voyages = [];

        // Parent table
        $parentShips = is_array($record->nama_kapal) ? $record->nama_kapal : ($record->nama_kapal ? [$record->nama_kapal] : []);
        foreach ($parentShips as $ship) {
            $trimmed = trim($ship);
            if ($trimmed !== '') {
                $ships[strtolower($trimmed)] = $trimmed;
            }
        }

        $parentVoyages = is_array($record->no_voyage) ? $record->no_voyage : ($record->no_voyage ? [$record->no_voyage] : []);
        foreach ($parentVoyages as $voyage) {
            $trimmed = trim($voyage);
            if ($trimmed !== '') {
                $voyages[strtolower($trimmed)] = $trimmed;
            }
        }

        // Details relations
        foreach ($this->relations as $relation) {
            if ($record->relationLoaded($relation)) {
                foreach ($record->{$relation} as $detail) {
                    if (isset($detail->kapal)) {
                        $trimmed = trim($detail->kapal);
                        if ($trimmed !== '') {
                            $ships[strtolower($trimmed)] = $trimmed;
                        }
                    }
                    if (isset($detail->voyage)) {
                        $trimmed = trim($detail->voyage);
                        if ($trimmed !== '') {
                            $voyages[strtolower($trimmed)] = $trimmed;
                        }
                    }
                }
            }
        }

        return [
            'ships' => array_values($ships),
            'voyages' => array_values($voyages),
        ];
    }

    /**
     * Calculate apportioned nominal, ppn, pph, and total_biaya for a specific ship and voyage.
     */
    public function getApportionedCostForRecord($item, $kapal, $voyage)
    {
        $kapalLower = strtolower(trim($kapal));
        $voyageLower = strtolower(trim($voyage));

        $hasDetails = false;

        $nominal = 0;
        $ppn = 0;
        $pph = 0;
        $total = 0;

        // Biaya buruh Batam menyimpan total per kapal/voyage pada tabel detail.
        if ($item->buruhBatamDetails->count() > 0 || $item->buruhBongkarDetails->count() > 0) {
            $hasDetails = true;
            $details = $item->buruhBatamDetails->concat($item->buruhBongkarDetails)
                ->filter(fn ($d) => strtolower(trim($d->kapal ?? '')) === $kapalLower && strtolower(trim($d->voyage ?? '')) === $voyageLower);
            $nominal = $details->sum(fn ($d) => (float) $d->nominal + (float) ($d->adjustment ?? 0));
            $pph = $details->sum('pph_amount');
            $total = $details->sum('total_nominal');
        }

        // 1. Biaya Buruh (barangDetails / tenagaKerjaDetails)
        elseif ($item->barangDetails->count() > 0 || $item->tenagaKerjaDetails->count() > 0) {
            $hasDetails = true;
            $barangItems = $item->barangDetails->filter(fn ($d) => strtolower(trim($d->kapal)) === $kapalLower && strtolower(trim($d->voyage)) === $voyageLower);
            $tkItems = $item->tenagaKerjaDetails->filter(fn ($d) => strtolower(trim($d->kapal)) === $kapalLower && strtolower(trim($d->voyage)) === $voyageLower);

            $subtotalBarang = $barangItems->sum('subtotal');
            $subtotalTk = $tkItems->sum('nominal');

            $adjustment = 0;
            $firstBarang = $barangItems->first();
            if ($firstBarang) {
                $adjustment = $firstBarang->adjustment ?? 0;
            }

            if ($barangItems->count() > 0) {
                $nominal = $subtotalBarang + $adjustment;
            } else {
                $nominal = $subtotalTk;
            }

            $parentNominal = $item->nominal ?: 1;
            $ratio = $nominal / $parentNominal;
            $ppn = $item->ppn * $ratio;
            $pph = $item->pph * $ratio;
            $total = $nominal + $ppn - $pph;
        }

        // 2. Biaya Air (airDetails)
        elseif ($item->airDetails->count() > 0) {
            $hasDetails = true;
            $details = $item->airDetails->filter(fn ($d) => strtolower(trim($d->kapal)) === $kapalLower && strtolower(trim($d->voyage)) === $voyageLower);
            $nominal = $details->sum('sub_total');
            $pph = $details->sum('pph');
            $total = $details->sum('grand_total');

            $parentNominal = $item->nominal ?: 1;
            $ratio = $nominal / $parentNominal;
            $ppn = $item->ppn * $ratio;
        }

        // 3. Biaya TKBM (tkbmDetails)
        elseif ($item->tkbmDetails->count() > 0) {
            $hasDetails = true;
            $details = $item->tkbmDetails->filter(fn ($d) => strtolower(trim($d->kapal)) === $kapalLower && strtolower(trim($d->voyage)) === $voyageLower);

            $sections = $this->tkbmSections($details);
            $nominal = $sections->sum(fn ($section) => (float) ($section->first()->total_nominal
                ?? ($section->sum('subtotal') + (float) ($section->first()->adjustment ?? 0))));
            $pph = $sections->sum(fn ($section) => (float) ($section->first()->pph ?? 0));
            $total = $sections->sum(fn ($section) => (float) ($section->first()->grand_total
                ?? ((float) ($section->first()->total_nominal ?? ($section->sum('subtotal') + (float) ($section->first()->adjustment ?? 0)))
                    - (float) ($section->first()->pph ?? 0))));

            $parentNominal = $item->nominal ?: 1;
            $ratio = $nominal / $parentNominal;
            $ppn = $item->ppn * $ratio;
        }

        // 4. Biaya Trucking (truckingDetails)
        elseif ($item->truckingDetails->count() > 0) {
            $hasDetails = true;
            $details = $item->truckingDetails->filter(fn ($d) => strtolower(trim($d->kapal)) === $kapalLower && strtolower(trim($d->voyage)) === $voyageLower);
            $nominal = $details->sum('subtotal');
            $pph = $details->sum('pph');
            $total = $details->sum('total_biaya');

            $parentNominal = $item->nominal ?: 1;
            $ratio = $nominal / $parentNominal;
            $ppn = $item->ppn * $ratio;
        }

        // 5. Stuffing (stuffingDetails)
        elseif ($item->stuffingDetails->count() > 0) {
            $hasDetails = true;
            $details = $item->stuffingDetails->filter(fn ($d) => strtolower(trim($d->kapal)) === $kapalLower && strtolower(trim($d->voyage)) === $voyageLower);
            $nominal = $details->sum('subtotal');
            $pph = $details->sum('pph');
            $total = $details->sum('total_biaya');

            $parentNominal = $item->nominal ?: 1;
            $ratio = $nominal / $parentNominal;
            $ppn = $item->ppn * $ratio;
        } elseif ($item->umumDetails->count() > 0) {
            $hasDetails = true;
            $details = $item->umumDetails->filter(fn ($d) => strtolower(trim($d->kapal ?? '')) === $kapalLower
                && strtolower(trim($d->voyage ?? '')) === $voyageLower);
            $nominal = $details->sum('nominal');
            $pph = $details->sum('pph');
            $total = $nominal - $pph;
        }

        // Claim amounts are stored per ship/voyage in the claim details.
        elseif ($item->klaimDetails->count() > 0) {
            $hasDetails = true;
            $details = $item->klaimDetails->filter(fn ($d) => strtolower(trim($d->kapal ?? '')) === $kapalLower
                && strtolower(trim($d->voyage ?? '')) === $voyageLower);
            $nominal = $details->sum(fn ($d) => (float) $d->subtotal ?: collect($d->kontainer_ids ?? [])->sum('biaya_klaim'));
            $total = $details->sum(fn ($d) => (float) $d->total_biaya
                ?: ((float) $d->subtotal ?: collect($d->kontainer_ids ?? [])->sum('biaya_klaim')));
        }

        // TEMAS costs follow the final invoice, rather than the DP cash advance.
        elseif ($item->temasDetails->count() > 0) {
            $hasDetails = true;
            $details = $item->temasDetails->filter(fn ($d) => strtolower(trim($d->kapal ?? '')) === $kapalLower
                && strtolower(trim($d->voyage ?? '')) === $voyageLower
                && $d->stage?->payment_mode !== 'dp');
            $nominal = $details->sum('sub_total');
            $total = $details->sum('rekap_total');
        }

        // Generic fallback for other details
        else {
            $relations = [
                'perlengkapanDetails',
                'labuhTambatDetails',
                'oppOptDetails',
                'thcDetails',
                'loloDetails',
                'storageDetails',
                'freightDetails',
                'perijinanDetails',
                'meratusDetails',
                'temasDetails',
                'tantoDetails',
                'demurrageDetails',
                'notaReturDetails',
                'operasionalDetails',
                'dokumens',
            ];

            foreach ($relations as $rel) {
                if ($item->{$rel}->count() > 0) {
                    $details = $item->{$rel}->filter(fn ($d) => isset($d->kapal) && strtolower(trim($d->kapal)) === $kapalLower && isset($d->voyage) && strtolower(trim($d->voyage)) === $voyageLower);
                    if ($details->count() > 0) {
                        $hasDetails = true;
                        $nominal += $details->sum('subtotal') ?: $details->sum('nominal') ?: $details->sum('total_biaya');
                        $pph += $details->sum('pph') ?: 0;
                        $total += $details->sum('total_biaya') ?: $details->sum('grand_total') ?: $nominal;
                    }
                }
            }
        }

        if (! $hasDetails) {
            $nominal = $item->nominal;
            $ppn = $item->ppn;
            $pph = $item->pph;
            $total = $item->total_biaya;
            // Older claims may only have the nominal on the parent transaction.
            if ((float) $total === 0.0 && stripos($item->klasifikasiBiaya?->nama ?? '', 'klaim') !== false) {
                $total = (float) $nominal + (float) $ppn - (float) $pph;
            }
        }

        return [
            'nominal' => $nominal,
            'ppn' => $ppn,
            'pph' => $pph,
            'total_biaya' => $total,
        ];
    }

    /**
     * Display a listing of the resource.
     */
    private function additionalShipVoyages()
    {
        $rows = collect();
        foreach ([\App\Models\Manifest::class, \App\Models\Bl::class, \App\Models\Prospek::class,
            \App\Models\SuratJalanBongkaran::class, \App\Models\SuratJalanBongkaranBatam::class,
            \App\Models\PranotaOb::class, PranotaObMuatTemas::class] as $model) {
            $rows = $rows->concat($model::get(['nama_kapal', 'no_voyage']));
        }

        return $rows;
    }

    public function index()
    {
        $kapals = [];
        $records = BiayaKapal::with($this->relations)->get();

        foreach ($records as $record) {
            $data = $this->getShipsAndVoyagesForRecord($record);
            foreach ($data['ships'] as $ship) {
                $kapals[$ship] = $ship;
            }
        }
        foreach ($this->additionalShipVoyages() as $row) {
            $ship = trim($row->nama_kapal ?? '');
            if ($ship !== '') {
                $kapals[$ship] = $ship;
            }
        }
        ksort($kapals);

        // Get Master Kapal data for Pemilik (Owner) filtering
        $masterKapals = \App\Models\MasterKapal::all();
        $pemilikList = $masterKapals->pluck('pelayaran')->filter()->unique()->sort()->values();

        $kapalPemilikMap = [];
        foreach ($masterKapals as $mk) {
            if ($mk->nama_kapal) {
                // Store mapped by lowercase name for robust matching
                $kapalPemilikMap[strtolower(trim($mk->nama_kapal))] = $mk->pelayaran;
            }
        }

        return view('rekap-biaya-kapal.index', compact('kapals', 'pemilikList', 'kapalPemilikMap'));
    }

    /**
     * Get voyages associated with a specific ship.
     */
    public function getVoyages(Request $request)
    {
        $selectedShip = $request->query('kapal');
        if (! $selectedShip) {
            return response()->json([]);
        }

        $voyages = [];
        $records = BiayaKapal::with($this->relations)->get();

        $selectedShipLower = strtolower(trim($selectedShip));

        foreach ($records as $record) {
            $hasAnyDetails = false;
            foreach ($this->relations as $relation) {
                if ($record->relationLoaded($relation) && $record->{$relation}->count() > 0) {
                    $hasAnyDetails = true;
                    foreach ($record->{$relation} as $detail) {
                        $dKapal = isset($detail->kapal) ? strtolower(trim($detail->kapal)) : '';
                        if ($dKapal === $selectedShipLower && isset($detail->voyage)) {
                            $trimmed = trim($detail->voyage);
                            if ($trimmed !== '') {
                                $voyages[strtolower($trimmed)] = $trimmed;
                            }
                        }
                    }
                }
            }

            if (! $hasAnyDetails) {
                $parentShips = is_array($record->nama_kapal) ? $record->nama_kapal : ($record->nama_kapal ? [$record->nama_kapal] : []);
                $parentShipsLower = array_map(fn ($s) => strtolower(trim($s)), $parentShips);
                $indices = array_keys($parentShipsLower, $selectedShipLower);

                if (! empty($indices)) {
                    $parentVoyages = is_array($record->no_voyage) ? $record->no_voyage : ($record->no_voyage ? [$record->no_voyage] : []);
                    foreach ($indices as $idx) {
                        $voyageToUse = null;
                        if (isset($parentVoyages[$idx])) {
                            $voyageToUse = $parentVoyages[$idx];
                        } elseif (count($parentVoyages) === 1) {
                            $voyageToUse = $parentVoyages[0];
                        }

                        if ($voyageToUse !== null) {
                            $trimmed = trim($voyageToUse);
                            if ($trimmed !== '') {
                                $voyages[strtolower($trimmed)] = $trimmed;
                            }
                        }
                    }
                }
            }
        }
        foreach ($this->additionalShipVoyages() as $row) {
            if (strtolower(trim($row->nama_kapal ?? '')) === $selectedShipLower && trim($row->no_voyage ?? '') !== '') {
                $voyages[strtolower(trim($row->no_voyage))] = trim($row->no_voyage);
            }
        }
        krsort($voyages);

        $voyageList = array_values($voyages);

        // Pindahkan "DOCK" ke paling bawah
        $dockItems = [];
        $otherItems = [];
        foreach ($voyageList as $v) {
            if (strtolower(trim($v)) === 'dock') {
                $dockItems[] = $v;
            } else {
                $otherItems[] = $v;
            }
        }

        // Urutkan berdasarkan tahun (2 digit terakhir) secara descending, lalu secara alfabetikal descending
        usort($otherItems, function ($a, $b) {
            $yearA = preg_match('/(\d{2})$/', trim($a), $matchesA) ? (int) $matchesA[1] : 0;
            $yearB = preg_match('/(\d{2})$/', trim($b), $matchesB) ? (int) $matchesB[1] : 0;

            if ($yearA !== $yearB) {
                return $yearB <=> $yearA;
            }

            // Jika tahun sama (atau tidak memiliki format tahun 2 digit di akhir), urutkan secara descending seperti semula
            return strcmp(strtolower(trim($b)), strtolower(trim($a)));
        });

        $finalVoyages = array_merge($otherItems, $dockItems);

        return response()->json($finalVoyages);
    }

    private function normalizeBl($number): string
    {
        return strtoupper(preg_replace('/-\d+$/', '', trim((string) $number)));
    }

    private function normalizeLocation($value): ?string
    {
        $value = strtolower(trim((string) $value));
        foreach (['jakarta', 'batam'] as $location) {
            if (preg_match('/\b'.$location.'\b/', $value)) {
                return $location;
            }
        }

        return null;
    }

    private function filterRecordLocation($record, string $lokasi, string $kapal, string $voyage): bool
    {
        if ($lokasi === '') {
            return true;
        }
        if (isset($record->is_pranota_uang_rit)) {
            return $lokasi === 'jakarta' && ! empty($record->rekap_rit_items);
        }
        if ($record instanceof PranotaObMuatTemas) {
            // Muat Temas entries have already been filtered by destination location.
            return ! empty($record->rekap_ob_temas_items);
        }
        if ($record instanceof BiayaKapal) {
            $parentLocation = $this->normalizeLocation($record->lokasi);
            $hasDetails = false;
            $hasMatchingDetail = false;
            foreach ($this->relations as $relation) {
                $hasDetails = $hasDetails || $record->{$relation}->isNotEmpty();
                $details = $record->{$relation}->filter(function ($detail) use ($relation, $parentLocation, $lokasi, $kapal, $voyage) {
                    $location = $relation === 'buruhBatamDetails' ? 'batam' : ($this->normalizeLocation($detail->lokasi) ?? $parentLocation);

                    return $location === $lokasi
                        && strtolower(trim($detail->kapal ?? '')) === strtolower(trim($kapal))
                        && strtolower(trim($detail->voyage ?? '')) === strtolower(trim($voyage));
                });
                $hasMatchingDetail = $hasMatchingDetail || $details->isNotEmpty();
                $record->setRelation($relation, $details);
            }

            return $hasDetails ? $hasMatchingDetail : $parentLocation === $lokasi;
        }
        if (isset($record->is_surat_jalan_bongkaran_batam)) {
            $location = 'batam';
        } elseif (isset($record->is_uang_jalan)) {
            $location = $record->surat_jalan_bongkaran_batam_id ? 'batam'
                : (($record->surat_jalan_id || $record->surat_jalan_bongkaran_id) ? 'jakarta' : null);
        } elseif (isset($record->is_tagihan_vendor)) {
            $location = 'jakarta'; // Vendor invoices use the Jakarta SuratJalan model.
        } elseif (isset($record->is_amprahan)) {
            $location = $this->normalizeLocation($record->stockAmprahan?->lokasi);
        } else {
            $location = $this->normalizeLocation($record->lokasi);
        }

        return $location === $lokasi;
    }

    private function splitCostForBl($record, RekapBlService $resolver, string $kapal, string $voyage, string|array $bl): array
    {
        $empty = ['nominal' => 0, 'ppn' => 0, 'pph' => 0, 'total_biaya' => 0];
        $selected = clone $record;
        $common = clone $record;
        $selected->apportioned = $empty;
        $common->apportioned = $empty;
        $hasSelected = false;
        $hasCommon = false;
        $add = function ($cost, $ratio) use ($selected, $common, &$hasSelected, &$hasCommon) {
            $target = $ratio === null ? $common : $selected;
            if ($ratio === 0.0) {
                return;
            }
            $totals = $target->apportioned;
            foreach ($totals as $field => $amount) {
                $totals[$field] += round((float) $cost[$field] * ($ratio ?? 1), 2);
            }
            $target->apportioned = $totals;
            if ($ratio === null) {
                $hasCommon = true;
            } else {
                $hasSelected = true;
            }
        };

        if ($record instanceof BiayaKapal) {
            $parentRatio = $resolver->ratio($record, $bl);
            $hasDetails = false;
            foreach ($this->relations as $relation) {
                $selected->setRelation($relation, collect());
                $common->setRelation($relation, collect());
                $relationDetails = $relation === 'tkbmDetails'
                    ? $this->tkbmSections($record->{$relation})->values()
                    : $record->{$relation}->map(fn ($detail) => collect([$detail]));
                foreach ($relationDetails as $detailGroup) {
                    $detail = $detailGroup->first();
                    if (strtolower(trim($detail->kapal ?? '')) !== strtolower(trim($kapal))
                        || strtolower(trim($detail->voyage ?? '')) !== strtolower(trim($voyage))) {
                        continue;
                    }
                    $hasDetails = true;
                    $manifestShares = null;
                    if ($relation === 'oppOptDetails' && $detail->manifests->isNotEmpty()) {
                        $manifests = $detail->manifests->unique('id')->values();
                        $manifestShares = $manifests->map(fn ($manifest) => $resolver->ratio($manifest, $bl));
                        $ratio = $manifestShares->every(fn ($share) => $share === null)
                            ? $parentRatio
                            : $manifestShares->sum() / $manifests->count();
                    } else {
                        $ratio = $resolver->ratio($detail, $bl) ?? $parentRatio;
                    }
                    $single = clone $record;
                    foreach ($this->relations as $name) {
                        $single->setRelation($name, collect());
                    }
                    $single->setRelation($relation, $detailGroup);
                    $cost = $this->getApportionedCostForRecord($single, $kapal, $voyage);
                    $add($cost, $ratio);
                    if ($ratio !== 0.0) {
                        $target = $ratio === null ? $common : $selected;
                        foreach ($detailGroup as $groupDetail) {
                            $copy = clone $groupDetail;
                            if ($relation === 'temasDetails') {
                                $copy->rekap_bl_total = round($groupDetail->rekap_total * ($ratio ?? 1), 2);
                            }
                            if ($relation === 'oppOptDetails') {
                                $copy->rekap_opp_opt_total = round($cost['total_biaya'] * ($ratio ?? 1), 2);
                                if ($manifestShares !== null && $ratio !== null) {
                                    $copy->setRelation('manifests', $manifests->filter(fn ($manifest, $index) => ($manifestShares[$index] ?? 0) > 0)
                                        ->map(function ($manifest, $index) use ($manifestShares) {
                                            $manifestCopy = clone $manifest;
                                            $manifestCopy->rekap_bl_share = $manifestShares[$index];

                                            return $manifestCopy;
                                        })->values());
                                }
                            }
                            $target->{$relation}->push($copy);
                        }
                    }
                }
            }
            if (! $hasDetails) {
                $add($record->apportioned, $parentRatio);
            }
        } elseif (isset($record->is_pranota_uang_rit)) {
            $selected->rekap_rit_items = [];
            $common->rekap_rit_items = [];
            foreach ($record->rekap_rit_items as $entry) {
                $amount = $entry['biaya'];
                $ratio = $resolver->transportRatio($entry['surat_jalan'], $kapal, $voyage, $bl);
                $add(['nominal' => $amount, 'ppn' => 0, 'pph' => 0, 'total_biaya' => $amount],
                    $ratio);
                if ($ratio !== 0.0) {
                    $target = $ratio === null ? $common : $selected;
                    $entries = $target->rekap_rit_items;
                    $entries[] = $entry;
                    $target->rekap_rit_items = $entries;
                }
            }
        } elseif (isset($record->is_surat_jalan_bongkaran_batam)) {
            // On Batam waybills, no_bl may hold an internal MNF reference.
            // The linked manifest contains the actual BL number.
            $add($record->apportioned, $resolver->ratio($record->manifest ?? $record, $bl));
        } elseif (isset($record->is_uang_jalan) || isset($record->is_tagihan_vendor)) {
            $sj = $record->suratJalan ?? $record->suratJalanBongkaran ?? $record->suratJalanBongkaranBatam;
            $add($record->apportioned, $resolver->transportRatio($sj, $kapal, $voyage, $bl));
        } elseif (isset($record->is_pranota_ob)) {
            $entries = $record instanceof PranotaObMuatTemas ? $record->rekap_ob_temas_items : $record->getEnrichedItems();
            $selectedTemasItems = [];
            $commonTemasItems = [];
            foreach ($entries as $entry) {
                $amount = (float) ($entry['biaya'] ?? 0);
                $ratio = $resolver->ratio($entry, $bl);
                $add(['nominal' => $amount, 'ppn' => 0, 'pph' => 0, 'total_biaya' => $amount], $ratio);
                if ($record instanceof PranotaObMuatTemas && $ratio !== 0.0) {
                    if ($ratio === null) {
                        $commonTemasItems[] = $entry;
                    } else {
                        $selectedTemasItems[] = $entry;
                    }
                }
            }
            if ($record instanceof PranotaObMuatTemas) {
                $selected->rekap_ob_temas_items = $selectedTemasItems;
                $common->rekap_ob_temas_items = $commonTemasItems;
            }
        } else {
            $add($record->apportioned, $resolver->ratio($record, $bl));
        }

        return [$hasSelected ? $selected : null, $hasCommon ? $common : null];
    }

    public function getBls(Request $request)
    {
        $data = $request->validate(['kapal' => 'required|string', 'voyage' => 'required|string']);
        $kapal = strtolower(trim($data['kapal']));
        $voyage = strtolower(trim($data['voyage']));
        $blResolver = RekapBlService::forVoyage($data['kapal'], $data['voyage']);
        $numbers = $blResolver->available();
        $containerCounts = $blResolver->containerCounts();

        $normalizeShip = fn ($name) => preg_replace('/[^a-z0-9]/', '', preg_replace('/^km[.\\s]+/i', '', strtolower(trim((string) $name))));
        $blMetadata = [];
        $addMetadata = function ($blNumbers, $shippers, $jenisBarang) use (&$blMetadata) {
            $shippers = collect($shippers)->filter(fn ($value) => filled($value))->unique()->values()->all();
            $jenisBarang = collect($jenisBarang)->filter(fn ($value) => filled($value))->unique()->values()->all();

            foreach ($blNumbers as $number) {
                $number = $this->normalizeBl($number);
                if ($number === '') {
                    continue;
                }
                $blMetadata[$number]['shipper'] = array_values(array_unique(array_merge($blMetadata[$number]['shipper'] ?? [], $shippers)));
                $blMetadata[$number]['jenis_barang'] = array_values(array_unique(array_merge($blMetadata[$number]['jenis_barang'] ?? [], $jenisBarang)));
            }
        };

        $manifestRecords = \App\Models\Manifest::with(['shipperConsignee', 'shipperJb', 'shipperDetails.shipperConsignee', 'prospek.tandaTerima'])
            ->whereRaw('LOWER(TRIM(no_voyage)) = ?', [$voyage])
            ->get()
            ->filter(fn ($record) => $normalizeShip($record->nama_kapal) === $normalizeShip($data['kapal']));

        $receiptNumbers = $manifestRecords->pluck('nomor_tanda_terima')->filter()->unique()->values();
        $receiptTypes = [];
        if ($receiptNumbers->isNotEmpty()) {
            foreach ([\App\Models\TandaTerima::class, \App\Models\TandaTerimaBatam::class] as $receiptModel) {
                foreach ($receiptModel::whereIn('no_surat_jalan', $receiptNumbers)->get(['no_surat_jalan', 'jenis_barang']) as $receipt) {
                    if (filled($receipt->jenis_barang)) $receiptTypes[$receipt->no_surat_jalan] = $receipt->jenis_barang;
                }
            }
            foreach ([\App\Models\TandaTerimaTanpaSuratJalan::class, \App\Models\TandaTerimaTanpaSuratJalanBatam::class] as $receiptModel) {
                foreach ($receiptModel::whereIn('nomor_tanda_terima', $receiptNumbers)->get(['nomor_tanda_terima', 'jenis_barang']) as $receipt) {
                    if (filled($receipt->jenis_barang)) $receiptTypes[$receipt->nomor_tanda_terima] = $receipt->jenis_barang;
                }
            }
            foreach (\App\Models\TandaTerimaTanpaSuratJalan::whereIn('no_tanda_terima', $receiptNumbers)->get(['no_tanda_terima', 'jenis_barang']) as $receipt) {
                if (filled($receipt->jenis_barang)) $receiptTypes[$receipt->no_tanda_terima] = $receipt->jenis_barang;
            }
            foreach (\App\Models\TandaTerimaTanpaSuratJalanBatam::whereIn('no_tanda_terima', $receiptNumbers)->get(['no_tanda_terima', 'jenis_barang']) as $receipt) {
                if (filled($receipt->jenis_barang)) $receiptTypes[$receipt->no_tanda_terima] = $receipt->jenis_barang;
            }
        }
        foreach ($manifestRecords as $record) {
            $details = $record->shipperDetails;
            $receiptType = $record->prospek?->tandaTerima?->jenis_barang
                ?: ($receiptTypes[$record->nomor_tanda_terima ?? ''] ?? null);
            $addMetadata(
                preg_split('/[,;\\n]+/', (string) $record->nomor_bl),
                collect([$record->pengirim, $record->shipperJb?->shipper, $record->shipperConsignee?->shipper])
                    ->merge($details->map(fn ($detail) => $detail->shipperConsignee?->shipper ?? $detail->pengirim)),
                collect([$receiptType])
            );
        }

        foreach (BiayaKapal::with($this->relations)->get() as $record) {
            if (! $this->recordHasShipAndVoyage($record, $data['kapal'], $data['voyage'])) {
                continue;
            }
            foreach ((array) $record->no_bl as $number) {
                $numbers->push($this->normalizeBl($number));
            }
            foreach ($this->relations as $relation) {
                foreach ($record->{$relation} as $detail) {
                    if (strtolower(trim($detail->kapal ?? '')) === $kapal && strtolower(trim($detail->voyage ?? '')) === $voyage) {
                        foreach (preg_split('/[,;\n]+/', $detail->nomor_bl ?? $detail->no_bl ?? '') as $number) {
                            $numbers->push($this->normalizeBl($number));
                        }
                    }
                }
            }
        }

        foreach ([\App\Models\SuratJalanBongkaran::class, \App\Models\SuratJalanBongkaranBatam::class] as $model) {
            $shipLike = '%'.preg_replace('/[^a-z0-9]+/i', '%', $data['kapal']).'%';
            foreach ($model::where('nama_kapal', 'like', $shipLike)->where('no_voyage', $data['voyage'])->pluck('no_bl') as $number) {
                foreach (preg_split('/[,;\n]+/', $number ?? '') as $part) {
                    $numbers->push($this->normalizeBl($part));
                }
            }
        }

        $options = $numbers->filter()->unique()->sort()->values()->map(function ($number) use ($blMetadata, $containerCounts) {
            $metadata = $blMetadata[$number] ?? [];

            return [
                'number' => $number,
                'shipper' => collect($metadata['shipper'] ?? [])->implode(', ') ?: '-',
                'jenis_barang' => collect($metadata['jenis_barang'] ?? [])->implode(', ') ?: '-',
                'jumlah_kontainer' => $containerCounts[$number] ?? 0,
            ];
        });

        return response()->json($options);
    }

    /** Show the costs for the selected ship, voyage, and optional BL. */
    public function show(Request $request)
    {
        // Keep existing links using ?bl=01 compatible with the multiple selection.
        if (is_string($request->input('bl'))) {
            $request->merge(['bl' => [$request->input('bl')]]);
        }
        $request->validate([
            'kapal' => 'required|string',
            'voyage' => 'required|string',
            'bl' => 'nullable|array',
            'bl.*' => 'nullable|string|max:255',
            'lokasi' => 'nullable|in:jakarta,batam',
        ]);

        $kapal = $request->kapal;
        $voyage = $request->voyage;
        $lokasi = $request->input('lokasi') ?? '';
        $bls = collect($request->input('bl', []))->map(fn ($number) => $this->normalizeBl($number))->filter()->unique()->values()->all();
        $bl = implode(', ', $bls);

        // Fetch all biaya kapals and load relations
        $allRelations = array_merge(['klasifikasiBiaya', 'vendor', 'temasDetails.stage.details', 'tkbmDetails.pricelistTkbm', 'oppOptDetails.manifests'], $this->relations);
        $biayaKapals = BiayaKapal::with($allRelations)
            ->get()
            ->filter(function ($record) use ($kapal, $voyage, $lokasi) {
                if ($record->temasDetails->isNotEmpty()) {
                    $details = $record->temasDetails->filter(fn ($detail) => $detail->stage?->payment_mode !== 'dp');
                    if ($details->isEmpty()) {
                        return false;
                    }
                    $record->setRelation('temasDetails', $details);
                }

                return $this->recordHasShipAndVoyage($record, $kapal, $voyage)
                    && $this->filterRecordLocation($record, $lokasi, $kapal, $voyage);
            });

        // Apportion each record
        foreach ($biayaKapals as $record) {
            $record->apportioned = $this->getApportionedCostForRecord($record, $kapal, $voyage);
        }

        $kapalLike = '%'.preg_replace('/[^a-z0-9]+/i', '%', $kapal).'%';

        // Fetch Pranota OB
        $pranotaObs = \App\Models\PranotaOb::where('nama_kapal', 'like', $kapalLike)
            ->where('no_voyage', $voyage)
            ->where('status', '!=', 'cancelled')
            ->get();
        foreach ($pranotaObs as $pranota) {
            $totalBiaya = $pranota->calculateTotalAmount();
            $pranota->apportioned = [
                'nominal' => $totalBiaya,
                'ppn' => 0,
                'pph' => 0,
                'total_biaya' => $totalBiaya,
            ];
            $pranota->display_total = $totalBiaya;
            $pranota->is_pranota_ob = true;
            $pranota->nomor_invoice = $pranota->nomor_pranota;
            $pranota->tanggal = $pranota->tanggal_ob;
            $pranota->jenis_biaya = 'Pranota OB';
            $biayaKapals->push($pranota);
        }

        // Include the saved Muat Temas costs and adjustment once, regardless of payment status.
        $pranotaTemas = PranotaObMuatTemas::with('items')
            ->where('nama_kapal', 'like', $kapalLike)->where('no_voyage', $voyage)
            ->where('status', '!=', 'cancelled')->get();
        $gudangLocations = $lokasi === '' || $pranotaTemas->isEmpty() ? collect()
            : Gudang::get(['nama_gudang', 'lokasi'])->keyBy(fn ($gudang) => strtoupper(trim($gudang->nama_gudang)));
        foreach ($pranotaTemas as $pranota) {
            $entries = collect($pranota->getPaymentItems())->filter(function ($entry) use ($lokasi, $gudangLocations) {
                if ($lokasi === '') {
                    return true;
                }
                $destination = strtoupper(trim($entry['tujuan_gudang'] ?? ''));
                $location = $this->normalizeLocation($entry['lokasi'] ?? '')
                    ?? $this->normalizeLocation($gudangLocations->get($destination)?->lokasi)
                    ?? $this->normalizeLocation($destination);
                if ($location === null && preg_match('/\bJKT\b/', $destination)) {
                    $location = 'jakarta';
                }

                return $location === $lokasi;
            })->values()->all();
            if (empty($entries)) {
                continue;
            }
            $totalBiaya = round(collect($entries)->sum('biaya'), 2);
            $pranota->rekap_ob_temas_items = $entries;
            $pranota->apportioned = ['nominal' => $totalBiaya, 'ppn' => 0, 'pph' => 0, 'total_biaya' => $totalBiaya];
            $pranota->display_total = $totalBiaya;
            $pranota->is_pranota_ob = true;
            $pranota->is_pranota_ob_muat_temas = true;
            $pranota->nomor_invoice = $pranota->nomor_pranota;
            $pranota->tanggal = $pranota->tanggal_pranota;
            $pranota->jenis_biaya = 'Pranota OB Muat Temas';
            $biayaKapals->push($pranota);
        }

        // Fetch Pemakaian Stock Amprahan
        $amprahanUsages = \App\Models\StockAmprahanUsage::with(['stockAmprahan'])
            ->whereHas('kapal', function ($q) use ($kapalLike) {
                $q->where('nama_kapal', 'like', $kapalLike);
            })
            ->where('nomor_voyage', $voyage)
            ->get();

        foreach ($amprahanUsages as $usage) {
            $totalBiaya = floatval($usage->jumlah) * floatval($usage->stockAmprahan->harga_satuan ?? 0);
            $usage->apportioned = [
                'nominal' => $totalBiaya,
                'ppn' => 0,
                'pph' => 0,
                'total_biaya' => $totalBiaya,
            ];
            $usage->is_amprahan = true;
            $usage->nomor_invoice = $usage->stockAmprahan->nomor_bukti ?? '-';
            $usage->tanggal = $usage->tanggal_pengambilan;
            $usage->jenis_biaya = 'Pemakaian Amprahan';
            $usage->nama_barang_amprahan = $usage->stockAmprahan->nama_barang ?? 'Barang';

            $biayaKapals->push($usage);
        }

        // Fetch Uang Jalan (Muat dan Bongkar)
        $uangJalans = \App\Models\UangJalan::where(function ($query) use ($kapalLike, $voyage) {
            $query->whereHas('suratJalan.prospeks', function ($q) use ($kapalLike, $voyage) {
                $q->where('nama_kapal', 'like', $kapalLike)->where('no_voyage', $voyage);
            })
                ->orWhereHas('suratJalanBongkaran', function ($q) use ($kapalLike, $voyage) {
                    $q->where('nama_kapal', 'like', $kapalLike)->where('no_voyage', $voyage);
                });
        })->with(['suratJalan.prospeks', 'suratJalanBongkaran'])
            ->where('status', '!=', 'dibatalkan')->get();

        foreach ($uangJalans as $uj) {
            $totalBiaya = floatval($uj->jumlah_total ?? 0);
            $uj->apportioned = [
                'nominal' => $totalBiaya,
                'ppn' => 0,
                'pph' => 0,
                'total_biaya' => $totalBiaya,
            ];
            $uj->is_uang_jalan = true;
            $uj->nomor_invoice = $uj->nomor_uang_jalan ?? '-';
            $uj->tanggal = $uj->tanggal_uang_jalan;
            $uj->jenis_biaya = 'Uang Jalan';

            $biayaKapals->push($uj);
        }

        // Surat Jalan Bongkaran Batam is a direct voyage cost source. It does not
        // require a related Uang Jalan record to appear in this recap.
        $suratJalanBongkaranBatams = \App\Models\SuratJalanBongkaranBatam::with('manifest')
            ->whereRaw('LOWER(TRIM(lokasi)) = ?', ['batam'])
            ->where(function ($query) use ($kapal, $voyage) {
                $query->where(function ($direct) use ($kapal, $voyage) {
                    $direct->whereRaw('LOWER(TRIM(nama_kapal)) = ?', [strtolower(trim($kapal))])
                        ->whereRaw('LOWER(TRIM(no_voyage)) = ?', [strtolower(trim($voyage))]);
                })->orWhereHas('manifest', function ($manifest) use ($kapal, $voyage) {
                    $manifest->whereRaw('LOWER(TRIM(nama_kapal)) = ?', [strtolower(trim($kapal))])
                        ->whereRaw('LOWER(TRIM(no_voyage)) = ?', [strtolower(trim($voyage))]);
                });
            })
            ->get();
        foreach ($suratJalanBongkaranBatams as $suratJalan) {
            $nominal = $suratJalan->tanpa_uang_jalan ? 0 : (float) ($suratJalan->uang_jalan_nominal ?? 0);
            $suratJalan->apportioned = [
                'nominal' => $nominal,
                'ppn' => 0,
                'pph' => 0,
                'total_biaya' => $nominal,
            ];
            $suratJalan->is_surat_jalan_bongkaran_batam = true;
            $suratJalan->nomor_invoice = $suratJalan->nomor_surat_jalan ?? '-';
            $suratJalan->tanggal = $suratJalan->tanggal_surat_jalan;
            $suratJalan->jenis_biaya = 'Surat Jalan Bongkaran Batam';
            $suratJalan->klasifikasiBiaya = (object) ['nama' => 'Surat Jalan Bongkaran Batam'];

            $biayaKapals->push($suratJalan);
        }

        // Resolve every saved surat jalan, including those beyond the first FK reference.
        $ritService = new RekapUangRitService;
        $ritPranotas = collect();
        foreach ([\App\Models\PranotaUangRit::class, \App\Models\PranotaUangRitKenek::class] as $model) {
            foreach ($model::with(['suratJalan', 'suratJalanBongkaran'])
                ->where('status', '!=', 'cancelled')->get() as $pranota) {
                $ritPranotas->push($pranota);
            }
        }
        $references = $ritPranotas->flatMap(fn ($pranota) => $ritService->references($pranota));
        $muat = \App\Models\SuratJalan::with(['prospeks', 'pengirimRelation'])
            ->whereIn('no_surat_jalan', $references->where('bongkaran', false)->pluck('number')->unique())
            ->get()->keyBy('no_surat_jalan');
        $bongkaran = \App\Models\SuratJalanBongkaran::whereIn('nomor_surat_jalan',
            $references->where('bongkaran', true)->pluck('number')->unique())
            ->get()->keyBy('nomor_surat_jalan');
        foreach ($ritPranotas as $pranota) {
            $entries = $ritService->entries($pranota, $muat, $bongkaran, $kapal, $voyage, $lokasi);
            if ($entries->isEmpty()) {
                continue;
            }
            $total = round($entries->sum('biaya'), 2);
            $pranota->rekap_rit_items = $entries->all();
            $pranota->apportioned = ['nominal' => $total, 'ppn' => 0, 'pph' => 0, 'total_biaya' => $total];
            $pranota->is_pranota_uang_rit = true;
            $pranota->is_pranota_uang_rit_kenek = $pranota instanceof \App\Models\PranotaUangRitKenek;
            $pranota->nomor_invoice = $pranota->no_pranota;
            $pranota->jenis_biaya = $pranota->is_pranota_uang_rit_kenek ? 'Pranota Uang Rit Kenek' : 'Pranota Uang Rit Supir';
            foreach ($ritService->rowsForSuratJalan($pranota) as $row) {
                $biayaKapals->push($row);
            }
        }

        // Fetch Tagihan Vendor Supir (Pranota Invoice Vendor Supir details)
        $tagihanVendors = \App\Models\TagihanSupirVendor::with(['vendor', 'suratJalan.prospeks'])
            ->whereHas('suratJalan.prospeks', function ($q) use ($kapalLike, $voyage) {
                $q->where('nama_kapal', 'like', $kapalLike)->where('no_voyage', $voyage);
            })
            ->where(function ($q) {
                $q->where('status_pembayaran', '!=', 'dibatalkan')->orWhereNull('status_pembayaran');
            })
            ->get();

        foreach ($tagihanVendors as $tagihan) {
            $totalBiaya = floatval($tagihan->nominal ?? 0) + floatval($tagihan->uang_muat ?? 0) + floatval($tagihan->adjustment ?? 0);
            $tagihan->apportioned = [
                'nominal' => $totalBiaya,
                'ppn' => 0,
                'pph' => 0,
                'total_biaya' => $totalBiaya,
            ];
            $tagihan->is_tagihan_vendor = true;
            $tagihan->nomor_invoice = $tagihan->suratJalan->no_surat_jalan ?? '-';
            $tagihan->tanggal = $tagihan->suratJalan->tanggal_surat_jalan ?? $tagihan->created_at;
            $tagihan->jenis_biaya = 'Tagihan Vendor Supir ('.($tagihan->vendor->nama_vendor ?? 'Vendor').')';

            $biayaKapals->push($tagihan);
        }

        $biayaUmum = collect();
        // BiayaKapal details were filtered before calculating their totals above.
        $biayaKapals = $biayaKapals->filter(fn ($record) => $record instanceof BiayaKapal
            || $this->filterRecordLocation($record, $lokasi, $kapal, $voyage));
        if ($bl !== '') {
            $resolver = RekapBlService::forVoyage($kapal, $voyage);
            $filtered = collect();
            foreach ($biayaKapals as $record) {
                [$selected, $common] = $this->splitCostForBl($record, $resolver, $kapal, $voyage, $bls);
                if ($selected) {
                    $filtered->push($selected);
                }
                if ($common) {
                    $biayaUmum->push($common);
                }
            }
            $biayaKapals = $filtered;
        }

        $catalogResolver = $resolver ?? RekapBlService::forVoyage($kapal, $voyage);
        $tkbmContainers = $catalogResolver->containerDetails($bls);
        $opslagContainers = null;
        foreach ($biayaKapals as $record) {
            if (! $record instanceof BiayaKapal) {
                continue;
            }
            if ($record->tkbmDetails->isNotEmpty()) {
                $record->rekap_tkbm_rows = $this->allocateTkbmRows($record, $tkbmContainers, $kapal, $voyage);
            }
            if ($record->oppOptDetails->isNotEmpty()) {
                if ($record->oppOptDetails->contains(fn ($detail) => strtolower(trim((string) $detail->klasifikasi)) === 'opslag'
                    && $detail->manifests->isEmpty())) {
                    $opslagContainers ??= $catalogResolver->opslagContainerDetails();
                }
                $record->rekap_opp_opt_rows = $this->allocateOppOptRows($record, $opslagContainers ?? collect(), $kapal, $voyage);
            }
        }

        // Calculate summaries based on apportioned costs
        $summary = [
            'total_nominal' => $biayaKapals->sum(fn ($item) => $item->apportioned['nominal']),
            'total_ppn' => $biayaKapals->sum(fn ($item) => $item->apportioned['ppn']),
            'total_pph' => $biayaKapals->sum(fn ($item) => $item->apportioned['pph']),
            'grand_total' => $biayaKapals->sum(fn ($item) => $item->apportioned['total_biaya']),
        ];

        // Group by classification/jenis_biaya
        $grouped = $biayaKapals->groupBy(function ($item) {
            if (isset($item->buruhBatamDetails) && $item->buruhBatamDetails->isNotEmpty()) {
                return 'BURUH BONGKAR BATAM';
            }

            return $item->klasifikasiBiaya->nama ?? $item->jenis_biaya ?? 'Lain-lain';
        });

        $temasContainers = $biayaKapals
            ->filter(fn ($item) => $item instanceof BiayaKapal)
            ->flatMap(fn ($item) => $item->temasDetails)
            ->filter(fn ($detail) => strtolower(trim($detail->kapal ?? '')) === strtolower(trim($kapal)) && strtolower(trim($detail->voyage ?? '')) === strtolower(trim($voyage)))
            ->flatMap(fn ($detail) => explode(',', $detail->nomor_kontainer ?? ''))
            ->map(fn ($number) => strtoupper(trim($number)))->filter()->unique()->values();
        $temasManifests = collect();
        if ($temasContainers->isNotEmpty()) {
            $temasManifests = \App\Models\Manifest::with(['shipperConsignee', 'shipperJb', 'shipperDetails.shipperConsignee'])
                ->whereRaw('LOWER(TRIM(no_voyage)) = ?', [strtolower(trim($voyage))])
                ->whereIn('nomor_kontainer', $temasContainers)->get();
        }

        return view('rekap-biaya-kapal.show', compact('kapal', 'voyage', 'lokasi', 'bl', 'biayaKapals', 'biayaUmum', 'summary', 'grouped', 'temasManifests'));
    }
}
