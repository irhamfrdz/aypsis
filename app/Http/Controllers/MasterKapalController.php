<?php

namespace App\Http\Controllers;

use App\Models\MasterKapal;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $kapals = $query->paginate(10)->withQueryString();

        return view('master-kapal.index', compact('kapals'));
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

            // Header with new fields: nickname and pelayaran (changed from lokasi)
            fputcsv($file, ['kode', 'kode_kapal', 'nama_kapal', 'nickname', 'pelayaran', 'catatan', 'status'], ';');

            // Example data rows
            fputcsv($file, ['K001', 'KP-001', 'MV SEJAHTERA', 'SEJAHTERA', 'PT Pelayaran Indonesia', 'Kapal kontainer 20 feet', 'aktif'], ';');
            fputcsv($file, ['K002', 'KP-002', 'MV NUSANTARA', 'NUSA', 'PT Samudera Lines', 'Kapal cargo besar', 'aktif'], ';');
            fputcsv($file, ['K003', 'KP-003', 'MV BAHARI', '', 'PT Pelni', 'Kapal penumpang', 'nonaktif'], ';');
            fputcsv($file, ['K004', '', 'MV SRIKANDI', 'KANDI', 'PT Berlian Shipping', '', 'aktif'], ';');

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

            // Remove header
            $header = array_shift($csvData);

            // Remove UTF-8 BOM if present and trim whitespace
            $header = array_map(function($value) {
                // Remove BOM (﻿) from UTF-8 encoded files
                $value = str_replace("\xEF\xBB\xBF", '', $value);
                return trim($value);
            }, $header);

            // Validate header
            $expectedHeader = ['kode', 'kode_kapal', 'nama_kapal', 'nickname', 'pelayaran', 'catatan', 'status'];
            if ($header !== $expectedHeader) {
                return redirect()
                    ->back()
                    ->with('error', 'Format header CSV tidak sesuai. Expected: ' . implode(';', $expectedHeader) . ' | Got: ' . implode(';', $header));
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

                // Skip if kode is empty
                if (empty(trim($row[0]))) {
                    $errors[] = "Baris {$rowNumber}: Kode tidak boleh kosong";
                    $skipped++;
                    continue;
                }

                $kode = trim($row[0]);
                $kode_kapal = !empty(trim($row[1])) ? trim($row[1]) : null;
                $nama_kapal = trim($row[2]);
                $nickname = !empty(trim($row[3])) ? trim($row[3]) : null;
                $pelayaran = !empty(trim($row[4])) ? trim($row[4]) : null;
                $catatan = !empty(trim($row[5])) ? trim($row[5]) : null;
                $status = trim($row[6]);

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
                    // Update existing
                    $existing->update([
                        'kode_kapal' => $kode_kapal,
                        'nama_kapal' => $nama_kapal,
                        'nickname' => $nickname,
                        'pelayaran' => $pelayaran,
                        'catatan' => $catatan,
                        'status' => $normalizedStatus,
                    ]);
                    $updated++;
                } else {
                    // Create new
                    MasterKapal::create([
                        'kode' => $kode,
                        'kode_kapal' => $kode_kapal,
                        'nama_kapal' => $nama_kapal,
                        'nickname' => $nickname,
                        'pelayaran' => $pelayaran,
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
}
