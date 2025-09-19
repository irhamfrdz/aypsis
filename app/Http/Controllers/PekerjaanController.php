<?php

namespace App\Http\Controllers;

use App\Models\Pekerjaan;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PekerjaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pekerjaan::query();

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        // Handle status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        $pekerjaans = $query->orderBy('nama_pekerjaan')->paginate(15);

        return view('master-pekerjaan.index', compact('pekerjaans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $divisis = Divisi::active()->orderBy('nama_divisi')->get();
        return view('master-pekerjaan.create', compact('divisis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pekerjaan' => 'required|string|max:100|unique:pekerjaans,nama_pekerjaan',
            'kode_pekerjaan' => 'required|string|max:20|unique:pekerjaans,kode_pekerjaan',
            'divisi' => 'required|string|max:100',
            'is_active' => 'boolean'
        ]);

        try {
            Pekerjaan::create([
                'nama_pekerjaan' => $request->nama_pekerjaan,
                'kode_pekerjaan' => strtoupper($request->kode_pekerjaan),
                'divisi' => $request->divisi,
                'is_active' => $request->has('is_active')
            ]);

            return redirect()->route('master.pekerjaan.index')
                           ->with('success', 'Pekerjaan berhasil ditambahkan!');
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
        $pekerjaan = Pekerjaan::with('karyawans')->findOrFail($id);
        return view('master-pekerjaan.show', compact('pekerjaan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        $divisis = Divisi::active()->orderBy('nama_divisi')->get();
        return view('master-pekerjaan.edit', compact('pekerjaan', 'divisis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);

        $request->validate([
            'nama_pekerjaan' => ['required', 'string', 'max:100', Rule::unique('pekerjaans')->ignore($pekerjaan->id)],
            'kode_pekerjaan' => ['required', 'string', 'max:20', Rule::unique('pekerjaans')->ignore($pekerjaan->id)],
            'divisi' => 'required|string|max:100',
            'is_active' => 'boolean'
        ]);

        try {
            $pekerjaan->update([
                'nama_pekerjaan' => $request->nama_pekerjaan,
                'kode_pekerjaan' => strtoupper($request->kode_pekerjaan),
                'divisi' => $request->divisi,
                'is_active' => $request->has('is_active')
            ]);

            return redirect()->route('master.pekerjaan.index')
                           ->with('success', 'Pekerjaan berhasil diperbarui!');
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
            $pekerjaan = Pekerjaan::findOrFail($id);

            // Check if pekerjaan has related karyawans
            if ($pekerjaan->karyawans()->count() > 0) {
                return back()->with('error', 'Tidak dapat menghapus pekerjaan yang masih memiliki karyawan terkait.');
            }

            $pekerjaan->delete();

            return redirect()->route('master.pekerjaan.index')
                           ->with('success', 'Pekerjaan berhasil dihapus!');
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
            $pekerjaan = Pekerjaan::findOrFail($id);
            $pekerjaan->update(['is_active' => !$pekerjaan->is_active]);

            $status = $pekerjaan->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return redirect()->route('master.pekerjaan.index')
                           ->with('success', 'Pekerjaan berhasil ' . $status . '!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Export CSV template for pekerjaan.
     */
    public function exportTemplate()
    {
        $filename = 'template_pekerjaan_' . date('Y-m-d') . '.csv';

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
                'nama_pekerjaan',
                'kode_pekerjaan',
                'divisi',
                'is_active'
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import pekerjaan from CSV file.
     */
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
        $rowNumber = 2; // Start from row 2 (after header)

        foreach ($data as $row) {
            try {
                if (count($row) < 2) { // Minimum required fields
                    $errors[] = "Baris {$rowNumber}: Data tidak lengkap";
                    $rowNumber++;
                    continue;
                }

                $pekerjaanData = [
                    'nama_pekerjaan' => trim($row[0] ?? ''),
                    'kode_pekerjaan' => trim($row[1] ?? ''),
                    'divisi' => trim($row[2] ?? ''),
                    'is_active' => isset($row[3]) ? (strtolower(trim($row[3])) === 'aktif' || trim($row[3]) === '1') : true,
                ];

                // Validate required fields
                if (empty($pekerjaanData['nama_pekerjaan']) || empty($pekerjaanData['kode_pekerjaan'])) {
                    $errors[] = "Baris {$rowNumber}: Nama pekerjaan dan kode pekerjaan wajib diisi";
                    $rowNumber++;
                    continue;
                }

                // Check for duplicates
                $existing = Pekerjaan::where('nama_pekerjaan', $pekerjaanData['nama_pekerjaan'])
                                    ->orWhere('kode_pekerjaan', $pekerjaanData['kode_pekerjaan'])
                                    ->first();

                if ($existing) {
                    $errors[] = "Baris {$rowNumber}: Pekerjaan dengan nama '{$pekerjaanData['nama_pekerjaan']}' atau kode '{$pekerjaanData['kode_pekerjaan']}' sudah ada";
                    $rowNumber++;
                    continue;
                }

                Pekerjaan::create($pekerjaanData);
                $successCount++;
                $rowNumber++;

            } catch (\Exception $e) {
                $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                $rowNumber++;
            }
        }

        $message = "Import selesai. {$successCount} data berhasil diimpor.";
        if (!empty($errors)) {
            $message .= " Terdapat " . count($errors) . " error(s).";
            session()->flash('import_errors', $errors);
        }

        return redirect()->route('master.pekerjaan.index')->with('success', $message);
    }
}
