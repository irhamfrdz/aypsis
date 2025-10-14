<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AktivitasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Aktivitas::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama_aktivitas', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 10);
        $aktivitas = $query->orderBy('kode')->paginate($perPage);

        return view('master-aktivitas.index', compact('aktivitas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-aktivitas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:255|unique:aktivitas,kode',
            'nama_aktivitas' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            Aktivitas::create($request->all());

            return redirect()->route('master-aktivitas.index')
                           ->with('success', 'Data aktivitas berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Aktivitas $aktivitas)
    {
        return view('master-aktivitas.show', compact('aktivitas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aktivitas $aktivitas)
    {
        return view('master-aktivitas.edit', compact('aktivitas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aktivitas $aktivitas)
    {
        $request->validate([
            'kode' => 'required|string|max:255|unique:aktivitas,kode,' . $aktivitas->id,
            'nama_aktivitas' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            $aktivitas->update($request->all());

            return redirect()->route('master-aktivitas.index')
                           ->with('success', 'Data aktivitas berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aktivitas $aktivitas)
    {
        try {
            $aktivitas->delete();

            return redirect()->route('master-aktivitas.index')
                           ->with('success', 'Data aktivitas berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Download CSV template for import.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_master_aktivitas.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Write UTF-8 BOM
            fwrite($file, "\xEF\xBB\xBF");

            // Write header
            fputcsv($file, ['kode', 'nama_aktivitas', 'catatan', 'status'], ';');

            // Write sample data with comments
            fputcsv($file, ['AKT001', 'Pemuatan Kontainer', 'Aktivitas pemuatan kontainer ke kapal', 'active'], ';');
            fputcsv($file, ['', 'Pembongkaran Kontainer', 'Aktivitas pembongkaran kontainer dari kapal', 'active'], ';'); // Kode kosong akan auto-generated
            fputcsv($file, ['', 'Stuffing Barang', '', 'inactive'], ';'); // Kode dan catatan kosong akan auto-generated

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show import form.
     */
    public function showImportForm()
    {
        return view('master-aktivitas.import');
    }

    /**
     * Import CSV data.
     */
    public function import(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();

            // Read CSV file
            $csv = array_map(function($line) {
                return str_getcsv($line, ';');
            }, file($path));

            // Remove header row
            $header = array_shift($csv);

            // Validate header format
            $expectedHeader = ['kode', 'nama_aktivitas', 'catatan', 'status'];
            $requiredHeader = ['nama_aktivitas']; // Only nama_aktivitas is required

            $foundRequiredHeaders = array_intersect($header, $requiredHeader);
            if (count($foundRequiredHeaders) < 1) {
                return redirect()->back()
                    ->withErrors(['csv_file' => 'Format header CSV tidak sesuai. Header minimal yang dibutuhkan: nama_aktivitas. Header opsional: kode (auto-generated jika kosong), catatan, status (default: active)'])
                    ->withInput();
            }

            $imported = 0;
            $errors = [];
            $duplicates = [];

            foreach ($csv as $index => $row) {
                $rowNumber = $index + 2; // +2 because index starts from 0 and we removed header

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Pad row to match header count
                $row = array_pad($row, count($expectedHeader), '');

                // Create associative array
                $data = array_combine($expectedHeader, $row);

                // Clean and validate data
                $data['kode'] = trim($data['kode'] ?? '');
                $data['nama_aktivitas'] = trim($data['nama_aktivitas'] ?? '');
                $data['catatan'] = trim($data['catatan'] ?? '');
                $data['status'] = trim(strtolower($data['status'] ?? ''));

                // Set default status to 'active' if empty
                if (empty($data['status'])) {
                    $data['status'] = 'active';
                }

                // Validate required field (hanya nama_aktivitas yang wajib)
                if (empty($data['nama_aktivitas'])) {
                    $errors[] = "Baris {$rowNumber}: Nama aktivitas wajib diisi";
                    continue;
                }

                // Generate kode otomatis jika kosong dengan format AKT00001
                if (empty($data['kode'])) {
                    $data['kode'] = $this->generateAktivitasCode();
                }

                // Check for duplicates in database (untuk kode yang diinput manual)
                if (Aktivitas::where('kode', $data['kode'])->exists()) {
                    $duplicates[] = "Baris {$rowNumber}: Kode '{$data['kode']}' sudah ada dalam database";
                    continue;
                }

                // Validate status
                if (!in_array($data['status'], ['active', 'inactive'])) {
                    $errors[] = "Baris {$rowNumber}: Status harus 'active' atau 'inactive'";
                    continue;
                }

                // Validate length
                if (strlen($data['kode']) > 50) {
                    $errors[] = "Baris {$rowNumber}: Kode maksimal 50 karakter";
                    continue;
                }

                if (strlen($data['nama_aktivitas']) > 255) {
                    $errors[] = "Baris {$rowNumber}: Nama aktivitas maksimal 255 karakter";
                    continue;
                }

                if (strlen($data['catatan']) > 1000) {
                    $errors[] = "Baris {$rowNumber}: Catatan maksimal 1000 karakter";
                    continue;
                }

                // Create record
                try {
                    Aktivitas::create([
                        'kode' => $data['kode'],
                        'nama_aktivitas' => $data['nama_aktivitas'],
                        'catatan' => $data['catatan'] ?: null,
                        'status' => $data['status']
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: Gagal menyimpan data - " . $e->getMessage();
                }
            }

            // Prepare result message
            $message = "Import selesai. {$imported} data berhasil diimport.";

            if (!empty($errors)) {
                $message .= " " . count($errors) . " data gagal diimport.";
            }

            if (!empty($duplicates)) {
                $message .= " " . count($duplicates) . " data duplikat dilewati.";
            }

            $sessionData = ['success' => $message];

            if (!empty($errors)) {
                $sessionData['import_errors'] = $errors;
            }

            if (!empty($duplicates)) {
                $sessionData['import_duplicates'] = $duplicates;
            }

            return redirect()->route('master-aktivitas.index')->with($sessionData);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['csv_file' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Generate kode aktivitas otomatis dengan format AKT00001
     */
    private function generateAktivitasCode()
    {
        // Get or create nomor terakhir for AKT module
        $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'AKT')->first();

        if (!$nomorTerakhir) {
            // Create AKT entry if not exists
            $nomorTerakhir = \App\Models\NomorTerakhir::create([
                'modul' => 'AKT',
                'nomor_terakhir' => 0,
                'keterangan' => 'Nomor terakhir untuk kode aktivitas'
            ]);
        }

        // Increment running number
        $runningNumber = $nomorTerakhir->nomor_terakhir + 1;

        // Update nomor terakhir
        $nomorTerakhir->update(['nomor_terakhir' => $runningNumber]);

        // Format: AKT + 5 digit running number
        return 'AKT' . str_pad($runningNumber, 5, '0', STR_PAD_LEFT);
    }
}
