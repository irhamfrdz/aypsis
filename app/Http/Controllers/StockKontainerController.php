<?php

namespace App\Http\Controllers;

use App\Models\StockKontainer;
use Illuminate\Http\Request;

class StockKontainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockKontainer::with('gudang');

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }



        // Search berdasarkan nomor kontainer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_seri_gabungan', 'like', '%' . $search . '%')
                  ->orWhere('awalan_kontainer', 'like', '%' . $search . '%')
                  ->orWhere('nomor_seri_kontainer', 'like', '%' . $search . '%')
                  ->orWhere('akhiran_kontainer', 'like', '%' . $search . '%');
            });
        }

        $stockKontainers = $query->latest()->paginate(15);

        return view('master-stock-kontainer.index', compact('stockKontainers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-stock-kontainer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'awalan_kontainer' => 'required|string|size:4',
            'nomor_seri_kontainer' => 'required|string|size:6',
            'akhiran_kontainer' => 'required|string|size:1',
            'ukuran' => 'nullable|string|in:20ft,40ft',
            'tipe_kontainer' => 'nullable|string',
            'status' => 'required|string|in:available,rented,maintenance,damaged,inactive',
            'gudangs_id' => 'nullable|exists:gudangs,id',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'tahun_pembuatan' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        // Gabungkan nomor seri
        $nomorSeriGabungan = $request->awalan_kontainer . $request->nomor_seri_kontainer . $request->akhiran_kontainer;

        // Validasi khusus: Cek duplikasi nomor_seri_kontainer + akhiran_kontainer
        $existingWithSameSerialAndSuffix = StockKontainer::where('nomor_seri_kontainer', $request->nomor_seri_kontainer)
            ->where('akhiran_kontainer', $request->akhiran_kontainer)
            ->where('status', 'active')
            ->first();

        if ($existingWithSameSerialAndSuffix) {
            // Set stock kontainer yang sudah ada ke inactive
            $existingWithSameSerialAndSuffix->update(['status' => 'inactive']);

            $warningMessage = "Stock kontainer dengan nomor seri {$request->nomor_seri_kontainer} dan akhiran {$request->akhiran_kontainer} sudah ada. Stock kontainer lama telah dinonaktifkan.";
            session()->flash('warning', $warningMessage);
        }

        // Validasi unique untuk nomor seri gabungan di stock_kontainers (sebagai backup)
        $existingStock = StockKontainer::where('nomor_seri_gabungan', $nomorSeriGabungan)
            ->where('id', '!=', $stockKontainer->id ?? null)
            ->first();
        if ($existingStock && $existingStock->status === 'active') {
            return back()->withErrors(['nomor_seri_gabungan' => 'Nomor kontainer sudah ada di stock kontainer aktif.'])->withInput();
        }

        // Cek apakah nomor kontainer sudah ada di tabel kontainers
        $existingKontainer = \App\Models\Kontainer::where('nomor_seri_gabungan', $nomorSeriGabungan)->first();
        $status = $request->status;

        if ($existingKontainer && $existingKontainer->status === 'active') {
            // Jika ada duplikasi dengan kontainer aktif, set status menjadi inactive
            $status = 'inactive';
            session()->flash('warning', 'Nomor kontainer sudah ada di master kontainer aktif. Status diset menjadi inactive untuk menghindari duplikasi.');
        }

        $data = $request->all();
        $data['nomor_seri_gabungan'] = $nomorSeriGabungan;
        $data['status'] = $status;

        StockKontainer::create($data);

        return redirect()->route('master.stock-kontainer.index')
            ->with('success', 'Stock kontainer berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StockKontainer $stockKontainer)
    {
        return view('master-stock-kontainer.show', compact('stockKontainer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockKontainer $stockKontainer)
    {
        return view('master-stock-kontainer.edit', compact('stockKontainer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockKontainer $stockKontainer)
    {
        $request->validate([
            'awalan_kontainer' => 'required|string|size:4',
            'nomor_seri_kontainer' => 'required|string|size:6',
            'akhiran_kontainer' => 'required|string|size:1',
            'ukuran' => 'nullable|string|in:20ft,40ft',
            'tipe_kontainer' => 'nullable|string',
            'status' => 'required|string|in:available,rented,maintenance,damaged,inactive',
            'gudangs_id' => 'nullable|exists:gudangs,id',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'tahun_pembuatan' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        // Gabungkan nomor seri
        $nomorSeriGabungan = $request->awalan_kontainer . $request->nomor_seri_kontainer . $request->akhiran_kontainer;

        // Validasi khusus: Cek duplikasi nomor_seri_kontainer + akhiran_kontainer (selain diri sendiri)
        $existingWithSameSerialAndSuffix = StockKontainer::where('nomor_seri_kontainer', $request->nomor_seri_kontainer)
            ->where('akhiran_kontainer', $request->akhiran_kontainer)
            ->where('status', 'active')
            ->where('id', '!=', $stockKontainer->id)
            ->first();

        if ($existingWithSameSerialAndSuffix) {
            // Set stock kontainer yang sudah ada ke inactive
            $existingWithSameSerialAndSuffix->update(['status' => 'inactive']);

            $warningMessage = "Stock kontainer lain dengan nomor seri {$request->nomor_seri_kontainer} dan akhiran {$request->akhiran_kontainer} sudah ada. Stock kontainer lama telah dinonaktifkan.";
            session()->flash('warning', $warningMessage);
        }

        // Validasi unique untuk nomor seri gabungan (kecuali untuk record yang sedang diupdate)
        $existingStock = StockKontainer::where('nomor_seri_gabungan', $nomorSeriGabungan)
                                      ->where('id', '!=', $stockKontainer->id)
                                      ->where('status', 'active')
                                      ->first();
        if ($existingStock) {
            return back()->withErrors(['nomor_seri_gabungan' => 'Nomor kontainer sudah ada di stock kontainer aktif.'])->withInput();
        }

        // Cek apakah nomor kontainer sudah ada di tabel kontainers
        $existingKontainer = \App\Models\Kontainer::where('nomor_seri_gabungan', $nomorSeriGabungan)->first();
        $status = $request->status;

        if ($existingKontainer && $existingKontainer->status === 'active' && $request->status !== 'inactive') {
            // Jika ada duplikasi dengan kontainer aktif dan user tidak sengaja memilih inactive, paksa jadi inactive
            $status = 'inactive';
            session()->flash('warning', 'Nomor kontainer sudah ada di master kontainer aktif. Status diset menjadi inactive untuk menghindari duplikasi.');
        }

        $data = $request->all();
        $data['nomor_seri_gabungan'] = $nomorSeriGabungan;
        $data['status'] = $status;

        $oldGudangId = $stockKontainer->gudangs_id;
        
        $stockKontainer->update($data);

        // Check if location (gudang) changed and log history
        if ($oldGudangId != $stockKontainer->gudangs_id) {
            $nomorKontainer = $stockKontainer->nomor_seri_gabungan;
            $userId = \Illuminate\Support\Facades\Auth::id();
            $now = now();
            
            $oldGudangName = '-';
            if ($oldGudangId) {
                $oldGudang = \App\Models\Gudang::find($oldGudangId);
                if ($oldGudang) $oldGudangName = $oldGudang->nama_gudang;
            }

            $newGudangName = '-';
            if ($stockKontainer->gudangs_id) {
                 $newGudang = \App\Models\Gudang::find($stockKontainer->gudangs_id);
                 if ($newGudang) $newGudangName = $newGudang->nama_gudang;
            }

            // Log 'Keluar' from old gudang
            if ($oldGudangId) {
                \App\Models\HistoryKontainer::create([
                    'nomor_kontainer' => $nomorKontainer,
                    'tipe_kontainer' => 'stock',
                    'jenis_kegiatan' => 'Keluar',
                    'tanggal_kegiatan' => $now,
                    'gudang_id' => $oldGudangId,
                    'keterangan' => 'Pemindahan lokasi (Edit) ke: ' . $newGudangName,
                    'created_by' => $userId,
                ]);
            }

            // Log 'Masuk' to new gudang
            if ($stockKontainer->gudangs_id) {
                \App\Models\HistoryKontainer::create([
                    'nomor_kontainer' => $nomorKontainer,
                    'tipe_kontainer' => 'stock',
                    'jenis_kegiatan' => 'Masuk',
                    'tanggal_kegiatan' => $now,
                    'gudang_id' => $stockKontainer->gudangs_id,
                    'keterangan' => 'Pemindahan lokasi (Edit) dari: ' . $oldGudangName,
                    'created_by' => $userId,
                ]);
            }
        }

        return redirect()->route('master.stock-kontainer.index')
            ->with('success', 'Stock kontainer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockKontainer $stockKontainer)
    {
        $stockKontainer->delete();

        return redirect()->route('master.stock-kontainer.index')
            ->with('success', 'Stock kontainer berhasil dihapus.');
    }

    /**
     * Download template CSV for import
     */
    public function downloadTemplate()
    {
        $filename = 'template_stock_kontainer.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header columns
            fputcsv($file, [
                'nomor_kontainer',
                'ukuran',
                'tipe_kontainer',
                'status',
                'nama_gudang',
                'tahun_pembuatan',
                'keterangan'
            ]);

            // Example data
            fputcsv($file, [
                'ABCD1234567',
                '20ft',
                'DRY',
                'available',
                'Gudang Utama',
                '2020',
                'Contoh keterangan'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import stock kontainer from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:csv,txt|max:5120', // 5MB
        ]);

        try {
            $file = $request->file('excel_file');
            $handle = fopen($file->getRealPath(), 'r');
            
            // Skip BOM if exists
            $bom = fread($handle, 3);
            if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
                rewind($handle);
            }
            
            $header = fgetcsv($handle);
            
            // Validate header
            $expectedHeaders = ['nomor_kontainer', 'ukuran', 'tipe_kontainer', 'status', 'nama_gudang', 'tahun_pembuatan', 'keterangan'];
            if ($header !== $expectedHeaders) {
                return back()->with('error', 'Format CSV tidak sesuai. Pastikan menggunakan template yang benar.');
            }

            $imported = 0;
            $updated = 0;
            $errors = [];
            $rowNumber = 1; // Header is row 1, data starts from row 2

            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    $nomorKontainer = trim($row[0] ?? '');
                    $ukuran = trim($row[1] ?? '');
                    $tipeKontainer = trim($row[2] ?? '');
                    $status = strtolower(trim($row[3] ?? 'available'));
                    $namaGudang = trim($row[4] ?? '');
                    $tahunPembuatan = trim($row[5] ?? '');
                    $keterangan = trim($row[6] ?? '');

                    // Validasi nomor kontainer
                    if (strlen($nomorKontainer) !== 11) {
                        $errors[] = "Baris $rowNumber: Nomor kontainer harus 11 karakter (contoh: ABCD1234567)";
                        continue;
                    }

                    // Validasi status
                    $validStatuses = ['available', 'rented', 'maintenance', 'damaged', 'inactive'];
                    if (!in_array($status, $validStatuses)) {
                        $errors[] = "Baris $rowNumber: Status tidak valid. Harus salah satu dari: " . implode(', ', $validStatuses);
                        continue;
                    }

                    // Parse nomor kontainer
                    $awalan = substr($nomorKontainer, 0, 4);
                    $nomorSeri = substr($nomorKontainer, 4, 6);
                    $akhiran = substr($nomorKontainer, 10, 1);

                    // Find gudang by nama_gudang
                    $gudangId = null;
                    if (!empty($namaGudang)) {
                        $gudang = \App\Models\Gudang::where('nama_gudang', 'like', '%' . $namaGudang . '%')->first();
                        if ($gudang) {
                            $gudangId = $gudang->id;
                        } else {
                            $errors[] = "Baris $rowNumber: Gudang '$namaGudang' tidak ditemukan";
                        }
                    }

                    // Validasi tahun pembuatan
                    $tahunPembuatanValue = null;
                    if (!empty($tahunPembuatan) && is_numeric($tahunPembuatan)) {
                        $tahunPembuatanValue = (int) $tahunPembuatan;
                        if ($tahunPembuatanValue < 1900 || $tahunPembuatanValue > (date('Y') + 1)) {
                            $errors[] = "Baris $rowNumber: Tahun pembuatan tidak valid";
                            continue;
                        }
                    }

                    // Check if exists
                    $existing = StockKontainer::where('nomor_seri_gabungan', $nomorKontainer)->first();

                    if ($existing) {
                        // Update existing
                        $existing->update([
                            'awalan_kontainer' => $awalan,
                            'nomor_seri_kontainer' => $nomorSeri,
                            'akhiran_kontainer' => $akhiran,
                            'ukuran' => $ukuran ?: $existing->ukuran,
                            'tipe_kontainer' => $tipeKontainer ?: $existing->tipe_kontainer,
                            'status' => $status,
                            'gudangs_id' => $gudangId,
                            'tahun_pembuatan' => $tahunPembuatanValue,
                            'keterangan' => $keterangan ?: $existing->keterangan,
                        ]);
                        $updated++;
                    } else {
                        // Create new
                        StockKontainer::create([
                            'awalan_kontainer' => $awalan,
                            'nomor_seri_kontainer' => $nomorSeri,
                            'akhiran_kontainer' => $akhiran,
                            'nomor_seri_gabungan' => $nomorKontainer,
                            'ukuran' => $ukuran,
                            'tipe_kontainer' => $tipeKontainer,
                            'status' => $status,
                            'gudangs_id' => $gudangId,
                            'tahun_pembuatan' => $tahunPembuatanValue,
                            'keterangan' => $keterangan,
                        ]);
                        $imported++;
                    }

                } catch (\Exception $e) {
                    $errors[] = "Baris $rowNumber: " . $e->getMessage();
                }
            }

            fclose($handle);

            $message = "Import selesai: $imported data baru ditambahkan, $updated data diperbarui.";
            
            if (!empty($errors)) {
                $message .= " Namun ada beberapa error: " . implode(' | ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " (dan " . (count($errors) - 5) . " error lainnya)";
                }
                return back()->with('warning', $message);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Error saat import: ' . $e->getMessage());
        }
    }

    /**
     * Download template CSV for update gudang
     */
    public function downloadTemplateGudang()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $sheet->setCellValue('A1', 'nomor_kontainer');
        $sheet->setCellValue('B1', 'nama_gudang');
        
        // Style headers
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        $sheet->getStyle('A1:B1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');
        
        // Add example rows
        $sheet->setCellValue('A2', 'ABCD1234567');
        $sheet->setCellValue('B2', 'Gudang Utama');
        $sheet->setCellValue('A3', 'EFGH7654321');
        $sheet->setCellValue('B3', 'Gudang Pelabuhan');
        
        // Auto-size columns
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        
        $filename = 'template_update_gudang_kontainer.xlsx';
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Update gudang from Excel/CSV
     */
    public function updateGudang(Request $request)
    {
        $request->validate([
            'gudang_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120', // 5MB
        ]);

        try {
            $file = $request->file('gudang_file');
            $extension = $file->getClientOriginalExtension();
            
            // Handle Excel files
            if (in_array($extension, ['xlsx', 'xls'])) {
                return $this->updateGudangFromExcel($file);
            }
            
            // Handle CSV files
            return $this->updateGudangFromCsv($file);

        } catch (\Exception $e) {
            return back()->with('error', 'Error saat update gudang: ' . $e->getMessage());
        }
    }

    /**
     * Update gudang from CSV file
     */
    private function updateGudangFromCsv($file)
    {
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip BOM if exists
        $bom = fread($handle, 3);
        if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
            rewind($handle);
        }
        
        $header = fgetcsv($handle);
        
        // Validate header
        $expectedHeaders = ['nomor_kontainer', 'nama_gudang'];
        if ($header !== $expectedHeaders) {
            fclose($handle);
            return back()->with('error', 'Format file tidak sesuai. Header harus: nomor_kontainer, nama_gudang');
        }

        $updated = 0;
        $notFound = 0;
        $kontainerNotFound = [];
        $gudangNotFound = [];
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            try {
                $nomorKontainer = trim($row[0] ?? '');
                $namaGudang = trim($row[1] ?? '');

                if (empty($nomorKontainer)) {
                    $errors[] = "Baris $rowNumber: Nomor kontainer kosong";
                    continue;
                }

                // Find stock kontainer
                $stockKontainer = StockKontainer::where('nomor_seri_gabungan', $nomorKontainer)->first();
                
                if (!$stockKontainer) {
                    $notFound++;
                    $kontainerNotFound[] = $nomorKontainer;
                    $errors[] = "Baris $rowNumber: Kontainer $nomorKontainer tidak ditemukan";
                    continue;
                }

                // Find gudang
                $gudangId = null;
                if (!empty($namaGudang)) {
                    $gudang = \App\Models\Gudang::where('nama_gudang', 'like', '%' . $namaGudang . '%')->first();
                    if ($gudang) {
                        $gudangId = $gudang->id;
                    } else {
                        if (!in_array($namaGudang, $gudangNotFound)) {
                            $gudangNotFound[] = $namaGudang;
                        }
                        $errors[] = "Baris $rowNumber: Kontainer $nomorKontainer - Gudang '$namaGudang' tidak ditemukan";
                        continue;
                    }
                }

                // Update gudangs_id
                $stockKontainer->update(['gudangs_id' => $gudangId]);
                $updated++;

            } catch (\Exception $e) {
                $errors[] = "Baris $rowNumber ($nomorKontainer): " . $e->getMessage();
            }
        }

        fclose($handle);

        $message = "Update gudang selesai: $updated kontainer berhasil diupdate.";
        
        if ($notFound > 0) {
            $message .= " $notFound kontainer tidak ditemukan";
            
            // Show list of containers not found
            if (!empty($kontainerNotFound)) {
                $kontainerList = implode(', ', array_slice($kontainerNotFound, 0, 5));
                if (count($kontainerNotFound) > 5) {
                    $kontainerList .= " (dan " . (count($kontainerNotFound) - 5) . " lainnya)";
                }
                $message .= ": " . $kontainerList;
            }
            $message .= ".";
        }

        if (!empty($gudangNotFound)) {
            $message .= " Gudang tidak ditemukan: " . implode(', ', array_slice($gudangNotFound, 0, 3));
            if (count($gudangNotFound) > 3) {
                $message .= " (dan " . (count($gudangNotFound) - 3) . " lainnya)";
            }
            $message .= ".";
        }

        if (!empty($errors) && count($errors) <= 10) {
            $message .= " Detail error: " . implode(' | ', $errors);
            return back()->with('warning', $message);
        } elseif (!empty($errors)) {
            $message .= " Total " . count($errors) . " error terjadi.";
            return back()->with('warning', $message);
        }

        return back()->with('success', $message);
    }

    /**
     * Update gudang from Excel file (xlsx/xls)
     */
    private function updateGudangFromExcel($file)
    {
        // Check if PhpSpreadsheet is available
        if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            return back()->with('error', 'Library PhpSpreadsheet tidak tersedia. Silakan gunakan format CSV.');
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Validate header
            $header = array_map('trim', $rows[0] ?? []);
            $expectedHeaders = ['nomor_kontainer', 'nama_gudang'];
            
            if ($header !== $expectedHeaders) {
                return back()->with('error', 'Format file tidak sesuai. Header harus: nomor_kontainer, nama_gudang');
            }

            $updated = 0;
            $notFound = 0;
            $kontainerNotFound = [];
            $gudangNotFound = [];
            $errors = [];

            // Start from row 2 (skip header)
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $rowNumber = $i + 1;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    $nomorKontainer = trim($row[0] ?? '');
                    $namaGudang = trim($row[1] ?? '');

                    if (empty($nomorKontainer)) {
                        $errors[] = "Baris $rowNumber: Nomor kontainer kosong";
                        continue;
                    }

                    // Find stock kontainer
                    $stockKontainer = StockKontainer::where('nomor_seri_gabungan', $nomorKontainer)->first();
                    
                    if (!$stockKontainer) {
                        $notFound++;
                        $kontainerNotFound[] = $nomorKontainer;
                        $errors[] = "Baris $rowNumber: Kontainer $nomorKontainer tidak ditemukan";
                        continue;
                    }

                    // Find gudang
                    $gudangId = null;
                    if (!empty($namaGudang)) {
                        $gudang = \App\Models\Gudang::where('nama_gudang', 'like', '%' . $namaGudang . '%')->first();
                        if ($gudang) {
                            $gudangId = $gudang->id;
                        } else {
                            if (!in_array($namaGudang, $gudangNotFound)) {
                                $gudangNotFound[] = $namaGudang;
                            }
                            $errors[] = "Baris $rowNumber: Kontainer $nomorKontainer - Gudang '$namaGudang' tidak ditemukan";
                            continue;
                        }
                    }

                    // Update gudangs_id
                    $stockKontainer->update(['gudangs_id' => $gudangId]);
                    $updated++;

                } catch (\Exception $e) {
                    $errors[] = "Baris $rowNumber ($nomorKontainer): " . $e->getMessage();
                }
            }

            $message = "Update gudang selesai: $updated kontainer berhasil diupdate.";
            
            if ($notFound > 0) {
                $message .= " $notFound kontainer tidak ditemukan";
                
                // Show list of containers not found
                if (!empty($kontainerNotFound)) {
                    $kontainerList = implode(', ', array_slice($kontainerNotFound, 0, 5));
                    if (count($kontainerNotFound) > 5) {
                        $kontainerList .= " (dan " . (count($kontainerNotFound) - 5) . " lainnya)";
                    }
                    $message .= ": " . $kontainerList;
                }
                $message .= ".";
            }

            if (!empty($gudangNotFound)) {
                $message .= " Gudang tidak ditemukan: " . implode(', ', array_slice($gudangNotFound, 0, 3));
                if (count($gudangNotFound) > 3) {
                    $message .= " (dan " . (count($gudangNotFound) - 3) . " lainnya)";
                }
                $message .= ".";
            }

            if (!empty($errors) && count($errors) <= 10) {
                $message .= " Detail error: " . implode(' | ', $errors);
                return back()->with('warning', $message);
            } elseif (!empty($errors)) {
                $message .= " Total " . count($errors) . " error terjadi.";
                return back()->with('warning', $message);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Error membaca file Excel: ' . $e->getMessage() . '. Silakan gunakan format CSV.');
        }
    }
}
