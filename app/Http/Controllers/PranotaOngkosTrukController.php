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
        $query = PranotaOngkosTruk::with(['supir', 'vendor', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('no_pranota', 'like', "%{$search}%")
                  ->orWhereHas('supir', function($q) use ($search) {
                      $q->where('nama_karyawan', 'like', "%{$search}%");
                  });
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

        $supirs = Karyawan::where('jabatan', 'like', '%Supir%')->get();
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
                'supir_id' => $request->supir_id,
                'vendor_id' => $request->vendor_id,
                'total_nominal' => collect($request->items)->sum('nominal'),
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
            return redirect()->route('pranota-ongkos-truk.show', $pranota->id)->with('success', 'Pranota Ongkos Truk berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan pranota: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $pranota = PranotaOngkosTruk::with(['items', 'supir', 'vendor', 'creator'])->findOrFail($id);
        return view('pranota-ongkos-truk.show', compact('pranota'));
    }

    public function destroy($id)
    {
        $pranota = PranotaOngkosTruk::findOrFail($id);
        $pranota->delete();
        return redirect()->route('pranota-ongkos-truk.index')->with('success', 'Pranota berhasil dihapus.');
    }
}
