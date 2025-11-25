<?php

namespace App\Http\Controllers;

use App\Models\MasterTujuanKirim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MasterTujuanKirimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = MasterTujuanKirim::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', '%' . $search . '%')
                  ->orWhere('nama_tujuan', 'like', '%' . $search . '%')
                  ->orWhere('catatan', 'like', '%' . $search . '%');
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $tujuanKirim = $query->orderBy('nama_tujuan')->paginate(10);

        return view('master.tujuan-kirim.index', compact('tujuanKirim', 'search', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $searchValue = $request->get('search', '');
        $isPopup = $request->has('popup');
        
        return view('master.tujuan-kirim.create', compact('searchValue', 'isPopup'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode' => 'required|string|max:10|unique:master_tujuan_kirim,kode',
            'nama_tujuan' => 'required|string|max:100',
            'catatan' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $tujuanKirim = MasterTujuanKirim::create($request->all());

        // Check if this is a popup request
        // Either has search parameter OR referer contains orders/create OR has popup parameter
        if ($request->query('search') ||
            $request->has('popup') ||
            ($request->header('referer') && strpos($request->header('referer'), 'orders/create') !== false) ||
            ($request->header('referer') && strpos($request->header('referer'), 'search') !== false)) {
            return view('master-tujuan-kirim.success', compact('tujuanKirim'));
        }

        return redirect()->route('tujuan-kirim.index')
            ->with('success', 'Tujuan kirim berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterTujuanKirim $tujuanKirim)
    {
        return view('master.tujuan-kirim.show', compact('tujuanKirim'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterTujuanKirim $tujuanKirim)
    {
        return view('master.tujuan-kirim.edit', compact('tujuanKirim'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterTujuanKirim $tujuanKirim)
    {
        $validator = Validator::make($request->all(), [
            'kode' => ['required', 'string', 'max:10', Rule::unique('master_tujuan_kirim')->ignore($tujuanKirim->id)],
            'nama_tujuan' => 'required|string|max:100',
            'catatan' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $tujuanKirim->update($request->all());

        return redirect()->route('tujuan-kirim.index')
            ->with('success', 'Tujuan kirim berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterTujuanKirim $tujuanKirim)
    {
        $tujuanKirim->delete();

        return redirect()->route('tujuan-kirim.index')
            ->with('success', 'Tujuan kirim berhasil dihapus.');
    }

    /**
     * Download CSV template for import.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_tujuan_kirim.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Write UTF-8 BOM
            fwrite($file, "\xEF\xBB\xBF");

            // Write header only (no sample data)
            fputcsv($file, ['kode', 'nama_tujuan', 'catatan', 'status'], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show import form.
     */
    public function showImport()
    {
        return view('master.tujuan-kirim.import');
    }

    /**
     * Import CSV data.
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
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
            $expectedHeader = ['kode', 'nama_tujuan', 'catatan', 'status'];
            if (count(array_intersect($header, $expectedHeader)) < 3) {
                return redirect()->back()
                    ->withErrors(['csv_file' => 'Format header CSV tidak sesuai. Header yang dibutuhkan: kode, nama_tujuan, catatan, status'])
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
                $data['nama_tujuan'] = trim($data['nama_tujuan'] ?? '');
                $data['catatan'] = trim($data['catatan'] ?? '');
                $data['status'] = trim(strtolower($data['status'] ?? ''));

                // Set default status to 'active' if empty
                if (empty($data['status'])) {
                    $data['status'] = 'active';
                }

                // Validate required fields
                if (empty($data['kode']) || empty($data['nama_tujuan'])) {
                    $errors[] = "Baris {$rowNumber}: Kode dan nama tujuan wajib diisi";
                    continue;
                }

                // Validate status (only if provided)
                if (!empty($data['status']) && !in_array($data['status'], ['active', 'inactive'])) {
                    $errors[] = "Baris {$rowNumber}: Status harus 'active' atau 'inactive'";
                    continue;
                }

                // Check for duplicates in database
                if (MasterTujuanKirim::where('kode', $data['kode'])->exists()) {
                    $duplicates[] = "Baris {$rowNumber}: Kode '{$data['kode']}' sudah ada dalam database";
                    continue;
                }

                // Validate length
                if (strlen($data['kode']) > 10) {
                    $errors[] = "Baris {$rowNumber}: Kode maksimal 10 karakter";
                    continue;
                }

                if (strlen($data['nama_tujuan']) > 100) {
                    $errors[] = "Baris {$rowNumber}: Nama tujuan maksimal 100 karakter";
                    continue;
                }

                if (strlen($data['catatan']) > 500) {
                    $errors[] = "Baris {$rowNumber}: Catatan maksimal 500 karakter";
                    continue;
                }

                // Create record
                try {
                    MasterTujuanKirim::create([
                        'kode' => $data['kode'],
                        'nama_tujuan' => $data['nama_tujuan'],
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

            return redirect()->route('tujuan-kirim.index')->with($sessionData);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['csv_file' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Export data tujuan kirim to CSV
     */
    public function export(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = MasterTujuanKirim::query();

        // Apply same filters as index page
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', '%' . $search . '%')
                  ->orWhere('nama_tujuan', 'like', '%' . $search . '%')
                  ->orWhere('catatan', 'like', '%' . $search . '%');
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $tujuanKirim = $query->orderBy('nama_tujuan')->get();

        // Generate CSV filename with timestamp
        $filename = 'tujuan-kirim-' . date('Ymd-His') . '.csv';

        // Create CSV content
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'public',
            'Expires' => '0'
        ];

        $callback = function() use ($tujuanKirim) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Headers with semicolon delimiter
            fputcsv($file, [
                'No',
                'Kode',
                'Nama Tujuan',
                'Catatan',
                'Status'
            ], ';');

            // CSV Data with semicolon delimiter
            foreach ($tujuanKirim as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->kode,
                    $item->nama_tujuan,
                    $item->catatan ?? '',
                    $item->status === 'active' ? 'Aktif' : 'Tidak Aktif'
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the form for creating a new resource specifically for Order form.
     * This method doesn't require permissions.
     */
    public function createForOrder(Request $request)
    {
        $searchValue = $request->query('search', '');
        
        return view('master.tujuan-kirim.create-for-order', compact('searchValue'));
    }

    /**
     * Store a newly created resource in storage specifically for Order form.
     * This method doesn't require permissions.
     */
    public function storeForOrder(Request $request)
    {
        // Handle code generation request
        if ($request->has('_generate_code_only')) {
            $code = $this->generateTujuanKirimCode();
            return response()->json(['code' => $code]);
        }

        $request->validate([
            'kode' => 'required|string|max:10|unique:master_tujuan_kirim,kode',
            'nama_tujuan' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $tujuanKirim = MasterTujuanKirim::create($request->all());

        return view('master.tujuan-kirim.success-for-order', compact('tujuanKirim'));
    }

    private function generateTujuanKirimCode()
    {
        $lastCode = MasterTujuanKirim::where('kode', 'like', 'TK%')
            ->orderBy('kode', 'desc')
            ->first();

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode->kode, 2);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'TK' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
