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
            'pelabuhan' => 'required|string|max:255',
            'kegiatan' => 'required|in:BATAL MUAT,CHANGE VASSEL,DELIVERY,DISCHARGE,DISCHARGE TL,LOADING,PENUMPUKAN BPRP,PERPANJANGAN DELIVERY,RECEIVING,RECEIVING LOSING',
            'biaya' => 'required|in:ADMINISTRASI,DERMAGA,HAULAGE,LOLO,MASA 1A,MASA 1B,MASA2,STEVEDORING,STRIPPING,STUFFING',
            'gudang' => 'nullable|in:CY,DERMAGA,SS',
            'kontainer' => 'nullable|in:20,40',
            'muatan' => 'nullable|in:EMPTY,FULL',
            'tarif' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        DB::beginTransaction();
        try {
            PricelistGateIn::create([
                'pelabuhan' => $request->pelabuhan,
                'kegiatan' => $request->kegiatan,
                'biaya' => $request->biaya,
                'gudang' => $request->gudang,
                'kontainer' => $request->kontainer,
                'muatan' => $request->muatan,
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
            'pelabuhan' => 'required|string|max:255',
            'kegiatan' => 'required|in:BATAL MUAT,CHANGE VASSEL,DELIVERY,DISCHARGE,DISCHARGE TL,LOADING,PENUMPUKAN BPRP,PERPANJANGAN DELIVERY,RECEIVING,RECEIVING LOSING',
            'biaya' => 'required|in:ADMINISTRASI,DERMAGA,HAULAGE,LOLO,MASA 1A,MASA 1B,MASA2,STEVEDORING,STRIPPING,STUFFING',
            'gudang' => 'nullable|in:CY,DERMAGA,SS',
            'kontainer' => 'nullable|in:20,40',
            'muatan' => 'nullable|in:EMPTY,FULL',
            'tarif' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        DB::beginTransaction();
        try {
            $pricelistGateIn->update([
                'pelabuhan' => $request->pelabuhan,
                'kegiatan' => $request->kegiatan,
                'biaya' => $request->biaya,
                'gudang' => $request->gudang,
                'kontainer' => $request->kontainer,
                'muatan' => $request->muatan,
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

            // Read CSV content with semicolon delimiter
            $csvData = array_map(function($line) {
                return str_getcsv($line, ';');
            }, file($path));

            // Check if file is empty
            if (empty($csvData)) {
                return back()->withErrors(['csv_file' => 'File CSV kosong atau tidak valid.']);
            }

            // Get headers (first row)
            $headers = array_shift($csvData);

            // Expected headers
            $expectedHeaders = ['pelabuhan', 'kegiatan', 'biaya', 'gudang', 'kontainer', 'muatan', 'tarif', 'status'];

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
                if (count($row) < 7) { // Minimum 7 columns (pelabuhan, kegiatan, biaya, gudang, kontainer, muatan, tarif)
                    $errors[] = "Baris {$rowNumber}: Data tidak lengkap";
                    $errorCount++;
                    continue;
                }

                // Extract data
                $pelabuhan = trim($row[0] ?? '');
                $kegiatan = trim($row[1] ?? '');
                $biaya = trim($row[2] ?? '');
                $gudang = trim($row[3] ?? '');
                $kontainer = trim($row[4] ?? '');
                $muatan = trim($row[5] ?? '');
                $tarif = trim($row[6] ?? '');
                $status = trim($row[7] ?? 'aktif');

                // Validate required fields
                if (empty($pelabuhan)) {
                    $errors[] = "Baris {$rowNumber}: Pelabuhan tidak boleh kosong";
                    $errorCount++;
                    continue;
                }

                if (empty($kegiatan)) {
                    $errors[] = "Baris {$rowNumber}: Kegiatan tidak boleh kosong";
                    $errorCount++;
                    continue;
                }

                if (empty($biaya)) {
                    $errors[] = "Baris {$rowNumber}: Biaya tidak boleh kosong";
                    $errorCount++;
                    continue;
                }

                if (empty($tarif)) {
                    $errors[] = "Baris {$rowNumber}: Tarif tidak boleh kosong";
                    $errorCount++;
                    continue;
                }

                // Validate data types and constraints
                if (strlen($pelabuhan) > 255) {
                    $errors[] = "Baris {$rowNumber}: Pelabuhan maksimal 255 karakter";
                    $errorCount++;
                    continue;
                }

                // Validate kegiatan enum values
                $validKegiatan = ['BATAL MUAT', 'CHANGE VASSEL', 'DELIVERY', 'DISCHARGE', 'DISCHARGE TL', 'LOADING', 'PENUMPUKAN BPRP', 'PERPANJANGAN DELIVERY', 'RECEIVING', 'RECEIVING LOSING'];
                if (!in_array(strtoupper($kegiatan), array_map('strtoupper', $validKegiatan))) {
                    $errors[] = "Baris {$rowNumber}: Kegiatan tidak valid. Nilai yang diperbolehkan: " . implode(', ', $validKegiatan);
                    $errorCount++;
                    continue;
                }

                // Validate biaya enum values (accept both formats)
                $validBiaya = ['ADMINISTRASI', 'DERMAGA', 'HAULAGE', 'LOLO', 'MASA 1A', 'MASA1A', 'MASA 1B', 'MASA1B', 'MASA2', 'STEVEDORING', 'STRIPPING', 'STUFFING'];
                if (!in_array(strtoupper($biaya), array_map('strtoupper', $validBiaya))) {
                    $errors[] = "Baris {$rowNumber}: Biaya tidak valid. Nilai yang diperbolehkan: " . implode(', ', array_unique($validBiaya));
                    $errorCount++;
                    continue;
                }

                // Normalize biaya values to match database format
                if (strtoupper($biaya) === 'MASA1A') {
                    $biaya = 'MASA 1A';
                } elseif (strtoupper($biaya) === 'MASA1B') {
                    $biaya = 'MASA 1B';
                }

                // Validate gudang enum values (nullable)
                if (!empty($gudang)) {
                    $validGudang = ['CY', 'DERMAGA', 'SS'];
                    if (!in_array(strtoupper($gudang), array_map('strtoupper', $validGudang))) {
                        $errors[] = "Baris {$rowNumber}: Gudang tidak valid. Nilai yang diperbolehkan: " . implode(', ', $validGudang) . " atau kosongkan";
                        $errorCount++;
                        continue;
                    }
                }

                // Validate kontainer enum values (nullable)
                if (!empty($kontainer)) {
                    $validKontainer = ['20', '40'];
                    if (!in_array($kontainer, $validKontainer)) {
                        $errors[] = "Baris {$rowNumber}: Kontainer tidak valid. Nilai yang diperbolehkan: " . implode(', ', $validKontainer) . " atau kosongkan";
                        $errorCount++;
                        continue;
                    }
                }

                // Validate muatan enum values (nullable)
                if (!empty($muatan)) {
                    $validMuatan = ['EMPTY', 'FULL'];
                    if (!in_array(strtoupper($muatan), $validMuatan)) {
                        $errors[] = "Baris {$rowNumber}: Muatan tidak valid. Nilai yang diperbolehkan: " . implode(', ', $validMuatan) . " atau kosongkan";
                        $errorCount++;
                        continue;
                    }
                }

                // Validate tarif (must be numeric)
                // First normalize European number format (comma decimal separator) to standard format
                $tarifNormalized = trim($tarif);
                // Remove spaces (thousand separators)
                $tarifNormalized = str_replace(' ', '', $tarifNormalized);
                // Convert comma to dot for decimal separator
                $tarifNormalized = str_replace(',', '.', $tarifNormalized);

                if (!is_numeric($tarifNormalized)) {
                    $errors[] = "Baris {$rowNumber}: Tarif harus berupa angka";
                    $errorCount++;
                    continue;
                }

                $tarif = (float) $tarifNormalized;
                if ($tarif < 0) {
                    $errors[] = "Baris {$rowNumber}: Tarif tidak boleh negatif";
                    $errorCount++;
                    continue;
                }

                // Validate status
                if (!in_array($status, ['aktif', 'nonaktif', ''])) {
                    $errors[] = "Baris {$rowNumber}: Status harus 'aktif' atau 'nonaktif'";
                    $errorCount++;
                    continue;
                }

                // Default status to aktif if empty
                if (empty($status)) {
                    $status = 'aktif';
                }

                // Check for duplicate combination (pelabuhan + kegiatan + biaya)
                $existingPricelist = PricelistGateIn::where('pelabuhan', $pelabuhan)
                    ->where('kegiatan', strtoupper($kegiatan))
                    ->where('biaya', $biaya)
                    ->when(!empty($gudang), fn($q) => $q->where('gudang', strtoupper($gudang)))
                    ->when(!empty($kontainer), fn($q) => $q->where('kontainer', $kontainer))
                    ->when(!empty($muatan), fn($q) => $q->where('muatan', strtoupper($muatan)))
                    ->first();

                if ($existingPricelist) {
                    $errors[] = "Baris {$rowNumber}: Kombinasi data sudah ada (Pelabuhan: {$pelabuhan}, Kegiatan: {$kegiatan}, Biaya: {$biaya})";
                    $errorCount++;
                    continue;
                }

                try {
                    // Create the record
                    PricelistGateIn::create([
                        'pelabuhan' => $pelabuhan,
                        'kegiatan' => strtoupper($kegiatan),
                        'biaya' => $biaya, // Already normalized above
                        'gudang' => !empty($gudang) ? strtoupper($gudang) : null,
                        'kontainer' => !empty($kontainer) ? $kontainer : null,
                        'muatan' => !empty($muatan) ? strtoupper($muatan) : null,
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

        $fileName = 'template_master_pricelist_gate_pelabuhan_sunda_kelapa_' . date('Y-m-d') . '.csv';

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
                'pelabuhan',
                'kegiatan',
                'biaya',
                'gudang',
                'kontainer',
                'muatan',
                'tarif',
                'status'
            ], ';');

            // Add sample data
            fputcsv($file, [
                'SUNDA KELAPA',
                'RECEIVING',
                'LOLO',
                'CY',
                '20',
                'FULL',
                '128.000,00',
                'aktif'
            ], ';');

            fputcsv($file, [
                'SUNDA KELAPA',
                'DELIVERY',
                'HAULAGE',
                'CY',
                '40',
                'EMPTY',
                '20.000,00',
                'aktif'
            ], ';');

            fputcsv($file, [
                'SUNDA KELAPA',
                'LOADING',
                'ADMINISTRASI',
                '',
                '',
                '',
                '10.000,00',
                'aktif'
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
