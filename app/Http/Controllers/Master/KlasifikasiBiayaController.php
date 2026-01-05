<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KlasifikasiBiaya;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KlasifikasiBiayaTemplateExport;

class KlasifikasiBiayaController extends Controller
{
    public function index(Request $request)
    {
        $query = KlasifikasiBiaya::query();

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('kode')->paginate(25);

        return view('master.klasifikasi_biaya.index', compact('items'));
    }

    public function create()
    {
        return view('master.klasifikasi_biaya.create');
    }

    /**
     * Get next kode (AJAX endpoint)
     */
    public function getNextKode()
    {
        try {
            // Get last kode
            $lastKode = KlasifikasiBiaya::orderBy('kode', 'desc')->first();
            
            if ($lastKode && preg_match('/^KB(\d+)$/', $lastKode->kode, $matches)) {
                // Extract number and increment
                $nextNumber = (int)$matches[1] + 1;
            } else {
                // First kode
                $nextNumber = 1;
            }
            
            $kode = 'KB' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            
            return response()->json([
                'success' => true,
                'kode' => $kode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate kode: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'kode' => 'required|string|max:50|unique:klasifikasi_biayas,kode',
                'nama' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
            ], [
                'kode.required' => 'Kode wajib diisi.',
                'kode.unique' => 'Kode sudah digunakan, silakan gunakan kode lain.',
                'kode.max' => 'Kode maksimal 50 karakter.',
                'nama.required' => 'Nama klasifikasi biaya wajib diisi.',
                'nama.max' => 'Nama maksimal 255 karakter.',
            ]);

            $data['is_active'] = $request->has('is_active');

            KlasifikasiBiaya::create($data);

            return redirect()->route('klasifikasi-biaya.index')->with('success', 'Klasifikasi biaya berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error saving klasifikasi biaya: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan klasifikasi biaya: ' . $e->getMessage());
        }
    }

    public function edit(KlasifikasiBiaya $klasifikasiBiaya)
    {
        return view('master.klasifikasi_biaya.edit', ['item' => $klasifikasiBiaya]);
    }

    public function update(Request $request, KlasifikasiBiaya $klasifikasiBiaya)
    {
        try {
            $data = $request->validate([
                'kode' => 'required|string|max:50|unique:klasifikasi_biayas,kode,' . $klasifikasiBiaya->id,
                'nama' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
            ], [
                'kode.required' => 'Kode wajib diisi.',
                'kode.unique' => 'Kode sudah digunakan, silakan gunakan kode lain.',
                'kode.max' => 'Kode maksimal 50 karakter.',
                'nama.required' => 'Nama klasifikasi biaya wajib diisi.',
                'nama.max' => 'Nama maksimal 255 karakter.',
            ]);

            $data['is_active'] = $request->has('is_active');

            $klasifikasiBiaya->update($data);

            return redirect()->route('klasifikasi-biaya.index')->with('success', 'Klasifikasi biaya berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error updating klasifikasi biaya: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui klasifikasi biaya: ' . $e->getMessage());
        }
    }

    public function destroy(KlasifikasiBiaya $klasifikasiBiaya)
    {
        $klasifikasiBiaya->delete();

        return redirect()->route('klasifikasi-biaya.index')->with('success', 'Klasifikasi biaya berhasil dihapus.');
    }

    /**
     * Download CSV template for import
     */
    public function downloadTemplate()
    {
        $fileName = 'master_klasifikasi_biaya_template.xlsx';

        $export = new KlasifikasiBiayaTemplateExport();

        return Excel::download($export, $fileName);
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        return view('master.klasifikasi_biaya.import');
    }

    /**
     * Import CSV data
     */
    public function import(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('csv_file');
            $extension = strtolower($file->getClientOriginalExtension());

            // Prepare variables
            $expectedHeader = ['kode', 'nama', 'deskripsi', 'is_active'];
            $requiredHeader = ['nama'];
            $rows = [];

            if (in_array($extension, ['xlsx', 'xls'])) {
                // Read Excel via Maatwebsite Excel to array
                $sheets = Excel::toArray([], $file);
                if (empty($sheets) || !isset($sheets[0]) || count($sheets[0]) == 0) {
                    return redirect()->back()->withErrors(['csv_file' => 'File Excel kosong atau tidak dapat dibaca'])->withInput();
                }

                $sheet = $sheets[0];
                // First row is header
                $header = array_map('strtolower', array_map('trim', $sheet[0]));

                // Validate header
                $foundRequired = array_intersect($header, $requiredHeader);
                if (count($foundRequired) < 1) {
                    return redirect()->back()->withErrors(['csv_file' => 'Format header Excel tidak sesuai. Header minimal: nama'])->withInput();
                }

                // Build rows (skip header)
                for ($i = 1; $i < count($sheet); $i++) {
                    $rows[] = $sheet[$i];
                }
            } else {
                // CSV handling (delimiter: ; )
                $path = $file->getRealPath();
                $csv = array_map(function($line) {
                    return str_getcsv($line, ';');
                }, file($path));

                $header = array_map('strtolower', array_map('trim', array_shift($csv)));

                $foundRequired = array_intersect($header, $requiredHeader);
                if (count($foundRequired) < 1) {
                    return redirect()->back()->withErrors(['csv_file' => "Format header CSV tidak sesuai. Header minimal: nama"])->withInput();
                }

                $rows = $csv;
            }

            $imported = 0;
            $errors = [];
            $duplicates = [];

            // Normalize and index header (remove BOM if present)
            $header = array_map(function($h){ return strtolower(str_replace("\xEF\xBB\xBF", '', trim((string)$h))); }, $header);
            $colIndex = array_flip($header);

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // header +1

                // Normalize row to expected header count
                $row = array_pad($row, max(count($expectedHeader), count($row)), '');

                // Build data using header mapping when possible, fallback to positional if header missing
                $data = [];
                foreach ($expectedHeader as $pos => $col) {
                    if (isset($colIndex[$col]) && isset($row[$colIndex[$col]])) {
                        $value = $row[$colIndex[$col]];
                    } else {
                        // fallback: position-based
                        $value = $row[$pos] ?? '';
                    }

                    $data[$col] = is_array($value) ? '' : trim((string)$value);
                }

                $isActiveRaw = trim(strtolower($data['is_active'] ?? ''));

                if (empty($data['nama'])) {
                    $errors[] = "Baris {$rowNumber}: Nama wajib diisi";
                    continue;
                }

                if (empty($data['kode'])) {
                    // Auto-generate kode when empty
                    $data['kode'] = $this->generateKlasifikasiBiayaCode();
                }

                // Normalize is_active: default to ACTIVE when empty; recognize common truthy/falsy values
                $activeValues = ['active','1','true','yes','y'];
                $inactiveValues = ['inactive','0','false','no','n'];

                if ($isActiveRaw === '') {
                    $isActive = true; // default
                } elseif (in_array($isActiveRaw, $inactiveValues, true)) {
                    $isActive = false;
                } elseif (in_array($isActiveRaw, $activeValues, true)) {
                    $isActive = true;
                } else {
                    // Unknown value => default to active for safety
                    $isActive = true;
                }

                // Check duplicate kode
                if (KlasifikasiBiaya::where('kode', $data['kode'])->exists()) {
                    $duplicates[] = "Baris {$rowNumber}: Kode '{$data['kode']}' sudah ada";
                    continue;
                }

                // Validate lengths
                if (strlen($data['kode']) > 50) {
                    $errors[] = "Baris {$rowNumber}: Kode maksimal 50 karakter";
                    continue;
                }

                if (strlen($data['nama']) > 255) {
                    $errors[] = "Baris {$rowNumber}: Nama maksimal 255 karakter";
                    continue;
                }

                if (strlen($data['deskripsi']) > 1000) {
                    $errors[] = "Baris {$rowNumber}: Deskripsi maksimal 1000 karakter";
                    continue;
                }

                // Save
                try {
                    KlasifikasiBiaya::create([
                        'kode' => $data['kode'],
                        'nama' => $data['nama'],
                        'deskripsi' => $data['deskripsi'] ?: null,
                        'is_active' => $isActive
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: Gagal menyimpan - " . $e->getMessage();
                }
            }

            $message = "Import selesai. {$imported} data berhasil diimport.";
            if (!empty($errors)) $message .= " " . count($errors) . " data gagal diimport.";
            if (!empty($duplicates)) $message .= " " . count($duplicates) . " data duplikat dilewati.";

            $sessionData = ['success' => $message];
            if (!empty($errors)) $sessionData['import_errors'] = $errors;
            if (!empty($duplicates)) $sessionData['import_duplicates'] = $duplicates;

            return redirect()->route('klasifikasi-biaya.index')->with($sessionData);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['csv_file' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Generate kode otomatis: KB00001
     */
    private function generateKlasifikasiBiayaCode()
    {
        $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'KB')->first();
        if (!$nomorTerakhir) {
            $nomorTerakhir = \App\Models\NomorTerakhir::create([
                'modul' => 'KB',
                'nomor_terakhir' => 0,
                'keterangan' => 'Nomor terakhir untuk kode klasifikasi biaya'
            ]);
        }

        $running = $nomorTerakhir->nomor_terakhir + 1;
        $nomorTerakhir->update(['nomor_terakhir' => $running]);

        return 'KB' . str_pad($running, 5, '0', STR_PAD_LEFT);
    }
}
