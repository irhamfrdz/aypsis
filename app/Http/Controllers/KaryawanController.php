<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\KaryawanFamilyMember;
use App\Models\CrewEquipment;
use App\Models\Divisi;
use App\Models\Pekerjaan;
use App\Models\Pajak;
use App\Models\Bank;
use App\Models\Cabang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

// ...existing code...

class KaryawanController extends Controller
{
    /**
     * Get next available NIK for auto-generation
     */
    public function getNextNik()
    {
        try {
            $nextNik = Karyawan::generateNextNik();
            return response()->json([
                'success' => true,
                'nik' => $nextNik
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate NIK'
            ], 500);
        }
    }

    /**
     * Onboarding crew checklist khusus untuk karyawan baru (public)
     */
    public function onboardingCrewChecklist($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        // Hanya untuk divisi ABK (atau sesuaikan kebutuhan)
        if (!$karyawan->isAbk()) {
            return redirect()->route('login')->with('error', 'Checklist onboarding crew hanya untuk divisi ABK.');
        }

        // Ambil atau buat checklist default
        $existingItems = $karyawan->crewChecklists()->pluck('item_name')->toArray();
        $defaultItems = CrewEquipment::getDefaultItems();
        foreach ($defaultItems as $item) {
            if (!in_array($item, $existingItems)) {
                $karyawan->crewChecklists()->create([
                    'item_name' => $item,
                    'status' => 'tidak'
                ]);
            }
        }
        $checklistItems = $karyawan->crewChecklists()->orderBy('item_name')->get();

        // View khusus onboarding, jika belum ada bisa pakai view checklist-new
        return view('master-karyawan.crew-checklist-new', compact('karyawan', 'checklistItems'));
    }
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

        // Precompute counts for header summary
        $totalCount = Karyawan::count();
        $aktifCount = Karyawan::whereNull('tanggal_berhenti')->count();
        $berhentiCount = Karyawan::whereNotNull('tanggal_berhenti')->count();
        $counts = [
            'total' => $totalCount,
            'aktif' => $aktifCount,
            'berhenti' => $berhentiCount,
        ];

        // Prepare filter options
        $divisiOptions = Karyawan::whereNotNull('divisi')->distinct()->orderBy('divisi')->pluck('divisi');
        $cabangOptions = Karyawan::whereNotNull('cabang')->distinct()->orderBy('cabang')->pluck('cabang');

        // Filter Status Logic
        if ($request->filled('show_all')) {
            // Tampilkan semua (tidak ada filter status)
        } elseif ($request->filled('show_berhenti')) {
            // Tampilkan hanya yang berhenti
            $query->whereNotNull('tanggal_berhenti');
        } elseif ($request->filled('search')) {
            // Jika mencari tanpa tombol status diklik, cari di semua data (Aktif & Berhenti)
            // Ini untuk mencegah bingung kenapa data tidak muncul karena filter default 'Aktif'
        } else {
            // Default: hanya tampilkan karyawan aktif
            $query->whereNull('tanggal_berhenti');
        }

        if ($request->filled('divisi')) {
            $query->where('divisi', $request->divisi);
        }
        if ($request->filled('cabang')) {
            $query->where('cabang', $request->cabang);
        }

        // Filter: Tanggal Masuk range
        if ($request->filled('tanggal_masuk_start')) {
            $query->whereDate('tanggal_masuk', '>=', $request->tanggal_masuk_start);
        }
        if ($request->filled('tanggal_masuk_end')) {
            $query->whereDate('tanggal_masuk', '<=', $request->tanggal_masuk_end);
        }

        // Filter: Tanggal Berhenti range
        if ($request->filled('tanggal_berhenti_start')) {
            $query->whereDate('tanggal_berhenti', '>=', $request->tanggal_berhenti_start);
            // Auto trigger show_berhenti if filtering by stop date
            $request->merge(['show_berhenti' => '1']);
        }
        if ($request->filled('tanggal_berhenti_end')) {
            $query->whereDate('tanggal_berhenti', '<=', $request->tanggal_berhenti_end);
            // Auto trigger show_berhenti if filtering by stop date
            $request->merge(['show_berhenti' => '1']);
        }

        // Jika ada parameter search, lakukan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nik', 'LIKE', "%{$search}%")
                  ->orWhere('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nama_panggilan', 'LIKE', "%{$search}%")
                  ->orWhere('ktp', 'LIKE', "%{$search}%")
                  ->orWhere('kk', 'LIKE', "%{$search}%")
                  ->orWhere('plat', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('no_hp', 'LIKE', "%{$search}%")
                  ->orWhere('alamat', 'LIKE', "%{$search}%")
                  ->orWhere('alamat_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('kelurahan', 'LIKE', "%{$search}%")
                  ->orWhere('kecamatan', 'LIKE', "%{$search}%")
                  ->orWhere('kabupaten', 'LIKE', "%{$search}%")
                  ->orWhere('provinsi', 'LIKE', "%{$search}%")
                  ->orWhere('tempat_lahir', 'LIKE', "%{$search}%")
                  ->orWhere('agama', 'LIKE', "%{$search}%")
                  ->orWhere('divisi', 'LIKE', "%{$search}%")
                  ->orWhere('pekerjaan', 'LIKE', "%{$search}%")
                  ->orWhere('status_pajak', 'LIKE', "%{$search}%")
                  ->orWhere('nama_bank', 'LIKE', "%{$search}%")
                  ->orWhere('bank_cabang', 'LIKE', "%{$search}%")
                  ->orWhere('akun_bank', 'LIKE', "%{$search}%")
                  ->orWhere('atas_nama', 'LIKE', "%{$search}%")
                  ->orWhere('jkn', 'LIKE', "%{$search}%")
                  ->orWhere('no_ketenagakerjaan', 'LIKE', "%{$search}%")
                  ->orWhere('cabang', 'LIKE', "%{$search}%")
                  ->orWhere('supervisor', 'LIKE', "%{$search}%")
                  ->orWhere('catatan', 'LIKE', "%{$search}%")
                  ->orWhere('catatan_pekerjaan', 'LIKE', "%{$search}%");
            });
        }

        // Handle sorting
        $sortField = $request->get('sort', 'nama_lengkap'); // Default sort by nama_lengkap
        $sortDirection = $request->get('direction', 'asc'); // Default ascending

        // Validate sort field untuk keamanan
        $allowedSortFields = ['nama_lengkap', 'nik', 'nama_panggilan', 'divisi', 'pekerjaan', 'jkn', 'no_ketenagakerjaan', 'no_hp', 'email', 'status_pajak', 'tanggal_masuk', 'tanggal_berhenti'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'nama_lengkap';
        }

        // Validate sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Apply sorting
        $query->orderBy($sortField, $sortDirection);

        // Handle per_page parameter for pagination
        $perPage = (int) $request->get('per_page', 15); // Default 15 per halaman
        $allowedPerPage = [10, 15, 25, 50, 100, 200];
        
        // If show_all is requested, we still want pagination but maybe a larger default?
        // Let's keep it consistent with the user's selection or 15.
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 15;
        }

        // Menggunakan paginate dengan per_page yang dinamis
        $karyawans = $query->paginate($perPage)->appends($request->query());

        return view('master-karyawan.index', compact('karyawans', 'counts', 'divisiOptions', 'cabangOptions'));
    }

    /**
     * Export all karyawans as CSV download.
     */
    public function export(\Illuminate\Http\Request $request)
    {
        // Clear all possible caches to ensure fresh data
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        
        // Clear Laravel caches
        \Illuminate\Support\Facades\Cache::flush();
        
        // Force database connection refresh
        \Illuminate\Support\Facades\DB::reconnect();
        
        // Allow caller to specify separator via ?sep=, default to semicolon for Excel compatibility
        $sep = $request->query('sep', ';');
        
        // Decode URL encoded separator
        $sep = urldecode($sep);
        
        // Determine delimiter - force semicolon as default for better Excel compatibility
        $delimiter = ';';
        if ($sep === ',') {
            $delimiter = ',';
        } elseif ($sep === ';') {
            $delimiter = ';';
        } elseif ($sep === "\t") {
            $delimiter = "\t";
        }

        // Check if this is a template request
        $isTemplate = $request->query('template', false);

        $columns = [
            'nik','nama_panggilan','nama_lengkap','plat','email','ktp','kk','alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos','alamat_lengkap','tempat_lahir','tanggal_lahir','no_hp','jenis_kelamin','status_perkawinan','agama','divisi','pekerjaan','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya','catatan','status_pajak','nama_bank','bank_cabang','akun_bank','atas_nama','jkn','no_ketenagakerjaan','no_sim','sim_berlaku_mulai','sim_berlaku_sampai','cabang','nik_supervisor','supervisor'
        ];

        $fileName = $isTemplate ? 'template_import_karyawan.csv' : 'karyawans_export_' . date('Ymd_His') . '.csv';

        $callback = function() use ($columns, $delimiter, $isTemplate) {
            $out = fopen('php://output', 'w');

            // Write UTF-8 BOM for Excel recognition
            fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write header row with proper delimiter using manual method to avoid unwanted quotes
            fwrite($out, implode($delimiter, $columns) . "\r\n");

            if ($isTemplate) {
                // Add sample data row for template
                $sampleData = [
                    '1234567890', // nik - clean format without apostrophe
                    'John', // nama_panggilan
                    'John Doe', // nama_lengkap
                    'B 1234 ABC', // plat
                    'john.doe@example.com', // email
                    '1234567890123456', // ktp - clean format
                    '1234567890123456', // kk - clean format
                    'Jl. Contoh No. 123', // alamat
                    '001/002', // rt_rw
                    'Kelurahan Contoh', // kelurahan
                    'Kecamatan Contoh', // kecamatan
                    'Kabupaten Contoh', // kabupaten
                    'Provinsi Contoh', // provinsi
                    '12345', // kode_pos
                    'Jl. Contoh No. 123, RT 001/RW 002, Kelurahan Contoh', // alamat_lengkap
                    'Jakarta', // tempat_lahir
                    '01/Jan/1990', // tanggal_lahir - format dd/mmm/yyyy
                    '081234567890', // no_hp - clean format
                    'L', // jenis_kelamin
                    'Belum Kawin', // status_perkawinan
                    'Islam', // agama
                    'IT', // divisi
                    'Programmer', // pekerjaan
                    '01/Jan/2024', // tanggal_masuk - format dd/mmm/yyyy
                    '', // tanggal_berhenti
                    '', // tanggal_masuk_sebelumnya
                    '', // tanggal_berhenti_sebelumnya
                    'Catatan contoh', // catatan
                    'K1', // status_pajak
                    'Bank BCA', // nama_bank
                    'Cabang Jakarta Pusat', // bank_cabang
                    '1234567890', // akun_bank - clean format
                    'John Doe', // atas_nama
                    '0001234567890', // jkn - clean format
                    '12345678901234567', // no_ketenagakerjaan - clean format
                    'Jakarta', // cabang
                    '', // nik_supervisor
                    '' // supervisor
                ];
                fwrite($out, implode($delimiter, $sampleData) . "\r\n");
            } else {
                // Use database transaction to ensure data consistency
                \Illuminate\Support\Facades\DB::transaction(function() use ($out, $columns, $delimiter) {
                    // Stream rows for actual export - use fresh() to ensure latest data
                    Karyawan::chunk(200, function($rows) use ($out, $columns, $delimiter) {
                        foreach ($rows as $r) {
                            // Get completely fresh instance to ensure we have the latest data
                            $r = Karyawan::find($r->id);
                            
                            $line = [];
                            foreach ($columns as $col) {
                                $val = $r->{$col} ?? '';

                                // Format dates to dd/mmm/yyyy for CSV export
                                if ($val instanceof \DateTimeInterface) {
                                    $val = $val->format('d/M/Y');
                                } elseif (in_array($col, ['tanggal_lahir', 'tanggal_masuk', 'tanggal_berhenti', 'tanggal_masuk_sebelumnya', 'tanggal_berhenti_sebelumnya'])) {
                                    // Handle date fields - format if not empty, keep empty if null
                                    if (!empty($val)) {
                                        try {
                                            $ts = strtotime($val);
                                            if ($ts !== false && $ts !== -1) {
                                                $val = date('d/M/Y', $ts);
                                            }
                                        } catch (\Throwable $e) {
                                            // Keep original value if parsing fails
                                        }
                                    } else {
                                        // Keep empty for null dates
                                        $val = '';
                                    }
                                }

                                // Add zero-width space to numeric fields to prevent scientific notation in Excel
                                // This is invisible but forces Excel to treat as text
                                if (in_array($col, ['nik', 'ktp', 'kk', 'no_hp', 'akun_bank', 'jkn', 'no_ketenagakerjaan']) && !empty($val)) {
                                    $val = "\u{200B}" . $val;
                                }
                                
                                $line[] = $val;
                            }
                            fwrite($out, implode($delimiter, $line) . "\r\n");
                        }
                    });
                });
            }
            fclose($out);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => 'Thu, 01 Jan 1970 00:00:00 GMT',
            'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT'
        ]);
    }

    /**
     * Export all karyawans as Excel-compatible CSV download with proper number formatting
     */
    public function exportExcel()
    {
        // Clear all possible caches to ensure fresh data
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        
        // Clear Laravel caches
        \Illuminate\Support\Facades\Cache::flush();
        
        // Force database connection refresh
        \Illuminate\Support\Facades\DB::reconnect();
        
        $columns = [
            'nik','nama_panggilan','nama_lengkap','plat','email','ktp','kk','alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos','alamat_lengkap','tempat_lahir','tanggal_lahir','no_hp','jenis_kelamin','status_perkawinan','agama','divisi','pekerjaan','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya','catatan','status_pajak','nama_bank','bank_cabang','akun_bank','atas_nama','jkn','no_ketenagakerjaan','no_sim','sim_berlaku_mulai','sim_berlaku_sampai','cabang','nik_supervisor','supervisor'
        ];

        $fileName = 'karyawans_excel_export_' . date('Ymd_His') . '.csv';

        $callback = function() use ($columns) {
            $out = fopen('php://output', 'w');

            // Write UTF-8 BOM for Excel recognition
            fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write header row with semicolon delimiter for Excel CSV compatibility
            fwrite($out, implode(";", $columns) . "\r\n");

            // Use database transaction to ensure data consistency
            \Illuminate\Support\Facades\DB::transaction(function() use ($out, $columns) {
                // Stream rows for actual export with proper formatting - get fresh data
                Karyawan::chunk(200, function($rows) use ($out, $columns) {
                    foreach ($rows as $r) {
                        // Get completely fresh instance to ensure we have the latest data
                        $r = Karyawan::find($r->id);
                        
                        $line = [];
                        foreach ($columns as $col) {
                            $val = $r->{$col} ?? '';

                            // Format dates to dd/mmm/yyyy for Excel export
                            if ($val instanceof \DateTimeInterface) {
                                $val = $val->format('d/M/Y');
                            } elseif (in_array($col, ['tanggal_lahir', 'tanggal_masuk', 'tanggal_berhenti', 'tanggal_masuk_sebelumnya', 'tanggal_berhenti_sebelumnya'])) {
                                // Handle date fields - format if not empty, keep empty if null
                                if (!empty($val)) {
                                    try {
                                        $ts = strtotime($val);
                                        if ($ts !== false && $ts !== -1) {
                                            $val = date('d/M/Y', $ts);
                                        }
                                    } catch (\Throwable $e) {
                                        // Keep original value if parsing fails
                                    }
                                } else {
                                    // Keep empty for null dates
                                    $val = '';
                                }
                            }

                            // For numeric fields, add invisible zero-width space to prevent scientific notation
                            // This forces Excel to treat as text without showing visible characters
                            if (in_array($col, ['nik', 'ktp', 'kk', 'no_hp', 'akun_bank', 'jkn', 'no_ketenagakerjaan']) && !empty($val)) {
                                $val = "\u{200B}" . $val; // Zero-width space
                            }

                            // Clean any problematic characters that might cause issues in CSV
                            $val = str_replace(["\r", "\n"], ' ', $val); // Replace line breaks with space

                            $line[] = $val;
                        }
                        fwrite($out, implode(";", $line) . "\r\n");
                    }
                });
            });

            fclose($out);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => 'Thu, 01 Jan 1970 00:00:00 GMT',
            'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT'
        ]);
    }

    /**
     * Export empty karyawan data form as Excel (HTML format)
     */
    public function exportEmpty()
    {
        $karyawan = new Karyawan(); // Empty instance
        $fileName = 'form_karyawan_kosong_' . date('Ymd_His') . '.xls';

        return response(view('master-karyawan.export-excel-form', compact('karyawan')))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"")
            ->header('Cache-Control', 'max-age=0');
    }

    /**
     * Export single karyawan data as Excel (HTML format)
     */
    public function exportSingle(Karyawan $karyawan)
    {
        $safeName = preg_replace('/[^a-zA-Z0-9]/', '_', $karyawan->nama_lengkap);
        $fileName = 'karyawan_' . $safeName . '_' . date('Ymd_His') . '.xls';

        return response(view('master-karyawan.export-excel-form', compact('karyawan')))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"")
            ->header('Cache-Control', 'max-age=0');
    }


    /**
     * Download CSV template for import
     */
    public function downloadTemplate(\Illuminate\Http\Request $request)
    {
        // Generate template manually to ensure proper formatting
        $columns = [
            'nik','nama_panggilan','nama_lengkap','plat','email','ktp','kk','alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos','alamat_lengkap','tempat_lahir','tanggal_lahir','no_hp','jenis_kelamin','status_perkawinan','agama','divisi','pekerjaan','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya','catatan','status_pajak','nama_bank','bank_cabang','akun_bank','atas_nama','jkn','no_ketenagakerjaan','no_sim','sim_berlaku_mulai','sim_berlaku_sampai','cabang','nik_supervisor','supervisor'
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
            'Cabang Jakarta Pusat', // bank_cabang
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
            'Cabang bank', // bank_cabang
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

            // Write instruction row for format guidance (clean data without quotes)
            $cleanInstructions = array_map(function($field) {
                // Clean any problematic characters
                return str_replace(["\r", "\n"], ' ', $field);
            }, $instructionData);

            fwrite($out, implode(';', $cleanInstructions) . "\r\n");

            // Write sample data manually with semicolon delimiter (clean data without quotes)
            $cleanData = array_map(function($field) {
                // Clean any problematic characters
                return str_replace(["\r", "\n"], ' ', $field);
            }, $sampleData);

            fwrite($out, implode(';', $cleanData) . "\r\n");
            fclose($out);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Download Excel template for import
     */
    public function downloadExcelTemplate()
    {
        $columns = [
            'nik','nama_panggilan','nama_lengkap','plat','email','ktp','kk','alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos','alamat_lengkap','tempat_lahir','tanggal_lahir','no_hp','jenis_kelamin','status_perkawinan','agama','divisi','pekerjaan','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya','catatan','catatan_pekerjaan','status_pajak','nama_bank','bank_cabang','akun_bank','atas_nama','jkn','no_ketenagakerjaan','no_sim','sim_berlaku_mulai','sim_berlaku_sampai','cabang','nik_supervisor','supervisor'
        ];

        $instructionData = [
            'Format: Text (pastikan tidak scientific notation)', // nik
            'Nama panggilan', // nama_panggilan
            'Nama lengkap sesuai KTP', // nama_lengkap
            'Nomor plat kendaraan', // plat
            'Email aktif', // email
            'Format: Text 16 digit', // ktp
            'Format: Text 16 digit', // kk
            'Alamat sesuai KTP', // alamat
            'RT/RW', // rt_rw
            'Kelurahan', // kelurahan
            'Kecamatan', // kecamatan
            'Kabupaten', // kabupaten
            'Provinsi', // provinsi
            'Kode pos', // kode_pos
            'Alamat lengkap gabungan', // alamat_lengkap
            'Tempat lahir', // tempat_lahir
            'Format: DD/MM/YYYY atau DD/MMM/YYYY atau YYYY-MM-DD', // tanggal_lahir
            'Format: Text nomor telepon', // no_hp
            'L atau P', // jenis_kelamin
            'Status perkawinan', // status_perkawinan
            'Agama', // agama
            'Divisi kerja', // divisi
            'Jabatan/pekerjaan', // pekerjaan
            'Format: DD/MM/YYYY atau DD/MMM/YYYY atau YYYY-MM-DD', // tanggal_masuk
            'Format: DD/MM/YYYY atau DD/MMM/YYYY (kosong jika aktif)', // tanggal_berhenti
            'Format: DD/MM/YYYY atau DD/MMM/YYYY atau YYYY-MM-DD', // tanggal_masuk_sebelumnya
            'Format: DD/MM/YYYY atau DD/MMM/YYYY atau YYYY-MM-DD', // tanggal_berhenti_sebelumnya
            'Catatan umum', // catatan
            'Catatan terkait pekerjaan', // catatan_pekerjaan
            'Status pajak (TK0/TK1/K0/K1/K2/K3/K/0/K/1)', // status_pajak
            'Nama bank', // nama_bank
            'Cabang bank', // bank_cabang
            'Format: Text nomor rekening', // akun_bank
            'Atas nama rekening', // atas_nama
            'Format: Text nomor JKN', // jkn
            'Format: Text nomor ketenagakerjaan', // no_ketenagakerjaan
            'Cabang/lokasi kerja', // cabang
            'NIK supervisor', // nik_supervisor
            'Nama supervisor' // supervisor
        ];

        // Create Excel-compatible CSV file
        $fileName = 'template_import_karyawan_excel.csv';

        $callback = function() use ($columns, $instructionData) {
            $out = fopen('php://output', 'w');

            // Write UTF-8 BOM for Excel recognition
            fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write header manually with semicolon delimiter for Excel
            fwrite($out, implode(';', $columns) . "\r\n");

            // Write instruction row only (no sample data, clean without quotes)
            $cleanInstructions = array_map(function($field) {
                // Clean any problematic characters
                return str_replace(["\r", "\n"], ' ', $field);
            }, $instructionData);

            fwrite($out, implode(';', $cleanInstructions) . "\r\n");

            // Add one empty row for user to start entering data
            $emptyRow = array_fill(0, count($columns), '');
            fwrite($out, implode(';', $emptyRow) . "\r\n");

            fclose($out);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Download simple Excel template with headers only (no instructions)
     */
    public function downloadSimpleExcelTemplate()
    {
        $columns = [
            'nik','nama_panggilan','nama_lengkap','plat','email','ktp','kk','alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos','alamat_lengkap','tempat_lahir','tanggal_lahir','no_hp','jenis_kelamin','status_perkawinan','agama','divisi','pekerjaan','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya','catatan','catatan_pekerjaan','status_pajak','nama_bank','bank_cabang','akun_bank','atas_nama','jkn','no_ketenagakerjaan','no_sim','sim_berlaku_mulai','sim_berlaku_sampai','cabang','nik_supervisor','supervisor'
        ];

        // Create simple Excel-compatible CSV file with headers only
        $fileName = 'template_simple_karyawan_excel.csv';

        $callback = function() use ($columns) {
            $out = fopen('php://output', 'w');

            // Write UTF-8 BOM for Excel recognition
            fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write header only with semicolon delimiter for Excel
            fwrite($out, implode(';', $columns) . "\r\n");

            // Add one empty row for user to start entering data
            $emptyRow = array_fill(0, count($columns), '');
            fwrite($out, implode(';', $emptyRow) . "\r\n");

            fclose($out);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Export karyawan dengan format Excel Indonesia (koma delimiter, quotes untuk koma dalam data)
     */
    public function exportExcelIndonesia()
    {
        // Clear all possible caches to ensure fresh data
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        
        // Clear Laravel caches
        \Illuminate\Support\Facades\Cache::flush();
        
        // Force database connection refresh
        \Illuminate\Support\Facades\DB::reconnect();
        
        $columns = [
            'nik','nama_panggilan','nama_lengkap','plat','email','ktp','kk','alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos','alamat_lengkap','tempat_lahir','tanggal_lahir','no_hp','jenis_kelamin','status_perkawinan','agama','divisi','pekerjaan','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya','catatan','catatan_pekerjaan','status_pajak','nama_bank','bank_cabang','akun_bank','atas_nama','jkn','no_ketenagakerjaan','no_sim','sim_berlaku_mulai','sim_berlaku_sampai','cabang','nik_supervisor','supervisor'
        ];

        $fileName = 'karyawans_excel_indonesia_' . date('Ymd_His') . '.csv';

        $callback = function() use ($columns) {
            $out = fopen('php://output', 'w');

            // Write UTF-8 BOM for Excel recognition
            fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write header row using fputcsv with comma delimiter and quotes
            fputcsv($out, $columns, ',', '"');

            // Use database transaction to ensure data consistency
            \Illuminate\Support\Facades\DB::transaction(function() use ($out, $columns) {
                // Stream rows for actual export with proper formatting - get fresh data
                Karyawan::chunk(200, function($rows) use ($out, $columns) {
                    foreach ($rows as $r) {
                        // Get completely fresh instance to ensure we have the latest data
                        $r = Karyawan::find($r->id);
                        
                        $line = [];
                        foreach ($columns as $col) {
                            $val = $r->{$col} ?? '';

                            // Format dates to dd/mmm/yyyy for Excel export
                            if ($val instanceof \DateTimeInterface) {
                                $val = $val->format('d/M/Y');
                            } elseif (in_array($col, ['tanggal_lahir', 'tanggal_masuk', 'tanggal_berhenti', 'tanggal_masuk_sebelumnya', 'tanggal_berhenti_sebelumnya'])) {
                                // Handle date fields - format if not empty, keep empty if null
                                if (!empty($val)) {
                                    try {
                                        $ts = strtotime($val);
                                        if ($ts !== false && $ts !== -1) {
                                            $val = date('d/M/Y', $ts);
                                        }
                                    } catch (\Throwable $e) {
                                        // Keep original value if parsing fails
                                    }
                                } else {
                                    // Keep empty for null dates
                                    $val = '';
                                }
                            }

                            // For numeric fields, add invisible zero-width space to prevent scientific notation
                            // This forces Excel to treat as text without showing visible characters
                            if (in_array($col, ['nik', 'ktp', 'kk', 'no_hp', 'akun_bank', 'jkn', 'no_ketenagakerjaan']) && !empty($val)) {
                                $val = "\u{200B}" . $val; // Zero-width space
                            }

                            $line[] = $val;
                        }
                        // Use fputcsv with comma delimiter and quotes to handle commas in data
                        fputcsv($out, $line, ',', '"');
                    }
                });
            });

            fclose($out);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => 'Thu, 01 Jan 1970 00:00:00 GMT',
            'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT'
        ]);
    }

    /**
     * Menampilkan formulir untuk membuat karyawan baru.
     */
    public function create()
    {
        $divisis = Divisi::active()->orderBy('nama_divisi')->get();
        $pekerjaans = Pekerjaan::active()->orderBy('nama_pekerjaan')->get();
        $pajaks = Pajak::orderBy('nama_status')->get();
        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $banks = Bank::orderBy('name')->get();

        // Group pekerjaan by divisi for JavaScript
        $pekerjaanByDivisi = [];
        foreach ($pekerjaans as $pekerjaan) {
            $divisi = $pekerjaan->divisi ?? '';
            if (!isset($pekerjaanByDivisi[$divisi])) {
                $pekerjaanByDivisi[$divisi] = [];
            }
            $pekerjaanByDivisi[$divisi][] = $pekerjaan->nama_pekerjaan;
        }

        // Generate next NIK for auto-fill (Optional, but user want to remove automatic feature)
        $nextNik = ''; // Karyawan::generateNextNik();

        return view('master-karyawan.create', compact('divisis', 'pekerjaans', 'pajaks', 'cabangs', 'banks', 'pekerjaanByDivisi', 'nextNik'));
    }

    /**
     * Menyimpan data karyawan baru ke dalam database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|max:255|unique:karyawans',
            'nama_panggilan' => 'required|string|max:255|unique:karyawans',
            'nama_lengkap' => 'required|string|max:255',
            'plat' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:karyawans,email,NULL,id,email,NULL',
            'ktp' => 'nullable|string|regex:/^[0-9]{16}$/|unique:karyawans',
            'kk' => 'nullable|string|regex:/^[0-9]{16}$/|unique:karyawans',
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
            'catatan_pekerjaan' => 'nullable|string|max:1000',
            'catatan' => 'nullable|string|max:1000',
            'status_pajak' => 'nullable|string|max:255',
            'nama_bank' => 'nullable|string|max:255',
            'bank_cabang' => 'nullable|string|max:255',
            'akun_bank' => 'nullable|string|max:255',
            'atas_nama' => 'nullable|string|max:255',
            'jkn' => 'nullable|string|max:255',
            'no_ketenagakerjaan' => 'nullable|string|max:255',
            'no_sim' => 'nullable|string|max:255',
            'sim_berlaku_mulai' => 'nullable|date',
            'sim_berlaku_sampai' => 'nullable|date',
            'cabang' => 'nullable|string|max:255',
            'nik_supervisor' => 'nullable|string|max:255',
            'supervisor' => 'nullable|string|max:255',
            'family_members' => 'nullable|array',
            'family_members.*.hubungan' => 'nullable|string|max:255',
            'family_members.*.nama' => 'nullable|string|max:255',
            'family_members.*.tanggal_lahir' => 'nullable|date',
            'family_members.*.alamat' => 'nullable|string|max:500',
            'family_members.*.no_telepon' => 'nullable|string|max:20',
            'family_members.*.nik_ktp' => 'nullable|string|regex:/^[0-9]{16}$/',
        ], [
            'ktp.regex' => 'Nomor KTP harus berupa 16 digit angka saja, tidak boleh ada huruf.',
            'kk.regex' => 'Nomor KK harus berupa 16 digit angka saja, tidak boleh ada huruf.',
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
            'ktp.unique' => 'Nomor KTP sudah terdaftar dalam sistem.',
            'kk.unique' => 'Nomor KK sudah terdaftar dalam sistem.',
            'family_members.*.nik_ktp.regex' => 'NIK/KTP anggota keluarga harus berupa 16 digit angka saja.',
        ]);

        // Data conversion and processing...
        
        // Convert data to uppercase except email and family_members
        $familyMembers = $validated['family_members'] ?? [];
        unset($validated['family_members']); // Remove from main data

        foreach ($validated as $key => $value) {
            if ($value !== null && $key !== 'email') {
                $validated[$key] = strtoupper($value);
            }
        }

        //Simpan data dalam database
        $karyawan = Karyawan::create($validated);

        // Handle family members
        if (!empty($familyMembers)) {
            foreach ($familyMembers as $memberData) {
                if (!empty($memberData['hubungan']) && !empty($memberData['nama'])) {
                    // Convert family member data to uppercase except date fields
                    foreach ($memberData as $key => $value) {
                        if ($value !== null && $key !== 'tanggal_lahir') {
                            $memberData[$key] = strtoupper($value);
                        }
                    }

                    $karyawan->familyMembers()->create($memberData);
                }
            }
        }

        if ($karyawan->isAbk()) {
            return redirect()->route('master.karyawan.crew-checklist-new', $karyawan->id)
                ->with('success', 'Data karyawan berhasil ditambahkan. Silakan lengkapi checklist kelengkapan crew.');
        }
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
        // Jika ada parameter onboarding, gunakan view onboarding-full
        if (request()->has('onboarding') && request()->onboarding == '1') {
            // Ambil data divisis dan pekerjaans untuk dropdown
            $divisis = \App\Models\Divisi::active()->orderBy('nama_divisi')->get();
            $pekerjaans = \App\Models\Pekerjaan::active()->orderBy('nama_pekerjaan')->get();
            $cabangs = \App\Models\Cabang::orderBy('nama_cabang')->get();
            $pajaks = \App\Models\Pajak::orderBy('nama_status')->get();
            $banks = \App\Models\Bank::orderBy('name')->get();

            // Group pekerjaan by divisi for JavaScript
            $pekerjaanByDivisi = [];
            foreach ($pekerjaans as $pekerjaan) {
                $divisi = $pekerjaan->divisi ?? '';
                if (!isset($pekerjaanByDivisi[$divisi])) {
                    $pekerjaanByDivisi[$divisi] = [];
                }
                $pekerjaanByDivisi[$divisi][] = $pekerjaan->nama_pekerjaan;
            }

            return view('karyawan.onboarding-full', compact('karyawan', 'divisis', 'pekerjaans', 'cabangs', 'pajaks', 'banks', 'pekerjaanByDivisi'));
        }

        // Ambil data untuk dropdown di form edit
        $divisis = Divisi::active()->orderBy('nama_divisi')->get();
        $pekerjaans = Pekerjaan::active()->orderBy('nama_pekerjaan')->get();
        $pajaks = Pajak::orderBy('nama_status')->get();
        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $banks = Bank::orderBy('name')->get();

        // Group pekerjaan by divisi for JavaScript
        $pekerjaanByDivisi = [];
        foreach ($pekerjaans as $pekerjaan) {
            $divisi = $pekerjaan->divisi ?? '';
            if (!isset($pekerjaanByDivisi[$divisi])) {
                $pekerjaanByDivisi[$divisi] = [];
            }
            $pekerjaanByDivisi[$divisi][] = $pekerjaan->nama_pekerjaan;
        }

        return view('master-karyawan.edit', compact('karyawan', 'divisis', 'pekerjaans', 'pajaks', 'cabangs', 'banks', 'pekerjaanByDivisi'));
    }

    /**
     * Menampilkan form edit khusus untuk onboarding karyawan baru (public)
     */
    public function onboardingEdit(Karyawan $karyawan)
    {
        // Ambil data divisis dan pekerjaans untuk dropdown
        $divisis = \App\Models\Divisi::active()->orderBy('nama_divisi')->get();
        $pekerjaans = \App\Models\Pekerjaan::active()->orderBy('nama_pekerjaan')->get();
        $cabangs = \App\Models\Cabang::orderBy('nama_cabang')->get();
        $pajaks = \App\Models\Pajak::orderBy('nama_status')->get();
        $banks = \App\Models\Bank::orderBy('name')->get();

        // Group pekerjaan by divisi for JavaScript
        $pekerjaanByDivisi = [];
        foreach ($pekerjaans as $pekerjaan) {
            $divisi = $pekerjaan->divisi ?? '';
            if (!isset($pekerjaanByDivisi[$divisi])) {
                $pekerjaanByDivisi[$divisi] = [];
            }
            $pekerjaanByDivisi[$divisi][] = $pekerjaan->nama_pekerjaan;
        }

        return view('karyawan.onboarding-full', compact('karyawan', 'divisis', 'pekerjaans', 'cabangs', 'pajaks', 'banks', 'pekerjaanByDivisi'));
    }

    /**
     * Memperbarui data karyawan di database.
     */
    public function update(Request $request, Karyawan $karyawan)
    {
        // Anda perlu menambahkan logika validasi di sini, mirip dengan metode store()
        $validated = $request->validate([
            'nik' => ['required', 'string', 'regex:/^[0-9]+$/', Rule::unique('karyawans')->ignore($karyawan->id)],
            'nama_panggilan' => ['required', 'string', 'max:255', Rule::unique('karyawans')->ignore($karyawan->id)],
            'nama_lengkap' => 'required|string|max:255',
            'plat' => 'nullable|string|max:255',
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('karyawans')->ignore($karyawan->id)->whereNotNull('email')->where('email', '!=', '')],
            'ktp' => ['nullable', 'string', 'regex:/^[0-9]{16}$/', Rule::unique('karyawans')->ignore($karyawan->id)->whereNotNull('ktp')->where('ktp', '!=', '')],
            'kk' => ['nullable', 'string', 'regex:/^[0-9]{16}$/'],
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
            'catatan_pekerjaan' => 'nullable|string|max:1000',
            'catatan' => 'nullable|string|max:1000',
            'status_pajak' => 'nullable|string|max:255',
            'nama_bank' => 'nullable|string|max:255',
            'bank_cabang' => 'nullable|string|max:255',
            'akun_bank' => 'nullable|string|max:255',
            'atas_nama' => 'nullable|string|max:255',
            'jkn' => 'nullable|string|max:255',
            'no_ketenagakerjaan' => 'nullable|string|max:255',
            'cabang' => 'nullable|string|max:255',
            'nik_supervisor' => 'nullable|string|max:255',
            'supervisor' => 'nullable|string|max:255',
            'family_members' => 'nullable|array',
            'family_members.*.id' => 'nullable|integer|exists:karyawan_family_members,id',
            'family_members.*.hubungan' => 'nullable|string|max:255',
            'family_members.*.nama' => 'nullable|string|max:255',
            'family_members.*.tanggal_lahir' => 'nullable|date',
            'family_members.*.alamat' => 'nullable|string|max:500',
            'family_members.*.no_telepon' => 'nullable|string|max:20',
            'family_members.*.nik_ktp' => 'nullable|string|regex:/^[0-9]{16}$/',
        ], [
            'nik.regex' => 'NIK harus berupa angka.',
            'ktp.regex' => 'Nomor KTP harus berupa 16 digit angka.',
            'kk.regex' => 'Nomor KK harus berupa 16 digit angka.',
            'nik.unique' => 'NIK sudah terdaftar dalam sistem.',
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
            'ktp.unique' => 'Nomor KTP sudah terdaftar dalam sistem.',
            'family_members.*.nik_ktp.regex' => 'NIK/KTP anggota keluarga harus berupa 16 digit angka saja.',
        ]);

        // Handle family members data separately
        $familyMembers = $validated['family_members'] ?? [];
        unset($validated['family_members']); // Remove from main data

        // Convert data to uppercase except email
        foreach ($validated as $key => $value) {
            if ($value !== null && $key !== 'email') {
                $validated[$key] = strtoupper($value);
            }
        }

        // Use database transaction to ensure data integrity
        DB::transaction(function () use ($karyawan, $validated, $familyMembers) {
            $karyawan->update($validated);

            // Handle family members update
            if (isset($familyMembers)) {
                // Get existing family member IDs to track which ones to keep
                $existingIds = collect($familyMembers)->pluck('id')->filter()->toArray();

                // Delete family members that are no longer in the form
                $karyawan->familyMembers()->whereNotIn('id', $existingIds)->delete();

                // Update or create family members
                foreach ($familyMembers as $memberData) {
                    if (!empty($memberData['hubungan']) && !empty($memberData['nama'])) {
                        // Convert family member data to uppercase except date fields
                        foreach ($memberData as $key => $value) {
                            if ($value !== null && $key !== 'tanggal_lahir' && $key !== 'id') {
                                $memberData[$key] = strtoupper($value);
                            }
                        }

                        if (!empty($memberData['id'])) {
                            // Update existing family member
                            $familyMember = $karyawan->familyMembers()->find($memberData['id']);
                            if ($familyMember) {
                                unset($memberData['id']); // Remove id from update data
                                $familyMember->update($memberData);
                            }
                        } else {
                            // Create new family member
                            unset($memberData['id']); // Remove id field for new records
                            $karyawan->familyMembers()->create($memberData);
                        }
                    }
                }
            }
        });

        // Clear any potential cache after update to ensure fresh data
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        // Jika akses dari onboarding, redirect ke crew checklist
        if (request()->has('onboarding') && request()->onboarding == '1') {
            if ($karyawan->isAbk()) {
                return redirect()->route('karyawan.onboarding-crew-checklist', $karyawan->id)
                    ->with('success', 'Data karyawan berhasil diperbarui. Silakan lengkapi checklist crew.');
            } else {
                return redirect()->route('dashboard')->with('success', 'Data karyawan berhasil diperbarui.');
            }
        }

        return redirect()->route('master.karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    /**
     * Update data karyawan khusus untuk onboarding (public)
     */
    public function onboardingUpdate(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'regex:/^[0-9]+$/', Rule::unique('karyawans')->ignore($karyawan->id)],
            'nama_panggilan' => 'required|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'plat' => 'nullable|string|max:255',
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('karyawans')->ignore($karyawan->id)->whereNotNull('email')],
            'ktp' => ['nullable', 'string', 'regex:/^[0-9]{16}$/', Rule::unique('karyawans')->ignore($karyawan->id)],
            'kk' => ['nullable', 'string', 'regex:/^[0-9]{16}$/', Rule::unique('karyawans')->ignore($karyawan->id)],
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
            'bank_cabang' => 'nullable|string|max:255',
            'akun_bank' => 'nullable|string|max:255',
            'atas_nama' => 'nullable|string|max:255',
            'jkn' => 'nullable|string|max:255',
            'no_ketenagakerjaan' => 'nullable|string|max:255',
            'no_ketenagakerjaan' => 'nullable|string|max:255',
            'no_sim' => 'nullable|string|max:255',
            'sim_berlaku_mulai' => 'nullable|date',
            'sim_berlaku_sampai' => 'nullable|date',
            'cabang' => 'nullable|string|max:255',
            'nik_supervisor' => 'nullable|string|max:255',
            'supervisor' => 'nullable|string|max:255',
        ], [
            'nik.regex' => 'NIK harus berupa angka.',
            'ktp.regex' => 'Nomor KTP harus berupa 16 digit angka.',
            'kk.regex' => 'Nomor KK harus berupa 16 digit angka.',
            'nik.unique' => 'NIK sudah terdaftar dalam sistem.',
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
            'ktp.unique' => 'Nomor KTP sudah terdaftar dalam sistem.',
        ]);

        // Convert data to uppercase except email
        foreach ($validated as $key => $value) {
            if ($value !== null && $key !== 'email') {
                $validated[$key] = strtoupper($value);
            }
        }

        $karyawan->update($validated);

        // Set user status to approved after successful onboarding
        $user = Auth::user();
        if ($user && $user->status !== 'approved') {
            $user->status = 'approved';
            $user->approved_at = now();
            $user->save();
        }

        // Untuk onboarding, selalu redirect ke crew checklist jika ABK
        if ($karyawan->isAbk()) {
            return redirect()->route('karyawan.onboarding-crew-checklist', $karyawan->id)
                ->with('success', 'Data karyawan berhasil diperbarui. Silakan lengkapi checklist crew.');
        } else {
            Auth::logout();
            return redirect()->route('login')->with('success', 'Data karyawan berhasil diperbarui. Silakan login kembali untuk melanjutkan.');
        }
    }

    /**
     * Menghapus data karyawan dari database.
     */
    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();
        return redirect()->back()->with('success', 'Data karyawan berhasil dihapus.');
    }

    /**
     * Menampilkan halaman print-friendly yang berisi semua field karyawan
     */
    /**
     * Menampilkan halaman print-friendly yang berisi semua field karyawan
     */
    public function print(Request $request)
    {
        $query = Karyawan::query();

        // Filter logic copied from index
        if ($request->filled('show_all')) {
            // show_all -> tampilkan semua karyawan (tidak menambah where)
        } elseif ($request->filled('show_berhenti')) {
            $query->whereNotNull('tanggal_berhenti');
        } else {
            // Default: hanya tampilkan karyawan aktif (belum berhenti)
            $query->whereNull('tanggal_berhenti');
        }

        if ($request->filled('divisi')) {
            $query->where('divisi', $request->divisi);
        }
        if ($request->filled('cabang')) {
            $query->where('cabang', $request->cabang);
        }

        // Filter: Tanggal Masuk range
        if ($request->filled('tanggal_masuk_start')) {
            $query->whereDate('tanggal_masuk', '>=', $request->tanggal_masuk_start);
        }
        if ($request->filled('tanggal_masuk_end')) {
            $query->whereDate('tanggal_masuk', '<=', $request->tanggal_masuk_end);
        }

        // Filter: Tanggal Berhenti range
        if ($request->filled('tanggal_berhenti_start')) {
            $query->whereDate('tanggal_berhenti', '>=', $request->tanggal_berhenti_start);
        }
        if ($request->filled('tanggal_berhenti_end')) {
            $query->whereDate('tanggal_berhenti', '<=', $request->tanggal_berhenti_end);
        }

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

        // Handle sorting
        $sortField = $request->get('sort', 'nama_lengkap');
        $sortDirection = $request->get('direction', 'asc');

        $allowedSortFields = ['nama_lengkap', 'nik', 'nama_panggilan', 'divisi', 'pekerjaan', 'jkn', 'no_ketenagakerjaan', 'no_hp', 'email', 'status_pajak', 'tanggal_masuk', 'tanggal_berhenti'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'nama_lengkap';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $query->orderBy($sortField, $sortDirection);

        $karyawans = $query->get();
        return view('master-karyawan.print', compact('karyawans'));
    }

    /**
     * Print multiple forms based on filters
     */
    public function printForms(Request $request)
    {
        $query = Karyawan::query();

        // Filter logic copied from index
        if ($request->filled('show_all')) {
            // show_all -> tampilkan semua karyawan (tidak menambah where)
        } elseif ($request->filled('show_berhenti')) {
            $query->whereNotNull('tanggal_berhenti');
        } else {
            // Default: hanya tampilkan karyawan aktif (belum berhenti)
            $query->whereNull('tanggal_berhenti');
        }

        if ($request->filled('divisi')) {
            $query->where('divisi', $request->divisi);
        }
        if ($request->filled('cabang')) {
            $query->where('cabang', $request->cabang);
        }

        // Filter: Tanggal Masuk range
        if ($request->filled('tanggal_masuk_start')) {
            $query->whereDate('tanggal_masuk', '>=', $request->tanggal_masuk_start);
        }
        if ($request->filled('tanggal_masuk_end')) {
            $query->whereDate('tanggal_masuk', '<=', $request->tanggal_masuk_end);
        }

        // Filter: Tanggal Berhenti range
        if ($request->filled('tanggal_berhenti_start')) {
            $query->whereDate('tanggal_berhenti', '>=', $request->tanggal_berhenti_start);
        }
        if ($request->filled('tanggal_berhenti_end')) {
            $query->whereDate('tanggal_berhenti', '<=', $request->tanggal_berhenti_end);
        }

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

        // Handle sorting
        $sortField = $request->get('sort', 'nama_lengkap');
        $sortDirection = $request->get('direction', 'asc');

        $allowedSortFields = ['nama_lengkap', 'nik', 'nama_panggilan', 'divisi', 'pekerjaan', 'jkn', 'no_ketenagakerjaan', 'no_hp', 'email', 'status_pajak', 'tanggal_masuk', 'tanggal_berhenti'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'nama_lengkap';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $query->orderBy($sortField, $sortDirection);

        $karyawans = $query->get();
        return view('master-karyawan.print-bulk', compact('karyawans'));
    }

    /**
     * Print a single karyawan in a detailed form layout.
     */
    public function printSingle(Karyawan $karyawan)
    {
        return view('master-karyawan.print-single', compact('karyawan'));
    }

    /**
     * Print an empty form layout.
     */
    public function printEmpty()
    {
        $karyawan = new Karyawan();
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
     * Handle CSV/Excel import (expects header row with column names matching model attributes like nik,nama_lengkap,...)
     */
    public function importStore(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        $file = $request->file('csv_file');
        $extension = $file->getClientOriginalExtension();
        $path = $file->getRealPath();

        // Handle Excel files by converting to CSV first
        if (in_array($extension, ['xlsx', 'xls'])) {
            $csvData = $this->convertExcelToCsv($path, $extension);
            if (!$csvData) {
                return redirect()->route('master.karyawan.index')
                    ->with('error', 'Gagal membaca file Excel. Pastikan file tidak corrupt.');
            }

            // Create temporary CSV file
            $tempPath = tempnam(sys_get_temp_dir(), 'excel_import_');
            file_put_contents($tempPath, $csvData);
            $path = $tempPath;
        }

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
                    'nama_bank','bank_cabang','akun_bank','atas_nama','status_pajak','jkn','no_ketenagakerjaan','no_sim','sim_berlaku_mulai','sim_berlaku_sampai','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya','catatan','catatan_pekerjaan'
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

                    // Handle dd/mm/yyyy format (17/02/2020)
                    if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $val, $matches)) {
                        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                        $year = $matches[3];
                        return $year . '-' . $month . '-' . $day;
                    }

                    // Handle dd-mm-yyyy format (17-02-2020)
                    if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $val, $matches)) {
                        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                        $year = $matches[3];
                        return $year . '-' . $month . '-' . $day;
                    }

                    // Handle dd/mmm/yyyy format (15/Jan/2024)
                    if (preg_match('/^(\d{1,2})\/([A-Za-z]{3})\/(\d{4})$/', $val, $matches)) {
                        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        $monthAbbr = strtolower($matches[2]);
                        $year = $matches[3];

                        // Map month abbreviations to numbers
                        $monthMap = [
                            'jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04',
                            'may' => '05', 'mei' => '05', 'jun' => '06', 'jul' => '07',
                            'aug' => '08', 'agu' => '08', 'sep' => '09', 'oct' => '10',
                            'okt' => '10', 'nov' => '11', 'dec' => '12', 'des' => '12'
                        ];

                        if (isset($monthMap[$monthAbbr])) {
                            return $year . '-' . $monthMap[$monthAbbr] . '-' . $day;
                        }
                    }

                    // Handle dd-mmm-yyyy format (15-Jan-2024)
                    if (preg_match('/^(\d{1,2})-([A-Za-z]{3})-(\d{4})$/', $val, $matches)) {
                        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        $monthAbbr = strtolower($matches[2]);
                        $year = $matches[3];

                        // Map month abbreviations to numbers
                        $monthMap = [
                            'jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04',
                            'may' => '05', 'mei' => '05', 'jun' => '06', 'jul' => '07',
                            'aug' => '08', 'agu' => '08', 'sep' => '09', 'oct' => '10',
                            'okt' => '10', 'nov' => '11', 'dec' => '12', 'des' => '12'
                        ];

                        if (isset($monthMap[$monthAbbr])) {
                            return $year . '-' . $monthMap[$monthAbbr] . '-' . $day;
                        }
                    }

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

    // Return the simplified new view (old view file removed)
    return view('master-karyawan.crew-checklist-new', compact('karyawan', 'checklistItems'));
    }

    /**
     * NEW: Simplified crew checklist page
     */
    public function crewChecklistNew($id)
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

        return view('master-karyawan.crew-checklist-new', compact('karyawan', 'checklistItems'));
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

        // Use DB transaction and handle failures so user sees a warning on failure
        try {
            DB::beginTransaction();

            // Debug: log incoming payload so we can inspect what the client actually sent
            try {
                Log::debug('updateCrewChecklist incoming request', $request->all());
            } catch (\Throwable $e) {
                // swallow logging errors to avoid breaking main flow
                Log::debug('updateCrewChecklist logging failed: ' . $e->getMessage());
            }

            // Validate that checklist exists and keep raw data so keys (item IDs) are preserved
            $request->validate([
                'checklist' => 'required|array'
            ]);

            $rawChecklist = $request->input('checklist', []);

            // Debug: log raw payload for extra insight
            Log::debug('updateCrewChecklist raw checklist payload', ['count' => count($rawChecklist)]);

            // Server-side status rule: if nomor_sertifikat has 4 or more alphanumeric chars -> ada, else tidak
            // (changed from exact-4 to at-least-4 to match new requirement)
            $fourAlnumPattern = '/^[A-Za-z0-9]{4,}$/';

            foreach ($rawChecklist as $itemId => $data) {
                // Normalize empty strings to null so 'nullable' rules accept empty HTML inputs
                $dataNormalized = array_map(function($v) {
                    return $v === '' ? null : $v;
                }, $data);

                // Validate each row individually to avoid losing original keys
                $rowValidated = \Illuminate\Support\Facades\Validator::make($dataNormalized, [
                    'item_name' => 'nullable|string|max:255',
                    'nomor_sertifikat' => 'nullable|string|max:255',
                    'issued_date' => 'nullable|date',
                    'expired_date' => 'nullable|date|after_or_equal:issued_date',
                    'catatan' => 'nullable|string|max:500'
                ])->validate();

                $checklist = $karyawan->crewChecklists()->find($itemId);

                // If checklist not found and item_name provided (new_x), create it
                if (!$checklist && !empty($rowValidated['item_name'])) {
                    $checklist = $karyawan->crewChecklists()->create([
                        'item_name' => $rowValidated['item_name'],
                        'status' => 'tidak',
                    ]);
                }

                if ($checklist) {
                    $nomor = isset($rowValidated['nomor_sertifikat']) ? trim($rowValidated['nomor_sertifikat']) : null;
                    $status = ($nomor && preg_match($fourAlnumPattern, $nomor)) ? 'ada' : 'tidak';

                    // If status is 'tidak', ensure dates are cleared server-side as well
                    $issued = ($status === 'ada') ? ($rowValidated['issued_date'] ?? null) : null;
                    $expired = ($status === 'ada') ? ($rowValidated['expired_date'] ?? null) : null;

                    $checklist->update([
                        'status' => $status,
                        'nomor_sertifikat' => $nomor,
                        'issued_date' => $issued ?: null,
                        'expired_date' => $expired ?: null,
                        'catatan' => $rowValidated['catatan'] ?? null
                    ]);
                }
            }

            DB::commit();

            // For onboarding crew checklist completion, redirect to dashboard
            // so users can see "setup permission" message if they don't have permissions
            if (request()->routeIs('karyawan.crew-checklist.update') ||
                str_contains(request()->url(), 'onboarding')) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('success', 'Checklist kelengkapan crew berhasil diperbarui. Silakan login kembali untuk melanjutkan.');
            }

            // For master context, redirect back to checklist page
            return redirect()->route('master.karyawan.crew-checklist-new', $id)
                ->with('success', 'Checklist kelengkapan crew berhasil diperbarui.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan crew checklist untuk karyawan ' . $id . ': ' . $e->getMessage());

            return redirect()->route('master.karyawan.crew-checklist-new', $id)
                ->with('error', 'Gagal menyimpan checklist. Silakan coba lagi atau hubungi admin.')
                ->withInput();
        }
    }

    /**
     * Update crew checklist khusus untuk onboarding - setelah selesai redirect ke dashboard
     */
    public function updateCrewChecklistOnboarding(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        if (!$karyawan->isAbk()) {
            return redirect()->route('dashboard')
                ->with('error', 'Checklist kelengkapan crew hanya untuk divisi ABK.');
        }

        // Use DB transaction and handle failures so user sees a warning on failure
        try {
            DB::beginTransaction();

            // Debug: log incoming payload so we can inspect what the client actually sent
            try {
                Log::debug('updateCrewChecklistOnboarding incoming request', $request->all());
            } catch (\Throwable $e) {
                // swallow logging errors to avoid breaking main flow
                Log::debug('updateCrewChecklistOnboarding logging failed: ' . $e->getMessage());
            }

            // Validate that checklist exists and keep raw data so keys (item IDs) are preserved
            $request->validate([
                'checklist' => 'required|array'
            ]);

            $rawChecklist = $request->input('checklist', []);

            // Debug: log raw payload for extra insight
            Log::debug('updateCrewChecklistOnboarding raw checklist payload', ['count' => count($rawChecklist)]);

            // Server-side status rule: if nomor_sertifikat has 4 or more alphanumeric chars -> ada, else tidak
            // (changed from exact-4 to at-least-4 to match new requirement)
            $fourAlnumPattern = '/^[A-Za-z0-9]{4,}$/';

            foreach ($rawChecklist as $itemId => $data) {
                // Normalize empty strings to null so 'nullable' rules accept empty HTML inputs
                $dataNormalized = array_map(function($v) {
                    return $v === '' ? null : $v;
                }, $data);

                // Validate each row individually to avoid losing original keys
                $rowValidated = \Illuminate\Support\Facades\Validator::make($dataNormalized, [
                    'item_name' => 'nullable|string|max:255',
                    'nomor_sertifikat' => 'nullable|string|max:255',
                    'issued_date' => 'nullable|date',
                    'expired_date' => 'nullable|date|after_or_equal:issued_date',
                    'catatan' => 'nullable|string|max:500'
                ])->validate();

                $checklist = $karyawan->crewChecklists()->find($itemId);

                // If checklist not found and item_name provided (new_x), create it
                if (!$checklist && !empty($rowValidated['item_name'])) {
                    $checklist = $karyawan->crewChecklists()->create([
                        'item_name' => $rowValidated['item_name'],
                        'status' => 'tidak',
                    ]);
                }

                if ($checklist) {
                    $nomor = isset($rowValidated['nomor_sertifikat']) ? trim($rowValidated['nomor_sertifikat']) : null;
                    $status = ($nomor && preg_match($fourAlnumPattern, $nomor)) ? 'ada' : 'tidak';

                    // If status is 'tidak', ensure dates are cleared server-side as well
                    $issued = ($status === 'ada') ? ($rowValidated['issued_date'] ?? null) : null;
                    $expired = ($status === 'ada') ? ($rowValidated['expired_date'] ?? null) : null;

                    $checklist->update([
                        'status' => $status,
                        'nomor_sertifikat' => $nomor,
                        'issued_date' => $issued ?: null,
                        'expired_date' => $expired ?: null,
                        'catatan' => $rowValidated['catatan'] ?? null
                    ]);
                }
            }

            DB::commit();

            // For onboarding crew checklist completion, logout user and redirect to login
            // This will eventually lead to dashboard which shows "setup permission" message
            Auth::logout();
            return redirect()->route('login')
                ->with('success', 'Checklist kelengkapan crew berhasil diperbarui. Silakan login kembali untuk melanjutkan.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan crew checklist onboarding untuk karyawan ' . $id . ': ' . $e->getMessage());

            return redirect()->route('karyawan.onboarding-crew-checklist', $id)
                ->with('error', 'Gagal menyimpan checklist. Silakan coba lagi atau hubungi admin.')
                ->withInput();
        }
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

    /**
     * Convert Excel file to CSV data using a simple ZIP-based approach for XLSX
     * or fallback for XLS files
     */
    private function convertExcelToCsv($filePath, $extension)
    {
        try {
            if ($extension === 'xlsx') {
                return $this->convertXlsxToCsv($filePath);
            } elseif ($extension === 'xls') {
                // For XLS files, try to read with a simpler approach
                // This is a basic implementation - you might want to enhance it
                return $this->convertXlsToCsv($filePath);
            }
        } catch (\Exception $e) {
            Log::error('Excel conversion error: ' . $e->getMessage());
            return false;
        }

        return false;
    }

    /**
     * Convert XLSX to CSV by reading the XML content from the ZIP file
     */
    private function convertXlsxToCsv($filePath)
    {
        if (!class_exists('ZipArchive')) {
            throw new \Exception('ZipArchive extension not available');
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== TRUE) {
            throw new \Exception('Cannot open XLSX file');
        }

        // Read shared strings
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml) {
            $sharedStringsDoc = new \DOMDocument();
            $sharedStringsDoc->loadXML($sharedStringsXml);
            $xpath = new \DOMXPath($sharedStringsDoc);
            $nodes = $xpath->query('//t');
            foreach ($nodes as $node) {
                $sharedStrings[] = $node->nodeValue;
            }
        }

        // Read the first worksheet
        $worksheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (!$worksheetXml) {
            throw new \Exception('Cannot read worksheet data');
        }

        $zip->close();

        // Parse worksheet XML
        $worksheetDoc = new \DOMDocument();
        $worksheetDoc->loadXML($worksheetXml);
        $xpath = new \DOMXPath($worksheetDoc);

        $rows = [];
        $rowNodes = $xpath->query('//row');

        foreach ($rowNodes as $rowNode) {
            $rowData = [];
            $cellNodes = $xpath->query('.//c', $rowNode);

            $maxCol = 0;
            $cells = [];

            foreach ($cellNodes as $cellNode) {
                /** @var \DOMElement $cellNode */
                $cellRef = $cellNode->getAttribute('r');
                preg_match('/([A-Z]+)(\d+)/', $cellRef, $matches);
                $colIndex = $this->columnLettersToNumber($matches[1]) - 1;
                $maxCol = max($maxCol, $colIndex);

                $cellType = $cellNode->getAttribute('t');
                $valueNode = $xpath->query('.//v', $cellNode)->item(0);

                if ($valueNode) {
                    $value = $valueNode->nodeValue;

                    if ($cellType === 's' && isset($sharedStrings[$value])) {
                        // Shared string
                        $value = $sharedStrings[$value];
                    } elseif (is_numeric($value) && $cellType !== 's') {
                        // Check if it's a date (Excel dates are numbers)
                        if ($value > 25569) { // Excel epoch starts 1900-01-01, Unix epoch is 1970-01-01
                            $excelEpoch = new \DateTime('1900-01-01');
                            $excelEpoch->add(new \DateInterval('P' . intval($value - 2) . 'D')); // -2 for Excel leap year bug
                            $value = $excelEpoch->format('Y-m-d');
                        }
                    }

                    $cells[$colIndex] = $value;
                } else {
                    $cells[$colIndex] = '';
                }
            }

            // Fill missing columns
            for ($i = 0; $i <= $maxCol; $i++) {
                $rowData[] = isset($cells[$i]) ? $cells[$i] : '';
            }

            $rows[] = $rowData;
        }

        // Convert to CSV format
        $csvOutput = '';
        foreach ($rows as $row) {
            $csvOutput .= '"' . implode('","', array_map(function($cell) {
                return str_replace('"', '""', $cell);
            }, $row)) . "\"\n";
        }

        return $csvOutput;
    }

    /**
     * Convert XLS to CSV (basic implementation)
     */
    private function convertXlsToCsv($filePath)
    {
        // This is a simplified approach for XLS files
        // In a production environment, you'd want to use a proper library
        throw new \Exception('XLS format not supported yet. Please convert to XLSX or CSV format.');
    }

    /**
     * Convert column letters to number (A=1, B=2, etc.)
     */
    private function columnLettersToNumber($letters)
    {
        $number = 0;
        $length = strlen($letters);

        for ($i = 0; $i < $length; $i++) {
            $number = $number * 26 + (ord($letters[$i]) - ord('A') + 1);
        }

        return $number;
    }

    /**
     * Update catatan pekerjaan karyawan via AJAX
     */
    public function updateCatatanPekerjaan(Request $request, Karyawan $karyawan)
    {
        // Validasi input
        $validated = $request->validate([
            'catatan_pekerjaan' => 'nullable|string|max:1000',
        ]);

        try {
            // Update catatan pekerjaan
            $karyawan->update([
                'catatan_pekerjaan' => $validated['catatan_pekerjaan'] ? strtoupper($validated['catatan_pekerjaan']) : null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Catatan pekerjaan berhasil diperbarui',
                'data' => [
                    'catatan_pekerjaan' => $karyawan->fresh()->catatan_pekerjaan
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui catatan pekerjaan: ' . $e->getMessage()
            ], 500);
        }
    }
}
