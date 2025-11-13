<?php

namespace App\Http\Controllers;

use App\Models\Bl;
use App\Models\MasterKapal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            'F1' => 'Pelabuhan Tujuan',
            'G1' => 'Nama Barang',
            'H1' => 'Tipe Kontainer',
            'I1' => 'Ukuran Kontainer',
            'J1' => 'Tonnage',
            'K1' => 'Volume',
            'L1' => 'Kuantitas',
            'M1' => 'Term',
            'N1' => 'Supir OB',
            'O1' => 'Tanggal Muat',
            'P1' => 'Jam Muat',
            'Q1' => 'Prospek ID',
            'R1' => 'Keterangan'
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
                '20 FT',
                '20x8x8.6',
                15.500,
                25.750,
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
                '40 FT',
                '40x8x8.6',
                25.000,
                45.300,
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
        foreach (range('A', 'R') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add instructions sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Petunjuk');
        
        $instructions = [
            ['PETUNJUK PENGGUNAAN TEMPLATE IMPORT BL'],
            [''],
            ['Kolom yang wajib diisi (bertanda *):'],
            ['- Nomor Kontainer: Nomor kontainer yang digunakan'],
            ['- Nama Kapal: Nama kapal yang mengangkut'],
            ['- No Voyage: Nomor voyage/pelayaran'],
            [''],
            ['Format Data:'],
            ['- Tanggal Muat: YYYY-MM-DD (contoh: 2025-11-12)'],
            ['- Jam Muat: HH:MM (contoh: 08:00)'],
            ['- Tonnage: Angka desimal dengan titik (contoh: 15.500)'],
            ['- Volume: Angka desimal dengan titik (contoh: 25.750)'],
            ['- Kuantitas: Angka bulat (contoh: 100)'],
            ['- Prospek ID: Kosongkan jika tidak ada prospek terkait'],
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
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            
            $importedCount = 0;
            $errors = [];
            $rowNumber = 1;

            if ($extension === 'csv') {
                // Handle CSV import
                $handle = fopen($file->getRealPath(), 'r');
                
                // Skip header row
                fgetcsv($handle);
                $rowNumber++;

                while (($row = fgetcsv($handle)) !== false) {
                    try {
                        // Validate required fields
                        if (empty($row[1]) || empty($row[3]) || empty($row[4])) {
                            $errors[] = "Baris {$rowNumber}: Nomor Kontainer, Nama Kapal, dan No Voyage wajib diisi";
                            $rowNumber++;
                            continue;
                        }

                        // Create BL record
                        Bl::create([
                            'nomor_bl' => $row[0] ?: null,
                            'nomor_kontainer' => $row[1],
                            'no_seal' => $row[2] ?: null,
                            'nama_kapal' => $row[3],
                            'no_voyage' => $row[4],
                            'tujuan' => $row[5] ?: null,
                            'nama_barang' => $row[6] ?: null,
                            'tipe_kontainer' => $row[7] ?: null,
                            'ukuran_kontainer' => $row[8] ?: null,
                            'tonnage' => !empty($row[9]) ? (float)str_replace(',', '.', $row[9]) : null,
                            'volume' => !empty($row[10]) ? (float)str_replace(',', '.', $row[10]) : null,
                            'kuantitas' => !empty($row[11]) ? (int)$row[11] : null,
                            'term' => $row[12] ?: null,
                            'supir_ob' => $row[13] ?: null,
                            'tanggal_muat' => !empty($row[14]) ? $row[14] : null,
                            'jam_muat' => $row[15] ?: null,
                            'prospek_id' => !empty($row[16]) ? (int)$row[16] : null,
                            'keterangan' => $row[17] ?: null,
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

                        // Skip empty rows
                        if (empty($nomorKontainer) && empty($namaKapal) && empty($noVoyage)) {
                            continue;
                        }

                        // Validate required fields
                        if (empty($nomorKontainer) || empty($namaKapal) || empty($noVoyage)) {
                            $errors[] = "Baris {$row}: Nomor Kontainer, Nama Kapal, dan No Voyage wajib diisi";
                            continue;
                        }

                        // Create BL record
                        Bl::create([
                            'nomor_bl' => $worksheet->getCell("A{$row}")->getValue() ?: null,
                            'nomor_kontainer' => $nomorKontainer,
                            'no_seal' => $worksheet->getCell("C{$row}")->getValue() ?: null,
                            'nama_kapal' => $namaKapal,
                            'no_voyage' => $noVoyage,
                            'tujuan' => $worksheet->getCell("F{$row}")->getValue() ?: null,
                            'nama_barang' => $worksheet->getCell("G{$row}")->getValue() ?: null,
                            'tipe_kontainer' => $worksheet->getCell("H{$row}")->getValue() ?: null,
                            'ukuran_kontainer' => $worksheet->getCell("I{$row}")->getValue() ?: null,
                            'tonnage' => $worksheet->getCell("J{$row}")->getValue() ?: null,
                            'volume' => $worksheet->getCell("K{$row}")->getValue() ?: null,
                            'kuantitas' => $worksheet->getCell("L{$row}")->getValue() ?: null,
                            'term' => $worksheet->getCell("M{$row}")->getValue() ?: null,
                            'supir_ob' => $worksheet->getCell("N{$row}")->getValue() ?: null,
                            'tanggal_muat' => $worksheet->getCell("O{$row}")->getValue() ?: null,
                            'jam_muat' => $worksheet->getCell("P{$row}")->getValue() ?: null,
                            'prospek_id' => $worksheet->getCell("Q{$row}")->getValue() ?: null,
                            'keterangan' => $worksheet->getCell("R{$row}")->getValue() ?: null,
                            'status_bongkar' => 'Belum Bongkar',
                        ]);

                        $importedCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Baris {$row}: " . $e->getMessage();
                    }
                }
            }

            $message = "Berhasil import {$importedCount} data BL.";
            if (!empty($errors)) {
                $message .= " Terdapat " . count($errors) . " error: " . implode("; ", array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " dan " . (count($errors) - 5) . " error lainnya.";
                }
            }

            return redirect()->route('bl.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }
}
