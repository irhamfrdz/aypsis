<?php

namespace App\Http\Controllers;

use App\Models\Bl;
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

        $query = Bl::with('prospek');

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
            $query->where('nama_kapal', 'like', "%{$request->kapal}%");
        }
        
        // Filter berdasarkan nama_kapal (dari select page)
        if ($request->filled('nama_kapal')) {
            $query->where('nama_kapal', 'like', "%{$request->nama_kapal}%");
        }

        // Filter berdasarkan voyage
        if ($request->filled('voyage')) {
            $query->where('no_voyage', $request->voyage);
        }
        
        // Filter berdasarkan no_voyage (dari select page)
        if ($request->filled('no_voyage')) {
            $query->where('no_voyage', $request->no_voyage);
        }

        // Sort berdasarkan parameter
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSorts = ['created_at', 'nomor_bl', 'nomor_kontainer', 'nama_kapal', 'no_voyage', 'nama_barang'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $bls = $query->paginate(15)->withQueryString();

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

        $masterKapals = MasterKapal::orderBy('nama_kapal')->get();
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
            'nama_barang_dipindah' => 'required|string|max:255',
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

        $bls = Bl::with('prospek')
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
            'F1' => 'Pelabuhan Asal',
            'G1' => 'Pelabuhan Tujuan',
            'H1' => 'Nama Barang',
            'I1' => 'Penerima',
            'J1' => 'Alamat Pengiriman',
            'K1' => 'Contact Person',
            'L1' => 'Tipe Kontainer',
            'M1' => 'Ukuran Kontainer',
            'N1' => 'Tonnage',
            'O1' => 'Volume',
            'P1' => 'Satuan',
            'Q1' => 'Kuantitas',
            'R1' => 'Term',
            'S1' => 'Supir OB',
            'T1' => 'Tanggal Muat',
            'U1' => 'Jam Muat',
            'V1' => 'Prospek ID',
            'W1' => 'Keterangan'
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
                'Batam',
                'Elektronik',
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
                'Jakarta',
                'Makanan & Minuman',
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
        foreach (range('A', 'V') as $col) {
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
            $rowNumber = 1;
            
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
                
                // Skip header row
                $header = fgetcsv($handle, 0, ';');
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
                            $errors[] = "Baris {$rowNumber}: Format CSV tidak lengkap (hanya " . count($row) . " kolom, minimal 15 kolom)";
                            $rowNumber++;
                            continue;
                        }
                        
                        // Mapping kolom sesuai format template:
                        // 0: nomor_bl
                        // 1: nomor_kontainer
                        // 2: no_seal
                        // 3: nama_kapal
                        // 4: no_voyage
                        // 5: pelabuhan_asal
                        // 6: pelabuhan_tujuan
                        // 7: nama_barang
                        // 8: penerima
                        // 9: alamat_pengiriman
                        // 10: contact_person
                        // 11: tipe_kontainer
                        // 12: ukuran_kontainer
                        // 13: tonnage
                        // 14: volume
                        // 15: satuan
                        // 16: kuantitas
                        // 17: term
                        // 18: supir_ob
                        // 19: tanggal_muat
                        // 20: jam_muat
                        // 21: prospek_id
                        // 22: keterangan
                        
                        $nomorKontainer = isset($row[1]) ? trim($row[1]) : null;
                        $namaKapal = isset($row[3]) ? trim($row[3]) : null;
                        $noVoyage = isset($row[4]) ? trim($row[4]) : null;
                        $sizeKontainerFromFile = isset($row[12]) ? trim($row[12]) : null;
                        
                        // Auto-generate container number if empty
                        if (empty($nomorKontainer)) {
                            $nomorKontainer = 'CARGO-' . $nextCargoNumber;
                            $nextCargoNumber++;
                        }
                        
                        // Auto-fill size kontainer from database if not provided in file
                        $autoFilledSize = $this->getContainerSize($nomorKontainer, $sizeKontainerFromFile);
                        if ($autoFilledSize['warning']) {
                            $errors[] = "Baris {$rowNumber}: " . $autoFilledSize['warning'];
                        }
                        
                        // Validate required fields (now only kapal and voyage since container is auto-generated)
                        if (empty($namaKapal) || empty($noVoyage)) {
                            $errors[] = "Baris {$rowNumber}: Nama Kapal dan No Voyage wajib diisi";
                            $rowNumber++;
                            continue;
                        }

                        // Parse tonnage dan volume
                        $tonnage = null;
                        if (isset($row[13]) && !empty(trim($row[13]))) {
                            $tonnageStr = str_replace(['.', ','], ['', '.'], trim($row[13]));
                            $tonnage = (float)$tonnageStr;
                        }
                        
                        $volume = null;
                        if (isset($row[14]) && !empty(trim($row[14]))) {
                            $volumeStr = str_replace(['.', ','], ['', '.'], trim($row[14]));
                            $volume = (float)$volumeStr;
                        }

                        // Create BL record
                        Bl::create([
                            'nomor_bl' => isset($row[0]) ? trim($row[0]) : null,
                            'nomor_kontainer' => $nomorKontainer,
                            'no_seal' => isset($row[2]) ? trim($row[2]) : null,
                            'nama_kapal' => $namaKapal,
                            'no_voyage' => $noVoyage,
                            'pelabuhan_asal' => isset($row[5]) ? trim($row[5]) : null,
                            'pelabuhan_tujuan' => isset($row[6]) ? trim($row[6]) : null,
                            'nama_barang' => isset($row[7]) ? trim($row[7]) : null,
                            'penerima' => isset($row[8]) ? trim($row[8]) : null,
                            'alamat_pengiriman' => isset($row[9]) ? trim($row[9]) : null,
                            'contact_person' => isset($row[10]) ? trim($row[10]) : null,
                            'tipe_kontainer' => isset($row[11]) ? trim($row[11]) : null,
                            'size_kontainer' => $autoFilledSize['size'],
                            'tonnage' => $tonnage,
                            'volume' => $volume,
                            'satuan' => isset($row[15]) ? trim($row[15]) : null,
                            'kuantitas' => isset($row[16]) && !empty(trim($row[16])) ? (int)trim($row[16]) : null,
                            'term' => isset($row[17]) ? trim($row[17]) : null,
                            'supir_ob' => isset($row[18]) ? trim($row[18]) : null,
                            'status_bongkar' => 'Belum Bongkar',
                        ]);

                        $importedCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                    }
                    
                    $rowNumber++;
                }
                
                fclose($handle);
            } else {
                // Handle Excel import using PhpSpreadsheet
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();

                for ($row = 2; $row <= $highestRow; $row++) {
                    try {
                        $nomorKontainer = $worksheet->getCell("B{$row}")->getValue();
                        $namaKapal = $worksheet->getCell("D{$row}")->getValue();
                        $noVoyage = $worksheet->getCell("E{$row}")->getValue();
                        $sizeKontainerFromFile = $worksheet->getCell("M{$row}")->getValue();

                        // Skip empty rows
                        if (empty($namaKapal) && empty($noVoyage)) {
                            continue;
                        }
                        
                        // Auto-generate container number if empty
                        if (empty($nomorKontainer)) {
                            $nomorKontainer = 'CARGO-' . $nextCargoNumber;
                            $nextCargoNumber++;
                        }
                        
                        // Auto-fill size kontainer from database if not provided in file
                        $autoFilledSize = $this->getContainerSize($nomorKontainer, $sizeKontainerFromFile);
                        if ($autoFilledSize['warning']) {
                            $errors[] = "Baris {$row}: " . $autoFilledSize['warning'];
                        }

                        // Validate required fields (now only kapal and voyage since container is auto-generated)
                        if (empty($namaKapal) || empty($noVoyage)) {
                            $errors[] = "Baris {$row}: Nama Kapal dan No Voyage wajib diisi";
                            continue;
                        }

                        // Create BL record
                        Bl::create([
                            'nomor_bl' => $worksheet->getCell("A{$row}")->getValue() ?: null,
                            'nomor_kontainer' => $nomorKontainer,
                            'no_seal' => $worksheet->getCell("C{$row}")->getValue() ?: null,
                            'nama_kapal' => $namaKapal,
                            'no_voyage' => $noVoyage,
                            'pelabuhan_asal' => $worksheet->getCell("F{$row}")->getValue() ?: null,
                            'pelabuhan_tujuan' => $worksheet->getCell("G{$row}")->getValue() ?: null,
                            'nama_barang' => $worksheet->getCell("H{$row}")->getValue() ?: null,
                            'penerima' => $worksheet->getCell("I{$row}")->getValue() ?: null,
                            'alamat_pengiriman' => $worksheet->getCell("J{$row}")->getValue() ?: null,
                            'contact_person' => $worksheet->getCell("K{$row}")->getValue() ?: null,
                            'tipe_kontainer' => $worksheet->getCell("L{$row}")->getValue() ?: null,
                            'size_kontainer' => $autoFilledSize['size'],
                            'tonnage' => $worksheet->getCell("N{$row}")->getValue() ?: null,
                            'volume' => $worksheet->getCell("O{$row}")->getValue() ?: null,
                            'satuan' => $worksheet->getCell("P{$row}")->getValue() ?: null,
                            'kuantitas' => $worksheet->getCell("Q{$row}")->getValue() ?: null,
                            'term' => $worksheet->getCell("R{$row}")->getValue() ?: null,
                            'supir_ob' => $worksheet->getCell("S{$row}")->getValue() ?: null,
                            'status_bongkar' => 'Belum Bongkar',
                        ]);

                        $importedCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Baris {$row}: " . $e->getMessage();
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
                    return redirect()->route('bl.index')
                        ->with('warning', "Import selesai dengan peringatan: {$importedCount} data berhasil diimport, {$errorCount} data gagal.")
                        ->with('import_errors', $errors);
                } else {
                    // Complete failure - nothing imported
                    \Log::error("Import completely failed: {$errorCount} errors");
                    return redirect()->route('bl.index')
                        ->with('error', "Import gagal! Tidak ada data yang berhasil diimport. Total {$errorCount} error.")
                        ->with('import_errors', $errors);
                }
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

        $query = Bl::with('prospek');

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

        // Skip auto-fill for auto-generated CARGO containers
        if (strpos($nomorKontainer, 'CARGO-') === 0) {
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

        // If container not found in either table, return warning
        return [
            'size' => null,
            'warning' => "Size kontainer untuk nomor '{$nomorKontainer}' tidak ditemukan di database. Silakan isi manual atau tambahkan ke master data kontainer."
        ];
    }
}
