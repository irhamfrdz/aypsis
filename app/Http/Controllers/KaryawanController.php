<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\CrewEquipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class KaryawanController extends Controller
{
    // Catatan: Constructor yang sebelumnya menyebabkan error telah dihapus.
    // Middleware untuk proteksi rute sekarang ditangani di file routes/web.php
    // dengan menambahkan ->middleware('can:master-karyawan') pada rute resource.

    /**
     * Menampilkan semua karyawan.
     */
    public function index(Request $request)
    {
        // Query builder untuk karyawan
        $query = Karyawan::query();

        // Jika ada parameter search, lakukan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nik', 'LIKE', "%{$search}%")
                  ->orWhere('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nama_panggilan', 'LIKE', "%{$search}%")
                  ->orWhere('divisi', 'LIKE', "%{$search}%")
                  ->orWhere('pekerjaan', 'LIKE', "%{$search}%")
                  ->orWhere('no_hp', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('jkn', 'LIKE', "%{$search}%")
                  ->orWhere('no_ketenagakerjaan', 'LIKE', "%{$search}%")
                  ->orWhere('status_pajak', 'LIKE', "%{$search}%");
            });
        }

        // Menggunakan paginate untuk efisiensi. Angka 15 bisa disesuaikan.
        // Ini akan memuat 15 karyawan per halaman.
        $karyawans = $query->paginate(15)->appends($request->query());

        return view('master-karyawan.index', compact('karyawans'));
    }

    /**
     * Export all karyawans as CSV download.
     */
    public function export(\Illuminate\Http\Request $request)
    {
        // Allow caller to specify separator via ?sep=, default to semicolon for Excel compatibility
        $sep = $request->query('sep', ';');
        $delimiter = $sep === ',' ? ',' : ';'; // Default to semicolon
        
        // Check if this is a template request
        $isTemplate = $request->query('template', false);

        $columns = [
            'nik','nama_panggilan','nama_lengkap','plat','email','ktp','kk','alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos','alamat_lengkap','tempat_lahir','tanggal_lahir','no_hp','jenis_kelamin','status_perkawinan','agama','divisi','pekerjaan','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya','catatan','status_pajak','nama_bank','akun_bank','atas_nama','jkn','no_ketenagakerjaan','cabang','nik_supervisor','supervisor'
        ];

        $fileName = $isTemplate ? 'template_import_karyawan.csv' : 'karyawans_export_' . date('Ymd_His') . '.csv';

        $callback = function() use ($columns, $delimiter, $isTemplate) {
            $out = fopen('php://output', 'w');
            
            // Write UTF-8 BOM for Excel recognition
            fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Write header row with proper delimiter
            fputcsv($out, $columns, $delimiter, '"');
            
            if ($isTemplate) {
                // Add sample data row for template
                $sampleData = [
                    '1234567890', // nik
                    'John', // nama_panggilan
                    'John Doe', // nama_lengkap
                    'B 1234 ABC', // plat
                    'john.doe@example.com', // email
                    '1234567890123456', // ktp
                    '1234567890123456', // kk
                    'Jl. Contoh No. 123', // alamat
                    '001/002', // rt_rw
                    'Kelurahan Contoh', // kelurahan
                    'Kecamatan Contoh', // kecamatan
                    'Kabupaten Contoh', // kabupaten
                    'Provinsi Contoh', // provinsi
                    '12345', // kode_pos
                    'Jl. Contoh No. 123, RT 001/RW 002, Kelurahan Contoh', // alamat_lengkap
                    'Jakarta', // tempat_lahir
                    '1990-01-01', // tanggal_lahir
                    '081234567890', // no_hp
                    'L', // jenis_kelamin
                    'Belum Kawin', // status_perkawinan
                    'Islam', // agama
                    'IT', // divisi
                    'Programmer', // pekerjaan
                    '2024-01-01', // tanggal_masuk
                    '', // tanggal_berhenti
                    '', // tanggal_masuk_sebelumnya
                    '', // tanggal_berhenti_sebelumnya
                    'Catatan contoh', // catatan
                    'K1', // status_pajak
                    'Bank BCA', // nama_bank
                    '1234567890', // akun_bank
                    'John Doe', // atas_nama
                    '0001234567890', // jkn
                    '12345678901234567', // no_ketenagakerjaan
                    'Jakarta', // cabang
                    '', // nik_supervisor
                    '' // supervisor
                ];
                fputcsv($out, $sampleData, $delimiter, '"');
            } else {
                // stream rows for actual export
                Karyawan::chunk(200, function($rows) use ($out, $columns, $delimiter) {
                    foreach ($rows as $r) {
                        $line = [];
                        foreach ($columns as $col) {
                            $val = $r->{$col} ?? '';
                            // format dates to Y-m-d for CSV
                            if ($val instanceof \DateTimeInterface) $val = $val->format('Y-m-d');
                            $line[] = $val;
                        }
                        fputcsv($out, $line, $delimiter, '"');
                    }
                });
            }
            fclose($out);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }    /**
     * Download CSV template for import
     */
    public function downloadTemplate(\Illuminate\Http\Request $request)
    {
        // Generate template manually to ensure proper formatting
        $columns = [
            'nik','nama_panggilan','nama_lengkap','plat','email','ktp','kk','alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos','alamat_lengkap','tempat_lahir','tanggal_lahir','no_hp','jenis_kelamin','status_perkawinan','agama','divisi','pekerjaan','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya','catatan','status_pajak','nama_bank','akun_bank','atas_nama','jkn','no_ketenagakerjaan','cabang','nik_supervisor','supervisor'
        ];
        
        $sampleData = [
            "'1234567890", // nik - dengan apostrophe untuk memaksa format text
            'John', // nama_panggilan
            'John Doe', // nama_lengkap
            'B 1234 ABC', // plat
            'john.doe@example.com', // email
            "'1234567890123456", // ktp - dengan apostrophe untuk memaksa format text
            "'1234567890123456", // kk - dengan apostrophe untuk memaksa format text
            'Jl. Contoh No. 123', // alamat
            '001/002', // rt_rw
            'Kelurahan Contoh', // kelurahan
            'Kecamatan Contoh', // kecamatan
            'Kabupaten Contoh', // kabupaten
            'Provinsi Contoh', // provinsi
            '12345', // kode_pos
            'Jl. Contoh No. 123, RT 001/RW 002, Kelurahan Contoh', // alamat_lengkap
            'Jakarta', // tempat_lahir
            '1990-01-01', // tanggal_lahir
            "'081234567890", // no_hp - dengan apostrophe untuk memaksa format text
            'L', // jenis_kelamin
            'Belum Kawin', // status_perkawinan
            'Islam', // agama
            'IT', // divisi
            'Programmer', // pekerjaan
            '2024-01-01', // tanggal_masuk
            '', // tanggal_berhenti
            '', // tanggal_masuk_sebelumnya
            '', // tanggal_berhenti_sebelumnya
            'Catatan contoh', // catatan
            'K1', // status_pajak
            'Bank BCA', // nama_bank
            "'1234567890", // akun_bank - dengan apostrophe untuk memaksa format text
            'John Doe', // atas_nama
            "'0001234567890", // jkn - dengan apostrophe untuk memaksa format text
            "'12345678901234567", // no_ketenagakerjaan - dengan apostrophe untuk memaksa format text
            'Jakarta', // cabang
            '', // nik_supervisor
            '' // supervisor
        ];
        
        // Add instruction row
        $instructionData = [
            'Format: Text (gunakan apostrophe diawal)', // nik
            'Nama panggilan', // nama_panggilan
            'Nama lengkap sesuai KTP', // nama_lengkap
            'Nomor plat kendaraan', // plat
            'Email aktif', // email
            'Format: Text 16 digit (gunakan apostrophe)', // ktp
            'Format: Text 16 digit (gunakan apostrophe)', // kk
            'Alamat sesuai KTP', // alamat
            'RT/RW', // rt_rw
            'Kelurahan', // kelurahan
            'Kecamatan', // kecamatan
            'Kabupaten', // kabupaten
            'Provinsi', // provinsi
            'Kode pos', // kode_pos
            'Alamat lengkap gabungan', // alamat_lengkap
            'Tempat lahir', // tempat_lahir
            'Format: YYYY-MM-DD', // tanggal_lahir
            'Format: Text (gunakan apostrophe)', // no_hp
            'L atau P', // jenis_kelamin
            'Status perkawinan', // status_perkawinan
            'Agama', // agama
            'Divisi kerja', // divisi
            'Jabatan/pekerjaan', // pekerjaan
            'Format: YYYY-MM-DD', // tanggal_masuk
            'Format: YYYY-MM-DD (kosongkan jika masih aktif)', // tanggal_berhenti
            'Format: YYYY-MM-DD', // tanggal_masuk_sebelumnya
            'Format: YYYY-MM-DD', // tanggal_berhenti_sebelumnya
            'Catatan tambahan', // catatan
            'Status pajak (TK0/TK1/K0/K1/K2/K3/K/0/K/1)', // status_pajak
            'Nama bank', // nama_bank
            'Format: Text (gunakan apostrophe)', // akun_bank
            'Atas nama rekening', // atas_nama
            'Format: Text (gunakan apostrophe)', // jkn
            'Format: Text (gunakan apostrophe)', // no_ketenagakerjaan
            'Cabang/lokasi kerja', // cabang
            'NIK supervisor', // nik_supervisor
            'Nama supervisor' // supervisor
        ];

        $fileName = 'template_import_karyawan.csv';
        
        // Manual CSV generation for better control
        $callback = function() use ($columns, $sampleData, $instructionData) {
            $out = fopen('php://output', 'w');
            
            // Write UTF-8 BOM for Excel recognition
            fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Write header manually with semicolon delimiter
            fwrite($out, implode(';', $columns) . "\r\n");
            
            // Write instruction row for format guidance
            $escapedInstructions = array_map(function($field) {
                // Escape fields that contain semicolons, quotes, or line breaks
                if (strpos($field, ';') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false || strpos($field, "\r") !== false) {
                    return '"' . str_replace('"', '""', $field) . '"';
                }
                return $field;
            }, $instructionData);
            
            fwrite($out, implode(';', $escapedInstructions) . "\r\n");
            
            // Write sample data manually with semicolon delimiter
            $escapedData = array_map(function($field) {
                // Escape fields that contain semicolons, quotes, or line breaks
                if (strpos($field, ';') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false || strpos($field, "\r") !== false) {
                    return '"' . str_replace('"', '""', $field) . '"';
                }
                return $field;
            }, $sampleData);
            
            fwrite($out, implode(';', $escapedData) . "\r\n");
            fclose($out);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Menampilkan formulir untuk membuat karyawan baru.
     */
    public function create()
    {
        return view('master-karyawan.create');
    }

    /**
     * Menyimpan data karyawan baru ke dalam database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|max:255|unique:karyawans',
            'nama_panggilan' => 'required|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'plat' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:karyawans',
            'ktp' => 'nullable|string|max:255|unique:karyawans',
            'kk' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'rt_rw' => 'nullable|string|max:255',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:255',
            'alamat_lengkap' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:255',
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'status_perkawinan' => 'nullable|string|max:255',
            'agama' => 'nullable|string|max:255',
            'divisi' => 'nullable|string|max:255',
            'pekerjaan' => 'nullable|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_berhenti' => 'nullable|date',
            'tanggal_masuk_sebelumnya' => 'nullable|date',
            'tanggal_berhenti_sebelumnya' => 'nullable|date',
            'catatan' => 'nullable|string|max:1000',
            'status_pajak' => 'nullable|string|max:255',
            'nama_bank' => 'nullable|string|max:255',
            'akun_bank' => 'nullable|string|max:255',
            'atas_nama' => 'nullable|string|max:255',
            'jkn' => 'nullable|string|max:255',
            'no_ketenagakerjaan' => 'nullable|string|max:255',
            'cabang' => 'nullable|string|max:255',
            'nik_supervisor' => 'nullable|string|max:255',
            'supervisor' => 'nullable|string|max:255',
        ]);

        // Convert data to uppercase except email
        foreach ($validated as $key => $value) {
            if ($value !== null && $key !== 'email') {
                $validated[$key] = strtoupper($value);
            }
        }

        //Simpan data dalam database
        Karyawan::create($validated);
        return redirect()->route('master.karyawan.index')->with('success','Data karyawan berhasil ditambahkan');
    }

    /**
     * Menampilkan detail satu karyawan.
     */
    public function show(Karyawan $karyawan)
    {
        return view('master-karyawan.show', compact('karyawan'));
    }

    /**
     * Menampilkan form untuk mengedit karyawan.
     */
    public function edit(Karyawan $karyawan)
    {
        return view('master-karyawan.edit', compact('karyawan'));
    }

    /**
     * Memperbarui data karyawan di database.
     */
    public function update(Request $request, Karyawan $karyawan)
    {
        // Anda perlu menambahkan logika validasi di sini, mirip dengan metode store()
        $validated = $request->validate([
            'nik' => ['required', 'string', 'max:255', Rule::unique('karyawans')->ignore($karyawan->id)],
            'nama_panggilan' => 'required|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'plat' => 'nullable|string|max:255',
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('karyawans')->ignore($karyawan->id)],
            'ktp' => ['nullable', 'string', 'max:255', Rule::unique('karyawans')->ignore($karyawan->id)],
            'kk' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'rt_rw' => 'nullable|string|max:255',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:255',
            'alamat_lengkap' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:255',
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'status_perkawinan' => 'nullable|string|max:255',
            'agama' => 'nullable|string|max:255',
            'divisi' => 'nullable|string|max:255',
            'pekerjaan' => 'nullable|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_berhenti' => 'nullable|date',
            'tanggal_masuk_sebelumnya' => 'nullable|date',
            'tanggal_berhenti_sebelumnya' => 'nullable|date',
            'catatan' => 'nullable|string|max:1000',
            'status_pajak' => 'nullable|string|max:255',
            'nama_bank' => 'nullable|string|max:255',
            'akun_bank' => 'nullable|string|max:255',
            'atas_nama' => 'nullable|string|max:255',
            'jkn' => 'nullable|string|max:255',
            'no_ketenagakerjaan' => 'nullable|string|max:255',
            'cabang' => 'nullable|string|max:255',
            'nik_supervisor' => 'nullable|string|max:255',
            'supervisor' => 'nullable|string|max:255',
        ]);

        // Convert data to uppercase except email
        foreach ($validated as $key => $value) {
            if ($value !== null && $key !== 'email') {
                $validated[$key] = strtoupper($value);
            }
        }

        $karyawan->update($validated);

        return redirect()->route('master.karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    /**
     * Menghapus data karyawan dari database.
     */
    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();
        return redirect()->route('master.karyawan.index')->with('success', 'Data karyawan berhasil dihapus.');
    }

    /**
     * Menampilkan halaman print-friendly yang berisi semua field karyawan
     */
    public function print()
    {
        $karyawans = Karyawan::all();
        return view('master-karyawan.print', compact('karyawans'));
    }

    /**
     * Print a single karyawan in a detailed form layout.
     */
    public function printSingle(Karyawan $karyawan)
    {
        return view('master-karyawan.print-single', compact('karyawan'));
    }

    /**
     * Show CSV import form.
     */
    public function importForm()
    {
        return view('master-karyawan.import');
    }

    /**
     * Handle CSV import (expects header row with column names matching model attributes like nik,nama_lengkap,...)
     */
    public function importStore(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        // Auto-detect delimiter (comma, semicolon or tab) so we support different template variants
        $contents = file_get_contents($path);
        $lines = preg_split('/\r\n|\n|\r/', $contents);
        $firstLine = '';
        foreach ($lines as $l) {
            if (trim($l) !== '') {
                $firstLine = $l;
                break;
            }
        }
        $delimiterCandidates = [',', ';', "\t"];
        $delimiter = ',';
        $bestCount = -1;
        foreach ($delimiterCandidates as $cand) {
            $cnt = substr_count($firstLine, $cand);
            if ($cnt > $bestCount) {
                $bestCount = $cnt;
                $delimiter = $cand;
            }
        }

        $processed = 0;
        $skipped = [];
        $errors = [];
        $successRows = [];
        $failedRows = [];

        if (($handle = fopen($path, 'r')) !== false) {
            $header = null;
            $lineNumber = 0;
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $lineNumber++;
                if (!$header) {
                    $header = array_map('trim', $row);
                    continue;
                }
                // If row columns don't match header, try to pad or truncate to avoid losing the whole row.
                if (count($row) < count($header)) {
                    while (count($row) < count($header)) {
                        $row[] = '';
                    }
                } elseif (count($row) > count($header)) {
                    // truncate any extra columns
                    $row = array_slice($row, 0, count($header));
                }

                // Normalize header keys to be case-insensitive, use underscores and map common synonyms
                $normalizedHeader = array_map(function($h){
                    $k = trim($h);
                    $k = strtolower($k);
                    // convert spaces/hyphens to underscores
                    $k = preg_replace('/[\s\-]+/', '_', $k);
                    // remove any chars that are not a-z, 0-9 or underscore
                    $k = preg_replace('/[^a-z0-9_]/', '', $k);
                    // collapse multiple underscores
                    $k = preg_replace('/_+/', '_', $k);
                    // map common variants to canonical column names
                    $map = [
                        'alamatlengkap' => 'alamat_lengkap',
                        'alamat_lengkap' => 'alamat_lengkap',
                        'nohp' => 'no_hp',
                        'nomorhp' => 'no_hp',
                        'nomor_hp' => 'no_hp',
                        'telepon' => 'no_hp',
                        'phone' => 'no_hp',
                        'kodepos' => 'kode_pos',
                        'kode_pos' => 'kode_pos'
                    ];
                    if (array_key_exists($k, $map)) return $map[$k];
                    return $k;
                }, $header);
                $dataRaw = array_combine($normalizedHeader, $row);
                if (!$dataRaw) {
                    $failedRows[] = "Baris {$lineNumber}: Gagal parse baris (kombinasi header/row tidak sesuai)";
                    continue;
                }
                // Use original keys where possible but operate case-insensitively
                $data = [];
                foreach ($dataRaw as $k => $v) {
                    $data[$k] = $v;
                }
                if (!$data) {
                    $failedRows[] = "Baris {$lineNumber}: Gagal parse data";
                    continue;
                }
                
                // Function to normalize numeric fields that might be in scientific notation
                $normalizeNumericField = function($value) {
                    if (empty($value)) return null;
                    
                    $value = trim($value);
                    
                    // Remove leading apostrophe if present (used to force text format in Excel)
                    if (substr($value, 0, 1) === "'") {
                        $value = substr($value, 1);
                    }
                    
                    // Check if it's in scientific notation (like 1.23E+15)
                    if (preg_match('/^-?\d*\.?\d*[eE][+-]?\d+$/', $value)) {
                        // Convert scientific notation to regular number
                        $number = sprintf('%.0f', (float)$value);
                        return $number;
                    }
                    
                    // For phone numbers, preserve leading zeros
                    if (preg_match('/^0\d+$/', $value)) {
                        return $value; // Keep as is for phone numbers starting with 0
                    }
                    
                    // Remove any non-digit characters but preserve the cleaned number
                    $cleaned = preg_replace('/[^\d]/', '', $value);
                    
                    return $cleaned ?: null;
                };
                
                // Normalize numeric fields that are prone to scientific notation
                $numericFields = ['nik', 'ktp', 'kk', 'no_hp'];
                foreach ($numericFields as $field) {
                    if (isset($data[$field])) {
                        $data[$field] = $normalizeNumericField($data[$field]);
                    }
                }
                
                // Use `nik` as unique key to create or update
                $nik = trim($data['nik'] ?? '');
                if (!$nik) {
                    $failedRows[] = "Baris {$lineNumber}: NIK kosong atau tidak ditemukan";
                    continue;
                }

                // Map only fillable attributes (best-effort) — case-insensitive keys
                $allowed = [
                    'nik','nama_lengkap','nama_panggilan','email','tempat_lahir','tanggal_lahir','jenis_kelamin','agama','status_perkawinan','no_hp',
                    'ktp','kk','divisi','pekerjaan','tanggal_masuk','tanggal_berhenti','nik_supervisor','supervisor','cabang','plat',
                    'alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos','alamat_lengkap',
                    'nama_bank','akun_bank','atas_nama','status_pajak','jkn','no_ketenagakerjaan','tanggungan_anak','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya','catatan'
                ];
                $payload = [];
                // Build payload using case-insensitive header names
                foreach ($allowed as $col) {
                    $lc = strtolower($col);
                    if (array_key_exists($lc, $data)) {
                        $val = $data[$lc];
                        $val = is_null($val) ? null : trim($val);
                        // convert empty string to null so unique/DATE columns don't get ''
                        if ($val === '') $val = null;
                        
                        // Convert to uppercase except for email field
                        if ($val !== null && $col !== 'email') {
                            $val = strtoupper($val);
                        }
                        
                        $payload[$col] = $val;
                    }
                }

                // Normalize and validate common date formats to Y-m-d for date columns.
                // Convert empty or unparseable dates to NULL to avoid inserting empty strings into DATE columns.
                $dateCols = ['tanggal_lahir','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya'];

                $normalizeDate = function($val) {
                    $val = trim((string)$val);
                    if ($val === '') return null;
                    // already ISO-like
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) return $val;

                    // replace common separators
                    $v = str_replace(['.', '/'], [' ', ' '], $val);

                    // map Indonesian month names/abbreviations to English so strtotime can parse
                    $map = [
                        'jan'=>'jan','januari'=>'jan',
                        'feb'=>'feb','februari'=>'feb',
                        'mar'=>'mar','maret'=>'mar',
                        'apr'=>'apr','april'=>'apr',
                        'mei'=>'may',
                        'jun'=>'jun','juni'=>'jun',
                        'jul'=>'jul','juli'=>'jul',
                        'agu'=>'aug','agustus'=>'aug',
                        'sep'=>'sep','september'=>'sep',
                        'okt'=>'oct','oktober'=>'oct',
                        'nov'=>'nov','november'=>'nov',
                        'des'=>'dec','desember'=>'dec',
                    ];

                    $v = preg_replace_callback('/\b([A-Za-z]+)\b/u', function($m) use ($map) {
                        $low = strtolower($m[1]);
                        return $map[$low] ?? $m[1];
                    }, $v);

                    $ts = strtotime($v);
                    if ($ts === false) return null;
                    return date('Y-m-d', $ts);
                };

                foreach ($dateCols as $dc) {
                    if (array_key_exists($dc, $payload)) {
                        $payload[$dc] = $normalizeDate($payload[$dc] ?? '');
                    }
                }

                // If alamat_lengkap wasn't provided in CSV, but individual address parts exist,
                // build a sensible alamat_lengkap automatically.
                if (empty($payload['alamat_lengkap'])) {
                    $parts = [];
                    foreach (['alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos'] as $part) {
                        if (!empty($payload[$part])) {
                            $parts[] = trim($payload[$part]);
                        }
                    }
                    if (count($parts) > 0) {
                        $payload['alamat_lengkap'] = implode(', ', $parts);
                    }
                }

                try {
                    $existingKaryawan = Karyawan::where('nik', $nik)->first();
                    $karyawan = Karyawan::updateOrCreate(['nik' => $nik], $payload);
                    
                    $namaLengkap = $payload['nama_lengkap'] ?? $nik;
                    if ($existingKaryawan) {
                        $successRows[] = "Baris {$lineNumber}: Data karyawan {$nik} - {$namaLengkap} berhasil diupdate";
                    } else {
                        $successRows[] = "Baris {$lineNumber}: Data karyawan baru {$nik} - {$namaLengkap} berhasil ditambahkan";
                    }
                    $processed++;
                } catch (\Exception $e) {
                    $errorMessage = $e->getMessage();
                    $namaLengkap = $payload['nama_lengkap'] ?? $nik;
                    
                    // Customize error messages for better user understanding
                    if (strpos($errorMessage, 'Duplicate entry') !== false) {
                        if (strpos($errorMessage, 'email') !== false) {
                            $failedRows[] = "Baris {$lineNumber}: Email " . ($payload['email'] ?? 'tidak valid') . " sudah digunakan karyawan lain";
                        } elseif (strpos($errorMessage, 'ktp') !== false) {
                            $failedRows[] = "Baris {$lineNumber}: Nomor KTP " . ($payload['ktp'] ?? 'tidak valid') . " sudah digunakan karyawan lain";
                        } else {
                            $failedRows[] = "Baris {$lineNumber}: Data duplikat untuk {$namaLengkap}";
                        }
                    } elseif (strpos($errorMessage, 'Data too long') !== false) {
                        $failedRows[] = "Baris {$lineNumber}: Data terlalu panjang untuk {$namaLengkap}";
                    } elseif (strpos($errorMessage, 'Incorrect date') !== false) {
                        $failedRows[] = "Baris {$lineNumber}: Format tanggal tidak valid untuk {$namaLengkap}";
                    } else {
                        $failedRows[] = "Baris {$lineNumber}: Error pada {$namaLengkap} - {$errorMessage}";
                    }
                }
            }
            fclose($handle);
        }

        // Build comprehensive flash messages
        $messages = [];
        $hasErrors = count($failedRows) > 0;
        $hasSuccess = $processed > 0;
        
        // Success message
        if ($hasSuccess) {
            $messages[] = "✅ {$processed} data karyawan berhasil diproses";
            
            // Show preview of successful imports (first 3)
            if (count($successRows) > 0) {
                $successPreview = array_slice($successRows, 0, 3);
                $messages[] = "Data berhasil: " . implode('; ', $successPreview) . 
                    (count($successRows) > 3 ? "; dan " . (count($successRows) - 3) . " lainnya" : "");
            }
        }
        
        // Error/Warning messages
        if ($hasErrors) {
            $totalFailed = count($failedRows);
            $messages[] = "⚠️ {$totalFailed} data gagal diproses";
            
            // Show detailed error information (first 5)
            $failedPreview = array_slice($failedRows, 0, 5);
            $messages[] = "Data gagal: " . implode('; ', $failedPreview) . 
                ($totalFailed > 5 ? "; dan " . ($totalFailed - 5) . " error lainnya" : "");
        }

        // Determine flash message type and redirect
        if ($hasErrors && !$hasSuccess) {
            // All failed
            return redirect()->route('master.karyawan.index')
                ->with('error', implode("\n", $messages));
        } elseif ($hasErrors && $hasSuccess) {
            // Partial success
            return redirect()->route('master.karyawan.index')
                ->with('warning', implode("\n", $messages));
        } else {
            // All success
            return redirect()->route('master.karyawan.index')
                ->with('success', implode("\n", $messages));
        }
    }

    /**
     * Show crew checklist form for ABK employees
     */
    public function crewChecklist($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        // Only allow ABK division employees
        if (!$karyawan->isAbk()) {
            return redirect()->route('master.karyawan.index')
                ->with('error', 'Checklist kelengkapan crew hanya untuk divisi ABK.');
        }

        // Get existing checklist items or create default ones
        $existingItems = $karyawan->crewChecklists()->pluck('item_name')->toArray();
        $defaultItems = CrewEquipment::getDefaultItems();

        // Create missing default items
        foreach ($defaultItems as $item) {
            if (!in_array($item, $existingItems)) {
                $karyawan->crewChecklists()->create([
                    'item_name' => $item,
                    'status' => 'tidak'
                ]);
            }
        }

        // Reload the checklist items
        $checklistItems = $karyawan->crewChecklists()->orderBy('item_name')->get();

        return view('master-karyawan.crew-checklist', compact('karyawan', 'checklistItems'));
    }

    /**
     * Update crew checklist
     */
    public function updateCrewChecklist(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        if (!$karyawan->isAbk()) {
            return redirect()->route('master.karyawan.index')
                ->with('error', 'Checklist kelengkapan crew hanya untuk divisi ABK.');
        }

        $validated = $request->validate([
            'checklist' => 'required|array',
            'checklist.*.status' => 'required|in:ada,tidak',
            'checklist.*.nomor_sertifikat' => 'nullable|string|max:255',
            'checklist.*.issued_date' => 'nullable|date',
            'checklist.*.expired_date' => 'nullable|date|after_or_equal:issued_date',
            'checklist.*.catatan' => 'nullable|string|max:500'
        ]);

        foreach ($validated['checklist'] as $itemId => $data) {
            $checklist = $karyawan->crewChecklists()->find($itemId);
            if ($checklist) {
                $checklist->update([
                    'status' => $data['status'],
                    'nomor_sertifikat' => $data['nomor_sertifikat'],
                    'issued_date' => $data['issued_date'] ?: null,
                    'expired_date' => $data['expired_date'] ?: null,
                    'catatan' => $data['catatan']
                ]);
            }
        }

        return redirect()->route('master.karyawan.crew-checklist', $id)
            ->with('success', 'Checklist kelengkapan crew berhasil diperbarui.');
    }

    /**
     * Print crew checklist
     */
    public function printCrewChecklist($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        if (!$karyawan->isAbk()) {
            abort(403, 'Checklist kelengkapan crew hanya untuk divisi ABK.');
        }

        $checklistItems = $karyawan->crewChecklists()->orderBy('item_name')->get();

        return view('master-karyawan.crew-checklist-print', compact('karyawan', 'checklistItems'));
    }
}
