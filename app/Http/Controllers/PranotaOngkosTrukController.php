<?php

namespace App\Http\Controllers;

use App\Models\PranotaOngkosTruk;
use App\Models\PranotaOngkosTrukItem;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use App\Models\NomorTerakhir;
use App\Models\Karyawan;
use App\Models\VendorSupir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PranotaOngkosTrukController extends Controller
{
    public function index(Request $request)
    {
        $query = PranotaOngkosTruk::with(['creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('no_pranota', 'like', "%{$search}%");
        }

        $pranotas = $query->latest()->paginate(10);

        return view('pranota-ongkos-truk.index', compact('pranotas'));
    }

    public function create(Request $request)
    {
        $selectedIds = $request->filled('selected_ids') ? explode(',', $request->selected_ids) : [];
        $sjs = $request->filled('sjs') ? explode(',', $request->sjs) : [];
        $types = $request->filled('types') ? explode(',', $request->types) : [];

        $items = collect();

        foreach ($selectedIds as $index => $id) {
            $type = $types[$index] ?? '';
            $no_sj = $sjs[$index] ?? '';

            $nominal = 0;
            $tanggal = null;
            $supir = null;
            $vendor = null;

            if ($type === 'SuratJalan') {
                $sj = SuratJalan::find($id);
                if ($sj) {
                    $nominal = $this->calculateOngkosTruk($sj);
                    $tanggal = $sj->tanggal_surat_jalan;
                    $supir = $sj->supir;
                    // Add more data if needed
                }
            } elseif ($type === 'SuratJalanBongkaran') {
                $sjb = SuratJalanBongkaran::find($id);
                if ($sjb) {
                    $nominal = $this->calculateOngkosTruk($sjb);
                    $tanggal = $sjb->tanggal_surat_jalan;
                    $supir = $sjb->supir;
                }
            }

            if ($id) {
                $items->push([
                    'id' => $id,
                    'no_surat_jalan' => $no_sj,
                    'tanggal' => $tanggal,
                    'nominal' => $nominal,
                    'type' => $type,
                    'supir' => $supir,
                ]);
            }
        }

        $supirs = Karyawan::where('pekerjaan', 'like', '%Supir%')->get();
        $vendors = VendorSupir::all();

        return view('pranota-ongkos-truk.create', compact('items', 'supirs', 'vendors'));
    }

    private function calculateOngkosTruk($item)
    {
        // Simple implementation mirroring ReportOngkosTrukController
        $ongkosTruk = 0;
        if ($item->tujuanPengambilanRelation) {
            $size = strtolower($item->size ?? '');
            if (str_contains($size, '40')) {
                $ongkosTruk = $item->tujuanPengambilanRelation->ongkos_truk_40ft ?? 0;
            } else {
                $ongkosTruk = $item->tujuanPengambilanRelation->ongkos_truk_20ft ?? 0;
            }
        }
        if ($item->tujuan_pengambilan == "PULO GADUNG ( BESI SCRAP )") {
            $ongkosTruk = 1050000;
        }
        return $ongkosTruk;
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pranota' => 'required|date',
            'items' => 'required|array',
            'items.*.nominal' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            // Generate nomor pranota
            $nomorTerakhir = NomorTerakhir::where('modul', 'POT')->lockForUpdate()->first();
            if (!$nomorTerakhir) {
                $nomorTerakhir = NomorTerakhir::create(['modul' => 'POT', 'nomor_terakhir' => 0]);
            }
            $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
            $tahun = now()->format('y');
            $bulan = now()->format('m');
            $no_pranota = "POT{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            
            $nomorTerakhir->nomor_terakhir = $nextNumber;
            $nomorTerakhir->save();

            $pranota = PranotaOngkosTruk::create([
                'no_pranota' => $no_pranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'adjustment' => $request->adjustment ?? 0,
                'total_nominal' => collect($request->items)->sum('nominal') + ($request->adjustment ?? 0),
                'keterangan' => $request->keterangan,
                'status' => 'submitted',
                'created_by' => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                PranotaOngkosTrukItem::create([
                    'pranota_ongkos_truk_id' => $pranota->id,
                    'surat_jalan_id' => $item['type'] === 'SuratJalan' ? $item['id'] : null,
                    'surat_jalan_bongkaran_id' => $item['type'] === 'SuratJalanBongkaran' ? $item['id'] : null,
                    'no_surat_jalan' => $item['no_surat_jalan'],
                    'tanggal' => $item['tanggal'],
                    'nominal' => $item['nominal'],
                    'type' => $item['type'],
                ]);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pranota Ongkos Truk berhasil disimpan.',
                    'redirect_url' => route('pranota-ongkos-truk.show', $pranota->id)
                ]);
            }

            return redirect()->route('pranota-ongkos-truk.show', $pranota->id)->with('success', 'Pranota Ongkos Truk berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan pranota: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Gagal menyimpan pranota: ' . $e->getMessage());
        }
    }

    public function generateNomorPranota()
    {
        try {
            $nomorTerakhir = NomorTerakhir::where('modul', 'POT')->first();
            $nextNumber = ($nomorTerakhir ? $nomorTerakhir->nomor_terakhir : 0) + 1;
            $tahun = now()->format('y');
            $bulan = now()->format('m');
            $no_pranota = "POT{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            return response()->json([
                'success' => true,
                'nomor_pranota' => $no_pranota
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getPreviewData(Request $request)
    {
        $selectedIds = $request->filled('selected_ids') ? explode(',', $request->selected_ids) : [];
        $types = $request->filled('types') ? explode(',', $request->types) : [];

        $items = collect();
        foreach ($selectedIds as $index => $id) {
            $type = $types[$index] ?? '';
            if ($type === 'SuratJalan') {
                $sj = SuratJalan::with(['supirKaryawan', 'tujuanPengambilanRelation', 'uangJalan'])->find($id);
                if ($sj) {
                    $ongkosTruk = $this->calculateOngkosTruk($sj);
                    $uangJalanNominal = $sj->uangJalan ? $sj->uangJalan->jumlah_total : 0;
                    $nominalBersih = $ongkosTruk - $uangJalanNominal;

                    $items->push([
                        'id' => $id,
                        'no_surat_jalan' => $sj->no_surat_jalan,
                        'tanggal' => $sj->tanggal_surat_jalan ? $sj->tanggal_surat_jalan->format('d/M/Y') : '-',
                        'nominal' => $nominalBersih,
                        'type' => $type,
                        'supir' => $sj->supirKaryawan ? ($sj->supirKaryawan->nama_panggilan ?? $sj->supirKaryawan->nama_lengkap) : ($sj->supir ?: '-'),
                        'no_plat' => $sj->no_plat ?: '-'
                    ]);
                }
            } elseif ($type === 'SuratJalanBongkaran') {
                $sjb = SuratJalanBongkaran::with(['supirKaryawan', 'tujuanPengambilanRelation', 'uangJalan'])->find($id);
                if ($sjb) {
                    $ongkosTruk = $this->calculateOngkosTruk($sjb);
                    $uangJalanNominal = $sjb->uangJalan ? $sjb->uangJalan->jumlah_total : 0;
                    $nominalBersih = $ongkosTruk - $uangJalanNominal;

                    $items->push([
                        'id' => $id,
                        'no_surat_jalan' => $sjb->nomor_surat_jalan,
                        'tanggal' => $sjb->tanggal_surat_jalan ? $sjb->tanggal_surat_jalan->format('d/M/Y') : '-',
                        'nominal' => $nominalBersih,
                        'type' => $type,
                        'supir' => $sjb->supirKaryawan ? ($sjb->supirKaryawan->nama_panggilan ?? $sjb->supirKaryawan->nama_lengkap) : ($sjb->supir ?: '-'),
                        'no_plat' => $sjb->no_plat ?: '-'
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'items' => $items
        ]);
    }

    public function show($id)
    {
        $pranota = PranotaOngkosTruk::with(['items', 'creator'])->findOrFail($id);
        return view('pranota-ongkos-truk.show', compact('pranota'));
    }

    public function destroy($id)
    {
        $pranota = PranotaOngkosTruk::findOrFail($id);
        $pranota->delete();
        return redirect()->route('pranota-ongkos-truk.index')->with('success', 'Pranota berhasil dihapus.');
    }
}
