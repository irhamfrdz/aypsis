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
            'catatan' => 'nullable|string',
            'lokasi' => 'nullable|string|max:255',
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
            'catatan' => 'nullable|string',
            'lokasi' => 'nullable|string|max:255',
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
            
            // Header only, no example data
            fputcsv($file, ['kode', 'kode_kapal', 'nama_kapal', 'lokasi', 'catatan', 'status'], ';');
            
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
            
            // Validate header
            $expectedHeader = ['kode', 'kode_kapal', 'nama_kapal', 'lokasi', 'catatan', 'status'];
            if ($header !== $expectedHeader) {
                return redirect()
                    ->back()
                    ->with('error', 'Format header CSV tidak sesuai. Gunakan template yang disediakan.');
            }

            $imported = 0;
            $updated = 0;
            $errors = [];

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
                    continue;
                }

                $kode = trim($row[0]);
                $kode_kapal = !empty(trim($row[1])) ? trim($row[1]) : null;
                $nama_kapal = trim($row[2]);
                $lokasi = !empty(trim($row[3])) ? trim($row[3]) : null;
                $catatan = !empty(trim($row[4])) ? trim($row[4]) : null;
                $status = trim($row[5]);

                // Validate required fields
                if (empty($nama_kapal)) {
                    $errors[] = "Baris {$rowNumber}: Nama kapal tidak boleh kosong";
                    continue;
                }

                // Validate status
                if (!in_array(strtolower($status), ['aktif', 'nonaktif', 'active', 'inactive'])) {
                    $errors[] = "Baris {$rowNumber}: Status harus 'aktif' atau 'nonaktif'";
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
                        'lokasi' => $lokasi,
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
                        'lokasi' => $lokasi,
                        'catatan' => $catatan,
                        'status' => $normalizedStatus,
                    ]);
                    $imported++;
                }
            }

            DB::commit();

            $message = "Import berhasil! {$imported} data baru ditambahkan, {$updated} data diperbarui.";
            
            if (!empty($errors)) {
                $message .= " Dengan " . count($errors) . " error.";
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
