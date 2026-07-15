<?php

namespace App\Http\Controllers;

use App\Models\BiayaKapal;
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
        'tenagaKerjaDetails',
        'operasionalDetails',
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
        if (!empty($indices)) {
            foreach ($indices as $idx) {
                if (isset($parentVoyagesLower[$idx])) {
                    if ($parentVoyagesLower[$idx] === $voyageLower) return true;
                } elseif (count($parentVoyagesLower) === 1) {
                    if ($parentVoyagesLower[0] === $voyageLower) return true;
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

        // 1. Biaya Buruh (barangDetails / tenagaKerjaDetails)
        if ($item->barangDetails->count() > 0 || $item->tenagaKerjaDetails->count() > 0) {
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
                'operasionalDetails',
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
                
                if (!empty($indices)) {
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
        ksort($voyages);

        return response()->json(array_values($voyages));
    }

    /**
     * Show the detailed costs for the selected ship and voyage.
     */
    public function show(Request $request)
    {
        $request->validate([
            'kapal' => 'required|string',
            'voyage' => 'required|string',
        ]);

        $kapal = $request->kapal;
        $voyage = $request->voyage;

        // Fetch all biaya kapals and load relations
        $allRelations = array_merge(['klasifikasiBiaya', 'vendor'], $this->relations);
        $biayaKapals = BiayaKapal::with($allRelations)
            ->get()
            ->filter(function ($record) use ($kapal, $voyage) {
                return $this->recordHasShipAndVoyage($record, $kapal, $voyage);
            });

        // Apportion each record
        foreach ($biayaKapals as $record) {
            $record->apportioned = $this->getApportionedCostForRecord($record, $kapal, $voyage);
        }

        // Fetch Pranota OB
        $pranotaObs = \App\Models\PranotaOb::where('nama_kapal', $kapal)
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
            $pranota->is_pranota_ob = true;
            $pranota->nomor_invoice = $pranota->nomor_pranota;
            $pranota->tanggal = $pranota->tanggal_ob;
            $pranota->jenis_biaya = 'Pranota OB';
            $biayaKapals->push($pranota);
        }

        // Fetch Pemakaian Stock Amprahan
        $amprahanUsages = \App\Models\StockAmprahanUsage::with(['stockAmprahan'])
            ->whereHas('kapal', function ($q) use ($kapal) {
                $q->where('nama_kapal', $kapal);
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
            $usage->jenis_biaya = 'Pemakaian Amprahan (' . ($usage->stockAmprahan->nama_barang ?? 'Barang') . ')';
            
            $biayaKapals->push($usage);
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
            return $item->klasifikasiBiaya->nama ?? $item->jenis_biaya ?? 'Lain-lain';
        });

        return view('rekap-biaya-kapal.show', compact('kapal', 'voyage', 'biayaKapals', 'summary', 'grouped'));
    }
}
