<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SuratJalanTarikKosongBatam;
use App\Models\Karyawan;
use App\Models\Mobil;
use Carbon\Carbon;

class SuratJalanTarikKosongBatamController extends Controller
{
    public function index(Request $request)
    {
        $query = SuratJalanTarikKosongBatam::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_surat_jalan', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_surat_jalan', '<=', $request->end_date);
        }

        $items = $query->orderBy('tanggal_surat_jalan', 'desc')
                       ->orderBy('created_at', 'desc')
                       ->paginate(15);

        return view('surat-jalan-tarik-kosong-batam.index', compact('items'));
    }

    public function create()
    {
        $supirs = Karyawan::where('status', 'active')->where('divisi', 'SUPIR')->orderBy('nama_lengkap')->get();
        $keneks = Karyawan::where('status', 'active')->where('divisi', 'KENEK')->orderBy('nama_lengkap')->get();
        $mobils = Mobil::orderBy('nomor_polisi')->get();
                // Get kontainer data dari 2 table: stock_kontainers dan kontainers
        $stockKontainersRaw = \App\Models\StockKontainer::whereIn('status', ['available', 'tersedia'])
                                                     ->orderBy('nomor_seri_gabungan')
                                                     ->get(['id', 'nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'status']);
        
        $kontainersRaw = \App\Models\Kontainer::where('status', 'tersedia')
                                          ->orderBy('nomor_seri_gabungan')
                                          ->get(['id', 'nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'status']);
        
        $allKontainers = collect();
        foreach ($stockKontainersRaw as $stock) {
            $allKontainers->push((object)[
                'id' => $stock->nomor_seri_gabungan,
                'nomor_seri_gabungan' => $stock->nomor_seri_gabungan,
                'ukuran' => $stock->ukuran,
                'tipe_kontainer' => $stock->tipe_kontainer,
                'source' => 'stock_kontainers'
            ]);
        }
        foreach ($kontainersRaw as $kontainer) {
            $allKontainers->push((object)[
                'id' => $kontainer->nomor_seri_gabungan,
                'nomor_seri_gabungan' => $kontainer->nomor_seri_gabungan,
                'ukuran' => $kontainer->ukuran,
                'tipe_kontainer' => $kontainer->tipe_kontainer,
                'source' => 'kontainers'
            ]);
        }
        $kontainers = $allKontainers->sortBy('nomor_seri_gabungan');
        
        $pricelistRings = \App\Models\PricelistUangJalanBatam::orderBy('ring')
            ->get(['ring', 'expedisi', 'tarif_20ft_full', 'tarif_20ft_empty', 'tarif_40ft_full', 'tarif_40ft_empty'])
            ->map(function($item) {
                return [
                    'name' => "Ring {$item->ring} {$item->expedisi}",
                    'rates' => [
                        '20_F' => $item->tarif_20ft_full,
                        '20_E' => $item->tarif_20ft_empty,
                        '40_F' => $item->tarif_40ft_full,
                        '40_E' => $item->tarif_40ft_empty,
                        '45_F' => $item->tarif_40ft_full,
                        '45_E' => $item->tarif_40ft_empty,
                    ]
                ];
            })
            ->unique('name')
            ->values();

        $locations = $pricelistRings->pluck('name');
            
        $warehouses = \App\Models\Gudang::orderBy('nama_gudang')->pluck('nama_gudang');

        return view('surat-jalan-tarik-kosong-batam.create', compact('supirs', 'keneks', 'mobils', 'kontainers', 'locations', 'warehouses', 'pricelistRings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_surat_jalan' => 'required|unique:surat_jalan_tarik_kosong_batams,no_surat_jalan',
            'tanggal_surat_jalan' => 'required|date',

            'tujuan_pengambilan' => 'nullable|string',
            'tujuan_pengiriman' => 'nullable|string',
            'supir' => 'nullable|string',
            'supir2' => 'nullable|string',
            'no_plat' => 'nullable|string',
            'kenek' => 'nullable|string',
            'no_kontainer' => 'nullable|string',
            'size' => 'nullable|string',
            'f_e' => 'nullable|string',
            'uang_jalan' => 'nullable|string',
            'status' => 'required|in:draft,active,completed,cancelled',
            'catatan' => 'nullable|string',
        ]);

        if ($request->filled('uang_jalan')) {
            $validated['uang_jalan'] = (float) str_replace(['.', ','], ['', '.'], $request->uang_jalan);
        }

        $validated['input_by'] = Auth::id();
        $validated['input_date'] = now();
        $validated['lokasi'] = 'batam';

        SuratJalanTarikKosongBatam::create($validated);

        return redirect()->route('surat-jalan-tarik-kosong-batam.index')->with('success', 'Surat Jalan Tarik Kosong Batam berhasil disimpan');
    }

    public function show($id)
    {
        $item = SuratJalanTarikKosongBatam::findOrFail($id);
        return view('surat-jalan-tarik-kosong-batam.show', compact('item'));
    }

    public function edit($id)
    {
        $item = SuratJalanTarikKosongBatam::findOrFail($id);
        $supirs = Karyawan::where('status', 'active')->where('divisi', 'SUPIR')->orderBy('nama_lengkap')->get();
        $keneks = Karyawan::where('status', 'active')->where('divisi', 'KENEK')->orderBy('nama_lengkap')->get();
        $mobils = Mobil::orderBy('nomor_polisi')->get();

        // Get kontainer data
        $stockKontainersRaw = \App\Models\StockKontainer::whereIn('status', ['available', 'tersedia'])
                                                     ->orderBy('nomor_seri_gabungan')
                                                     ->get(['id', 'nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'status']);
        
        $kontainersRaw = \App\Models\Kontainer::where('status', 'tersedia')
                                          ->orderBy('nomor_seri_gabungan')
                                          ->get(['id', 'nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'status']);
        
        $allKontainers = collect();
        foreach ($stockKontainersRaw as $stock) {
            $allKontainers->push((object)[
                'id' => $stock->nomor_seri_gabungan,
                'nomor_seri_gabungan' => $stock->nomor_seri_gabungan,
                'ukuran' => $stock->ukuran,
                'tipe_kontainer' => $stock->tipe_kontainer,
                'source' => 'stock_kontainers'
            ]);
        }
        foreach ($kontainersRaw as $kontainer) {
            $allKontainers->push((object)[
                'id' => $kontainer->nomor_seri_gabungan,
                'nomor_seri_gabungan' => $kontainer->nomor_seri_gabungan,
                'ukuran' => $kontainer->ukuran,
                'tipe_kontainer' => $kontainer->tipe_kontainer,
                'source' => 'kontainers'
            ]);
        }
        $kontainers = $allKontainers->sortBy('nomor_seri_gabungan');
        
        $pricelistRings = \App\Models\PricelistUangJalanBatam::orderBy('ring')
            ->get(['ring', 'expedisi', 'tarif_20ft_full', 'tarif_20ft_empty', 'tarif_40ft_full', 'tarif_40ft_empty'])
            ->map(function($item) {
                return [
                    'name' => "Ring {$item->ring} {$item->expedisi}",
                    'rates' => [
                        '20_F' => $item->tarif_20ft_full,
                        '20_E' => $item->tarif_20ft_empty,
                        '40_F' => $item->tarif_40ft_full,
                        '40_E' => $item->tarif_40ft_empty,
                        '45_F' => $item->tarif_40ft_full,
                        '45_E' => $item->tarif_40ft_empty,
                    ]
                ];
            })
            ->unique('name')
            ->values();

        $locations = $pricelistRings->pluck('name');

        $warehouses = \App\Models\Gudang::orderBy('nama_gudang')->pluck('nama_gudang');

        return view('surat-jalan-tarik-kosong-batam.edit', compact('item', 'supirs', 'keneks', 'mobils', 'kontainers', 'locations', 'warehouses', 'pricelistRings'));
    }

    public function update(Request $request, $id)
    {
        $item = SuratJalanTarikKosongBatam::findOrFail($id);

        $validated = $request->validate([
            'no_surat_jalan' => 'required|unique:surat_jalan_tarik_kosong_batams,no_surat_jalan,' . $id,
            'tanggal_surat_jalan' => 'required|date',

            'tujuan_pengambilan' => 'nullable|string',
            'tujuan_pengiriman' => 'nullable|string',
            'supir' => 'nullable|string',
            'supir2' => 'nullable|string',
            'no_plat' => 'nullable|string',
            'kenek' => 'nullable|string',
            'no_kontainer' => 'nullable|string',
            'size' => 'nullable|string',
            'f_e' => 'nullable|string',
            'uang_jalan' => 'nullable|string',
            'status' => 'required|in:draft,active,completed,cancelled',
            'catatan' => 'nullable|string',
        ]);

        if ($request->filled('uang_jalan')) {
            $validated['uang_jalan'] = (float) str_replace(['.', ','], ['', '.'], $request->uang_jalan);
        }

        $item->update($validated);

        return redirect()->route('surat-jalan-tarik-kosong-batam.index')->with('success', 'Surat Jalan Tarik Kosong Batam berhasil diperbarui');
    }

    public function destroy($id)
    {
        $item = SuratJalanTarikKosongBatam::findOrFail($id);
        $item->delete();

        return redirect()->route('surat-jalan-tarik-kosong-batam.index')->with('success', 'Surat Jalan Tarik Kosong Batam berhasil dihapus');
    }

    public function generateNumber(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : now();
        $year = $date->format('Y');
        $month = $date->format('m');
        
        $lastSj = SuratJalanTarikKosongBatam::whereYear('tanggal_surat_jalan', $year)
                                            ->whereMonth('tanggal_surat_jalan', $month)
                                            ->orderBy('no_surat_jalan', 'desc')
                                            ->first();

        $nextNumber = 1;
        if ($lastSj) {
            $parts = explode('/', $lastSj->no_surat_jalan);
            $lastNum = (int) end($parts);
            $nextNumber = $lastNum + 1;
        }

        $formattedNumber = "SJTK/{$year}/{$month}/" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return response()->json(['number' => $formattedNumber]);
    }

    public function print($id)
    {
        $item = SuratJalanTarikKosongBatam::findOrFail($id);
        return view('surat-jalan-tarik-kosong-batam.print', compact('item'));
    }
}
