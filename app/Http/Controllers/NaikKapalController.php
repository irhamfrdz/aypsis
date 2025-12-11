<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NaikKapal;
use App\Models\Prospek;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NaikKapalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Redirect to select page if required parameters are missing
        if (!$request->filled('kapal_id') || !$request->filled('no_voyage')) {
            return redirect()->route('naik-kapal.select')
                ->with('info', 'Silakan pilih kapal dan voyage terlebih dahulu.');
        }
        
        $query = NaikKapal::with(['prospek', 'createdBy']);
        
        // Filter by kapal and voyage
        $kapal = \App\Models\MasterKapal::find($request->kapal_id);
        if ($kapal) {
            $query->where('nama_kapal', $kapal->nama_kapal)
                  ->where('no_voyage', $request->no_voyage);
        }
        
        // Additional filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by status BL
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
        
        $naikKapals = $query->orderBy('created_at', 'desc')->paginate(15);
        
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

        // Get naik kapal data
        $naikKapals = NaikKapal::with(['prospek.tandaTerima'])
            ->where('nama_kapal', $kapal->nama_kapal)
            ->where('no_voyage', $noVoyage)
            ->get();

        if ($naikKapals->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data untuk diekspor'
            ], 404);
        }

        // Generate filename
        $filename = 'Naik_Kapal_' . str_replace(' ', '_', $kapal->nama_kapal) . '_' . str_replace('/', '-', $noVoyage) . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Create Excel export
        return $this->generateExcel($naikKapals, $kapal, $noVoyage, $filename);
    }

    private function generateExcel($naikKapals, $kapal, $noVoyage, $filename)
    {
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        $callback = function() use ($naikKapals, $kapal, $noVoyage) {
            $file = fopen('php://output', 'w');
            
            // Write UTF-8 BOM for proper encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write header
            fputcsv($file, [
                'LAPORAN DATA NAIK KAPAL'
            ]);
            
            fputcsv($file, [
                'Kapal: ' . $kapal->nama_kapal . ($kapal->nickname ? ' (' . $kapal->nickname . ')' : ''),
                'Voyage: ' . $noVoyage,
                'Tanggal Export: ' . date('d/m/Y H:i:s')
            ]);
            
            fputcsv($file, []); // Empty row
            
            // Column headers
            fputcsv($file, [
                'No',
                'Nomor Kontainer',
                'Ukuran Kontainer', 
                'No Seal',
                'Jenis Barang',
                'Tipe Kontainer',
                'Volume (m³)',
                'Tonase (Ton)',
                'Kuantitas',
                'Tanggal Muat',
                'Jam Muat',
                'Pelabuhan Asal',
                'Pelabuhan Tujuan',
                'Prospek ID',
                'Nama Supir',
                'Pengirim',
                'Penerima',
                'Status'
            ]);
            
            // Data rows
            foreach ($naikKapals as $index => $naikKapal) {
                $prospek = $naikKapal->prospek;
                $tandaTerima = $prospek ? $prospek->tandaTerima : null;
                
                fputcsv($file, [
                    $index + 1,
                    $naikKapal->nomor_kontainer ?: '-',
                    $naikKapal->ukuran_kontainer ?: '-',
                    $naikKapal->no_seal ?: '-',
                    $naikKapal->jenis_barang ?: '-',
                    $naikKapal->tipe_kontainer ?: '-',
                    $naikKapal->total_volume ?: '0',
                    $naikKapal->total_tonase ?: '0',
                    $naikKapal->kuantitas ?: '0',
                    $naikKapal->tanggal_muat ? date('d/m/Y', strtotime($naikKapal->tanggal_muat)) : '-',
                    $naikKapal->jam_muat ? date('H:i', strtotime($naikKapal->jam_muat)) : '-',
                    $naikKapal->pelabuhan_asal ?: '-',
                    $naikKapal->pelabuhan_tujuan ?: '-',
                    $prospek ? $prospek->id : '-',
                    $prospek ? $prospek->nama_supir : '-',
                    $prospek ? $prospek->pt_pengirim : ($tandaTerima ? $tandaTerima->pengirim : '-'),
                    $tandaTerima ? $tandaTerima->penerima : '-',
                    $naikKapal->status ?: 'Active'
                ]);
            }
            
            // Summary
            fputcsv($file, []); // Empty row
            fputcsv($file, [
                'RINGKASAN:',
                'Total Data: ' . $naikKapals->count(),
                'Total Volume: ' . number_format($naikKapals->sum('total_volume'), 3) . ' m³',
                'Total Tonase: ' . number_format($naikKapals->sum('total_tonase'), 3) . ' Ton'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
