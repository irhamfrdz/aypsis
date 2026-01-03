<?php

namespace App\Http\Controllers;

use App\Models\Bl;
use App\Models\Prospek;
use Carbon\Carbon;
use App\Models\MasterKapal;
use App\Models\StockKontainer;
use App\Models\Kontainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BlController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of BL records.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check permission (you may want to adjust this based on your permission system)
        if (!in_array($user->role, ["admin", "user_admin"])) {
            // Check specific permissions if needed
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", "bl-view")
                ->exists();
            
            if (!$hasPermission) {
                abort(403, "Tidak memiliki akses untuk melihat data BL");
            }
        }

        // Log filter parameters untuk debugging
        \Log::info('BL Index Filter Parameters:', [
            'nama_kapal' => $request->get('nama_kapal'),
            'no_voyage' => $request->get('no_voyage'),
            'kapal' => $request->get('kapal'),
            'voyage' => $request->get('voyage'),
            'search' => $request->get('search'),
            'all_params' => $request->all()
        ]);

        $query = Bl::with(['prospek.suratJalan']);

        // Filter berdasarkan search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_bl', 'like', "%{$search}%")
                  ->orWhere('nomor_kontainer', 'like', "%{$search}%")
                  ->orWhere('no_voyage', 'like', "%{$search}%")
                  ->orWhere('nama_kapal', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan kapal
        if ($request->filled('kapal')) {
            $kapal = trim($request->kapal);
            // Remove dots and use LIKE to handle inconsistencies
            $kapalPattern = str_replace('.', '', $kapal);
            $query->where(DB::raw('REPLACE(nama_kapal, ".", "")'), 'LIKE', "%{$kapalPattern}%");
            \Log::info("Filter by kapal (flexible): {$kapal} -> pattern: {$kapalPattern}");
        }
        
        // Filter berdasarkan nama_kapal (dari select page)
        if ($request->filled('nama_kapal')) {
            $namaKapal = trim($request->nama_kapal);
            // Remove dots and use LIKE to handle inconsistencies
            $kapalPattern = str_replace('.', '', $namaKapal);
            $query->where(DB::raw('REPLACE(nama_kapal, ".", "")'), 'LIKE', "%{$kapalPattern}%");
            \Log::info("Filter by nama_kapal (flexible): {$namaKapal} -> pattern: {$kapalPattern}");
        }

        // Filter berdasarkan voyage
        if ($request->filled('voyage')) {
            $voyage = trim($request->voyage);
            $query->where('no_voyage', $voyage);
            \Log::info("Filter by voyage (exact): {$voyage}");
        }
        
        // Filter berdasarkan no_voyage (dari select page)
        if ($request->filled('no_voyage')) {
            $noVoyage = trim($request->no_voyage);
            $query->where('no_voyage', $noVoyage);
            \Log::info("Filter by no_voyage (exact): {$noVoyage}");
        }

        // Sort berdasarkan parameter
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSorts = ['created_at', 'nomor_bl', 'nomor_kontainer', 'nama_kapal', 'no_voyage', 'nama_barang'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Log SQL query
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        \Log::info("BL Query SQL: {$sql}", ['bindings' => $bindings]);

        $bls = $query->paginate(15)->withQueryString();
        
        \Log::info("BL Query Results: {$bls->count()} records found, Total: {$bls->total()}");

        return view('bl.index', compact('bls'));
    }

    /**
     * Show the form for selecting kapal and voyage.
     */
    public function select()
    {
        $user = Auth::user();
        
        // Check permission
        if (!in_array($user->role, ["admin", "user_admin"])) {
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", "bl-view")
                ->exists();
            
            if (!$hasPermission) {
                abort(403, "Tidak memiliki akses untuk membuat BL");
            }
        }

        // Get distinct kapal names from bls table with normalization
        $masterKapals = Bl::select('nama_kapal')
            ->whereNotNull('nama_kapal')
            ->where('nama_kapal', '!=', '')
            ->get()
            ->map(function($item) {
                // Normalize: remove dots after KM/KMP, trim spaces, uppercase
                $normalized = trim(str_replace(['KM.', 'KMP.'], ['KM', 'KMP'], strtoupper($item->nama_kapal)));
                // Return object with nama_kapal property for compatibility with blade
                return (object)['nama_kapal' => $normalized];
            })
            ->unique('nama_kapal')
            ->sortBy('nama_kapal')
            ->values();
            
        return view('bl.select', compact('masterKapals'));
    }

    public function getVoyageByKapal(Request $request)
    {
        $namaKapal = $request->input('nama_kapal');
        
        if (!$namaKapal) {
            return response()->json([
                'success' => false,
                'message' => 'Nama kapal tidak ditemukan'
            ]);
        }
        
        // Hapus "KM." atau "KM" dari awal nama kapal untuk pencarian yang lebih fleksibel
        $kapalClean = preg_replace('/^KM\.?\s*/i', '', $namaKapal);
        
        // Ambil voyage unik dari tabel bls berdasarkan nama kapal
        // Gunakan LIKE untuk menangani perbedaan format (KM. vs KM)
        $voyages = Bl::where(function($query) use ($namaKapal, $kapalClean) {
                $query->where('nama_kapal', $namaKapal)
                      ->orWhere('nama_kapal', 'like', '%' . $kapalClean . '%');
            })
            ->whereNotNull('no_voyage')
            ->distinct()
            ->orderBy('no_voyage', 'desc')
            ->pluck('no_voyage')
            ->toArray();
        
        return response()->json([
            'success' => true,
            'voyages' => $voyages
        ]);
    }

    /**
     * Store a newly created BL.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check permission
        if (!in_array($user->role, ["admin", "user_admin"])) {
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", "bl-create")
                ->exists();
            
            if (!$hasPermission) {
                abort(403, "Tidak memiliki akses untuk membuat BL");
            }
        }

        // Validate input
        $request->validate([
            'kapal_id' => 'required|exists:master_kapals,id',
            'no_voyage' => 'required|string|max:50',
        ]);

        // For now just return confirmation
        // Later this can be extended to show a form for creating BL details
        $masterKapal = MasterKapal::find($request->kapal_id);
        
        return redirect()->route('bl.index')
            ->with('success', "BL request received for kapal {$masterKapal->nama_kapal} voyage {$request->no_voyage}");
    }

    /**
     * Display the specified BL.
     */
    public function show(Bl $bl)
    {
        $user = Auth::user();
        
        // Check permission
        if (!in_array($user->role, ["admin", "user_admin"])) {
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", "bl-view")
                ->exists();
            
            if (!$hasPermission) {
                abort(403, "Tidak memiliki akses untuk melihat detail BL");
            }
        }

        $bl->load('prospek');
        return view('bl.show', compact('bl'));
    }

    /**
     * Update the nomor_bl field for a BL record.
     */
    public function updateNomorBl(Request $request, Bl $bl)
    {
        $user = Auth::user();
        
        // Check permission
        if (!in_array($user->role, ["admin", "user_admin"])) {
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", "bl-edit")
                ->exists();
            
            if (!$hasPermission) {
                return response()->json(['error' => 'Tidak memiliki akses untuk mengupdate BL'], 403);
            }
        }

        // Validate input
        $request->validate([
            'nomor_bl' => 'nullable|string|max:255',
        ]);

        try {
            $bl->update([
                'nomor_bl' => $request->nomor_bl
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nomor BL berhasil diupdate',
                'nomor_bl' => $bl->nomor_bl ?: '-'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate nomor BL: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update status bongkar for a BL
     */
    public function updateStatusBongkar(Request $request, Bl $bl)
    {
        $user = Auth::user();
        
        // Check permission
        if (!in_array($user->role, ["admin", "user_admin"])) {
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", "bl-edit")
                ->exists();
            
            if (!$hasPermission) {
                return response()->json(['error' => 'Tidak memiliki akses untuk mengupdate BL'], 403);
            }
        }

        // Validate input
        $request->validate([
            'status_bongkar' => 'required|in:Sudah Bongkar,Belum Bongkar',
        ]);

        try {
            $bl->update([
                'status_bongkar' => $request->status_bongkar
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status bongkar berhasil diupdate',
                'status_bongkar' => $bl->status_bongkar
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status bongkar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate containers for bulk operations
     */
    public function validateContainers(Request $request)
    {
        $user = Auth::user();
        
        // Check permission
        if (!in_array($user->role, ["admin", "user_admin"])) {
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", "bl-edit")
                ->exists();
            
            if (!$hasPermission) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:bls,id'
        ]);

        $bls = Bl::whereIn('id', $request->ids)->get();
        
        // Check if all selected items have the same container number
        $containerNumbers = $bls->pluck('nomor_kontainer')->filter()->unique();
        $hasDifferentContainers = $containerNumbers->count() > 1;
        
        // Check if any items don't have container numbers
        $hasNoContainer = $bls->whereNull('nomor_kontainer')->count() > 0 || 
                         $bls->where('nomor_kontainer', '')->count() > 0;
        
        $containerInfo = '';
        if ($hasDifferentContainers) {
            $containerInfo = "Nomor kontainer yang ditemukan:\n" . $containerNumbers->implode("\n");
        }

        return response()->json([
            'success' => true,
            'has_different_containers' => $hasDifferentContainers,
            'has_no_container' => $hasNoContainer,
            'container_info' => $containerInfo,
            'selected_count' => $bls->count()
        ]);
    }

    /**
     * Bulk split selected BL records - create new BL with same container but different tonnage
     */
    public function bulkSplit(Request $request)
    {
        $user = Auth::user();
        
        // Check permission
        if (!in_array($user->role, ["admin", "user_admin"])) {
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", "bl-edit")
                ->exists();
            
            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Tidak memiliki akses untuk melakukan operasi ini.');
            }
        }

        $request->validate([
            'ids' => 'required|string',
            'tonnage_dipindah' => 'required|numeric|min:0.01',
            'volume_dipindah' => 'required|numeric|min:0.001',
            'nama_barang_dipindah' => 'required|string',
            'term_baru' => 'nullable|string|max:100',
            'keterangan' => 'required|string|max:1000'
        ]);

        $ids = json_decode($request->input('ids'), true);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada item yang dipilih.');
        }

        $tonnageDipindah = $request->tonnage_dipindah;
        $volumeDipindah = $request->volume_dipindah;
        $processedCount = 0;
        
        DB::transaction(function () use ($ids, $request, $tonnageDipindah, $volumeDipindah, &$processedCount) {
            
            foreach ($ids as $originalId) {
                $originalBl = Bl::findOrFail($originalId);
                
                // Check if we have enough tonnage and volume to split
                $currentTonnage = $originalBl->tonnage ?? 0;
                $currentVolume = $originalBl->volume ?? 0;
                
                if ($currentTonnage < $tonnageDipindah) {
                    continue; // Skip this item if not enough tonnage
                }
                
                if ($currentVolume < $volumeDipindah) {
                    continue; // Skip this item if not enough volume
                }
                
                // Generate new BL number with suffix
                $newNomorBl = ($originalBl->nomor_bl ?: 'BL-AUTO') . '-SPLIT';
                
                // Create new BL record for split - same container, different tonnage and cargo name
                $newBl = Bl::create([
                    'nomor_bl' => $newNomorBl,
                    'nomor_kontainer' => $originalBl->nomor_kontainer, // Same container
                    'tipe_kontainer' => $originalBl->tipe_kontainer,   // Same type
                    'no_seal' => $originalBl->no_seal,                 // Same seal
                    'nama_kapal' => $originalBl->nama_kapal,
                    'no_voyage' => $originalBl->no_voyage,
                    'nama_barang' => $request->nama_barang_dipindah,   // Different cargo name
                    'tonnage' => $tonnageDipindah,                     // Split tonnage
                    'volume' => $volumeDipindah,                       // Split volume
                    'term' => $request->term_baru ?: $originalBl->term, // New term or same as original
                    'prospek_id' => $originalBl->prospek_id,
                    'keterangan' => $request->keterangan,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id()
                ]);
                
                // Update original BL - reduce tonnage and volume
                $remainingTonnage = $currentTonnage - $tonnageDipindah;
                $remainingVolume = $currentVolume - $volumeDipindah;
                
                $originalBl->update([
                    'tonnage' => max(0, $remainingTonnage),
                    'volume' => max(0, $remainingVolume),
                    'keterangan' => ($originalBl->keterangan ?? '') . ' [SEBAGIAN DIPINDAH KE: ' . $newNomorBl . ']',
                    'updated_by' => Auth::id(),
                ]);
                
                $processedCount++;
            }
        });

        if ($processedCount == 0) {
            // Get first selected item to show current capacity
            $firstId = $ids[0] ?? null;
            if ($firstId) {
                $firstBl = Bl::find($firstId);
                if ($firstBl) {
                    $currentTonnage = $firstBl->tonnage ?? 0;
                    $currentVolume = $firstBl->volume ?? 0;
                    $message = "Tidak ada BL yang dapat dipecah. Kapasitas tersedia pada BL pertama: {$currentTonnage} ton, {$currentVolume} m³. Pastikan tonnage dan volume yang diminta tidak melebihi kapasitas ini.";
                } else {
                    $message = 'Tidak ada BL yang dapat dipecah. Pastikan tonnage dan volume yang diminta tidak melebihi kapasitas yang tersedia.';
                }
            } else {
                $message = 'Tidak ada BL yang dapat dipecah. Pastikan tonnage dan volume yang diminta tidak melebihi kapasitas yang tersedia.';
            }
            
            return redirect()->back()->with('error', $message);
        }
        
        return redirect()->route('bl.index')
                        ->with('success', "Berhasil memecah {$processedCount} BL. BL baru telah dibuat dengan tonnage {$tonnageDipindah} ton dan volume {$volumeDipindah} m³ (kontainer tetap sama).");
    }

    /**
     * Get BL data by kapal and voyage (API endpoint)
     */
    public function getByKapalVoyage(Request $request)
    {
        $user = Auth::user();
        
        // Check permission
        if (!in_array($user->role, ["admin", "user_admin"])) {
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", "bl-view")
                ->exists();
            
            if (!$hasPermission) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $request->validate([
            'nama_kapal' => 'required|string',
            'no_voyage' => 'required|string',
        ]);

        $bls = Bl::with(['prospek.suratJalan'])
            ->where('nama_kapal', $request->nama_kapal)
            ->where('no_voyage', $request->no_voyage)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bls,
            'count' => $bls->count()
        ]);
    }

    /**
     * Download template Excel untuk import BL
     */
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Aypsis System')
            ->setTitle('Template Import BL')
            ->setDescription('Template untuk import data Bill of Lading');

        // Header columns
        $headers = [
            'A1' => 'Nomor BL',
            'B1' => 'Nomor Kontainer*',
            'C1' => 'No Seal',
            'D1' => 'Nama Kapal*',
            'E1' => 'No Voyage*',
            'F1' => 'Tanggal Berangkat',
            'G1' => 'Pelabuhan Asal',
            'H1' => 'Pelabuhan Tujuan',
            'I1' => 'Nama Barang',
            'J1' => 'Pengirim',
            'K1' => 'Penerima',
            'L1' => 'Alamat Pengiriman',
            'M1' => 'Contact Person',
            'N1' => 'Tipe Kontainer',
            'O1' => 'Ukuran Kontainer',
            'P1' => 'Tonnage',
            'Q1' => 'Volume',
            'R1' => 'Satuan',
            'S1' => 'Kuantitas',
            'T1' => 'Term',
            'U1' => 'Supir OB',
            'V1' => 'Tanggal Muat',
            'W1' => 'Jam Muat',
            'X1' => 'Prospek ID',
            'Y1' => 'Keterangan'
        ];

        // Set headers with styling
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4472C4');
            $sheet->getStyle($cell)->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Add example data
        $exampleData = [
            [
                'BL-' . date('Ymd') . '-001',
                'CONT' . date('Ymd') . '001',
                'SEAL001',
                'KM SINAR HARAPAN',
                'SH001',
                date('Y-m-d'),
                'Batam',
                'Jakarta',
                'Elektronik',
                'PT. Supplier Electronics',
                'PT. ABC Indonesia',
                'Jl. Raya Industri No. 123, Batam',
                'Budi Santoso (08123456789)',
                '20 FT',
                '20x8x8.6',
                15.500,
                25.750,
                'Ton/m³',
                100,
                'COD',
                'Budi Santoso',
                date('Y-m-d'),
                '08:00',
                1,
                'Contoh data BL untuk import'
            ],
            [
                'BL-' . date('Ymd') . '-002',
                'CONT' . date('Ymd') . '002',
                'SEAL002',
                'KM CAHAYA LAUT',
                'CL002',
                date('Y-m-d'),
                'Jakarta',
                'Surabaya',
                'Makanan & Minuman',
                'CV. Produsen Makanan',
                'CV. XYZ Trading',
                'Jl. Mangga Dua Raya No. 456, Jakarta',
                'Ahmad Wijaya (08234567890)',
                '40 FT',
                '40x8x8.6',
                25.000,
                45.300,
                'Ton/m³',
                200,
                'Credit 30',
                'Ahmad Wijaya',
                date('Y-m-d'),
                '14:30',
                2,
                'Contoh data BL kedua'
            ]
        ];

        $row = 2;
        foreach ($exampleData as $data) {
            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'Y') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add instructions sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Petunjuk');
        
        $instructions = [
            ['PETUNJUK PENGGUNAAN TEMPLATE IMPORT BL'],
            [''],
            ['Kolom yang wajib diisi (bertanda *):'],
            ['- Nama Kapal: Nama kapal yang mengangkut *'],
            ['- No Voyage: Nomor voyage/pelayaran *'],
            [''],
            ['Kolom Opsional:'],
            ['- Nomor Kontainer: Jika kosong akan otomatis diisi CARGO-1, CARGO-2, dst'],
            ['- Nomor BL, No Seal, Pelabuhan, dll: Boleh dikosongkan'],
            [''],
            ['Format Data:'],
            ['- Tonnage: Angka desimal dengan titik (contoh: 15.500)'],
            ['- Volume: Angka desimal dengan titik (contoh: 25.750)'],
            ['- Kuantitas: Angka bulat (contoh: 100)'],
            [''],
            ['Catatan:'],
            ['- Hapus baris contoh sebelum import'],
            ['- Pastikan format sesuai dengan petunjuk'],
            ['- Status bongkar otomatis akan diset "Belum Bongkar"'],
            ['- Periksa data sebelum melakukan import']
        ];

        $row = 1;
        foreach ($instructions as $instruction) {
            $instructionSheet->setCellValue('A' . $row, $instruction[0]);
            if ($row === 1) {
                $instructionSheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
            }
            $row++;
        }
        $instructionSheet->getColumnDimension('A')->setWidth(80);

        // Set active sheet back to data sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Create Excel file
        $filename = 'template_bl_' . date('Y-m-d') . '.xlsx';
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Import BL from Excel file
     */
    public function import(Request $request)
    {
        $user = Auth::user();
        
        // Check permission
        if (!in_array($user->role, ["admin", "user_admin"])) {
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", "bl-create")
                ->exists();
            
            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Tidak memiliki akses untuk import BL');
            }
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ], [
            'file.required' => 'File wajib dipilih',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv',
            'file.max' => 'Ukuran file maksimal 10MB'
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            
            $importedCount = 0;
            $errors = [];
            $warnings = []; // Non-blocking issues, e.g., container size not found in DB
            $rowNumber = 1;
            // Map nomorKontainer => array of pengirim seen for that nomor
            $containerNumbersSeen = [];
            
            // Get next cargo number for auto-generated container numbers
            $lastCargoNumber = Bl::where('nomor_kontainer', 'LIKE', 'CARGO-%')
                ->orderBy('nomor_kontainer', 'desc')
                ->value('nomor_kontainer');
            
            $nextCargoNumber = 1;
            if ($lastCargoNumber) {
                $parts = explode('-', $lastCargoNumber);
                if (count($parts) > 1 && is_numeric($parts[1])) {
                    $nextCargoNumber = (int)$parts[1] + 1;
                }
            }

            \Log::info('Starting BL import', ['extension' => $extension, 'file' => $file->getClientOriginalName(), 'nextCargoNumber' => $nextCargoNumber]);

                if ($extension === 'csv') {
                // Handle CSV import with semicolon delimiter
                $handle = fopen($file->getRealPath(), 'r');
                
                if (!$handle) {
                    throw new \Exception('Tidak dapat membuka file CSV');
                }
                
                // Read header row and build header map for robust mapping
                $header = fgetcsv($handle, 0, ';');
                // Normalize header values for basic validation
                $normalizedHeader = array_map(function($h) {
                    return strtolower(trim(preg_replace('/\s+/', ' ', $h ?? '')));
                }, (array)$header);

                // Build header -> index map
                $headerMap = [];
                foreach ($normalizedHeader as $idx => $colName) {
                    $clean = $colName;
                    // Remove asterisks and parentheses content, replace _ and - with space
                    $clean = str_replace('*', '', $clean);
                    $clean = preg_replace('/\s*\([^\)]*\)\s*/', ' ', $clean);
                    $clean = str_replace(['_', '-'], ' ', $clean);
                    $clean = trim(preg_replace('/\s+/', ' ', $clean));
                    $headerMap[$clean] = $idx;
                }
                // Check if critical columns exist in header
                $requiredHeaderKeywords = ['nama', 'kapal', 'voyage', 'kontainer'];
                foreach (['kapal', 'voyage'] as $keyword) {
                    $found = false;
                    foreach ($normalizedHeader as $h) {
                        if (strpos($h, $keyword) !== false) {
                            $found = true; break;
                        }
                    }
                    if (!$found) {
                        $errors[] = "Header CSV tidak mengandung kolom yang mengindikasikan '{$keyword}' - periksa nama kolom: " . implode(', ', $header);
                    }
                }
                $rowNumber++;
                
                \Log::info('CSV Header', ['header' => $header]);

                while (($row = fgetcsv($handle, 0, ';')) !== false) {
                    try {
                        // Skip completely empty rows
                        $filteredRow = array_filter($row, function($value) {
                            return !empty(trim($value));
                        });
                        
                        if (empty($filteredRow)) {
                            $rowNumber++;
                            continue;
                        }
                        
                        \Log::info("Row $rowNumber data", ['row' => $row, 'count' => count($row)]);
                        
                        // Pastikan array memiliki minimal 15 kolom
                        if (count($row) < 15) {
                            $errors[] = "Baris {$rowNumber}: Format CSV tidak lengkap (hanya " . count($row) . " kolom, minimal 15 kolom). Header: " . implode(', ', array_map('trim', (array)$header));
                            $rowNumber++;
                            continue;
                        }
                        
                        // Use headerMap when available to locate columns robustly
                        // 0: nomor_bl
                        // 1: nomor_kontainer
                        // 2: no_seal
                        // 3: nama_kapal
                        // 4: no_voyage
                        // 5: pelabuhan_asal
                        // 6: pelabuhan_tujuan
                        // 7: nama_barang
                        // 8: pengirim
                        // 9: penerima
                        // 10: alamat_pengiriman
                        // 11: contact_person
                        // 12: tipe_kontainer
                        // 13: ukuran_kontainer
                        // 14: tonnage
                        // 15: volume
                        // 16: satuan
                        // 17: kuantitas
                        // 18: term
                        // 19: supir_ob
                        // 20: tanggal_muat
                        // 21: jam_muat
                        // 22: prospek_id
                        // 23: keterangan
                        
                        $getCol = function($aliases, $defaultIndex = null) use ($row, $headerMap) {
                            foreach ((array)$aliases as $alias) {
                                $key = strtolower(trim(preg_replace('/\s+/', ' ', $alias)));
                                if (array_key_exists($key, $headerMap)) {
                                    $idx = $headerMap[$key];
                                    return isset($row[$idx]) ? trim($row[$idx]) : null;
                                }
                            }
                            // fallback: use default index if provided
                            if (is_numeric($defaultIndex) && isset($row[$defaultIndex])) {
                                return trim($row[$defaultIndex]);
                            }
                            return null;
                        };

                        // Resolve important fields using header aliases with fallback indexes (original template positions)
                        $nomorKontainer = $getCol(['nomor kontainer', 'nomor_kontainer', 'nomor kontainer*'], 1);
                        $namaKapal = $getCol(['nama kapal', 'nama_kapal', 'nama kapal*'], 3);
                        $noVoyage = $getCol(['no voyage', 'no_voyage', 'no voyage*'], 4);
                        $sizeKontainerFromFile = $getCol(['ukuran kontainer', 'ukuran_kontainer', 'ukuran kontainer*', 'size kontainer'], 13);

                        // Prepare short row preview for diagnostics
                        $rowPreview = 'nomor_kontainer=' . ($nomorKontainer ?? '-') . ', nama_kapal=' . ($namaKapal ?? '-') . ', no_voyage=' . ($noVoyage ?? '-') ;
                        
                        // Auto-generate container number if empty - fill with literal 'cargo'
                        if (empty($nomorKontainer)) {
                            $nomorKontainer = 'cargo';
                        }

                        // Check duplicate container numbers within the same uploaded file
                        // Allow same nomor_kontainer if pengirim is different
                        $pengirim = $getCol(['pengirim', 'pengirim*'], 8) ?: '';
                        if (!empty($nomorKontainer)) {
                            $existingPengirimList = $containerNumbersSeen[$nomorKontainer] ?? [];
                            // Normalize pengirim for comparison
                            $normalizedPengirim = mb_strtolower(trim($pengirim ?? ''));
                            $matchFound = false;
                            foreach ($existingPengirimList as $ep) {
                                if (mb_strtolower(trim($ep)) === $normalizedPengirim) {
                                    $matchFound = true; break;
                                }
                            }
                            if ($matchFound) {
                                // Do not block import for duplicate container numbers even when pengirim is the same.
                                // Add a non-blocking warning instead and continue to insert the row.
                                $warnings[] = "Baris {$rowNumber}: Duplikat nomor kontainer di file dengan pengirim sama: {$nomorKontainer}. Baris ini akan diimport juga. ({$rowPreview})";
                            }
                            // Record the pengirim occurrence regardless to keep track of duplicates
                            $containerNumbersSeen[$nomorKontainer][] = $pengirim;
                        }
                        
                        // Auto-fill size kontainer from database if not provided in file; allow using file size if present
                        $autoFilledSize = $this->getContainerSize($nomorKontainer, $sizeKontainerFromFile);
                        if (empty($autoFilledSize['size'])) {
                            // Do not block import even if size not found — treat as a warning and proceed with NULL size
                            $warnings[] = "Baris {$rowNumber}: ukuran kontainer tidak ditemukan di database untuk nomor {$nomorKontainer} dan tidak ada ukuran yang disertakan pada file. Data akan tetap disimpan tanpa ukuran. ({$rowPreview})";
                        }
                        
                        // Validate required fields (now only kapal and voyage since container is auto-generated)
                        if (empty($namaKapal) || empty($noVoyage)) {
                            $errors[] = "Baris {$rowNumber}: Nama Kapal dan No Voyage wajib diisi ({$rowPreview})";
                            $rowNumber++;
                            continue;
                        }

                        // Parse tonnage dan volume (validate numeric)
                        $tonnage = null;
                        // Determine tonnage/volume using header aliases and robust parsing
                        $tonnageRaw = $getCol(['tonnage', 'tonnage (ton)', 'tonnage*'], 14);
                        if (isset($tonnageRaw) && $tonnageRaw !== '') {
                            $tonnageStr = str_replace(['.', ','], ['', '.'], trim($tonnageRaw));
                            if (!is_numeric($tonnageStr)) {
                                $errors[] = "Baris {$rowNumber}: Nilai tonnage tidak valid ('{$tonnageRaw}') ({$rowPreview})";
                                $tonnage = null;
                            } else {
                                $tonnage = (float)$tonnageStr;
                            }
                        }
                        
                        $volume = null;
                        $volumeRaw = $getCol(['volume', 'volume (m³)', 'volume*'], 15);
                        if (isset($volumeRaw) && $volumeRaw !== '') {
                            $volumeStr = str_replace(['.', ','], ['', '.'], trim($volumeRaw));
                            if (!is_numeric($volumeStr)) {
                                $errors[] = "Baris {$rowNumber}: Nilai volume tidak valid ('{$volumeRaw}') ({$rowPreview})";
                                $volume = null;
                            } else {
                                $volume = (float)$volumeStr;
                            }
                        }

                        // Check tanggal_muat (if present) parsing
                        $tanggalMuatRaw = $getCol(['tanggal muat', 'tanggal_muat'], 20);
                        if (!empty($tanggalMuatRaw)) {
                            try {
                                $tanggalMuat = Carbon::parse($tanggalMuatRaw);
                            } catch (\Exception $e) {
                                $errors[] = "Baris {$rowNumber}: Format tanggal muat tidak valid ('{$tanggalMuatRaw}') ({$rowPreview})";
                            }
                        }

                        // Validate prospek_id if provided
                        $prospekId = $getCol(['prospek id', 'prospek_id'], 22);
                        if (!empty($prospekId)) {
                            $prospekId = trim($prospekId);
                            if (!is_numeric($prospekId) || !Prospek::find($prospekId)) {
                                $errors[] = "Baris {$rowNumber}: Prospek dengan ID '{$prospekId}' tidak ditemukan ({$rowPreview})";
                            }
                        }

                        // For debugging convenience, prepare a short row preview for possible error messages
                        $rowPreview = 'nomor_kontainer=' . ($nomorKontainer ?? '-') . ', nama_kapal=' . ($namaKapal ?? '-') . ', no_voyage=' . ($noVoyage ?? '-');

                        // Create BL record
                        Bl::create([
                            'nomor_bl' => $getCol(['nomor bl', 'nomor_bl'], 0) ?: null,
                            'nomor_kontainer' => $nomorKontainer,
                            'no_seal' => $getCol(['no seal', 'no_seal'], 2) ?: null,
                            'nama_kapal' => $namaKapal,
                            'no_voyage' => $noVoyage,
                            'tanggal_berangkat' => $getCol(['tanggal berangkat', 'tanggal_berangkat'], 5) ?: null,
                            'pelabuhan_asal' => $getCol(['pelabuhan asal', 'pelabuhan_asal'], 6) ?: null,
                            'pelabuhan_tujuan' => $getCol(['pelabuhan tujuan', 'pelabuhan_tujuan'], 7) ?: null,
                            'nama_barang' => $getCol(['nama barang', 'nama_barang'], 8) ?: null,
                            'pengirim' => $pengirim ?: $getCol(['pengirim'], 9),
                            'penerima' => $getCol(['penerima'], 10) ?: null,
                            'alamat_pengiriman' => $getCol(['alamat pengiriman', 'alamat_pengiriman'], 11) ?: null,
                            'contact_person' => $getCol(['contact person', 'contact_person'], 12) ?: null,
                            'tipe_kontainer' => $getCol(['tipe kontainer', 'tipe_kontainer'], 13) ?: null,
                            'size_kontainer' => $autoFilledSize['size'],
                            'tonnage' => $tonnage,
                            'volume' => $volume,
                            'satuan' => $getCol(['satuan'], 17) ?: null,
                            'kuantitas' => $getCol(['kuantitas'], 18) ? (int)$getCol(['kuantitas'], 18) : null,
                            'term' => $getCol(['term'], 19) ?: null,
                            'supir_ob' => $getCol(['supir ob', 'supir_ob'], 20) ?: null,
                            'status_bongkar' => 'Belum Bongkar',
                        ]);

                        // Update status prospek jika kontainer ada di prospek
                        if (!empty($nomorKontainer) && $nomorKontainer !== 'cargo') {
                            Prospek::where('nomor_kontainer', $nomorKontainer)
                                ->where('status', '!=', Prospek::STATUS_SUDAH_MUAT)
                                ->update([
                                    'status' => Prospek::STATUS_SUDAH_MUAT,
                                    'updated_by' => Auth::id()
                                ]);
                        }

                        $importedCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Baris {$rowNumber}: " . $e->getMessage() . " ({$rowPreview})";
                    }
                    
                    $rowNumber++;
                }
                
                fclose($handle);
            } else {
                // Handle Excel import using PhpSpreadsheet
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();

                // Build header map from first row to support header-aware mapping
                $highestCol = $worksheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);
                $xlsHeaderMap = [];
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $headerVal = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
                    if ($headerVal !== null) {
                        $clean = strtolower(trim(preg_replace('/\s+/', ' ', (string)$headerVal)));
                        // Remove asterisk markers and parentheses content, replace _ and - with space
                        $clean = str_replace('*', '', $clean);
                        $clean = preg_replace('/\s*\([^\)]*\)\s*/', ' ', $clean);
                        $clean = str_replace(['_', '-'], ' ', $clean);
                        $clean = trim(preg_replace('/\s+/', ' ', $clean));
                        $xlsHeaderMap[$clean] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    }
                }

                $getXlsCell = function($aliases, $fallbackCell) use ($worksheet, $xlsHeaderMap) {
                    foreach ((array)$aliases as $alias) {
                        $key = strtolower(trim(preg_replace('/\s+/', ' ', $alias)));
                        if (isset($xlsHeaderMap[$key])) {
                            $col = $xlsHeaderMap[$key];
                            return $worksheet->getCell("{$col}{ROW}")->getValue();
                        }
                    }
                    // replace {ROW} at call time
                    return null;
                };

                for ($row = 2; $row <= $highestRow; $row++) {
                    try {
                        // Resolve using header mapping whenever possible with fallback to original column letters
                        $nomorKontainer = null;
                        if (isset($xlsHeaderMap['nomor kontainer'])) {
                            $col = $xlsHeaderMap['nomor kontainer'];
                            $nomorKontainer = $worksheet->getCell("{$col}{$row}")->getValue();
                        } else {
                            $nomorKontainer = $worksheet->getCell("B{$row}")->getValue();
                        }

                        $namaKapal = null;
                        if (isset($xlsHeaderMap['nama kapal'])) {
                            $col = $xlsHeaderMap['nama kapal'];
                            $namaKapal = $worksheet->getCell("{$col}{$row}")->getValue();
                        } else {
                            $namaKapal = $worksheet->getCell("D{$row}")->getValue();
                        }

                        $noVoyage = null;
                        if (isset($xlsHeaderMap['no voyage'])) {
                            $col = $xlsHeaderMap['no voyage'];
                            $noVoyage = $worksheet->getCell("{$col}{$row}")->getValue();
                        } else {
                            $noVoyage = $worksheet->getCell("E{$row}")->getValue();
                        }

                        $sizeKontainerFromFile = null;
                        if (isset($xlsHeaderMap['ukuran kontainer'])) {
                            $col = $xlsHeaderMap['ukuran kontainer'];
                            $sizeKontainerFromFile = $worksheet->getCell("{$col}{$row}")->getValue();
                        } else {
                            $sizeKontainerFromFile = $worksheet->getCell("N{$row}")->getValue();
                        }

                        // Prepare short row preview for diagnostics
                        $rowPreview = 'nomor_kontainer=' . ($nomorKontainer ?? '-') . ', nama_kapal=' . ($namaKapal ?? '-') . ', no_voyage=' . ($noVoyage ?? '-') ;

                        // Skip empty rows
                        if (empty($namaKapal) && empty($noVoyage)) {
                            continue;
                        }
                        
                        // Auto-generate container number if empty - fill with literal 'cargo'
                        if (empty($nomorKontainer)) {
                            $nomorKontainer = 'cargo';
                        }

                        // Check duplicate container numbers within the same uploaded file
                        // Allow same nomor_kontainer if pengirim is different
                        $pengirim = null;
                        if (isset($xlsHeaderMap['pengirim'])) {
                            $col = $xlsHeaderMap['pengirim'];
                            $pengirim = $worksheet->getCell("{$col}{$row}")->getValue();
                        } else {
                            $pengirim = $worksheet->getCell("I{$row}")->getValue();
                        }
                        if (!empty($nomorKontainer)) {
                            $existingPengirimList = $containerNumbersSeen[$nomorKontainer] ?? [];
                            $normalizedPengirim = mb_strtolower(trim($pengirim ?? ''));
                            $matchFound = false;
                            foreach ($existingPengirimList as $ep) {
                                if (mb_strtolower(trim($ep)) === $normalizedPengirim) {
                                    $matchFound = true; break;
                                }
                            }
                            if ($matchFound) {
                                // Do not block import for duplicate container numbers even when pengirim is the same.
                                // Add a non-blocking warning instead and continue to import the row.
                                $warnings[] = "Baris {$row}: Duplikat nomor kontainer di file dengan pengirim sama: {$nomorKontainer}. Baris ini akan diimport juga. ({$rowPreview})";
                            }
                            // Record the pengirim occurrence regardless to keep track of duplicates
                            $containerNumbersSeen[$nomorKontainer][] = $pengirim;
                        }
                        
                        // Auto-fill size kontainer from database if not provided in file; allow using file size if present
                        $autoFilledSize = $this->getContainerSize($nomorKontainer, $sizeKontainerFromFile);
                        if (empty($autoFilledSize['size'])) {
                            // Do not block import even if size not found — treat as a warning and proceed with NULL size
                            $warnings[] = "Baris {$row}: ukuran kontainer tidak ditemukan di database untuk nomor {$nomorKontainer} dan tidak ada ukuran yang disertakan pada file. Data akan tetap disimpan tanpa ukuran. ({$rowPreview})";
                        }

                        // Validate required fields (now only kapal and voyage since container is auto-generated)
                        if (empty($namaKapal) || empty($noVoyage)) {
                            $errors[] = "Baris {$row}: Nama Kapal dan No Voyage wajib diisi ({$rowPreview})";
                            continue;
                        }

                        // Validate tonnage and volume
                        $rawTonnage = null;
                        if (isset($xlsHeaderMap['tonnage'])) {
                            $col = $xlsHeaderMap['tonnage'];
                            $rawTonnage = $worksheet->getCell("{$col}{$row}")->getValue();
                        } else {
                            $rawTonnage = $worksheet->getCell("O{$row}")->getValue();
                        }
                        $tonnage = null;
                        if (!empty($rawTonnage)) {
                            $tonnageStr = str_replace(['.', ','], ['', '.'], trim($rawTonnage));
                            if (!is_numeric($tonnageStr)) {
                                $errors[] = "Baris {$row}: Nilai tonnage tidak valid ('{$rawTonnage}') ({$rowPreview})";
                            } else {
                                $tonnage = (float)$tonnageStr;
                            }
                        }

                        $rawVolume = null;
                        if (isset($xlsHeaderMap['volume'])) {
                            $col = $xlsHeaderMap['volume'];
                            $rawVolume = $worksheet->getCell("{$col}{$row}")->getValue();
                        } else {
                            $rawVolume = $worksheet->getCell("P{$row}")->getValue();
                        }
                        $volume = null;
                        if (!empty($rawVolume)) {
                            $volumeStr = str_replace(['.', ','], ['', '.'], trim($rawVolume));
                            if (!is_numeric($volumeStr)) {
                                $errors[] = "Baris {$row}: Nilai volume tidak valid ('{$rawVolume}') ({$rowPreview})";
                            } else {
                                $volume = (float)$volumeStr;
                            }
                        }

                        // Validate tanggal muat if present
                        $tanggalMuatCell = null;
                        if (isset($xlsHeaderMap['tanggal muat'])) {
                            $col = $xlsHeaderMap['tanggal muat'];
                            $tanggalMuatCell = $worksheet->getCell("{$col}{$row}")->getValue();
                        } else {
                            $tanggalMuatCell = $worksheet->getCell("U{$row}")->getValue();
                        }
                        if (!empty($tanggalMuatCell)) {
                            try {
                                Carbon::parse($tanggalMuatCell);
                            } catch (\Exception $e) {
                                $errors[] = "Baris {$row}: Format tanggal muat tidak valid ('{$tanggalMuatCell}') ({$rowPreview})";
                            }
                        }

                        // Validate prospek_id if provided
                        $prospekCell = null;
                        if (isset($xlsHeaderMap['prospek id'])) {
                            $col = $xlsHeaderMap['prospek id'];
                            $prospekCell = $worksheet->getCell("{$col}{$row}")->getValue();
                        } else {
                            $prospekCell = $worksheet->getCell("W{$row}")->getValue();
                        }
                        if (!empty($prospekCell)) {
                            $prospekId = trim($prospekCell);
                            if (!is_numeric($prospekId) || !Prospek::find($prospekId)) {
                                $errors[] = "Baris {$row}: Prospek dengan ID '{$prospekCell}' tidak ditemukan ({$rowPreview})";
                            }
                        }

                        // Parse kuantitas - integer
                        $kuantitasRaw = null;
                        if (isset($xlsHeaderMap['kuantitas'])) {
                            $col = $xlsHeaderMap['kuantitas'];
                            $kuantitasRaw = $worksheet->getCell("{$col}{$row}")->getValue();
                        } else {
                            $kuantitasRaw = $worksheet->getCell("S{$row}")->getValue();
                        }
                        $kuantitas = null;
                        if (!empty($kuantitasRaw) && is_numeric($kuantitasRaw)) {
                            $kuantitas = (int)$kuantitasRaw;
                        }

                        // For debugging convenience, prepare a short row preview for possible error messages
                        $rowPreview = 'nomor_kontainer=' . ($nomorKontainer ?? '-') . ', nama_kapal=' . ($namaKapal ?? '-') . ', no_voyage=' . ($noVoyage ?? '-');

                        // Handle tanggal_berangkat - convert Excel serial date to MySQL date
                        $tanggalBerangkatRaw = (isset($xlsHeaderMap['tanggal berangkat']) ? ($worksheet->getCell("{$xlsHeaderMap['tanggal berangkat']}{$row}")->getValue() ?: null) : ($worksheet->getCell("F{$row}")->getValue() ?: null));
                        $tanggalBerangkat = null;
                        if (!empty($tanggalBerangkatRaw)) {
                            try {
                                // Check if it's an Excel serial date number
                                if (is_numeric($tanggalBerangkatRaw)) {
                                    $tanggalBerangkat = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalBerangkatRaw)->format('Y-m-d');
                                } else {
                                    // Try to parse as string date
                                    $tanggalBerangkat = Carbon::parse($tanggalBerangkatRaw)->format('Y-m-d');
                                }
                            } catch (\Exception $e) {
                                \Log::warning("Baris {$row}: Gagal konversi tanggal berangkat '{$tanggalBerangkatRaw}': " . $e->getMessage());
                                $tanggalBerangkat = null;
                            }
                        }

                        // Create BL record
                        Bl::create([
                            'nomor_bl' => (isset($xlsHeaderMap['nomor bl']) ? ($worksheet->getCell("{$xlsHeaderMap['nomor bl']}{$row}")->getValue() ?: null) : ($worksheet->getCell("A{$row}")->getValue() ?: null)),
                            'nomor_kontainer' => $nomorKontainer,
                            'no_seal' => (isset($xlsHeaderMap['no seal']) ? ($worksheet->getCell("{$xlsHeaderMap['no seal']}{$row}")->getValue() ?: null) : ($worksheet->getCell("C{$row}")->getValue() ?: null)),
                            'nama_kapal' => $namaKapal,
                            'no_voyage' => $noVoyage,
                            'tanggal_berangkat' => $tanggalBerangkat,
                            'pelabuhan_asal' => (isset($xlsHeaderMap['pelabuhan asal']) ? ($worksheet->getCell("{$xlsHeaderMap['pelabuhan asal']}{$row}")->getValue() ?: null) : ($worksheet->getCell("G{$row}")->getValue() ?: null)),
                            'pelabuhan_tujuan' => (isset($xlsHeaderMap['pelabuhan tujuan']) ? ($worksheet->getCell("{$xlsHeaderMap['pelabuhan tujuan']}{$row}")->getValue() ?: null) : ($worksheet->getCell("H{$row}")->getValue() ?: null)),
                            'nama_barang' => (isset($xlsHeaderMap['nama barang']) ? ($worksheet->getCell("{$xlsHeaderMap['nama barang']}{$row}")->getValue() ?: null) : ($worksheet->getCell("I{$row}")->getValue() ?: null)),
                            'pengirim' => $pengirim ?: (isset($xlsHeaderMap['pengirim']) ? ($worksheet->getCell("{$xlsHeaderMap['pengirim']}{$row}")->getValue() ?: null) : ($worksheet->getCell("J{$row}")->getValue() ?: null)),
                            'penerima' => (isset($xlsHeaderMap['penerima']) ? ($worksheet->getCell("{$xlsHeaderMap['penerima']}{$row}")->getValue() ?: null) : ($worksheet->getCell("K{$row}")->getValue() ?: null)),
                            'alamat_pengiriman' => (isset($xlsHeaderMap['alamat pengiriman']) ? ($worksheet->getCell("{$xlsHeaderMap['alamat pengiriman']}{$row}")->getValue() ?: null) : ($worksheet->getCell("L{$row}")->getValue() ?: null)),
                            'contact_person' => (isset($xlsHeaderMap['contact person']) ? ($worksheet->getCell("{$xlsHeaderMap['contact person']}{$row}")->getValue() ?: null) : ($worksheet->getCell("M{$row}")->getValue() ?: null)),
                            'tipe_kontainer' => (isset($xlsHeaderMap['tipe kontainer']) ? ($worksheet->getCell("{$xlsHeaderMap['tipe kontainer']}{$row}")->getValue() ?: null) : ($worksheet->getCell("N{$row}")->getValue() ?: null)),
                            'size_kontainer' => $autoFilledSize['size'],
                            'tonnage' => $tonnage,
                            'volume' => $volume,
                            'satuan' => (isset($xlsHeaderMap['satuan']) ? ($worksheet->getCell("{$xlsHeaderMap['satuan']}{$row}")->getValue() ?: null) : ($worksheet->getCell("S{$row}")->getValue() ?: null)),
                            'kuantitas' => $kuantitas,
                            'term' => (isset($xlsHeaderMap['term']) ? ($worksheet->getCell("{$xlsHeaderMap['term']}{$row}")->getValue() ?: null) : ($worksheet->getCell("U{$row}")->getValue() ?: null)),
                            'supir_ob' => (isset($xlsHeaderMap['supir ob']) ? ($worksheet->getCell("{$xlsHeaderMap['supir ob']}{$row}")->getValue() ?: null) : ($worksheet->getCell("V{$row}")->getValue() ?: null)),
                            'status_bongkar' => 'Belum Bongkar',
                        ]);

                        // Update status prospek jika kontainer ada di prospek
                        if (!empty($nomorKontainer) && $nomorKontainer !== 'cargo') {
                            Prospek::where('nomor_kontainer', $nomorKontainer)
                                ->where('status', '!=', Prospek::STATUS_SUDAH_MUAT)
                                ->update([
                                    'status' => Prospek::STATUS_SUDAH_MUAT,
                                    'updated_by' => Auth::id()
                                ]);
                        }

                        $importedCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Baris {$row}: " . $e->getMessage() . " ({$rowPreview})";
                    }
                }
            }

            $message = "Berhasil import {$importedCount} data BL.";
            
            \Log::info('Import completed', [
                'imported' => $importedCount,
                'errors' => count($errors),
                'error_details' => $errors
            ]);
            
            if (!empty($errors)) {
                $errorCount = count($errors);
                
                if ($importedCount > 0) {
                    // Partial success - some imported, some failed
                    \Log::warning("Partial import success: {$importedCount} imported, {$errorCount} failed");
                    $firstErrors = array_slice($errors, 0, 3);
                    $firstErrorsMsg = implode('; ', $firstErrors);
                    $moreErrors = max(0, $errorCount - count($firstErrors));
                    $detailsSummary = $firstErrorsMsg . ($moreErrors > 0 ? "; dan {$moreErrors} error lainnya" : '');
                    return redirect()->route('bl.index')
                        ->with('warning', "Import selesai dengan peringatan: {$importedCount} data berhasil diimport, {$errorCount} data gagal. Contoh error: {$detailsSummary}")
                        ->with('import_errors', $errors)
                        ->with('import_warnings', $warnings);
                } else {
                    // Complete failure - nothing imported
                    \Log::error("Import completely failed: {$errorCount} errors");
                    $firstErrors = array_slice($errors, 0, 5);
                    $firstErrorsMsg = implode('; ', $firstErrors);
                    return redirect()->route('bl.index')
                        ->with('error', "Import gagal! Tidak ada data yang berhasil diimport. Total {$errorCount} error. Contoh: {$firstErrorsMsg}")
                        ->with('import_errors', $errors)
                        ->with('import_warnings', $warnings);
                }
            }

            if (empty($errors) && !empty($warnings)) {
                // All rows imported but with non-blocking warnings (e.g., size not found). Show as a warning message.
                $warningCount = count($warnings);
                $firstWarnings = array_slice($warnings, 0, 3);
                $firstWarningsMsg = implode('; ', $firstWarnings);
                $moreWarnings = max(0, $warningCount - count($firstWarnings));
                $detailsSummary = $firstWarningsMsg . ($moreWarnings > 0 ? "; dan {$moreWarnings} peringatan lainnya" : '');
                return redirect()->route('bl.index')
                    ->with('warning', "Import selesai dengan peringatan: {$importedCount} data berhasil diimport, {$warningCount} peringatan. Contoh peringatan: {$detailsSummary}")
                    ->with('import_warnings', $warnings);
            }

            \Log::info("Import fully successful: {$importedCount} records");
            return redirect()->route('bl.index')->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Import exception caught', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Gagal import data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get unique ships for export filter
     */
    public function getShips()
    {
        $ships = Bl::whereNotNull('nama_kapal')
                   ->distinct()
                   ->pluck('nama_kapal')
                   ->sort()
                   ->values();
        
        return response()->json(['ships' => $ships]);
    }

    /**
     * Get voyages for specific ship
     */
    public function getVoyages(Request $request)
    {
        $voyages = Bl::where('nama_kapal', $request->nama_kapal)
                     ->whereNotNull('no_voyage')
                     ->distinct()
                     ->pluck('no_voyage')
                     ->sort()
                     ->values();
        
        return response()->json(['voyages' => $voyages]);
    }

    /**
     * Export BL data to Excel
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        
        // Check permission
        if (!in_array($user->role, ["admin", "user_admin"])) {
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", "bl-view")
                ->exists();
            
            if (!$hasPermission) {
                abort(403, "Tidak memiliki akses untuk export data BL");
            }
        }

        $query = Bl::with(['prospek.suratJalan']);

        // Apply filters
        if ($request->filled('nama_kapal')) {
            $query->where('nama_kapal', $request->nama_kapal);
        }

        if ($request->filled('no_voyage')) {
            $query->where('no_voyage', $request->no_voyage);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_bl', 'like', "%{$search}%")
                  ->orWhere('nomor_kontainer', 'like', "%{$search}%")
                  ->orWhere('no_voyage', 'like', "%{$search}%")
                  ->orWhere('nama_kapal', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%");
            });
        }

        // Get data
        $bls = $query->orderBy('created_at', 'desc')->get();

        // Define all available columns
        $availableColumns = [
            'nomor_bl' => 'Nomor BL',
            'nomor_kontainer' => 'Nomor Kontainer',
            'no_seal' => 'No Seal',
            'nama_kapal' => 'Nama Kapal',
            'no_voyage' => 'No Voyage',
            'pelabuhan_asal' => 'Pelabuhan Asal',
            'pelabuhan_tujuan' => 'Pelabuhan Tujuan',
            'nama_barang' => 'Nama Barang',
            'tipe_kontainer' => 'Tipe Kontainer',
            'size_kontainer' => 'Size Kontainer',
            'tonnage' => 'Tonnage (Ton)',
            'volume' => 'Volume (m³)',
            'kuantitas' => 'Kuantitas',
            'satuan' => 'Satuan',
            'term' => 'Term',
            'penerima' => 'Penerima',
            'no_surat_jalan' => 'No. Surat Jalan',
            'alamat_pengiriman' => 'Alamat Pengiriman',
            'contact_person' => 'Contact Person',
            'supir_ob' => 'Supir OB',
            'status_bongkar' => 'Status Bongkar',
            'sudah_ob' => 'Sudah OB',
            'created_at' => 'Tanggal Dibuat'
        ];

        // Get selected columns
        $selectedColumns = $request->input('columns', array_keys($availableColumns));
        
        // Create filename with filters
        $filename = 'bl_export_' . date('Y-m-d_H-i-s');
        if ($request->filled('nama_kapal')) {
            $filename .= '_' . str_replace(' ', '_', $request->nama_kapal);
        }
        if ($request->filled('no_voyage')) {
            $filename .= '_voyage_' . $request->no_voyage;
        }
        $filename .= '.xlsx';

        // Create Excel file
        return $this->createExcelExport($bls, $availableColumns, $selectedColumns, $filename);
    }

    /**
     * Create Excel export file
     */
    private function createExcelExport($bls, $availableColumns, $selectedColumns, $filename)
    {
        // Create new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data BL');

        // Set headers
        $headers = [];
        foreach ($selectedColumns as $column) {
            $headers[] = $availableColumns[$column] ?? $column;
        }

        // Write headers to first row
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            
            // Style header
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE6E6E6');
            $sheet->getStyle($col . '1')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $col++;
        }

        // Write data rows
        $row = 2;
        foreach ($bls as $bl) {
            $col = 'A';
            foreach ($selectedColumns as $column) {
                $value = '';
                switch ($column) {
                    case 'tonnage':
                        $value = $bl->tonnage ? number_format($bl->tonnage, 3, '.', '') : '';
                        break;
                    case 'volume':
                        $value = $bl->volume ? number_format($bl->volume, 3, '.', '') : '';
                        break;
                    case 'kuantitas':
                        $value = $bl->kuantitas ? number_format($bl->kuantitas, 0) : '';
                        break;
                    case 'sudah_ob':
                        $value = $bl->sudah_ob ? 'Ya' : 'Tidak';
                        break;
                    case 'created_at':
                        $value = $bl->created_at ? $bl->created_at->format('d/m/Y H:i') : '';
                        break;
                    case 'updated_at':
                        $value = $bl->updated_at ? $bl->updated_at->format('d/m/Y H:i') : '';
                        break;
                    case 'alamat_pengiriman':
                        $value = $bl->alamat_pengiriman ? strip_tags($bl->alamat_pengiriman) : '';
                        break;
                    case 'no_surat_jalan':
                        $value = ($bl->prospek && $bl->prospek->suratJalan) ? $bl->prospek->suratJalan->no_surat_jalan : '';
                        break;
                    default:
                        // Handle all other fields safely
                        if (isset($bl->$column)) {
                            $value = $bl->$column ?? '';
                        } else {
                            $value = '';
                        }
                        break;
                }
                
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create Excel writer
        $writer = new Xlsx($spreadsheet);
        
        // Set headers for download
        $filename = 'BL_Export_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return response()->stream(function() use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Get container size from stock_kontainers or kontainers table
     */
    private function getContainerSize($nomorKontainer, $sizeFromFile = null)
    {
        // If size is already provided in file, use it
        if (!empty($sizeFromFile)) {
            return [
                'size' => trim($sizeFromFile),
                'warning' => null
            ];
        }

        // Skip auto-fill for auto-generated cargo containers (case-insensitive)
        if (stripos($nomorKontainer, 'cargo') === 0) {
            return [
                'size' => null,
                'warning' => null
            ];
        }

        \Log::info("Searching container size for: {$nomorKontainer}");

        // First, try to find in stock_kontainers table
        $stockKontainer = StockKontainer::where('nomor_seri_gabungan', $nomorKontainer)->first();
        
        if (!$stockKontainer) {
            // Try searching by parts if the number is formatted or needs parsing
            $cleanNomor = str_replace([' ', '-'], '', $nomorKontainer);
            \Log::info("Clean nomor: {$cleanNomor}, length: " . strlen($cleanNomor));
            
            if (strlen($cleanNomor) === 11) {
                // Standard format: ABCD1234567 (4+6+1)
                $awalan = substr($cleanNomor, 0, 4);
                $seri = substr($cleanNomor, 4, 6);
                $akhiran = substr($cleanNomor, 10, 1);
            } elseif (strlen($cleanNomor) === 10) {
                // Alternative format: ABCD123456 (4+5+1)
                $awalan = substr($cleanNomor, 0, 4);
                $seri = substr($cleanNomor, 4, 5);
                $akhiran = substr($cleanNomor, 9, 1);
            } else {
                \Log::info("Unsupported nomor format length: " . strlen($cleanNomor));
                $awalan = $seri = $akhiran = null;
            }
            
            if ($awalan && $seri && $akhiran) {
                \Log::info("Searching by parts: awalan={$awalan}, seri={$seri}, akhiran={$akhiran}");
                
                $stockKontainer = StockKontainer::where('awalan_kontainer', $awalan)
                    ->where('nomor_seri_kontainer', $seri)
                    ->where('akhiran_kontainer', $akhiran)
                    ->first();
            }
        }

        if ($stockKontainer) {
            \Log::info("Found in stock_kontainers: " . ($stockKontainer->ukuran ?? 'NULL'));
            if ($stockKontainer->ukuran) {
                return [
                    'size' => $stockKontainer->ukuran,
                    'warning' => null
                ];
            }
        }

        // If not found in stock_kontainers, try kontainers table
        // Note: kontainers table uses 'nomor_seri_gabungan' and 'ukuran' columns
        $kontainer = Kontainer::where('nomor_seri_gabungan', $nomorKontainer)->first();
        
        if (!$kontainer) {
            // Try searching by parts
            $cleanNomor = str_replace([' ', '-'], '', $nomorKontainer);
            
            if (strlen($cleanNomor) === 11) {
                $awalan = substr($cleanNomor, 0, 4);
                $seri = substr($cleanNomor, 4, 6);
                $akhiran = substr($cleanNomor, 10, 1);
            } elseif (strlen($cleanNomor) === 10) {
                $awalan = substr($cleanNomor, 0, 4);
                $seri = substr($cleanNomor, 4, 5);
                $akhiran = substr($cleanNomor, 9, 1);
            } else {
                $awalan = $seri = $akhiran = null;
            }
            
            if ($awalan && $seri && $akhiran) {
                $kontainer = Kontainer::where('awalan_kontainer', $awalan)
                    ->where('nomor_seri_kontainer', $seri)
                    ->where('akhiran_kontainer', $akhiran)
                    ->first();
            }
        }
        
        if ($kontainer) {
            \Log::info("Found in kontainers: " . ($kontainer->ukuran ?? 'NULL'));
            if ($kontainer->ukuran) {
                return [
                    'size' => $kontainer->ukuran,
                    'warning' => null
                ];
            }
        }

        \Log::warning("Container size not found for: {$nomorKontainer}");

        // If container not found in either table, do not stop import — return the size from file if provided, otherwise null
        return [
            'size' => $sizeFromFile ? trim($sizeFromFile) : null,
            'warning' => null
        ];
    }
}
