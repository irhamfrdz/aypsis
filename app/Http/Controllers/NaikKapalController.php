<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NaikKapal;
use App\Models\Prospek;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exports\NaikKapalExport;
use Maatwebsite\Excel\Facades\Excel;

class NaikKapalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Allow show_all parameter to view all data without filtering
        $showAll = $request->filled('show_all') && $request->show_all == 1;
        
        // Redirect to select page if required parameters are missing (unless show_all is set)
        if (!$showAll && (!$request->filled('kapal_id') || !$request->filled('no_voyage'))) {
            return redirect()->route('naik-kapal.select')
                ->with('info', 'Silakan pilih kapal dan voyage terlebih dahulu.');
        }
        
        $query = NaikKapal::with(['prospek', 'createdBy']);
        
        // Filter by kapal and voyage only if not showing all
        if (!$showAll) {
            $kapal = \App\Models\MasterKapal::find($request->kapal_id);
            if ($kapal) {
                $query->where('nama_kapal', $kapal->nama_kapal)
                      ->where('no_voyage', $request->no_voyage);
            }
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_kontainer', 'like', "%{$search}%")
                  ->orWhere('jenis_barang', 'like', "%{$search}%")
                  ->orWhere('no_seal', 'like', "%{$search}%")
                  ->orWhere('ukuran_kontainer', 'like', "%{$search}%");
            });
        }
        
        // Filter by status BL
        if ($request->filled('status_bl')) {
            if ($request->status_bl === 'sudah_bl') {
                $query->where('status', 'Moved to BLS');
            } elseif ($request->status_bl === 'belum_bl') {
                $query->where(function($q) {
                    $q->where('status', '!=', 'Moved to BLS')
                      ->orWhereNull('status');
                });
            }
        }
        
        // Filter by tipe kontainer
        if ($request->filled('tipe_kontainer')) {
            $query->where('tipe_kontainer', $request->tipe_kontainer);
        }
        
        // Additional filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by status BL (legacy support)
        if ($request->filled('status_filter')) {
            if ($request->status_filter === 'sudah_bl') {
                $query->where('status', 'Moved to BLS');
            } elseif ($request->status_filter === 'belum_bl') {
                $query->where(function($q) {
                    $q->where('status', '!=', 'Moved to BLS')
                      ->orWhereNull('status');
                });
            }
        }
        
        if ($request->filled('tanggal_muat')) {
            $query->whereDate('tanggal_muat', $request->tanggal_muat);
        }
        
        $naikKapals = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->query());
        
        // Get selected kapal info for display
        $selectedKapal = null;
        if ($request->filled('kapal_id')) {
            $selectedKapal = \App\Models\MasterKapal::find($request->kapal_id);
        }

        return view('naik-kapal.index', compact('naikKapals', 'selectedKapal'));
    }

    /**
     * Show the selection form for kapal and voyage.
     */
    public function select()
    {
        $kapals = \App\Models\MasterKapal::where('status', 'aktif')
            ->orderBy('nama_kapal')
            ->get();
            
        return view('naik-kapal.select', compact('kapals'));
    }

    /**
     * Get voyages by kapal name from naik_kapal table.
     */
    public function getVoyagesByKapal(Request $request)
    {
        // Support both kapal_id and nama_kapal for backward compatibility
        $namaKapal = null;
        
        if ($request->filled('kapal_id')) {
            // Get nama_kapal from MasterKapal by ID
            $kapal = \App\Models\MasterKapal::find($request->kapal_id);
            if ($kapal) {
                $namaKapal = $kapal->nama_kapal;
            }
        } elseif ($request->filled('nama_kapal')) {
            // Direct nama_kapal (legacy support)
            $namaKapal = $request->nama_kapal;
        }
        
        if (!$namaKapal) {
            return response()->json([
                'success' => false,
                'message' => 'Kapal tidak ditemukan'
            ], 400);
        }
        
        // Get distinct voyages from naik_kapal table for this kapal
        $voyages = NaikKapal::where('nama_kapal', $namaKapal)
            ->whereNotNull('no_voyage')
            ->where('no_voyage', '!=', '')
            ->distinct()
            ->orderBy('no_voyage', 'desc')
            ->pluck('no_voyage')
            ->toArray();
        
        return response()->json([
            'success' => true,
            'voyages' => $voyages,
            'kapal' => [
                'nama' => $namaKapal
            ]
        ]);
    }

    /**
     * Print page for selected kapal and voyage.
     */
    public function print(Request $request)
    {
        // Validate required parameters
        if (!$request->has('kapal_id') || !$request->has('no_voyage')) {
            return redirect()->route('naik-kapal.select')
                ->with('error', 'Silakan pilih kapal dan voyage terlebih dahulu.');
        }
        
        $kapal = \App\Models\MasterKapal::find($request->kapal_id);
        
        if (!$kapal) {
            return redirect()->route('naik-kapal.select')
                ->with('error', 'Kapal tidak ditemukan. Silakan pilih kapal terlebih dahulu.');
        }
        
        if (empty($request->no_voyage)) {
            return redirect()->route('naik-kapal.select')
                ->with('error', 'Nomor voyage tidak valid. Silakan pilih voyage terlebih dahulu.');
        }
        
        $query = NaikKapal::with(['prospek.tandaTerima', 'createdBy'])
            ->where('nama_kapal', $kapal->nama_kapal)
            ->where('no_voyage', $request->no_voyage);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_kontainer', 'like', "%{$search}%")
                  ->orWhere('jenis_barang', 'like', "%{$search}%")
                  ->orWhere('no_seal', 'like', "%{$search}%")
                  ->orWhere('ukuran_kontainer', 'like', "%{$search}%");
            });
        }
        
        // Filter by status BL
        if ($request->filled('status_bl')) {
            if ($request->status_bl === 'sudah_bl') {
                $query->where('status', 'Moved to BLS');
            } elseif ($request->status_bl === 'belum_bl') {
                $query->where(function($q) {
                    $q->where('status', '!=', 'Moved to BLS')
                      ->orWhereNull('status');
                });
            }
        }
        
        // Filter by tipe kontainer
        if ($request->filled('tipe_kontainer')) {
            $query->where('tipe_kontainer', $request->tipe_kontainer);
        }
        
        // Filter by status BL if provided (legacy support)
        if ($request->filled('status_filter')) {
            if ($request->status_filter === 'sudah_bl') {
                $query->where('status', 'Moved to BLS');
            } elseif ($request->status_filter === 'belum_bl') {
                $query->where(function($q) {
                    $q->where('status', '!=', 'Moved to BLS')
                      ->orWhereNull('status');
                });
            }
        }
        
        // Get data and sort by nomor_kontainer (ignoring first 4 digits) and tanggal_tanda_terima
        $naikKapals = $query->get()->sort(function ($a, $b) {
            // Sort by nomor_kontainer, ignoring first 4 characters
            $kontainerA = substr($a->nomor_kontainer ?? '', 4);
            $kontainerB = substr($b->nomor_kontainer ?? '', 4);
            
            $kontainerCompare = $kontainerA <=> $kontainerB;
            
            // If container numbers are different, return the comparison
            if ($kontainerCompare !== 0) {
                return $kontainerCompare;
            }
            
            // If container numbers are the same, sort by tanggal_tanda_terima
            // Use tanggal or fallback to tanggal_checkpoint_supir
            $dateA = $a->prospek?->tandaTerima?->tanggal 
                     ?? $a->prospek?->tandaTerima?->tanggal_checkpoint_supir 
                     ?? null;
            $dateB = $b->prospek?->tandaTerima?->tanggal 
                     ?? $b->prospek?->tandaTerima?->tanggal_checkpoint_supir 
                     ?? null;
            
            // Handle null dates - put them at the end
            if ($dateA === null && $dateB === null) return 0;
            if ($dateA === null) return 1;
            if ($dateB === null) return -1;
            
            return $dateA <=> $dateB;
        })->values();
        
        return view('naik-kapal.print', [
            'naikKapals' => $naikKapals,
            'kapal' => $kapal,
            'voyage' => $request->no_voyage,
            'statusFilter' => $request->status_filter
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not implemented - create functionality not needed
        return redirect()->route('naik-kapal.index')
            ->with('info', 'Fitur tambah data naik kapal tidak tersedia.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Not implemented - store functionality not needed
        return redirect()->route('naik-kapal.index')
            ->with('info', 'Fitur tambah data naik kapal tidak tersedia.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $naikKapal = NaikKapal::with(['prospek', 'createdBy', 'updatedBy'])->findOrFail($id);
        return view('naik-kapal.show', compact('naikKapal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $naikKapal = NaikKapal::findOrFail($id);
        $prospeks = Prospek::where('status', 'aktif')
            ->orWhere('id', $naikKapal->prospek_id)
            ->orderBy('tanggal', 'desc')
            ->get();
            
        return view('naik-kapal.edit', compact('naikKapal', 'prospeks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $naikKapal = NaikKapal::findOrFail($id);
        
        $request->validate([
            'prospek_id' => 'required|exists:prospek,id',
            'nomor_kontainer' => 'required|string|max:255',
            'tanggal_muat' => 'required|date',
            'jam_muat' => 'nullable|date_format:H:i',
            'nama_kapal' => 'required|string|max:255',
            'status' => 'required|in:menunggu,dimuat,selesai,batal'
        ]);

        DB::beginTransaction();
        try {
            // Get prospek data
            $prospek = Prospek::findOrFail($request->prospek_id);
            
            $naikKapal->update([
                'prospek_id' => $request->prospek_id,
                'nomor_kontainer' => $request->nomor_kontainer,
                'no_seal' => $request->no_seal,
                'tipe_kontainer' => $request->tipe_kontainer,
                'ukuran_kontainer' => $request->ukuran_kontainer,
                'nama_kapal' => $request->nama_kapal,
                'no_voyage' => $request->no_voyage,
                'pelabuhan_asal' => $request->pelabuhan_asal,
                'pelabuhan_tujuan' => $request->pelabuhan_tujuan,
                'tanggal_muat' => $request->tanggal_muat,
                'jam_muat' => $request->jam_muat,
                'total_volume' => round($prospek->total_volume ?? 0, 3),
                'total_tonase' => round($prospek->total_ton ?? 0, 3),
                'kuantitas' => $prospek->kuantitas ?? 0,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
                'updated_by' => Auth::id()
            ]);

            // Update prospek status if naik kapal is completed
            if ($request->status == 'selesai') {
                $prospek->update(['status' => 'sudah_muat']);
            } elseif ($request->status == 'batal') {
                $prospek->update(['status' => 'aktif']);
            }

            DB::commit();
            
            return redirect()->route('naik-kapal.index')
                ->with('success', 'Data naik kapal berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update size kontainer inline
     */
    public function updateSize(Request $request, $id)
    {
        $naikKapal = NaikKapal::findOrFail($id);
        
        $request->validate([
            'size_kontainer' => 'nullable|string|max:50'
        ]);

        $naikKapal->update([
            'size_kontainer' => $request->size_kontainer
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Size kontainer berhasil diperbarui'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $naikKapal = NaikKapal::findOrFail($id);
            
            DB::beginTransaction();
            
            // Reset prospek status if naik kapal was completed
            if ($naikKapal->status == 'selesai') {
                $naikKapal->prospek->update(['status' => 'aktif']);
            }
            
            $naikKapal->delete();
            
            DB::commit();
            
            return redirect()->route('naik-kapal.index')
                ->with('success', 'Data naik kapal berhasil dihapus.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download template CSV untuk import Naik Kapal
     */
    public function downloadTemplate()
    {
        $filename = 'template_naik_kapal_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Write BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Header columns
            $header = [
                'nomor_kontainer',
                'ukuran_kontainer',
                'no_seal',
                'nama_kapal',
                'no_voyage',
                'pelabuhan_tujuan',
                'jenis_barang',
                'tipe_kontainer',
                'size_kontainer',
                'volume',
                'tonase',
                'kuantitas',
                'tanggal_muat',
                'jam_muat',
                'prospek_id',
                'nama_supir',
                'keterangan'
            ];
            
            fputcsv($file, $header);
            
            // Example data rows
            $exampleData = [
                [
                    'CONT' . date('Ymd') . '001',
                    '20x8x8.6',
                    'SEAL001',
                    'KM SINAR HARAPAN',
                    'SH001',
                    'Batam',
                    'Elektronik',
                    '20 FT',
                    '20x8x8.6',
                    '25.750',
                    '15.500',
                    '100',
                    date('Y-m-d'),
                    '08:00',
                    '1',
                    'SUPIR A',
                    'Contoh data naik kapal untuk import'
                ],
                [
                    'CONT' . date('Ymd') . '002',
                    '40x8x8.6',
                    'SEAL002',
                    'KM CAHAYA LAUT',
                    'CL002',
                    'Jakarta',
                    'Makanan & Minuman',
                    '40 FT',
                    '40x8x8.6',
                    '45.300',
                    '25.000',
                    '200',
                    date('Y-m-d'),
                    '14:30',
                    '2',
                    'SUPIR B',
                    'Contoh data naik kapal kedua'
                ]
            ];
            
            foreach ($exampleData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Handle bulk actions for naik kapal records
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:masukkan_ke_bls,tidak_naik_kapal',
            'selected_ids' => 'required|string'
        ]);

        try {
            $selectedIds = json_decode($request->selected_ids, true);
            
            if (empty($selectedIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang dipilih'
                ]);
            }

            $naikKapals = NaikKapal::with(['prospek.tandaTerima'])->whereIn('id', $selectedIds)->get();
            
            if ($naikKapals->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            DB::beginTransaction();

            if ($request->action === 'masukkan_ke_bls') {
                $this->processToBlsAction($naikKapals);
                $message = count($selectedIds) . ' data berhasil dimasukkan ke BLS';
            } elseif ($request->action === 'tidak_naik_kapal') {
                $this->processTidakNaikKapalAction($naikKapals);
                $message = count($selectedIds) . ' data berhasil ditandai sebagai tidak naik kapal';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bulk action error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Process moving naik kapal records to BLS table
     */
    private function processToBlsAction($naikKapals)
    {
        foreach ($naikKapals as $naikKapal) {
            // Load related prospek and tanda terima data with eager loading
            $naikKapal->load(['prospek.tandaTerima']);
            $prospek = $naikKapal->prospek;
            $tandaTerima = $prospek ? $prospek->tandaTerima : null;
            
            // Prepare BL data with complete information from prospek and tanda terima
            $blData = [
                'prospek_id' => $naikKapal->prospek_id,
                'nomor_kontainer' => $naikKapal->nomor_kontainer,
                'no_seal' => $naikKapal->no_seal,
                'tipe_kontainer' => $naikKapal->tipe_kontainer,
                'size_kontainer' => $naikKapal->size_kontainer ?: $naikKapal->ukuran_kontainer,
                'no_voyage' => $naikKapal->no_voyage,
                'nama_kapal' => $naikKapal->nama_kapal,
                'pelabuhan_asal' => $naikKapal->pelabuhan_asal,
                'pelabuhan_tujuan' => $naikKapal->pelabuhan_tujuan,
                'nama_barang' => $naikKapal->jenis_barang,
                'tonnage' => $naikKapal->total_tonase ?: ($prospek ? $prospek->total_ton : null),
                'volume' => $naikKapal->total_volume ?: ($prospek ? $prospek->total_volume : null),
                'kuantitas' => $naikKapal->kuantitas ?: ($prospek ? $prospek->kuantitas : null),
                'term' => null, // Will be set from tanda terima if available
                'status_bongkar' => 'Belum Bongkar',
                'sudah_ob' => false,
                'supir_ob' => $prospek ? $prospek->supir_ob : null,
                'created_by' => Auth::id()
            ];

            // Get comprehensive data from prospek if available
            if ($prospek) {
                $blData['pengirim'] = $prospek->pt_pengirim;
                
                // Use prospek data as fallback if naik kapal data is empty
                if (!$blData['nama_barang'] && $prospek->barang) {
                    $blData['nama_barang'] = $prospek->barang;
                }
                if (!$blData['pelabuhan_asal'] && $prospek->pelabuhan_asal) {
                    $blData['pelabuhan_asal'] = $prospek->pelabuhan_asal;
                }
                if (!$blData['size_kontainer'] && $prospek->ukuran) {
                    $blData['size_kontainer'] = $prospek->ukuran;
                }
                if (!$blData['tipe_kontainer'] && $prospek->tipe) {
                    $blData['tipe_kontainer'] = $prospek->tipe;
                }
            }

            // Get comprehensive data from tanda terima if available
            if ($tandaTerima) {
                $blData['penerima'] = $tandaTerima->penerima;
                $blData['alamat_pengiriman'] = $tandaTerima->alamat_penerima ?: $tandaTerima->tujuan_pengiriman;
                $blData['contact_person'] = $tandaTerima->pic ?? null;
                
                // Get term from tanda terima
                $blData['term'] = $tandaTerima->term;
                
                // Override with more accurate data from tanda terima if available
                if (!$blData['nama_barang'] && $tandaTerima->jenis_barang) {
                    $blData['nama_barang'] = $tandaTerima->jenis_barang;
                }
                
                // Handle nama_barang as array if needed
                if (!$blData['nama_barang'] && $tandaTerima->nama_barang) {
                    if (is_array($tandaTerima->nama_barang)) {
                        $blData['nama_barang'] = implode(', ', $tandaTerima->nama_barang);
                    } else {
                        $blData['nama_barang'] = $tandaTerima->nama_barang;
                    }
                }
                
                // Use tanda terima measurements with proper fallbacks
                if (!$blData['tonnage'] && $tandaTerima->tonase) {
                    $blData['tonnage'] = $tandaTerima->tonase;
                }
                if (!$blData['volume'] && $tandaTerima->meter_kubik) {
                    $blData['volume'] = $tandaTerima->meter_kubik;
                }
                if (!$blData['kuantitas'] && $tandaTerima->jumlah) {
                    $blData['kuantitas'] = $tandaTerima->jumlah;
                }
                
                // Additional fields from tanda terima
                if ($tandaTerima->satuan) {
                    $blData['satuan'] = $tandaTerima->satuan;
                }
                if ($tandaTerima->no_kontainer && !$blData['nomor_kontainer']) {
                    $blData['nomor_kontainer'] = $tandaTerima->no_kontainer;
                }
                if ($tandaTerima->no_seal && !$blData['no_seal']) {
                    $blData['no_seal'] = $tandaTerima->no_seal;
                }
                if ($tandaTerima->size && !$blData['size_kontainer']) {
                    $blData['size_kontainer'] = $tandaTerima->size;
                }
                if ($tandaTerima->tipe_kontainer && !$blData['tipe_kontainer']) {
                    $blData['tipe_kontainer'] = $tandaTerima->tipe_kontainer;
                }
                if ($tandaTerima->pengirim && !$blData['pengirim']) {
                    $blData['pengirim'] = $tandaTerima->pengirim;
                }
                if ($tandaTerima->estimasi_nama_kapal && !$blData['nama_kapal']) {
                    $blData['nama_kapal'] = $tandaTerima->estimasi_nama_kapal;
                }
            }

            // Ensure we have fallback values for critical fields
            $blData['nama_barang'] = $blData['nama_barang'] ?: 'Tidak diketahui';
            $blData['pengirim'] = $blData['pengirim'] ?: 'Tidak diketahui';
            $blData['penerima'] = $blData['penerima'] ?: 'Tidak diketahui';

            // Create BL record with complete data
            \App\Models\Bl::create($blData);

            // Update prospek status if needed
            if ($prospek) {
                $prospek->update([
                    'status' => 'sudah_muat'
                ]);
            }

            // Mark naik kapal as processed
            $naikKapal->update(['status' => 'Moved to BLS']);
        }
    }

    /**
     * Process tidak naik kapal action - delete records and update prospek status
     */
    private function processTidakNaikKapalAction($naikKapals)
    {
        foreach ($naikKapals as $naikKapal) {
            // Update prospek status to 'batal' before deleting naik kapal record
            if ($naikKapal->prospek) {
                $naikKapal->prospek->update([
                    'status' => 'batal'
                ]);
            }
            
            // Delete the naik kapal record completely
            $naikKapal->delete();
        }
    }

    /**
     * Export naik kapal data to Excel
     */
    public function export(Request $request)
    {
        $request->validate([
            'kapal_id' => 'required|exists:master_kapals,id',
            'no_voyage' => 'required|string'
        ]);

        $kapal = \App\Models\MasterKapal::find($request->kapal_id);
        $noVoyage = $request->no_voyage;

        // Generate filename
        $filename = 'Naik_Kapal_' . str_replace(' ', '_', $kapal->nama_kapal) . '_' . str_replace('/', '-', $noVoyage) . '_' . date('YmdHis') . '.xlsx';

        // Return Excel download - export semua data tanpa filter
        return Excel::download(new NaikKapalExport([], $kapal->nama_kapal, $noVoyage), $filename);
    }
}
