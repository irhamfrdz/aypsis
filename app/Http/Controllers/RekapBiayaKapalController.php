<?php

namespace App\Http\Controllers;

use App\Models\BiayaKapal;
use App\Services\RekapBlService;
use Illuminate\Http\Request;

class RekapBiayaKapalController extends Controller
{
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

            $subtotal = $details->sum('subtotal');
            $adjustment = 0;
            $nominal = 0;
            $pph = 0;
            $total = 0;

            $first = $details->first();
            if ($first) {
                $adjustment = $first->adjustment ?? 0;
                $nominal = $first->total_nominal ?? ($subtotal + $adjustment);
                $pph = $first->pph ?? 0;
                $total = $first->grand_total ?? ($nominal - $pph);
            }

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
        }

        elseif ($item->umumDetails->count() > 0) {
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
            \App\Models\PranotaOb::class] as $model) {
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
                foreach ($record->{$relation} as $detail) {
                    if (strtolower(trim($detail->kapal ?? '')) !== strtolower(trim($kapal))
                        || strtolower(trim($detail->voyage ?? '')) !== strtolower(trim($voyage))) {
                        continue;
                    }
                    $hasDetails = true;
                    $ratio = $resolver->ratio($detail, $bl) ?? $parentRatio;
                    $single = clone $record;
                    foreach ($this->relations as $name) {
                        $single->setRelation($name, collect());
                    }
                    $single->setRelation($relation, collect([$detail]));
                    $cost = $this->getApportionedCostForRecord($single, $kapal, $voyage);
                    $add($cost, $ratio);
                    if ($ratio !== 0.0) {
                        $target = $ratio === null ? $common : $selected;
                        $copy = clone $detail;
                        if ($relation === 'temasDetails') {
                            $copy->rekap_bl_total = round($detail->rekap_total * ($ratio ?? 1), 2);
                        }
                        $target->{$relation}->push($copy);
                    }
                }
            }
            if (! $hasDetails) {
                $add($record->apportioned, $parentRatio);
            }
        } elseif (isset($record->is_uang_jalan) || isset($record->is_tagihan_vendor)) {
            $sj = $record->suratJalan ?? $record->suratJalanBongkaran ?? $record->suratJalanBongkaranBatam;
            $add($record->apportioned, $resolver->transportRatio($sj, $kapal, $voyage, $bl));
        } elseif (isset($record->is_pranota_ob)) {
            foreach ($record->getEnrichedItems() as $entry) {
                $amount = (float) ($entry['biaya'] ?? 0);
                $add(['nominal' => $amount, 'ppn' => 0, 'pph' => 0, 'total_biaya' => $amount], $resolver->ratio($entry, $bl));
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
        $numbers = RekapBlService::forVoyage($data['kapal'], $data['voyage'])->available();
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

        return response()->json($numbers->filter()->unique()->sort()->values());
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
        ]);

        $kapal = $request->kapal;
        $voyage = $request->voyage;
        $bls = collect($request->input('bl', []))->map(fn ($number) => $this->normalizeBl($number))->filter()->unique()->values()->all();
        $bl = implode(', ', $bls);

        // Fetch all biaya kapals and load relations
        $allRelations = array_merge(['klasifikasiBiaya', 'vendor', 'temasDetails.stage.details'], $this->relations);
        $biayaKapals = BiayaKapal::with($allRelations)
            ->get()
            ->filter(function ($record) use ($kapal, $voyage) {
                if ($record->temasDetails->isNotEmpty()) {
                    $details = $record->temasDetails->filter(fn ($detail) => $detail->stage?->payment_mode !== 'dp');
                    if ($details->isEmpty()) {
                        return false;
                    }
                    $record->setRelation('temasDetails', $details);
                }

                return $this->recordHasShipAndVoyage($record, $kapal, $voyage);
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
                })
                ->orWhereHas('suratJalanBongkaranBatam', function ($q) use ($kapalLike, $voyage) {
                    $q->where('nama_kapal', 'like', $kapalLike)->where('no_voyage', $voyage);
                });
        })->with(['suratJalan.prospeks', 'suratJalanBongkaran', 'suratJalanBongkaranBatam'])
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

        return view('rekap-biaya-kapal.show', compact('kapal', 'voyage', 'bl', 'biayaKapals', 'biayaUmum', 'summary', 'grouped', 'temasManifests'));
    }
}
