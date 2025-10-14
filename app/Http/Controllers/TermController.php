<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Term::query();

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('kode', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('nama_status', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('catatan', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $terms = $query->paginate(15);

        return view('master-term.index', compact('terms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-term.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|unique:terms,kode',
            'nama_status' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $term = Term::create($request->all());

        // Check if this is a popup request
        // Either has nama_status parameter OR referer contains orders/create OR has popup parameter
        if ($request->query('nama_status') ||
            $request->has('popup') ||
            ($request->header('referer') && strpos($request->header('referer'), 'orders/create') !== false) ||
            ($request->header('referer') && strpos($request->header('referer'), 'nama_status') !== false)) {

            // Add popup flag to indicate this should show success page
            return view('master-term.success', compact('term'));
        }

        return redirect()->route('term.index')->with('success', 'Term berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $term = Term::findOrFail($id);
        return view('master-term.show', compact('term'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $term = Term::findOrFail($id);
        return view('master-term.edit', compact('term'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $term = Term::findOrFail($id);

        $request->validate([
            'kode' => 'required|string|unique:terms,kode,' . $id,
            'nama_status' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $term->update($request->all());

        return redirect()->route('term.index')->with('success', 'Term berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $term = Term::findOrFail($id);
        $term->delete();

        return redirect()->route('term.index')->with('success', 'Term berhasil dihapus.');
    }

    /**
     * Download CSV template for import.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_term.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Write UTF-8 BOM
            fwrite($file, "\xEF\xBB\xBF");

            // Write header
            fputcsv($file, ['kode', 'nama_status', 'catatan', 'status'], ';');

            // Write sample data with comments
            fputcsv($file, ['TERM001', 'Contoh Term 1', 'Catatan opsional', 'active'], ';');
            fputcsv($file, ['', 'Contoh Term 2', 'Catatan lainnya', 'inactive'], ';'); // Kode kosong akan auto-generated
            fputcsv($file, ['', 'Contoh Term 3', '', ''], ';'); // Kode dan status kosong akan auto-generated

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show import form.
     */
    public function showImport()
    {
        return view('master-term.import');
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
            $expectedHeader = ['kode', 'nama_status', 'catatan', 'status'];
            $requiredHeader = ['nama_status']; // Only nama_status is required

            $foundRequiredHeaders = array_intersect($header, $requiredHeader);
            if (count($foundRequiredHeaders) < 1) {
                return redirect()->back()
                    ->withErrors(['csv_file' => 'Format header CSV tidak sesuai. Header minimal yang dibutuhkan: nama_status. Header opsional: kode (auto-generated jika kosong), catatan, status (default: active)'])
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
                $data['nama_status'] = trim($data['nama_status'] ?? '');
                $data['catatan'] = trim($data['catatan'] ?? '');
                $data['status'] = trim(strtolower($data['status'] ?? ''));

                // Set default status to 'active' if empty
                if (empty($data['status'])) {
                    $data['status'] = 'active';
                }

                // Validate required field (hanya nama_status yang wajib)
                if (empty($data['nama_status'])) {
                    $errors[] = "Baris {$rowNumber}: Nama status wajib diisi";
                    continue;
                }

                // Generate kode otomatis jika kosong
                if (empty($data['kode'])) {
                    // Buat kode berdasarkan nama_status
                    $baseCode = strtoupper(str_replace([' ', '-', '_', 'to'], '', $data['nama_status']));
                    // Hilangkan karakter khusus dan ambil maksimal 8 karakter pertama
                    $baseCode = preg_replace('/[^A-Z0-9]/', '', $baseCode);
                    $baseCode = substr($baseCode, 0, 8);

                    // Jika baseCode kosong, gunakan default
                    if (empty($baseCode)) {
                        $baseCode = 'TERM';
                    }

                    // Cek apakah kode sudah ada, jika ya tambahkan nomor urut
                    $counter = 1;
                    $generatedCode = $baseCode;

                    while (Term::where('kode', $generatedCode)->exists()) {
                        $generatedCode = $baseCode . str_pad($counter, 2, '0', STR_PAD_LEFT);
                        $counter++;

                        // Batasi maksimal 99 untuk menghindari infinite loop
                        if ($counter > 99) {
                            $generatedCode = $baseCode . date('His'); // fallback dengan timestamp
                            break;
                        }
                    }

                    $data['kode'] = $generatedCode;
                }

                // Check for duplicates in database (untuk kode yang diinput manual)
                if (Term::where('kode', $data['kode'])->exists()) {
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

                if (strlen($data['nama_status']) > 255) {
                    $errors[] = "Baris {$rowNumber}: Nama status maksimal 255 karakter";
                    continue;
                }

                if (strlen($data['catatan']) > 1000) {
                    $errors[] = "Baris {$rowNumber}: Catatan maksimal 1000 karakter";
                    continue;
                }

                // Create record
                try {
                    Term::create([
                        'kode' => $data['kode'],
                        'nama_status' => $data['nama_status'],
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

            return redirect()->route('term.index')->with($sessionData);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['csv_file' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
