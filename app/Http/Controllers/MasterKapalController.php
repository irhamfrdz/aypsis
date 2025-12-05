<?php

namespace App\Http\Controllers;

use App\Models\MasterKapal;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MasterKapalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterKapal::query();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('kode_kapal', 'like', "%{$search}%")
                  ->orWhere('nama_kapal', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%")
                  ->orWhere('pelayaran', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by pemilik/pelayaran
        if ($request->has('pemilik') && $request->pemilik != '') {
            $query->where('pelayaran', $request->pemilik);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Rows per page
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $kapals = $query->paginate($perPage)->withQueryString();

        // Get distinct pemilik list for filter dropdown
        $pemilikList = MasterKapal::whereNotNull('pelayaran')
            ->where('pelayaran', '!=', '')
            ->distinct()
            ->orderBy('pelayaran')
            ->pluck('pelayaran');

        return view('master-kapal.index', compact('kapals', 'pemilikList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-kapal.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:master_kapals,kode',
            'kode_kapal' => 'nullable|string|max:100',
            'nama_kapal' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'pelayaran' => 'nullable|string|max:255',
            'kapasitas_kontainer_palka' => 'nullable|integer|min:0',
            'kapasitas_kontainer_deck' => 'nullable|integer|min:0',
            'gross_tonnage' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        try {
            MasterKapal::create($validated);

            return redirect()
                ->route('master-kapal.index')
                ->with('success', 'Data kapal berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data kapal: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterKapal $masterKapal)
    {
        return view('master-kapal.show', compact('masterKapal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterKapal $masterKapal)
    {
        return view('master-kapal.edit', compact('masterKapal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterKapal $masterKapal)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:master_kapals,kode,' . $masterKapal->id,
            'kode_kapal' => 'nullable|string|max:100',
            'nama_kapal' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'pelayaran' => 'nullable|string|max:255',
            'kapasitas_kontainer_palka' => 'nullable|integer|min:0',
            'kapasitas_kontainer_deck' => 'nullable|integer|min:0',
            'gross_tonnage' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        try {
            $masterKapal->update($validated);

            return redirect()
                ->route('master-kapal.index')
                ->with('success', 'Data kapal berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data kapal: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterKapal $masterKapal)
    {
        try {
            $masterKapal->delete();

            return redirect()
                ->route('master-kapal.index')
                ->with('success', 'Data kapal berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus data kapal: ' . $e->getMessage());
        }
    }

    /**
     * Download CSV template for import
     */
    public function downloadTemplate()
    {
        $filename = 'template_master_kapal.csv';

        // Header CSV dengan delimiter titik koma
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header with capacity fields included
            fputcsv($file, ['kode', 'kode_kapal', 'nama_kapal', 'nickname', 'pelayaran', 'kapasitas_kontainer_palka', 'kapasitas_kontainer_deck', 'gross_tonnage', 'catatan', 'status'], ';');

            // Example data rows with capacity examples
            fputcsv($file, ['K001', 'KP-001', 'MV SEJAHTERA', 'SEJAHTERA', 'PT Pelayaran Indonesia', '120', '80', '2500.50', 'Kapal kontainer 20 feet', 'aktif'], ';');
            fputcsv($file, ['K002', 'KP-002', 'MV NUSANTARA', 'NUSA', 'PT Samudera Lines', '150', '100', '3200.75', 'Kapal cargo besar', 'aktif'], ';');
            fputcsv($file, ['K003', 'KP-003', 'MV BAHARI', '', 'PT Pelni', '', '', '1800.00', 'Kapal penumpang', 'nonaktif'], ';');
            fputcsv($file, ['K004', '', 'MV SRIKANDI', 'KANDI', 'PT Berlian Shipping', '90', '60', '', '', 'aktif'], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('master-kapal.import');
    }

    /**
     * Process CSV import
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
        ]);

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();

            // Open and read CSV
            $csvData = array_map(function($line) {
                return str_getcsv($line, ';');
            }, file($path));

            // If semicolon delimiter doesn't work, try comma
            if (count($csvData[0]) === 1) {
                $csvData = array_map(function($line) {
                    return str_getcsv($line, ',');
                }, file($path));
            }

            // Remove header
            $header = array_shift($csvData);

            // Remove UTF-8 BOM if present and trim whitespace
            $header = array_map(function($value) {
                // Remove BOM (﻿) from UTF-8 encoded files
                $value = str_replace("\xEF\xBB\xBF", '', $value);
                return trim($value, '"');
            }, $header);

            // Validate header - Support both import template and export format
            $expectedImportHeader = ['kode', 'kode_kapal', 'nama_kapal', 'nickname', 'pelayaran', 'kapasitas_kontainer_palka', 'kapasitas_kontainer_deck', 'gross_tonnage', 'catatan', 'status'];
            $expectedImportHeaderOld = ['kode', 'kode_kapal', 'nama_kapal', 'nickname', 'pelayaran', 'catatan', 'status']; // Legacy format
            $expectedExportHeader = ['No', 'Kode', 'Kode Kapal', 'Nama Kapal', 'Nickname', 'Pelayaran (Pemilik)', 'Kapasitas Palka', 'Kapasitas Deck', 'Gross Tonnage', 'Total Kapasitas', 'Catatan', 'Status', 'Tanggal Dibuat', 'Tanggal Diperbarui'];
            
            $isImportFormat = ($header === $expectedImportHeader);
            $isImportFormatOld = ($header === $expectedImportHeaderOld);
            $isExportFormat = ($header === $expectedExportHeader);
            
            if (!$isImportFormat && !$isImportFormatOld && !$isExportFormat) {
                return redirect()
                    ->back()
                    ->with('error', 'Format header CSV tidak sesuai. 
                    Format Import Baru: ' . implode(';', $expectedImportHeader) . ' 
                    Format Import Lama: ' . implode(';', $expectedImportHeaderOld) . ' 
                    Format Export: ' . implode(',', $expectedExportHeader) . ' 
                    | Got: ' . implode(',', $header));
            }

            $imported = 0;
            $updated = 0;
            $errors = [];
            $skipped = 0;

            DB::beginTransaction();

            foreach ($csvData as $index => $row) {
                $rowNumber = $index + 2; // +2 karena header di row 1 dan index mulai dari 0

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Parse data based on format
                if ($isImportFormat) {
                    // New Import template format: kode;kode_kapal;nama_kapal;nickname;pelayaran;kapasitas_kontainer_palka;kapasitas_kontainer_deck;gross_tonnage;catatan;status
                    $kode = trim($row[0]);
                    $kode_kapal = !empty(trim($row[1])) ? trim($row[1]) : null;
                    $nama_kapal = trim($row[2]);
                    $nickname = !empty(trim($row[3])) ? trim($row[3]) : null;
                    $pelayaran = !empty(trim($row[4])) ? trim($row[4]) : null;
                    $kapasitas_palka = isset($row[5]) && !empty(trim($row[5])) ? (float)trim($row[5]) : null;
                    $kapasitas_deck = isset($row[6]) && !empty(trim($row[6])) ? (float)trim($row[6]) : null;
                    $gross_tonnage = isset($row[7]) && !empty(trim($row[7])) ? (float)trim($row[7]) : null;
                    $catatan = !empty(trim($row[8])) ? trim($row[8]) : null;
                    $status = trim($row[9]);
                } elseif ($isImportFormatOld) {
                    // Old Import template format: kode;kode_kapal;nama_kapal;nickname;pelayaran;catatan;status
                    $kode = trim($row[0]);
                    $kode_kapal = !empty(trim($row[1])) ? trim($row[1]) : null;
                    $nama_kapal = trim($row[2]);
                    $nickname = !empty(trim($row[3])) ? trim($row[3]) : null;
                    $pelayaran = !empty(trim($row[4])) ? trim($row[4]) : null;
                    $catatan = !empty(trim($row[5])) ? trim($row[5]) : null;
                    $status = trim($row[6]);
                    // Old template doesn't include capacity fields
                    $kapasitas_palka = null;
                    $kapasitas_deck = null;
                    $gross_tonnage = null;
                } else {
                    // Export format: No,Kode,Kode Kapal,Nama Kapal,Nickname,Pelayaran (Pemilik),Kapasitas Palka,Kapasitas Deck,Gross Tonnage,Total Kapasitas,Catatan,Status,Tanggal Dibuat,Tanggal Diperbarui
                    // Make sure we have enough columns
                    if (count($row) < 12) {
                        $errors[] = "Baris {$rowNumber}: Format export tidak lengkap, minimal 12 kolom diperlukan";
                        $skipped++;
                        continue;
                    }
                    
                    $kode = trim($row[1]); // Column B: Kode
                    $kode_kapal = !empty(trim($row[2])) ? trim($row[2]) : null; // Column C: Kode Kapal
                    $nama_kapal = trim($row[3]); // Column D: Nama Kapal
                    $nickname = !empty(trim($row[4])) ? trim($row[4]) : null; // Column E: Nickname
                    $pelayaran = !empty(trim($row[5])) ? trim($row[5]) : null; // Column F: Pelayaran (Pemilik)
                    $kapasitas_palka = isset($row[6]) && !empty(trim($row[6])) ? (float)trim($row[6]) : null; // Column G: Kapasitas Palka
                    $kapasitas_deck = isset($row[7]) && !empty(trim($row[7])) ? (float)trim($row[7]) : null; // Column H: Kapasitas Deck
                    $gross_tonnage = isset($row[8]) && !empty(trim($row[8])) ? (float)trim($row[8]) : null; // Column I: Gross Tonnage
                    $catatan = isset($row[10]) && !empty(trim($row[10])) ? trim($row[10]) : null; // Column K: Catatan
                    $status = trim($row[11]); // Column L: Status
                }

                // Skip if kode is empty
                if (empty($kode)) {
                    $errors[] = "Baris {$rowNumber}: Kode tidak boleh kosong";
                    $skipped++;
                    continue;
                }

                // Validate required fields
                if (empty($nama_kapal)) {
                    $errors[] = "Baris {$rowNumber}: Nama kapal tidak boleh kosong";
                    $skipped++;
                    continue;
                }

                // Validate status - accept both Indonesian and English
                if (!in_array(strtolower($status), ['aktif', 'nonaktif', 'active', 'inactive'])) {
                    $errors[] = "Baris {$rowNumber}: Status harus 'aktif'/'active' atau 'nonaktif'/'inactive'";
                    $skipped++;
                    continue;
                }

                // Normalize status
                $normalizedStatus = in_array(strtolower($status), ['aktif', 'active']) ? 'aktif' : 'nonaktif';

                // Check if exists
                $existing = MasterKapal::where('kode', $kode)->first();

                if ($existing) {
                    // Update existing - only update capacity fields if they have values (not null)
                    $updateData = [
                        'kode_kapal' => $kode_kapal,
                        'nama_kapal' => $nama_kapal,
                        'nickname' => $nickname,
                        'pelayaran' => $pelayaran,
                        'catatan' => $catatan,
                        'status' => $normalizedStatus,
                    ];
                    
                    // Only update capacity fields if they have values (to preserve existing data)
                    if ($kapasitas_palka !== null) {
                        $updateData['kapasitas_kontainer_palka'] = $kapasitas_palka;
                    }
                    if ($kapasitas_deck !== null) {
                        $updateData['kapasitas_kontainer_deck'] = $kapasitas_deck;
                    }
                    if ($gross_tonnage !== null) {
                        $updateData['gross_tonnage'] = $gross_tonnage;
                    }
                    
                    $existing->update($updateData);
                    $updated++;
                } else {
                    // Create new
                    MasterKapal::create([
                        'kode' => $kode,
                        'kode_kapal' => $kode_kapal,
                        'nama_kapal' => $nama_kapal,
                        'nickname' => $nickname,
                        'pelayaran' => $pelayaran,
                        'kapasitas_kontainer_palka' => $kapasitas_palka,
                        'kapasitas_kontainer_deck' => $kapasitas_deck,
                        'gross_tonnage' => $gross_tonnage,
                        'catatan' => $catatan,
                        'status' => $normalizedStatus,
                    ]);
                    $imported++;
                }
            }

            DB::commit();

            $message = "Import berhasil! {$imported} data baru ditambahkan, {$updated} data diperbarui";

            if ($skipped > 0) {
                $message .= ", {$skipped} data dilewati";
            }

            $message .= ".";

            if (!empty($errors)) {
                $message .= " Detail error: " . count($errors) . " baris bermasalah.";
            }

            return redirect()
                ->route('master-kapal.index')
                ->with('success', $message)
                ->with('import_errors', $errors);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    /**
     * Export data to CSV or Excel
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        $query = MasterKapal::query();

        // Apply same filters as index method
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('kode_kapal', 'like', "%{$search}%")
                  ->orWhere('nama_kapal', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%")
                  ->orWhere('pelayaran', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $kapals = $query->get();

        switch ($format) {
            case 'excel':
                $fileName = 'master-kapal-' . date('Y-m-d-H-i-s') . '.xlsx';
                return Excel::download(new \App\Exports\KapalExport($kapals), $fileName);
            case 'csv':
            default:
                return $this->exportToCsv($kapals);
        }
    }

    /**
     * Export data to CSV format
     */
    private function exportToCsv($kapals)
    {
        $filename = 'master-kapal-' . date('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($kapals) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Headers
            fputcsv($file, [
                'No',
                'Kode',
                'Kode Kapal',
                'Nama Kapal',
                'Nickname',
                'Pelayaran (Pemilik)',
                'Kapasitas Palka',
                'Kapasitas Deck',
                'Gross Tonnage',
                'Total Kapasitas',
                'Catatan',
                'Status',
                'Tanggal Dibuat',
                'Tanggal Diperbarui'
            ]);

            // Data rows
            foreach ($kapals as $index => $kapal) {
                $totalKapasitas = ($kapal->kapasitas_kontainer_palka ?? 0) + ($kapal->kapasitas_kontainer_deck ?? 0);

                fputcsv($file, [
                    $index + 1,
                    $kapal->kode,
                    $kapal->kode_kapal ?? '',
                    $kapal->nama_kapal,
                    $kapal->nickname ?? '',
                    $kapal->pelayaran ?? '',
                    $kapal->kapasitas_kontainer_palka ?? '',
                    $kapal->kapasitas_kontainer_deck ?? '',
                    $kapal->gross_tonnage ?? '',
                    $totalKapasitas > 0 ? $totalKapasitas : '',
                    $kapal->catatan ?? '',
                    ucfirst($kapal->status),
                    $kapal->created_at ? $kapal->created_at->format('Y-m-d H:i:s') : '',
                    $kapal->updated_at ? $kapal->updated_at->format('Y-m-d H:i:s') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
