<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JenisBarang;
use App\Models\NomorTerakhir;
use Illuminate\Http\Request;

class JenisBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = JenisBarang::query();

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('kode', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('nama_barang', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('catatan', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $jenisBarangs = $query->paginate(15);

        return view('master-jenis-barang.index', compact('jenisBarangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-jenis-barang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'nullable|string|unique:jenis_barangs,kode',
            'nama_barang' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $data = $request->all();

        // Auto-generate kode jika kosong
        if (empty($data['kode'])) {
            $data['kode'] = $this->generateJenisBarangCode();
        }

        // Set status default ke 'active' jika kosong
        if (empty($data['status'])) {
            $data['status'] = 'active';
        }

        $jenisBarang = JenisBarang::create($data);

        // Check if this is a popup request
        // Either has search parameter OR referer contains orders/create OR has popup parameter
        if ($request->query('search') ||
            $request->has('popup') ||
            ($request->header('referer') && strpos($request->header('referer'), 'orders/create') !== false) ||
            ($request->header('referer') && strpos($request->header('referer'), 'search') !== false)) {
            return view('master-jenis-barang.success', compact('jenisBarang'));
        }

        return redirect()->route('jenis-barang.index')->with('success', 'Jenis barang berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jenisBarang = JenisBarang::findOrFail($id);
        return view('master-jenis-barang.show', compact('jenisBarang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jenisBarang = JenisBarang::findOrFail($id);
        return view('master-jenis-barang.edit', compact('jenisBarang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $jenisBarang = JenisBarang::findOrFail($id);

        $request->validate([
            'kode' => 'required|string|unique:jenis_barangs,kode,' . $id,
            'nama_barang' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $jenisBarang->update($request->all());

        return redirect()->route('jenis-barang.index')->with('success', 'Jenis barang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jenisBarang = JenisBarang::findOrFail($id);
        $jenisBarang->delete();

        return redirect()->route('jenis-barang.index')->with('success', 'Jenis barang berhasil dihapus.');
    }

    /**
     * Download CSV template for import.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_jenis_barang.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Write UTF-8 BOM
            fwrite($file, "\xEF\xBB\xBF");

            // Write header
            fputcsv($file, ['kode', 'nama_barang', 'catatan', 'status'], ';');

            // Write sample data with comments
            fputcsv($file, ['JB001', 'Elektronik', 'Barang elektronik', 'active'], ';');
            fputcsv($file, ['', 'Furniture', 'Barang furniture', 'inactive'], ';'); // Kode kosong akan auto-generated
            fputcsv($file, ['', 'Tekstil', '', ''], ';'); // Kode dan status kosong akan auto-generated

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show import form.
     */
    public function showImportForm()
    {
        return view('master-jenis-barang.import');
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
            $expectedHeader = ['kode', 'nama_barang', 'catatan', 'status'];
            $requiredHeader = ['nama_barang']; // Only nama_barang is required

            $foundRequiredHeaders = array_intersect($header, $requiredHeader);
            if (count($foundRequiredHeaders) < 1) {
                return redirect()->back()
                    ->withErrors(['csv_file' => 'Format header CSV tidak sesuai. Header minimal yang dibutuhkan: nama_barang. Header opsional: kode (auto-generated jika kosong), catatan, status (default: active)'])
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
                $data['nama_barang'] = trim($data['nama_barang'] ?? '');
                $data['catatan'] = trim($data['catatan'] ?? '');
                $data['status'] = trim(strtolower($data['status'] ?? ''));

                // Set default status to 'active' if empty
                if (empty($data['status'])) {
                    $data['status'] = 'active';
                }

                // Validate required field (hanya nama_barang yang wajib)
                if (empty($data['nama_barang'])) {
                    $errors[] = "Baris {$rowNumber}: Nama barang wajib diisi";
                    continue;
                }

                // Generate kode otomatis jika kosong dengan format JB00001
                if (empty($data['kode'])) {
                    $data['kode'] = $this->generateJenisBarangCode();
                }

                // Check for duplicates in database (untuk kode yang diinput manual)
                if (JenisBarang::where('kode', $data['kode'])->exists()) {
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

                if (strlen($data['nama_barang']) > 255) {
                    $errors[] = "Baris {$rowNumber}: Nama barang maksimal 255 karakter";
                    continue;
                }

                if (strlen($data['catatan']) > 1000) {
                    $errors[] = "Baris {$rowNumber}: Catatan maksimal 1000 karakter";
                    continue;
                }

                // Create record
                try {
                    JenisBarang::create([
                        'kode' => $data['kode'],
                        'nama_barang' => $data['nama_barang'],
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

            return redirect()->route('jenis-barang.index')->with($sessionData);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['csv_file' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Generate kode jenis barang otomatis dengan format JB00001
     */
    private function generateJenisBarangCode()
    {
        // Get or create nomor terakhir for JB module
        $nomorTerakhir = NomorTerakhir::where('modul', 'JB')->first();

        if (!$nomorTerakhir) {
            // Create JB entry if not exists
            $nomorTerakhir = NomorTerakhir::create([
                'modul' => 'JB',
                'nomor_terakhir' => 0,
                'keterangan' => 'Nomor terakhir untuk kode jenis barang'
            ]);
        }

        // Increment running number
        $runningNumber = $nomorTerakhir->nomor_terakhir + 1;

        // Update nomor terakhir
        $nomorTerakhir->update(['nomor_terakhir' => $runningNumber]);

        // Format: JB + 5 digit running number
        return 'JB' . str_pad($runningNumber, 5, '0', STR_PAD_LEFT);
    }
}
