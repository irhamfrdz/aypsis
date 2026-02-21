<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Manifest;
use App\Models\Prospek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ManifestTableExport;

class ManifestController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manifest-view')->only(['index', 'show', 'export']);
        $this->middleware('permission:manifest-create')->only(['create', 'store']);
        $this->middleware('permission:manifest-edit')->only(['edit', 'update']);
        $this->middleware('permission:manifest-delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if kapal and voyage filters are provided
        $namaKapal = $request->get('nama_kapal');
        $noVoyage = $request->get('no_voyage');

        // If no filters, redirect to select ship page
        if (!$namaKapal || !$noVoyage) {
            return redirect()->route('report.manifests.select-ship');
        }

        // Show filtered manifest data
        return $this->showManifestData($request, $namaKapal, $noVoyage);
    }

    /**
     * Display ship selection page
     */
    public function selectShip(Request $request)
    {
        // Get list of ships from manifests table
        $shipsFromManifests = Manifest::whereNotNull('nama_kapal')
            ->select('nama_kapal')
            ->distinct()
            ->pluck('nama_kapal');

        // Get ships from naik_kapal table as well
        $shipsFromNaikKapal = \App\Models\NaikKapal::whereNotNull('nama_kapal')
            ->select('nama_kapal')
            ->distinct()
            ->pluck('nama_kapal');

        // Merge and get unique ship names
        $shipNames = $shipsFromManifests->merge($shipsFromNaikKapal)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        // Convert to objects for view compatibility
        $ships = $shipNames->map(function ($name) {
            return (object)['nama_kapal' => $name];
        });

        return view('manifests.select-ship', compact('ships'));
    }

    /**
     * Display manifest data for selected ship and voyage
     */
    private function showManifestData(Request $request, $namaKapal, $noVoyage)
    {
        // Normalize ship name for flexible matching (consistent with ObController)
        // Remove dots, double spaces, and uppercase
        $normalizedKapal = strtoupper(trim(str_replace('.', '', $namaKapal)));
        $normalizedKapal = str_replace('  ', ' ', $normalizedKapal);
        $noVoyage = trim($noVoyage);

        $query = Manifest::with(['prospek.tandaTerima', 'createdBy', 'updatedBy'])
            ->whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->where('no_voyage', $noVoyage);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_bl', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_kontainer', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_tanda_terima', 'LIKE', "%{$search}%")
                  ->orWhere('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('pengirim', 'LIKE', "%{$search}%")
                  ->orWhere('penerima', 'LIKE', "%{$search}%");
            });
        }

        // Filter by tipe kontainer
        if ($request->filled('tipe_kontainer')) {
            $query->where('tipe_kontainer', $request->tipe_kontainer);
        }

        // Filter by size kontainer
        if ($request->filled('size_kontainer')) {
            $query->where('size_kontainer', $request->size_kontainer);
        }

        $manifests = $query->orderBy('created_at', 'desc')->paginate(20);

        // Store selection in session
        session([
            'selected_manifest_ship' => $namaKapal,
            'selected_manifest_voyage' => $noVoyage
        ]);

        return view('manifests.index', compact('manifests', 'namaKapal', 'noVoyage'));
    }

    /**
     * Export manifest data to Excel
     */
    public function export(Request $request)
    {
        $namaKapal = $request->get('nama_kapal');
        $noVoyage = $request->get('no_voyage');

        if (!$namaKapal || !$noVoyage) {
            return redirect()->back()->with('error', 'Nama Kapal dan No Voyage harus ada untuk export');
        }

        $normalizedKapal = strtoupper(trim(str_replace('.', '', $namaKapal)));
        $normalizedKapal = str_replace('  ', ' ', $normalizedKapal);
        $noVoyage = trim($noVoyage);

        $query = Manifest::with(['prospek.tandaTerima'])->whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->where('no_voyage', $noVoyage);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_bl', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_kontainer', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_tanda_terima', 'LIKE', "%{$search}%")
                  ->orWhere('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('pengirim', 'LIKE', "%{$search}%")
                  ->orWhere('penerima', 'LIKE', "%{$search}%");
            });
        }

        // Filter by tipe kontainer
        if ($request->filled('tipe_kontainer')) {
            $query->where('tipe_kontainer', $request->tipe_kontainer);
        }

        // Filter by size kontainer
        if ($request->filled('size_kontainer')) {
            $query->where('size_kontainer', $request->size_kontainer);
        }

        $manifests = $query->orderBy('created_at', 'desc')->get();

        $filename = 'Manifest_' . str_replace(' ', '_', $namaKapal) . '_' . str_replace('/', '-', $noVoyage) . '.xlsx';

        return Excel::download(new ManifestTableExport($manifests), $filename);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $prospeks = Prospek::orderBy('pt_pengirim')->get();
        return view('manifests.create', compact('prospeks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_bl' => 'required|string|max:255',
            'nomor_urut' => 'nullable|integer',
            'prospek_id' => 'nullable|exists:prospek,id',
            'nomor_kontainer' => 'required|string|max:255',
            'no_seal' => 'nullable|string|max:255',
            'tipe_kontainer' => 'nullable|string|max:255',
            'size_kontainer' => 'nullable|string|max:255',
            'no_voyage' => 'nullable|string|max:255',
            'pelabuhan_asal' => 'nullable|string|max:255',
            'pelabuhan_tujuan' => 'nullable|string|max:255',
            'nama_kapal' => 'nullable|string|max:255',
            'tanggal_berangkat' => 'nullable|date',
            'nama_barang' => 'nullable|string',
            'asal_kontainer' => 'nullable|string|max:255',
            'ke' => 'nullable|string|max:255',
            'pengirim' => 'nullable|string|max:255',
            'penerima' => 'nullable|string|max:255',
            'alamat_pengiriman' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'tonnage' => 'nullable|numeric',
            'volume' => 'nullable|numeric',
            'satuan' => 'nullable|string|max:255',
            'term' => 'nullable|string|max:255',
            'kuantitas' => 'nullable|integer',
            'penerimaan' => 'nullable|date',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        Manifest::create($validated);

        return redirect()->route('report.manifests.index', [
            'nama_kapal' => $validated['nama_kapal'] ?? '',
            'no_voyage' => $validated['no_voyage'] ?? ''
        ])->with('success', 'Manifest berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $manifest = Manifest::with(['prospek', 'createdBy', 'updatedBy'])->findOrFail($id);
        return view('manifests.show', compact('manifest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $manifest = Manifest::findOrFail($id);
        $prospeks = Prospek::orderBy('pt_pengirim')->get();
        return view('manifests.edit', compact('manifest', 'prospeks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $manifest = Manifest::findOrFail($id);

        $validated = $request->validate([
            'nomor_bl' => 'required|string|max:255',
            'nomor_urut' => 'nullable|integer',
            'prospek_id' => 'nullable|exists:prospek,id',
            'nomor_kontainer' => 'required|string|max:255',
            'no_seal' => 'nullable|string|max:255',
            'tipe_kontainer' => 'nullable|string|max:255',
            'size_kontainer' => 'nullable|string|max:255',
            'no_voyage' => 'nullable|string|max:255',
            'pelabuhan_asal' => 'nullable|string|max:255',
            'pelabuhan_tujuan' => 'nullable|string|max:255',
            'nama_kapal' => 'nullable|string|max:255',
            'tanggal_berangkat' => 'nullable|date',
            'nama_barang' => 'nullable|string',
            'asal_kontainer' => 'nullable|string|max:255',
            'ke' => 'nullable|string|max:255',
            'pengirim' => 'nullable|string|max:255',
            'penerima' => 'nullable|string|max:255',
            'alamat_pengiriman' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'tonnage' => 'nullable|numeric',
            'volume' => 'nullable|numeric',
            'satuan' => 'nullable|string|max:255',
            'term' => 'nullable|string|max:255',
            'kuantitas' => 'nullable|integer',
            'penerimaan' => 'nullable|date',
        ]);

        $validated['updated_by'] = Auth::id();

        $manifest->update($validated);

        return redirect()->route('report.manifests.index', [
            'nama_kapal' => $manifest->nama_kapal,
            'no_voyage' => $manifest->no_voyage
        ])->with('success', 'Manifest berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $manifest = Manifest::findOrFail($id);
        $namaKapal = $manifest->nama_kapal;
        $noVoyage = $manifest->no_voyage;

        $manifest->delete();

        return redirect()->route('report.manifests.index', [
            'nama_kapal' => $namaKapal,
            'no_voyage' => $noVoyage
        ])->with('success', 'Manifest berhasil dihapus');
    }

    /**
     * Update nomor BL via AJAX
     */
    public function updateNomorBl(Request $request, string $id)
    {
        $manifest = Manifest::findOrFail($id);

        $validated = $request->validate([
            'nomor_bl' => 'required|string|max:255',
        ]);

        $validated['updated_by'] = Auth::id();

        $manifest->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Nomor BL berhasil diperbarui',
            'nomor_bl' => $manifest->nomor_bl
        ]);
    }

    /**
     * Auto update nomor urut (FCL 1,2.. LCL 1,2..) for a ship/voyage
     */
    public function autoUpdateNomorUrut(Request $request)
    {
        $namaKapal = $request->input('nama_kapal');
        $noVoyage = $request->input('no_voyage');

        if (!$namaKapal || !$noVoyage) {
            return response()->json(['success' => false, 'message' => 'Data kapal dan voyage tidak valid'], 400);
        }

        $normalizedKapal = strtoupper(trim(str_replace('.', '', $namaKapal)));
        $normalizedKapal = str_replace('  ', ' ', $normalizedKapal);
        $noVoyage = trim($noVoyage);

        // Get all manifests for this voyage, ordered by ID (creation order)
        $manifests = Manifest::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->where('no_voyage', $noVoyage)
            ->orderBy('id', 'asc')
            ->get();

        $fclCounter = 1;
        $lclCounter = 1;
        $updated = 0;

        foreach ($manifests as $manifest) {
            $isLcl = false;
            $isCargo = false;
            
            // Determine if Cargo based on tipe_kontainer or size_kontainer
            if (
                (!empty($manifest->tipe_kontainer) && stripos($manifest->tipe_kontainer, 'Cargo') !== false) ||
                (!empty($manifest->size_kontainer) && stripos($manifest->size_kontainer, 'Cargo') !== false)
            ) {
                $isCargo = true;
            }

            // Determine if LCL based on tipe_kontainer or size_kontainer
            if (
                (!empty($manifest->tipe_kontainer) && stripos($manifest->tipe_kontainer, 'LCL') !== false) ||
                (!empty($manifest->size_kontainer) && stripos($manifest->size_kontainer, 'LCL') !== false)
            ) {
                $isLcl = true;
            }

            if ($isCargo) {
                $manifest->nomor_urut = null; // Cargo gets no number
            } elseif ($isLcl) {
                $manifest->nomor_urut = $lclCounter++;
            } else {
                $manifest->nomor_urut = $fclCounter++;
            }

            $manifest->save();
            $updated++;
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil update nomor urut untuk {$updated} data manifest (FCL: " . ($fclCounter - 1) . ", LCL: " . ($lclCounter - 1) . ")",
        ]);
    }

    /**
     * Update nomor urut via AJAX
     */
    public function updateNomorUrut(Request $request, string $id)
    {
        $manifest = Manifest::findOrFail($id);

        $validated = $request->validate([
            'nomor_urut' => 'nullable|integer',
        ]);

        $validated['updated_by'] = Auth::id();

        $manifest->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Nomor urut berhasil diperbarui',
            'nomor_urut' => $manifest->nomor_urut
        ]);
    }

    /**
     * Import manifests from Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
            'nama_kapal' => 'required|string',
            'no_voyage' => 'required|string',
        ]);

        try {
            $file = $request->file('file');
            $namaKapal = $request->input('nama_kapal');
            $noVoyage = $request->input('no_voyage');

            $import = new \App\Imports\ManifestImport($namaKapal, $noVoyage);
            $result = $import->import($file);

            if ($result === false) {
                return redirect()->back()
                    ->with('error', 'Import gagal: ' . implode(', ', $import->getErrors()));
            }

            $successCount = $result['success_count'];
            $errors = $result['errors'];

            if ($successCount > 0 && empty($errors)) {
                return redirect()->route('report.manifests.index', [
                    'nama_kapal' => $namaKapal,
                    'no_voyage' => $noVoyage
                ])->with('success', "Berhasil import {$successCount} data manifest");
            } elseif ($successCount > 0 && !empty($errors)) {
                return redirect()->route('report.manifests.index', [
                    'nama_kapal' => $namaKapal,
                    'no_voyage' => $noVoyage
                ])->with('warning', "Import selesai dengan {$successCount} data berhasil, namun ada " . count($errors) . " error: " . implode('; ', array_slice($errors, 0, 3)));
            } else {
                return redirect()->back()
                    ->with('error', 'Import gagal: ' . implode('; ', array_slice($errors, 0, 5)));
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel untuk import
     */
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'No BL',
            'No Manifest',
            'No Kontainer',
            'No Seal',
            'Tipe Kontainer',
            'Size Kontainer',
            'Nama Barang',
            'SHIPPER',
            'Alamat Pengirim',
            'CONSIGNEE',
            'Term'
        ];

        // Write headers in row 1
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Write example data in row 2
        $exampleData = [
            'BL001',
            'MN001',
            'CONT001',
            'SEAL001',
            'Dry Container',
            '20',
            'Barang Contoh',
            'PT Pengirim',
            'Alamat Pengirim Contoh',
            'PT Penerima',
            'FOB'
        ];

        $col = 'A';
        foreach ($exampleData as $data) {
            $sheet->setCellValue($col . '2', $data);
            $col++;
        }

        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template_import_manifest.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Bulk import manifests from Excel (with ship and voyage in file)
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('file');
            
            // Parse Excel to get all data
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = [];

            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];

                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }

                $rows[] = $rowData;
            }

            if (empty($rows) || count($rows) < 2) {
                return redirect()->back()->with('error', 'File Excel kosong atau tidak valid');
            }

            // Remove header row
            $header = array_shift($rows);
            
            $successCount = 0;
            $errors = [];
            $shipsProcessed = [];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;
                
                // Extract data - sekarang dengan 25 kolom lengkap
                $namaKapal = trim($row[0] ?? '');
                $noVoyage = trim($row[1] ?? '');
                $tanggalBerangkat = trim($row[2] ?? '');
                $nomorBl = trim($row[3] ?? '');
                $nomorManifest = trim($row[4] ?? '');
                $nomorTandaTerima = trim($row[5] ?? '');
                $nomorKontainer = trim($row[6] ?? '');
                $noSeal = trim($row[7] ?? '');
                $tipeKontainer = trim($row[8] ?? '');
                $sizeKontainer = trim($row[9] ?? '');
                $namaBarang = trim($row[10] ?? '');
                $pengirim = trim($row[11] ?? '');
                $alamatPengirim = trim($row[12] ?? '');
                $penerima = trim($row[13] ?? '');
                $alamatPenerima = trim($row[14] ?? '');
                $contactPerson = trim($row[15] ?? '');
                $term = trim($row[16] ?? '');
                $tonnage = trim($row[17] ?? '');
                $volume = trim($row[18] ?? '');
                $satuan = trim($row[19] ?? '');
                $kuantitas = trim($row[20] ?? '');
                $pelabuhanMuat = trim($row[21] ?? '');
                $pelabuhanBongkar = trim($row[22] ?? '');
                $asalKontainer = trim($row[23] ?? '');
                $ke = trim($row[24] ?? '');
                
                // Parse tanggal berangkat
                if (!empty($tanggalBerangkat)) {
                    try {
                        if (is_numeric($tanggalBerangkat)) {
                            // Excel date format (serial number)
                            $tanggalBerangkat = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalBerangkat)->format('Y-m-d');
                        } else {
                            // String date format
                            $tanggalBerangkat = date('Y-m-d', strtotime($tanggalBerangkat));
                        }
                    } catch (\Exception $e) {
                        $tanggalBerangkat = null;
                    }
                } else {
                    $tanggalBerangkat = null;
                }

                // Jika nomor kontainer kosong, isi dengan "Cargo" dan set tipe kontainer menjadi "Cargo"
                if (empty($nomorKontainer)) {
                    $nomorKontainer = 'Cargo';
                    $tipeKontainer = 'Cargo';
                }

                // Validate required fields
                if (empty($namaKapal) || empty($noVoyage) || empty($nomorBl)) {
                    $errors[] = "Baris {$rowNumber}: Nama Kapal, No Voyage, dan No BL wajib diisi";
                    continue;
                }

                try {
                    // Check if manifest already exists
                    $existing = Manifest::where('nomor_bl', $nomorBl)
                        ->where('nomor_kontainer', $nomorKontainer)
                        ->where('nama_kapal', $namaKapal)
                        ->where('no_voyage', $noVoyage)
                        ->first();

                    if ($existing) {
                        // Update existing
                        $existing->update([
                            'tanggal_berangkat' => $tanggalBerangkat ?: $existing->tanggal_berangkat,
                            'nomor_manifest' => $nomorManifest ?: $existing->nomor_manifest,
                            'nomor_tanda_terima' => $nomorTandaTerima ?: $existing->nomor_tanda_terima,
                            'no_seal' => $noSeal ?: $existing->no_seal,
                            'tipe_kontainer' => $tipeKontainer ?: $existing->tipe_kontainer,
                            'size_kontainer' => $sizeKontainer ?: $existing->size_kontainer,
                            'nama_barang' => $namaBarang ?: $existing->nama_barang,
                            'pengirim' => $pengirim ?: $existing->pengirim,
                            'alamat_pengirim' => $alamatPengirim ?: $existing->alamat_pengirim,
                            'penerima' => $penerima ?: $existing->penerima,
                            'alamat_penerima' => $alamatPenerima ?: $existing->alamat_penerima,
                            'contact_person' => $contactPerson ?: $existing->contact_person,
                            'term' => $term ?: $existing->term,
                            'tonnage' => $tonnage ?: $existing->tonnage,
                            'volume' => $volume ?: $existing->volume,
                            'satuan' => $satuan ?: $existing->satuan,
                            'kuantitas' => $kuantitas ?: $existing->kuantitas,
                            'pelabuhan_muat' => $pelabuhanMuat ?: $existing->pelabuhan_muat,
                            'pelabuhan_bongkar' => $pelabuhanBongkar ?: $existing->pelabuhan_bongkar,
                            'asal_kontainer' => $asalKontainer ?: $existing->asal_kontainer,
                            'ke' => $ke ?: $existing->ke,
                        ]);
                    } else {
                        // Create new
                        Manifest::create([
                            'nomor_bl' => $nomorBl,
                            'nomor_manifest' => $nomorManifest,
                            'nomor_tanda_terima' => $nomorTandaTerima,
                            'nomor_kontainer' => $nomorKontainer,
                            'no_seal' => $noSeal,
                            'nama_kapal' => $namaKapal,
                            'no_voyage' => $noVoyage,
                            'tanggal_berangkat' => $tanggalBerangkat,
                            'tipe_kontainer' => $tipeKontainer,
                            'size_kontainer' => $sizeKontainer,
                            'nama_barang' => $namaBarang,
                            'pengirim' => $pengirim,
                            'alamat_pengirim' => $alamatPengirim,
                            'penerima' => $penerima,
                            'alamat_penerima' => $alamatPenerima,
                            'contact_person' => $contactPerson,
                            'term' => $term,
                            'tonnage' => $tonnage ?: null,
                            'volume' => $volume ?: null,
                            'satuan' => $satuan,
                            'kuantitas' => $kuantitas ?: null,
                            'pelabuhan_muat' => $pelabuhanMuat,
                            'pelabuhan_bongkar' => $pelabuhanBongkar,
                            'asal_kontainer' => $asalKontainer,
                            'ke' => $ke,
                            'created_by' => Auth::id(),
                        ]);
                    }

                    $successCount++;
                    
                    // Track ships processed
                    $shipKey = $namaKapal . '|' . $noVoyage;
                    if (!isset($shipsProcessed[$shipKey])) {
                        $shipsProcessed[$shipKey] = [
                            'nama_kapal' => $namaKapal,
                            'no_voyage' => $noVoyage,
                            'count' => 0
                        ];
                    }
                    $shipsProcessed[$shipKey]['count']++;

                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }

            // Build success message with ship summary
            $shipSummary = '';
            if (!empty($shipsProcessed)) {
                $shipSummary = ' (' . implode(', ', array_map(function($ship) {
                    return $ship['nama_kapal'] . ' - ' . $ship['no_voyage'] . ': ' . $ship['count'] . ' manifest';
                }, $shipsProcessed)) . ')';
            }

            if ($successCount > 0 && empty($errors)) {
                // Redirect to first ship's manifest page
                $firstShip = reset($shipsProcessed);
                return redirect()->route('report.manifests.index', [
                    'nama_kapal' => $firstShip['nama_kapal'],
                    'no_voyage' => $firstShip['no_voyage']
                ])->with('success', "Berhasil import {$successCount} data manifest{$shipSummary}");
            } elseif ($successCount > 0 && !empty($errors)) {
                $firstShip = reset($shipsProcessed);
                return redirect()->route('report.manifests.index', [
                    'nama_kapal' => $firstShip['nama_kapal'],
                    'no_voyage' => $firstShip['no_voyage']
                ])->with('warning', "Import selesai dengan {$successCount} data berhasil{$shipSummary}, namun ada " . count($errors) . " error");
            } else {
                return redirect()->back()
                    ->with('error', 'Import gagal: ' . implode('; ', array_slice($errors, 0, 5)));
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download bulk template Excel (with ship and voyage columns)
     */
    public function downloadBulkTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers - kolom lengkap untuk table manifests
        $headers = [
            'Nama Kapal',
            'No Voyage',
            'Tanggal Berangkat',
            'No BL',
            'No Manifest',
            'No Tanda Terima',
            'No Kontainer',
            'No Seal',
            'Tipe Kontainer',
            'Size Kontainer',
            'Nama Barang',
            'SHIPPER',
            'Alamat Pengirim',
            'CONSIGNEE',
            'Alamat Penerima',
            'Contact Person',
            'Term',
            'Tonnage',
            'Volume',
            'Satuan',
            'Kuantitas',
            'Pelabuhan Muat',
            'Pelabuhan Bongkar',
            'Asal Kontainer',
            'Ke'
        ];

        // Write headers in row 1
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Write example data in row 2
        $exampleData = [
            'KM EXAMPLE',           // Nama Kapal
            '001',                  // No Voyage
            '2026-01-15',          // Tanggal Berangkat
            'BL001',               // No BL
            'MN001',               // No Manifest
            'TT001',               // No Tanda Terima
            'CONT001',             // No Kontainer
            'SEAL001',             // No Seal
            'Dry Container',       // Tipe Kontainer
            '20',                  // Size Kontainer
            'Barang Contoh',       // Nama Barang
            'PT Pengirim',         // Pengirim
            'Jl. Pengirim No.1',  // Alamat Pengirim
            'PT Penerima',         // Penerima
            'Jl. Penerima No.2',  // Alamat Penerima
            '08123456789',         // Contact Person
            'FOB',                 // Term
            '10.500',              // Tonnage
            '25.000',              // Volume
            'M3',                  // Satuan
            '100',                 // Kuantitas
            'Jakarta',             // Pelabuhan Muat
            'Surabaya',            // Pelabuhan Bongkar
            'Jakarta',             // Asal Kontainer
            'Gudang A'             // Ke
        ];

        $col = 'A';
        foreach ($exampleData as $data) {
            $sheet->setCellValue($col . '2', $data);
            $col++;
        }

        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template_bulk_import_manifest.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Get voyages by ship name (for AJAX)
     */
    public function getVoyagesByShip($namaKapal)
    {
        try {
            // Decode URL-encoded ship name
            $namaKapal = urldecode($namaKapal);
            
            // Normalize ship name for loose matching
            $normalizedKapal = strtoupper(trim(str_replace('.', '', $namaKapal)));
            $normalizedKapal = str_replace('  ', ' ', $normalizedKapal);

            // Get distinct normalized voyages for the ship using loose matching
            $voyages = Manifest::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
                ->select('no_voyage')
                ->distinct()
                ->orderBy('no_voyage', 'asc')
                ->pluck('no_voyage')
                ->map(function($voyage) {
                    // Normalize voyage: trim and uppercase
                    return strtoupper(trim($voyage));
                })
                ->unique()
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'voyages' => $voyages,
                'count' => count($voyages)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'voyages' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

