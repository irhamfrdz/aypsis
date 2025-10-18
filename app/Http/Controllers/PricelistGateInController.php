<?php

namespace App\Http\Controllers;

use App\Models\PricelistGateIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PricelistGateInController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('master-pricelist-gate-in-view');

        $pricelistGateIns = PricelistGateIn::orderBy('created_at', 'desc')
            ->paginate(15);

        return view('master.pricelist-gate-in.index', compact('pricelistGateIns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('master-pricelist-gate-in-create');

        return view('master.pricelist-gate-in.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('master-pricelist-gate-in-create');

        $request->validate([
            'kode' => 'required|string|max:255|unique:pricelist_gate_ins,kode',
            'keterangan' => 'nullable|string|max:1000',
            'catatan' => 'nullable|string|max:1000',
            'tarif' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        DB::beginTransaction();
        try {
            PricelistGateIn::create([
                'kode' => $request->kode,
                'keterangan' => $request->keterangan,
                'catatan' => $request->catatan,
                'tarif' => $request->tarif,
                'status' => $request->status
            ]);

            DB::commit();

            return redirect()->route('master.pricelist-gate-in.index')
                ->with('success', 'Pricelist Gate In berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan Pricelist Gate In: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PricelistGateIn $pricelistGateIn)
    {
        $this->authorize('master-pricelist-gate-in-view');

        return view('master.pricelist-gate-in.show', compact('pricelistGateIn'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PricelistGateIn $pricelistGateIn)
    {
        $this->authorize('master-pricelist-gate-in-update');

        return view('master.pricelist-gate-in.edit', compact('pricelistGateIn'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PricelistGateIn $pricelistGateIn)
    {
        $this->authorize('master-pricelist-gate-in-update');

        $request->validate([
            'kode' => 'required|string|max:255|unique:pricelist_gate_ins,kode,' . $pricelistGateIn->id,
            'keterangan' => 'nullable|string|max:1000',
            'catatan' => 'nullable|string|max:1000',
            'tarif' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        DB::beginTransaction();
        try {
            $pricelistGateIn->update([
                'kode' => $request->kode,
                'keterangan' => $request->keterangan,
                'catatan' => $request->catatan,
                'tarif' => $request->tarif,
                'status' => $request->status
            ]);

            DB::commit();

            return redirect()->route('master.pricelist-gate-in.show', $pricelistGateIn)
                ->with('success', 'Pricelist Gate In berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui Pricelist Gate In: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PricelistGateIn $pricelistGateIn)
    {
        $this->authorize('master-pricelist-gate-in-delete');

        DB::beginTransaction();
        try {
            $pricelistGateIn->update(['status' => 'nonaktif']);
            $pricelistGateIn->delete(); // Soft delete

            DB::commit();

            return redirect()->route('master.pricelist-gate-in.index')
                ->with('success', 'Pricelist Gate In berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat menghapus Pricelist Gate In: ' . $e->getMessage());
        }
    }

    /**
     * Show import form
     */
    public function import()
    {
        $this->authorize('master-pricelist-gate-in-create');
        return view('master.pricelist-gate-in.import');
    }

    /**
     * Process CSV import
     */
    public function importProcess(Request $request)
    {
        $this->authorize('master-pricelist-gate-in-create');

        // Validate upload
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048', // 2MB max
        ]);

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();

            // Read CSV content
            $csvData = array_map('str_getcsv', file($path));

            // Check if file is empty
            if (empty($csvData)) {
                return back()->withErrors(['csv_file' => 'File CSV kosong atau tidak valid.']);
            }

            // Get headers (first row)
            $headers = array_shift($csvData);

            // Expected headers
            $expectedHeaders = ['kode', 'keterangan', 'catatan', 'tarif', 'status'];

            // Normalize headers (remove BOM, trim, lowercase)
            $normalizedHeaders = array_map(function($header) {
                return strtolower(trim($header, " \t\n\r\0\x0B\xEF\xBB\xBF"));
            }, $headers);

            // Check if headers match
            if ($normalizedHeaders !== $expectedHeaders) {
                return back()->withErrors([
                    'csv_file' => 'Format header tidak sesuai. Header yang diperlukan: ' . implode(', ', $expectedHeaders)
                ]);
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($csvData as $rowIndex => $row) {
                $rowNumber = $rowIndex + 2; // +2 karena header sudah di-shift dan index mulai dari 0

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Ensure we have enough columns
                if (count($row) < 4) { // Minimum 4 columns (kode, keterangan, catatan, tarif)
                    $errors[] = "Baris {$rowNumber}: Data tidak lengkap";
                    $errorCount++;
                    continue;
                }

                // Extract data
                $kode = trim($row[0] ?? '');
                $keterangan = trim($row[1] ?? '');
                $catatan = trim($row[2] ?? '');
                $tarif = trim($row[3] ?? '');
                $status = trim($row[4] ?? 'aktif');

                // Validate required fields
                if (empty($kode)) {
                    $errors[] = "Baris {$rowNumber}: Kode tidak boleh kosong";
                    $errorCount++;
                    continue;
                }

                if (empty($keterangan)) {
                    $errors[] = "Baris {$rowNumber}: Keterangan tidak boleh kosong";
                    $errorCount++;
                    continue;
                }

                if (empty($tarif)) {
                    $errors[] = "Baris {$rowNumber}: Tarif tidak boleh kosong";
                    $errorCount++;
                    continue;
                }

                // Validate data types and constraints
                if (strlen($kode) > 20) {
                    $errors[] = "Baris {$rowNumber}: Kode maksimal 20 karakter";
                    $errorCount++;
                    continue;
                }

                if (strlen($keterangan) > 255) {
                    $errors[] = "Baris {$rowNumber}: Keterangan maksimal 255 karakter";
                    $errorCount++;
                    continue;
                }

                if (!empty($catatan) && strlen($catatan) > 500) {
                    $errors[] = "Baris {$rowNumber}: Catatan maksimal 500 karakter";
                    $errorCount++;
                    continue;
                }

                // Validate tarif (must be numeric)
                if (!is_numeric($tarif)) {
                    $errors[] = "Baris {$rowNumber}: Tarif harus berupa angka";
                    $errorCount++;
                    continue;
                }

                $tarif = (float) $tarif;
                if ($tarif < 0) {
                    $errors[] = "Baris {$rowNumber}: Tarif tidak boleh negatif";
                    $errorCount++;
                    continue;
                }

                // Validate status
                if (!in_array($status, ['aktif', 'tidak_aktif', ''])) {
                    $errors[] = "Baris {$rowNumber}: Status harus 'aktif' atau 'tidak_aktif'";
                    $errorCount++;
                    continue;
                }

                // Default status to aktif if empty
                if (empty($status)) {
                    $status = 'aktif';
                }

                // Check if kode already exists
                $existingPricelist = PricelistGateIn::where('kode', $kode)->first();
                if ($existingPricelist) {
                    $errors[] = "Baris {$rowNumber}: Kode '{$kode}' sudah ada";
                    $errorCount++;
                    continue;
                }

                try {
                    // Create the record
                    PricelistGateIn::create([
                        'kode' => $kode,
                        'keterangan' => $keterangan,
                        'catatan' => !empty($catatan) ? $catatan : null,
                        'tarif' => $tarif,
                        'status' => $status,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);

                    $successCount++;

                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: Gagal menyimpan data - {$e->getMessage()}";
                    $errorCount++;
                }
            }

            DB::commit();

            // Prepare result message
            $message = "Import selesai. Berhasil: {$successCount} data";
            if ($errorCount > 0) {
                $message .= ", Gagal: {$errorCount} data";
            }

            if (!empty($errors)) {
                $errorMessage = $message . ". Detail error:\n" . implode("\n", array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $errorMessage .= "\n... dan " . (count($errors) - 10) . " error lainnya.";
                }
                return back()->withErrors(['import' => $errorMessage]);
            }

            return redirect()->route('master.pricelist-gate-in.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['csv_file' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()]);
        }
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $this->authorize('master-pricelist-gate-in-view');

        $fileName = 'template_pricelist_gate_in_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Add headers
            fputcsv($file, [
                'kode',
                'keterangan',
                'catatan',
                'tarif',
                'status'
            ], ';');

            // Add sample data
            fputcsv($file, [
                'GATE20',
                'Gate In 20 Feet',
                'Tarif gate in kontainer 20 feet',
                '150000',
                'aktif'
            ], ';');

            fputcsv($file, [
                'GATE40',
                'Gate In 40 Feet',
                'Tarif gate in kontainer 40 feet',
                '250000',
                'aktif'
            ], ';');

            fputcsv($file, [
                'GATEOV',
                'Gate In Over Size',
                '',
                '500000',
                ''
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
