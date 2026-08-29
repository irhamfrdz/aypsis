<?php

namespace App\Http\Controllers;

use App\Models\Perincian;
use App\Models\Prospek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerincianController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:perincian-view')->only(['index', 'show', 'export']);
        $this->middleware('permission:perincian-create')->only(['create', 'store']);
        $this->middleware('permission:perincian-edit')->only(['edit', 'update']);
        $this->middleware('permission:perincian-delete')->only(['destroy']);
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
        if (! $namaKapal || ! $noVoyage) {
            return redirect()->route('report.perincians.select-ship');
        }

        // Show filtered perincian data
        return $this->showPerincianData($request, $namaKapal, $noVoyage);
    }

    /**
     * Display ship selection page
     */
    public function selectShip(Request $request)
    {
        // Get list of ships from perincians table
        $shipsFromPerincians = Perincian::whereNotNull('nama_kapal')
            ->select('nama_kapal')
            ->distinct()
            ->pluck('nama_kapal');

        // Get ships from naik_kapal table as well
        $shipsFromNaikKapal = \App\Models\NaikKapal::whereNotNull('nama_kapal')
            ->select('nama_kapal')
            ->distinct()
            ->pluck('nama_kapal');

        // Merge and get unique ship names
        $shipNames = $shipsFromPerincians->merge($shipsFromNaikKapal)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        // Convert to objects for view compatibility
        $ships = $shipNames->map(function ($name) {
            return (object) ['nama_kapal' => $name];
        });

        return view('perincians.select-ship', compact('ships'));
    }

    /**
     * Display perincian data for selected ship and voyage
     */
    private function showPerincianData(Request $request, $namaKapal, $noVoyage)
    {
        // Normalize ship name for flexible matching
        $normalizedKapal = strtoupper(trim(str_replace('.', '', $namaKapal)));
        $normalizedKapal = str_replace('  ', ' ', $normalizedKapal);
        $noVoyage = trim($noVoyage);

        $query = Perincian::with(['prospek.tandaTerima', 'createdBy', 'updatedBy'])
            ->whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->where('no_voyage', $noVoyage);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
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
            $tipe = $request->tipe_kontainer;
            $query->whereRaw('UPPER(tipe_kontainer) = ?', [strtoupper($tipe)]);
        }

        // Filter by size kontainer
        if ($request->filled('size_kontainer')) {
            $query->where('size_kontainer', $request->size_kontainer);
        }

        $perincians = $query->orderByRaw("FIELD(UPPER(tipe_kontainer), 'FCL', 'LCL', 'CARGO') ASC")
                           ->orderByRaw('ISNULL(nomor_urut), nomor_urut ASC')
                           ->orderBy('created_at', 'desc')
                           ->paginate(20);

        // Store selection in session
        session([
            'selected_perincian_ship' => $namaKapal,
            'selected_perincian_voyage' => $noVoyage,
        ]);

        return view('perincians.index', compact('perincians', 'namaKapal', 'noVoyage'));
    }

    /**
     * Export perincian data to Excel
     */
    public function export(Request $request)
    {
        $namaKapal = $request->get('nama_kapal');
        $noVoyage = $request->get('no_voyage');

        if (! $namaKapal || ! $noVoyage) {
            return redirect()->back()->with('error', 'Nama Kapal dan No Voyage harus ada untuk export');
        }

        $normalizedKapal = strtoupper(trim(str_replace('.', '', $namaKapal)));
        $normalizedKapal = str_replace('  ', ' ', $normalizedKapal);
        $noVoyage = trim($noVoyage);

        $perincians = Perincian::with(['prospek.tandaTerima'])->whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
            ->where('no_voyage', $noVoyage)
            ->orderByRaw("FIELD(UPPER(tipe_kontainer), 'FCL', 'LCL', 'CARGO') ASC")
            ->orderByRaw('ISNULL(nomor_urut), nomor_urut ASC')
            ->orderBy('created_at', 'desc')
            ->get();

        // Simple CSV-style export using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'No', 'No Urut', 'No BL', 'No Tanda Terima', 'No Kontainer', 'No Seal',
            'Tipe', 'Size', 'Nama Barang', 'Pengirim', 'Alamat Pengirim',
            'Penerima', 'Alamat Penerima', 'Contact Person', 'Tonnage', 'Tonnage Perincian',
            'Volume', 'Volume Perincian', 'Satuan', 'Kuantitas', 'Term',
            'Pelabuhan Muat', 'Pelabuhan Bongkar', 'Tanggal Berangkat',
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $row = 2;
        foreach ($perincians as $i => $p) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $p->nomor_urut);
            $sheet->setCellValue('C' . $row, $p->nomor_bl);
            $sheet->setCellValue('D' . $row, $p->nomor_tanda_terima_display);
            $sheet->setCellValue('E' . $row, $p->nomor_kontainer);
            $sheet->setCellValue('F' . $row, $p->no_seal);
            $sheet->setCellValue('G' . $row, $p->tipe_kontainer);
            $sheet->setCellValue('H' . $row, $p->size_kontainer);
            $sheet->setCellValue('I' . $row, $p->nama_barang);
            $sheet->setCellValue('J' . $row, $p->pengirim);
            $sheet->setCellValue('K' . $row, $p->alamat_pengirim);
            $sheet->setCellValue('L' . $row, $p->penerima);
            $sheet->setCellValue('M' . $row, $p->alamat_penerima);
            $sheet->setCellValue('N' . $row, $p->contact_person);
            $sheet->setCellValue('O' . $row, $p->tonnage);
            $sheet->setCellValue('P' . $row, $p->tonnage_perincian);
            $sheet->setCellValue('Q' . $row, $p->volume);
            $sheet->setCellValue('R' . $row, $p->volume_perincian);
            $sheet->setCellValue('S' . $row, $p->satuan);
            $sheet->setCellValue('T' . $row, $p->kuantitas);
            $sheet->setCellValue('U' . $row, $p->term);
            $sheet->setCellValue('V' . $row, $p->pelabuhan_muat);
            $sheet->setCellValue('W' . $row, $p->pelabuhan_bongkar);
            $sheet->setCellValue('X' . $row, $p->tanggal_berangkat ? $p->tanggal_berangkat->format('d/m/Y') : '');
            $row++;
        }

        $filename = 'Perincian_' . str_replace(' ', '_', $namaKapal) . '_' . str_replace('/', '-', $noVoyage) . '.xlsx';

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $prospeks = \App\Models\Prospek::orderBy('pt_pengirim')->limit(20)->get();

        return view('perincians.create', compact('prospeks'));
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
            'shipper_id' => 'nullable|integer',
            'pengirim' => 'nullable|string|max:255',
            'alamat_pengirim' => 'nullable|string',
            'penerima' => 'nullable|string|max:255',
            'alamat_pengiriman' => 'nullable|string',
            'notify_party' => 'nullable|string|max:255',
            'alamat_notify_party' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'tonnage' => 'nullable|numeric',
            'tonnage_perincian' => 'nullable|numeric',
            'volume' => 'nullable|numeric',
            'volume_perincian' => 'nullable|numeric',
            'satuan' => 'nullable|string|max:255',
            'term' => 'nullable|string|max:255',
            'kuantitas' => 'nullable|integer',
            'hs_code' => 'nullable|string|max:255',
            'penerimaan' => 'nullable|date',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        if (! empty($validated['pengirim'])) {
            $dbPengirim = \App\Models\Pengirim::where('nama_pengirim', $validated['pengirim'])->orWhere('nickname1', $validated['pengirim'])->first();
            if ($dbPengirim && ! empty($dbPengirim->nickname1)) {
                $validated['pengirim'] = $dbPengirim->nickname1;
            }
        }

        Perincian::create($validated);

        return redirect()->route('report.perincians.index', [
            'nama_kapal' => $validated['nama_kapal'] ?? '',
            'no_voyage' => $validated['no_voyage'] ?? '',
        ])->with('success', 'Perincian berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $perincian = Perincian::with(['prospek', 'createdBy', 'updatedBy'])->findOrFail($id);

        return view('perincians.show', compact('perincian'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $perincian = Perincian::findOrFail($id);
        $prospeks = \App\Models\Prospek::where('id', $perincian->prospek_id)
            ->union(\App\Models\Prospek::orderBy('pt_pengirim')->limit(20))
            ->get();

        return view('perincians.edit', compact('perincian', 'prospeks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $perincian = Perincian::findOrFail($id);

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
            'shipper_id' => 'nullable|integer',
            'pengirim' => 'nullable|string|max:255',
            'alamat_pengirim' => 'nullable|string',
            'penerima' => 'nullable|string|max:255',
            'alamat_pengiriman' => 'nullable|string',
            'notify_party' => 'nullable|string|max:255',
            'alamat_notify_party' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'tonnage' => 'nullable|numeric',
            'tonnage_perincian' => 'nullable|numeric',
            'volume' => 'nullable|numeric',
            'volume_perincian' => 'nullable|numeric',
            'satuan' => 'nullable|string|max:255',
            'term' => 'nullable|string|max:255',
            'kuantitas' => 'nullable|integer',
            'hs_code' => 'nullable|string|max:255',
            'penerimaan' => 'nullable|date',
        ]);

        $validated['updated_by'] = Auth::id();

        if (! empty($validated['pengirim'])) {
            $dbPengirim = \App\Models\Pengirim::where('nama_pengirim', $validated['pengirim'])->orWhere('nickname1', $validated['pengirim'])->first();
            if ($dbPengirim && ! empty($dbPengirim->nickname1)) {
                $validated['pengirim'] = $dbPengirim->nickname1;
            }
        }

        $perincian->update($validated);

        return redirect()->route('report.perincians.index', [
            'nama_kapal' => $perincian->nama_kapal,
            'no_voyage' => $perincian->no_voyage,
        ])->with('success', 'Perincian berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $perincian = Perincian::findOrFail($id);
        $namaKapal = $perincian->nama_kapal;
        $noVoyage = $perincian->no_voyage;

        $perincian->delete();

        return redirect()->route('report.perincians.index', [
            'nama_kapal' => $namaKapal,
            'no_voyage' => $noVoyage,
        ])->with('success', 'Perincian berhasil dihapus');
    }

    /**
     * Sync nama_barang in Perincian based on data from NaikKapal or TandaTerima.
     */
    public function sync(Request $request)
    {
        $namaKapal = $request->input('nama_kapal');
        $noVoyage = $request->input('no_voyage');

        if (!$namaKapal || !$noVoyage) {
            return redirect()->back()->with('error', 'Nama Kapal dan Voyage harus diisi untuk melakukan sinkronisasi.');
        }

        // Fetch all perincian for this ship and voyage
        $perincians = Perincian::where('nama_kapal', $namaKapal)
            ->where('no_voyage', $noVoyage)
            ->get();

        $updatedCount = 0;

        foreach ($perincians as $perincian) {
            $naikKapalQuery = \App\Models\NaikKapal::where('no_voyage', $perincian->no_voyage)
                ->where('nama_kapal', $perincian->nama_kapal);
                
            if ($perincian->prospek_id) {
                $naikKapalQuery->where('prospek_id', $perincian->prospek_id);
            } else {
                $naikKapalQuery->where('nomor_kontainer', $perincian->nomor_kontainer);
            }
            
            $naikKapal = $naikKapalQuery->first();
            $newNamaBarang = null;

            if ($naikKapal) {
                if ($naikKapal->prospek && $naikKapal->prospek->tandaTerima) {
                    $tt = $naikKapal->prospek->tandaTerima;
                    $itemNames = [];
                    if (! empty($tt->dimensi_items) && is_array($tt->dimensi_items)) {
                        foreach ($tt->dimensi_items as $item) {
                            if (! empty($item['nama_barang'])) {
                                $itemNames[] = $item['nama_barang'];
                            }
                        }
                    } elseif (! empty($tt->dimensi_details) && is_array($tt->dimensi_details)) {
                        foreach ($tt->dimensi_details as $item) {
                            if (! empty($item['nama_barang'])) {
                                $itemNames[] = $item['nama_barang'];
                            }
                        }
                    } elseif (! empty($tt->nama_barang)) {
                        if (is_array($tt->nama_barang)) {
                            $itemNames = $tt->nama_barang;
                        } elseif (is_string($tt->nama_barang) && $tt->nama_barang !== 'null') {
                            $itemNames[] = $tt->nama_barang;
                        }
                    }

                    if (! empty($itemNames)) {
                        $newNamaBarang = implode(', ', array_unique($itemNames));
                    }
                }

                if (empty($newNamaBarang)) {
                    $newNamaBarang = $naikKapal->jenis_barang;
                }
                
                if (!empty($newNamaBarang) && (empty($perincian->nama_barang) || $perincian->nama_barang !== $newNamaBarang)) {
                    $perincian->nama_barang = $newNamaBarang;
                    $perincian->save();
                    $updatedCount++;
                }
            }
        }

        return redirect()->back()->with('success', "Sinkronisasi berhasil. $updatedCount data nama barang telah diperbarui.");
    }

    /**
     * Get voyages by ship name (for AJAX)
     */
    public function getVoyagesByShip($namaKapal)
    {
        try {
            $namaKapal = urldecode($namaKapal);

            $normalizedKapal = strtoupper(trim(str_replace('.', '', $namaKapal)));
            $normalizedKapal = str_replace('  ', ' ', $normalizedKapal);

            $voyages = Perincian::whereRaw("UPPER(REPLACE(REPLACE(nama_kapal, '.', ''), '  ', ' ')) = ?", [$normalizedKapal])
                ->select('no_voyage')
                ->distinct()
                ->orderBy('no_voyage', 'asc')
                ->pluck('no_voyage')
                ->map(function ($voyage) {
                    return strtoupper(trim($voyage));
                })
                ->unique()
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'voyages' => $voyages,
                'count' => count($voyages),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'voyages' => [],
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
