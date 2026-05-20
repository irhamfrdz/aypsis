<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TandaTerima;
use App\Models\TandaTerimaTanpaSuratJalan;
use App\Models\TandaTerimaLcl;
use App\Models\Prospek;
use App\Models\MasterKapal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DokumenTandaTerimaController extends Controller
{
    /**
     * Show selection screen for vessel and voyage.
     */
    public function select()
    {
        // Get unique vessel names from manifests table
        $shipsWithData = DB::table('manifests')
            ->whereNotNull('nama_kapal')
            ->where('nama_kapal', '!=', '')
            ->select('nama_kapal')
            ->distinct()
            ->pluck('nama_kapal');

        // Query MasterKapal matching those ship names
        $kapals = MasterKapal::whereIn('nama_kapal', $shipsWithData)
            ->orderBy('nama_kapal')
            ->get();

        return view('dokumen-tanda-terima.select', compact('kapals'));
    }

    /**
     * Get voyages by kapal name/ID via Ajax.
     */
    public function getVoyages(Request $request)
    {
        $kapalId = $request->kapal_id;
        $kapal = MasterKapal::find($kapalId);
        if (!$kapal) {
            return response()->json([
                'success' => false,
                'message' => 'Kapal tidak ditemukan.'
            ], 400);
        }

        $namaKapal = $kapal->nama_kapal;

        // Get distinct voyages from manifests
        $voyages = DB::table('manifests')
            ->where('nama_kapal', $namaKapal)
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '!=', '')
            ->select('no_voyage')
            ->distinct()
            ->pluck('no_voyage')
            ->sortDesc()
            ->values()
            ->toArray();

        return response()->json([
            'success' => true,
            'voyages' => $voyages
        ]);
    }

    /**
     * Display consolidated tanda terima list.
     */
    public function index(Request $request)
    {
        // Require kapal_id and no_voyage parameters
        if (!$request->filled('kapal_id') || !$request->filled('no_voyage')) {
            return redirect()->route('dokumen-tanda-terima.select')
                ->with('info', 'Silakan pilih kapal dan voyage terlebih dahulu.');
        }

        $selectedKapal = MasterKapal::find($request->kapal_id);
        if (!$selectedKapal) {
            return redirect()->route('dokumen-tanda-terima.select')
                ->with('error', 'Kapal tidak ditemukan.');
        }

        $namaKapal = $selectedKapal->nama_kapal;
        $noVoyage = $request->no_voyage;
        $search = $request->search;

        // 1. Fetch TandaTerima (FCL) associated with Manifests on this voyage
        $tandaTerimasQuery = TandaTerima::with(['suratJalan', 'creator'])
            ->whereHas('prospeks.manifests', function($q) use ($namaKapal, $noVoyage) {
                $q->where('nama_kapal', $namaKapal)
                  ->where('no_voyage', $noVoyage);
            });

        if (!empty($search)) {
            $tandaTerimasQuery->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%");
            });
        }
        $tandaTerimas = $tandaTerimasQuery->get();

        // 2. Fetch TandaTerimaTanpaSuratJalan (Cargo) matched by no_tanda_terima / nomor_tanda_terima in manifests
        $tanpaSjQuery = TandaTerimaTanpaSuratJalan::where(function($q) use ($namaKapal, $noVoyage) {
            $q->whereIn('no_tanda_terima', function($sub) use ($namaKapal, $noVoyage) {
                $sub->select('nomor_tanda_terima')
                    ->from('manifests')
                    ->where('nama_kapal', $namaKapal)
                    ->where('no_voyage', $noVoyage)
                    ->whereNotNull('nomor_tanda_terima');
            })
            ->orWhereIn('nomor_tanda_terima', function($sub) use ($namaKapal, $noVoyage) {
                $sub->select('nomor_tanda_terima')
                    ->from('manifests')
                    ->where('nama_kapal', $namaKapal)
                    ->where('no_voyage', $noVoyage)
                    ->whereNotNull('nomor_tanda_terima');
            })
            ->orWhereIn('no_tanda_terima', function($sub) use ($namaKapal, $noVoyage) {
                $sub->select('p.no_surat_jalan')
                    ->from('prospek as p')
                    ->join('manifests as m', 'm.prospek_id', '=', 'p.id')
                    ->where('m.nama_kapal', $namaKapal)
                    ->where('m.no_voyage', $noVoyage)
                    ->whereNotNull('p.no_surat_jalan');
            })
            ->orWhereIn('nomor_tanda_terima', function($sub) use ($namaKapal, $noVoyage) {
                $sub->select('p.no_surat_jalan')
                    ->from('prospek as p')
                    ->join('manifests as m', 'm.prospek_id', '=', 'p.id')
                    ->where('m.nama_kapal', $namaKapal)
                    ->where('m.no_voyage', $noVoyage)
                    ->whereNotNull('p.no_surat_jalan');
            });
        });

        if (!empty($search)) {
            $tanpaSjQuery->where(function($q) use ($search) {
                $q->where('no_tanda_terima', 'like', "%{$search}%")
                  ->orWhere('nomor_tanda_terima', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%");
            });
        }
        $tandaTerimaTanpaSuratJalans = $tanpaSjQuery->get();

        // 3. Fetch TandaTerimaLcl (LCL) matched by Container Pivot or nomor_tanda_terima in manifests
        $lclQuery = TandaTerimaLcl::with(['items', 'kontainerPivot', 'tujuanPengiriman'])
            ->where(function($q) use ($namaKapal, $noVoyage) {
                // Match via kontainer pivot (nomor_kontainer and nomor_seal) belonging to any manifest in this voyage
                $q->whereHas('kontainerPivot', function($pivotQ) use ($namaKapal, $noVoyage) {
                    $pivotQ->whereIn(DB::raw("CONCAT(nomor_kontainer, '---', COALESCE(nomor_seal, ''))"), function($sub) use ($namaKapal, $noVoyage) {
                        $sub->select(DB::raw("CONCAT(nomor_kontainer, '---', COALESCE(no_seal, ''))"))
                            ->from('manifests')
                            ->where('nama_kapal', $namaKapal)
                            ->where('no_voyage', $noVoyage)
                            ->whereNotNull('nomor_kontainer');
                    });
                })
                // Or match where nomor_tanda_terima matches manifests.nomor_tanda_terima
                ->orWhereIn('nomor_tanda_terima', function($sub) use ($namaKapal, $noVoyage) {
                    $sub->select('nomor_tanda_terima')
                        ->from('manifests')
                        ->where('nama_kapal', $namaKapal)
                        ->where('no_voyage', $noVoyage)
                        ->whereNotNull('nomor_tanda_terima');
                })
                // Or match where nomor_tanda_terima matches the no_surat_jalan of a prospek that has a manifest on this voyage
                ->orWhereIn('nomor_tanda_terima', function($sub) use ($namaKapal, $noVoyage) {
                    $sub->select('p.no_surat_jalan')
                        ->from('prospek as p')
                        ->join('manifests as m', 'm.prospek_id', '=', 'p.id')
                        ->where('m.nama_kapal', $namaKapal)
                        ->where('m.no_voyage', $noVoyage)
                        ->whereNotNull('p.no_surat_jalan');
                });
            });

        if (!empty($search)) {
            $lclQuery->where(function($q) use ($search) {
                $q->where('nomor_tanda_terima', 'like', "%{$search}%")
                  ->orWhere('nama_penerima', 'like', "%{$search}%")
                  ->orWhere('nama_pengirim', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhereHas('items', function($itemQ) use ($search) {
                      $itemQ->where('nama_barang', 'like', "%{$search}%");
                  });
            });
        }
        $tandaTerimaLcls = $lclQuery->get();

        // Calculate statistics
        $stats = [
            'total_fcl' => count($tandaTerimas),
            'total_tanpa_sj' => count($tandaTerimaTanpaSuratJalans),
            'total_lcl' => count($tandaTerimaLcls),
            'total_volume' => $tandaTerimas->sum('meter_kubik') + 
                              $tandaTerimaTanpaSuratJalans->sum('meter_kubik') + 
                              $tandaTerimaLcls->sum(fn($tt) => $tt->items->sum('meter_kubik')),
            'total_weight' => $tandaTerimas->sum('tonase') + 
                              $tandaTerimaTanpaSuratJalans->sum('tonase') + 
                              $tandaTerimaLcls->sum(fn($tt) => $tt->items->sum('tonase')),
        ];

        return view('dokumen-tanda-terima.index', compact(
            'tandaTerimas',
            'tandaTerimaTanpaSuratJalans',
            'tandaTerimaLcls',
            'selectedKapal',
            'noVoyage',
            'stats'
        ));
    }
}
