<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistSewaKontainer;
use Illuminate\Http\Request;

class MasterPricelistSewaKontainerController extends Controller
{
    public function index()
    {
        $pricelists = MasterPricelistSewaKontainer::orderBy('created_at', 'desc')->paginate(10);
        return view('master-pricelist-sewa-kontainer.index', compact('pricelists'));
    }

    public function create()
    {
        return view('master-pricelist-sewa-kontainer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor' => 'required',
            'tarif' => 'required',
            'ukuran_kontainer' => 'required',
            'harga' => 'required|numeric',
            'tanggal_harga_awal' => 'required|date',
            'tanggal_harga_akhir' => 'nullable|date',
        ]);
        $data = $request->all();
        // Normalize date inputs to date-only strings to avoid sqlite storing timestamps
        try {
            if (!empty($data['tanggal_harga_awal'])) {
                $data['tanggal_harga_awal'] = \Carbon\Carbon::parse($data['tanggal_harga_awal'])->toDateString();
            }
            if (!empty($data['tanggal_harga_akhir'])) {
                $data['tanggal_harga_akhir'] = \Carbon\Carbon::parse($data['tanggal_harga_akhir'])->toDateString();
            }
        } catch (\Exception $e) {
            // leave input as-is if parsing fails
        }
        // Some test DBs (sqlite) may still have the legacy nomor_tagihan column. If present, ensure it exists in data.
        if (\Illuminate\Support\Facades\Schema::hasColumn('master_pricelist_sewa_kontainers', 'nomor_tagihan') && !array_key_exists('nomor_tagihan', $data)) {
            // If the legacy column exists (tests/sqlite), assign a generated unique placeholder to satisfy NOT NULL/unique.
            $data['nomor_tagihan'] = 'PR-' . time() . '-' . rand(1000, 9999);
        }
        MasterPricelistSewaKontainer::create($data);
    return redirect()->route('master.pricelist-sewa-kontainer.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $pricelist = MasterPricelistSewaKontainer::findOrFail($id);
        return view('master-pricelist-sewa-kontainer.edit', compact('pricelist'));
    }

    public function update(Request $request, $id)
    {
        $pricelist = MasterPricelistSewaKontainer::findOrFail($id);
        $request->validate([
            'vendor' => 'required',
            'tarif' => 'required',
            'ukuran_kontainer' => 'required',
            'harga' => 'required|numeric',
            'tanggal_harga_awal' => 'required|date',
            'tanggal_harga_akhir' => 'nullable|date',
        ]);
        $pricelist->update($request->all());
    return redirect()->route('master.pricelist-sewa-kontainer.index')->with('success', 'Data berhasil diupdate');
    }

    public function exportTemplate()
    {
        $filename = 'template_pricelist_sewa_kontainer_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Header row with semicolon delimiter
            fputcsv($file, [
                'vendor',
                'tarif',
                'ukuran_kontainer',
                'harga',
                'tanggal_harga_awal',
                'tanggal_harga_akhir',
                'keterangan'
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $data = array_map(function($line) {
            return str_getcsv($line, ';'); // Use semicolon as delimiter
        }, file($path));
        $header = array_shift($data); // Remove header row

        $successCount = 0;
        $errors = [];

        foreach ($data as $rowIndex => $row) {
            try {
                if (count($row) < 4) { // Minimum required fields
                    $errors[] = "Baris " . ($rowIndex + 2) . ": Data tidak lengkap";
                    continue;
                }

                $pricelistData = [
                    'vendor' => trim($row[0] ?? ''),
                    'tarif' => trim($row[1] ?? ''),
                    'ukuran_kontainer' => trim($row[2] ?? ''),
                    'harga' => trim($row[3] ?? ''),
                    'tanggal_harga_awal' => trim($row[4] ?? ''),
                    'tanggal_harga_akhir' => trim($row[5] ?? ''),
                    'keterangan' => trim($row[6] ?? ''),
                ];

                // Validate required fields
                if (empty($pricelistData['vendor']) || empty($pricelistData['tarif']) ||
                    empty($pricelistData['ukuran_kontainer']) || empty($pricelistData['harga'])) {
                    $errors[] = "Baris " . ($rowIndex + 2) . ": Field wajib (vendor, tarif, ukuran_kontainer, harga) tidak boleh kosong";
                    continue;
                }

                // Validate numeric fields
                if (!is_numeric($pricelistData['harga'])) {
                    $errors[] = "Baris " . ($rowIndex + 2) . ": Harga harus berupa angka";
                    continue;
                }

                // Validate dates
                if (!empty($pricelistData['tanggal_harga_awal'])) {
                    $pricelistData['tanggal_harga_awal'] = date('Y-m-d', strtotime($pricelistData['tanggal_harga_awal']));
                }

                if (!empty($pricelistData['tanggal_harga_akhir'])) {
                    $pricelistData['tanggal_harga_akhir'] = date('Y-m-d', strtotime($pricelistData['tanggal_harga_akhir']));
                }

                MasterPricelistSewaKontainer::create($pricelistData);
                $successCount++;

            } catch (\Exception $e) {
                $errors[] = "Baris " . ($rowIndex + 2) . ": " . $e->getMessage();
            }
        }

        $message = "Import selesai. {$successCount} data berhasil diimpor.";
        if (!empty($errors)) {
            $message .= " Error: " . implode('; ', array_slice($errors, 0, 5)); // Show first 5 errors
            if (count($errors) > 5) {
                $message .= " (dan " . (count($errors) - 5) . " error lainnya)";
            }
        }

        return redirect()->route('master.pricelist-sewa-kontainer.index')->with('success', $message);
    }
}
