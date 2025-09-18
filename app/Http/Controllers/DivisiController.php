<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DivisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Divisi::query();

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        // Handle status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        $divisis = $query->orderBy('nama_divisi')->paginate(15);

        return view('master-divisi.index', compact('divisis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-divisi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_divisi' => 'required|string|max:100|unique:divisis,nama_divisi',
            'kode_divisi' => 'required|string|max:20|unique:divisis,kode_divisi',
            'deskripsi' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        try {
            Divisi::create([
                'nama_divisi' => $request->nama_divisi,
                'kode_divisi' => strtoupper($request->kode_divisi),
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->has('is_active')
            ]);

            return redirect()->route('master.divisi.index')
                           ->with('success', 'Divisi berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $divisi = Divisi::with('karyawans')->findOrFail($id);
        return view('master-divisi.show', compact('divisi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $divisi = Divisi::findOrFail($id);
        return view('master-divisi.edit', compact('divisi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $divisi = Divisi::findOrFail($id);

        $request->validate([
            'nama_divisi' => ['required', 'string', 'max:100', Rule::unique('divisis')->ignore($divisi->id)],
            'kode_divisi' => ['required', 'string', 'max:20', Rule::unique('divisis')->ignore($divisi->id)],
            'deskripsi' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        try {
            $divisi->update([
                'nama_divisi' => $request->nama_divisi,
                'kode_divisi' => strtoupper($request->kode_divisi),
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->has('is_active')
            ]);

            return redirect()->route('master.divisi.index')
                           ->with('success', 'Divisi berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $divisi = Divisi::findOrFail($id);

            // Check if divisi has related karyawans
            if ($divisi->karyawans()->count() > 0) {
                return back()->with('error', 'Tidak dapat menghapus divisi yang masih memiliki karyawan terkait.');
            }

            $divisi->delete();

            return redirect()->route('master.divisi.index')
                           ->with('success', 'Divisi berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status of the specified resource.
     */
    public function toggleStatus(string $id)
    {
        try {
            $divisi = Divisi::findOrFail($id);
            $divisi->update(['is_active' => !$divisi->is_active]);

            $status = $divisi->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return redirect()->route('master.divisi.index')
                           ->with('success', 'Divisi berhasil ' . $status . '!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download template CSV for divisi import.
     */
    public function downloadTemplate()
    {
        $filename = 'template_import_divisi.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $templateData = [
            ['nama_divisi', 'deskripsi', 'is_active'],
        ];

        $callback = function() use ($templateData) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for proper Excel recognition
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            foreach ($templateData as $row) {
                fputcsv($file, $row, ';'); // Use semicolon as delimiter for better Excel compatibility
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate Excel XML content for template.
     */
    private function generateExcelXML()
    {
        $templateData = [
            ['nama_divisi', 'kode_divisi', 'deskripsi', 'is_active'],
        ];

        $xml = '     */

    /**
     * Import divisi data from CSV file.
     */';

        return $xml;
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120'
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();

            if (in_array($extension, ['xlsx', 'xls'])) {
                // For Excel files, we'll treat them as CSV for now
                // In a real implementation, you'd use PhpSpreadsheet or similar
                return back()->withErrors(['file' => 'Format Excel belum didukung untuk import. Gunakan format CSV dengan delimiter titik koma (;).']);
            }

            $path = $file->getRealPath();

            // Read file content
            $content = file_get_contents($path);

            // Detect delimiter (prefer semicolon for Excel compatibility)
            $delimiter = ';';
            if (strpos($content, ',') !== false && strpos($content, ';') === false) {
                $delimiter = ',';
            }

            // Parse CSV with detected delimiter
            $data = array_map(function($line) use ($delimiter) {
                return str_getcsv($line, $delimiter);
            }, file($path));

            // Remove header row if exists
            $header = array_shift($data);

            $imported = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($data as $rowIndex => $row) {
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Expected format: nama_divisi, deskripsi, is_active
                    $namaDivisi = trim($row[0] ?? '');
                    $deskripsi = trim($row[1] ?? '');
                    $isActive = trim($row[2] ?? '1');

                    if (empty($namaDivisi)) {
                        $errors[] = "Baris " . ($rowIndex + 2) . ": Nama divisi wajib diisi";
                        continue;
                    }

                    // Check for duplicates (only by nama_divisi)
                    $existing = Divisi::where('nama_divisi', $namaDivisi)->first();

                    if ($existing) {
                        $errors[] = "Baris " . ($rowIndex + 2) . ": Divisi '{$namaDivisi}' sudah ada";
                        continue;
                    }

                    Divisi::create([
                        'nama_divisi' => $namaDivisi,
                        'deskripsi' => $deskripsi,
                        'is_active' => filter_var($isActive, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true
                    ]);

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($rowIndex + 2) . ": " . $e->getMessage();
                }
            }

            if ($imported > 0) {
                DB::commit();
                $message = "Berhasil mengimport {$imported} divisi!";
                if (!empty($errors)) {
                    $message .= " Namun ada " . count($errors) . " error(s).";
                }
                return redirect()->route('master.divisi.index')->with('success', $message);
            } else {
                DB::rollBack();
                return back()->with('error', 'Tidak ada data yang berhasil diimport. Errors: ' . implode(', ', $errors));
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}
