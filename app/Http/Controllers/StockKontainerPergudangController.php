<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gudang;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StockKontainerPergudangController extends Controller
{
    public function index()
    {
        $gudangs = Gudang::where('status', 'aktif')->get();
        if ($gudangs->isEmpty()) {
            $gudangs = Gudang::all(); // Fallback if status is different
        }

        // Count for Kontainer (Sewa)
        $kontainerCounts = Kontainer::select('gudangs_id', DB::raw('count(*) as total'))
            ->groupBy('gudangs_id')
            ->pluck('total', 'gudangs_id');

        // Count for Stock Kontainer
        $stockCounts = StockKontainer::select('gudangs_id', DB::raw('count(*) as total'))
            ->groupBy('gudangs_id')
            ->pluck('total', 'gudangs_id');

        // Map data for display
        $data = $gudangs->map(function($gudang) use ($kontainerCounts, $stockCounts) {
            $sewaCount = $kontainerCounts[$gudang->id] ?? 0;
            $stockCount = $stockCounts[$gudang->id] ?? 0;
            return [
                'id' => $gudang->id,
                'nama_gudang' => $gudang->nama_gudang,
                'lokasi' => $gudang->lokasi,
                'total_sewa' => $sewaCount,
                'total_stock' => $stockCount,
                'total_gabungan' => $sewaCount + $stockCount
            ];
        });

        // Add a "Tanpa Gudang" record for items without a warehouse
        $unassignedSewa = Kontainer::whereNull('gudangs_id')->count();
        $unassignedStock = StockKontainer::whereNull('gudangs_id')->count();
        
        if ($unassignedSewa > 0 || $unassignedStock > 0) {
            $data->push([
                'id' => null,
                'nama_gudang' => 'Tanpa Gudang / Belum Ditentukan',
                'lokasi' => '-',
                'total_sewa' => $unassignedSewa,
                'total_stock' => $unassignedStock,
                'total_gabungan' => $unassignedSewa + $unassignedStock
            ]);
        }

        return view('master-kontainer.stock-pergudang', compact('data'));
    }

    public function show($id)
    {
        $gudang = null;
        if ($id !== 'none' && $id != '') {
            $gudang = Gudang::find($id);
            if (!$gudang) {
                return redirect()->route('master.kontainer.stock-pergudang')->with('error', 'Gudang tidak ditemukan.');
            }
        }

        $namaGudang = $gudang ? $gudang->nama_gudang : 'Tanpa Gudang / Belum Ditentukan';

        // 1. Ambil dari Kontainer (Sewa)
        $querySewa = \App\Models\Kontainer::where('status', '!=', 'inactive');
        if ($id === 'none' || $id == '') {
            $querySewa->whereNull('gudangs_id');
        } else {
            $querySewa->where('gudangs_id', $id);
        }
        $sewas = $querySewa->select('nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'status')->get()->map(function($i) {
            $i->tipe_sumber = 'Sewa';
            return $i;
        });

        // 2. Ambil dari StockKontainer (Milik Sendiri)
        $queryStock = \App\Models\StockKontainer::where('status', '!=', 'inactive');
        if ($id === 'none' || $id == '') {
            $queryStock->whereNull('gudangs_id');
        } else {
            $queryStock->where('gudangs_id', $id);
        }
        $stocks = $queryStock->select('nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'status')->get()->map(function($i) {
            $i->tipe_sumber = 'Stock';
            return $i;
        });

        // Merge lists together
        $allContainers = $sewas->concat($stocks);

        return view('master-kontainer.stock-pergudang-detail', compact('allContainers', 'namaGudang', 'id'));
    }

    public function exportDetail($id)
    {
        $gudang = null;
        if ($id !== 'none' && $id != '') {
            $gudang = Gudang::find($id);
        }
        
        $namaGudangStr = $gudang ? $gudang->nama_gudang : 'Tanpa Gudang';
        $fileName = 'Daftar_Kontainer_' . str_replace([' ', '/', '\\'], '_', $namaGudangStr) . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new \App\Exports\GudangKontainerExport($id), $fileName);
    }

    public function downloadTemplate()
    {
        $headers = ['Nomor Kontainer'];
        
        // Use a simple CSV output for the template
        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            // Add some example rows
            fputcsv($file, ['AYPU1234567']);
            fputcsv($file, ['AYPU7654321']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=template_sync_kontainer.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    public function storeUpload(Request $request, $id)
    {
        $request->validate([
            'container_numbers' => 'nullable|string',
            'file_excel' => 'nullable|file',
        ]);

        $gudangId = $id === 'none' || $id == '' ? null : $id;
        $inputNumbers = [];

        // 1. Process Excel File if uploaded
        if ($request->hasFile('file_excel')) {
            try {
                $data = Excel::toArray([], $request->file('file_excel'));
                if (!empty($data) && count($data) > 0) {
                    foreach ($data[0] as $row) {
                        $val = trim($row[0] ?? '');
                        if ($val && strtolower($val) !== 'nomor kontainer' && strtolower($val) !== 'no kontainer') {
                            $inputNumbers[] = $val;
                        }
                    }
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal membaca file Excel/CSV. Pastikan format file benar. Error: ' . $e->getMessage());
            }
        }

        // 2. Process Text Area Input
        if ($request->container_numbers) {
            $textNumbers = preg_split('/[\n,\r\s]+/', $request->container_numbers);
            foreach ($textNumbers as $n) {
                $trimmed = trim($n);
                if ($trimmed) $inputNumbers[] = $trimmed;
            }
        }

        $inputNumbers = array_unique($inputNumbers);

        if (empty($inputNumbers)) {
            return redirect()->back()->with('error', 'Daftar kontainer kosong. Silakan isi text area atau upload file Excel.');
        }

        // 1. Identify "excess" containers (In DB for this warehouse, but NOT in uploaded list)
        // We do this by finding all containers currently in this warehouse and checking if they are in the input
        
        // Sewa Containers
        $sewasInWarehouse = \App\Models\Kontainer::where('gudangs_id', $gudangId)->get();
        foreach ($sewasInWarehouse as $sewa) {
            if (!in_array($sewa->nomor_seri_gabungan, $inputNumbers)) {
                $sewa->update(['gudangs_id' => null]);
            }
        }

        // Stock Containers
        $stocksInWarehouse = \App\Models\StockKontainer::where('gudangs_id', $gudangId)->get();
        foreach ($stocksInWarehouse as $stock) {
            if (!in_array($stock->nomor_seri_gabungan, $inputNumbers)) {
                $stock->update(['gudangs_id' => null]);
            }
        }

        // 2. Add/Move containers from the list to this warehouse (Optional, but usually expected for a sync)
        // If the user only wants to DELETE excess, step 1 is enough. 
        // But usually "upload to warehouse" means ensuring these ARE the containers in the warehouse.
        if ($gudangId !== null) {
            foreach ($inputNumbers as $number) {
                // Try Sewa first
                $sewa = \App\Models\Kontainer::where('nomor_seri_gabungan', 'like', "%$number%")->first();
                if ($sewa) {
                    $sewa->update(['gudangs_id' => $gudangId]);
                    continue;
                }
                
                // Then Stock
                $stock = \App\Models\StockKontainer::where('nomor_seri_gabungan', 'like', "%$number%")->first();
                if ($stock) {
                    $stock->update(['gudangs_id' => $gudangId]);
                }
            }
        }

        return redirect()->back()->with('success', 'Sinkronisasi kontainer gudang berhasil. Kontainer yang tidak ada dalam daftar telah dihapus lokasinya dari gudang ini.');
    }
}
