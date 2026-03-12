<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrderBatam;
use App\Models\SuratJalan;
use App\Models\Term;
use App\Models\Pengirim;
use App\Models\Penerima;
use App\Models\JenisBarang;
use App\Models\MasterTujuanKirim;
use App\Models\TujuanKegiatanUtama;
use App\Models\StockKontainer;
use App\Models\NomorTerakhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderBatamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = OrderBatam::with(['term', 'pengirim', 'jenisBarang']);

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nomor_order', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('tujuan_kirim', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('tujuan_ambil', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('no_tiket_do', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhereHas('pengirim', function ($query) use ($searchTerm) {
                      $query->where('nama_pengirim', 'LIKE', '%' . $searchTerm . '%');
                  });
            });
        }

        $orderBatams = $query->latest()->paginate(15);

        return view('orders-batam.index', compact('orderBatams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $terms = Term::where('status', 'active')->get();
        $pengirims = Pengirim::where('status', 'active')->get();
        $penerimas = Penerima::where('status', 'active')->orderBy('nama_penerima')->get();
        $jenisBarangs = JenisBarang::where('status', 'active')->get();
        $tujuanKirims = MasterTujuanKirim::where('status', 'active')->orderBy('nama_tujuan')->get();
        $tujuanKegiatanUtamas = TujuanKegiatanUtama::where('aktif', true)->orderBy('ke')->get();

        // Get distinct ukuran kontainer from stock kontainer
        $ukuranKontainers = StockKontainer::select('ukuran')
            ->distinct()
            ->whereNotNull('ukuran')
            ->where('ukuran', '!=', '')
            ->orderBy('ukuran')
            ->pluck('ukuran')
            ->toArray();

        // Generate next order number
        $nextOrderNumber = $this->generateNextOrderNumber();

        return view('orders-batam.create', compact('terms', 'pengirims', 'penerimas', 'jenisBarangs', 'tujuanKirims', 'tujuanKegiatanUtamas', 'ukuranKontainers', 'nextOrderNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Base validation rules
        $rules = [
            'nomor_order' => 'required|string|unique:order_batams,nomor_order',
            'tanggal_order' => 'required|date',
            'tujuan_kirim_id' => 'required|exists:master_tujuan_kirim,id',
            'tujuan_ambil_id' => 'required|exists:tujuan_kegiatan_utamas,id',
            'penerima' => 'nullable|string|max:255',
            'penerima_id' => 'nullable|exists:penerimas,id',
            'alamat_penerima' => 'nullable|string',
            'kontak_penerima' => 'nullable|string|max:255',
            'tipe_kontainer' => 'required|in:fcl,lcl,cargo,fcl_plus',
            'tanggal_pickup' => 'nullable|date',
            'satuan' => 'nullable|string|in:kg,ton,m3,unit,pcs,dus,karung,kontainer',
            'no_tiket_do' => 'nullable|string|max:255',
            'term_id' => 'nullable|exists:terms,id',
            'pengirim_id' => 'nullable|exists:pengirims,id',
            'jenis_barang_id' => 'nullable|exists:jenis_barangs,id',
            'catatan' => 'nullable|string',
            'ftz03_option' => 'nullable|in:exclude,include,none',
            'sppb_option' => 'nullable|in:exclude,include,none',
            'buruh_bongkar_option' => 'nullable|in:exclude,include,none',
        ];

        // Add size_kontainer and unit_kontainer validation only if tipe_kontainer is not 'cargo'
        if ($request->tipe_kontainer !== 'cargo') {
            $rules['size_kontainer'] = 'required|string|max:255';
            $rules['unit_kontainer'] = 'required|integer|min:1';
        } else {
            // For cargo type, these fields are optional
            $rules['size_kontainer'] = 'nullable|string|max:255';
            $rules['unit_kontainer'] = 'nullable|integer|min:1';
        }

        // Debug: Log the request data
        Log::info('Order Store Request:', [
            'tipe_kontainer' => $request->tipe_kontainer,
            'size_kontainer' => $request->size_kontainer,
            'unit_kontainer' => $request->unit_kontainer,
            'all_data' => $request->all()
        ]);

        $request->validate($rules);

        $data = $request->all();

        // Don't auto set status anymore - use what user selected
        // $data['status'] = 'confirmed';  // Removed this line

        // Get tujuan kirim name from database
        $tujuanKirim = MasterTujuanKirim::find($request->tujuan_kirim_id);
        $data['tujuan_kirim'] = $tujuanKirim ? $tujuanKirim->nama_tujuan : '';

        // Get tujuan ambil name from database
        $tujuanAmbil = TujuanKegiatanUtama::find($request->tujuan_ambil_id);
        $data['tujuan_ambil'] = $tujuanAmbil ? $tujuanAmbil->ke : '';

        // Convert radio button options to boolean fields
        $data['exclude_ftz03'] = $request->ftz03_option === 'exclude';
        $data['include_ftz03'] = $request->ftz03_option === 'include';
        $data['exclude_sppb'] = $request->sppb_option === 'exclude';
        $data['include_sppb'] = $request->sppb_option === 'include';
        $data['exclude_buruh_bongkar'] = $request->buruh_bongkar_option === 'exclude';
        $data['include_buruh_bongkar'] = $request->buruh_bongkar_option === 'include';

        // Remove the radio button fields from data
        unset($data['ftz03_option'], $data['sppb_option'], $data['buruh_bongkar_option'], $data['tujuan_kirim_id'], $data['tujuan_ambil_id']);

        // Handle cargo type - set default values for size_kontainer and unit_kontainer if empty
        if ($request->tipe_kontainer === 'cargo') {
            if (empty($data['size_kontainer'])) {
                $data['size_kontainer'] = null;
            }
            if (empty($data['unit_kontainer'])) {
                $data['unit_kontainer'] = 1; // Default to 1 for cargo
            }
        }

        // Initialize outstanding tracking fields
        // Use unit_kontainer for outstanding tracking instead of units input
        $data['units'] = $data['unit_kontainer'] ?? 1; // For outstanding tracking, use actual container units or default to 1
        $data['sisa'] = $data['unit_kontainer'] ?? 1; // Initially, all container units are remaining or default to 1
        
        // Set outstanding_status based on order status
        if ($data['status'] === 'confirmed') {
            $data['outstanding_status'] = 'pending'; // Ready to be processed (enum only has pending, partial, completed)
        } else {
            $data['outstanding_status'] = 'pending'; // Draft or pending orders
        }
        
        $data['completion_percentage'] = 0.00;
        $data['processing_history'] = json_encode([]);

        // Update nomor terakhir for ODS if nomor_order follows ODS format
        if (strpos($request->nomor_order, 'ODS') === 0) {
            $this->updateNomorTerakhir($request->nomor_order);
        }

        // Set penerima string field from penerimas table
        if (!empty($data['penerima_id'])) {
            $penerimaObj = Penerima::find($data['penerima_id']);
            $data['penerima'] = $penerimaObj ? $penerimaObj->nama_penerima : null;
        }

        OrderBatam::create($data);

        return redirect()->route('orders-batam.index')->with('success', 'Order berhasil ditambahkan.');
    }

    /**
     * Generate next order number in format: ODS + 2digit month + 2digit year + 6digit running number
     * Example: ODS1025000001 for October 2025, running number 1
     */
    private function generateNextOrderNumber()
    {
        $currentDate = now();
        $month = $currentDate->format('m'); // 2 digit month
        $year = $currentDate->format('y');  // 2 digit year
        
        // Format prefix for current month/year
        $prefix = 'ODS' . $month . $year;

        // Find the last order number with current month/year prefix
        $lastOrder = OrderBatam::where('nomor_order', 'like', $prefix . '%')
            ->orderBy('nomor_order', 'desc')
            ->first();

        $runningNumber = 1; // Default starting number

        if ($lastOrder) {
            // Extract the running number from the last order
            // Format: ODS + MM + YY + 6digit running number
            $lastNumber = substr($lastOrder->nomor_order, -6);
            $runningNumber = (int) $lastNumber + 1;
        } else {
            // If no orders found for current month, check nomor_terakhir table
            $nomorTerakhir = NomorTerakhir::where('modul', 'ODS')->first();
            
            if ($nomorTerakhir && $nomorTerakhir->nomor_terakhir > 0) {
                $runningNumber = $nomorTerakhir->nomor_terakhir + 1;
            }
        }

        // Format: ODS + MM + YY + 6digit running number
        $orderBatamNumber = $prefix . str_pad($runningNumber, 6, '0', STR_PAD_LEFT);

        return $orderBatamNumber;
    }

    /**
     * AJAX endpoint to generate new order number
     */
    public function generateOrderBatamNumber()
    {
        try {
            $orderBatamNumber = $this->generateNextOrderNumber();

            return response()->json([
                'success' => true,
                'order_number' => $orderBatamNumber
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update nomor terakhir based on order number
     */
    private function updateNomorTerakhir($orderBatamNumber)
    {
        // Extract running number from ODS format: ODS + MM + YY + 6digit
        if (preg_match('/^ODS\d{4}(\d{6})$/', $orderBatamNumber, $matches)) {
            $runningNumber = (int) $matches[1];

            // Update nomor terakhir
            NomorTerakhir::where('modul', 'ODS')->update([
                'nomor_terakhir' => $runningNumber
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orderBatam = OrderBatam::with(['term', 'pengirim', 'jenisBarang'])->findOrFail($id);
        return view('orders-batam.show', compact('orderBatam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $orderBatam = OrderBatam::findOrFail($id);
        $terms = Term::where('status', 'active')->get();
        $pengirims = Pengirim::where('status', 'active')->get();
        $penerimas = Penerima::where('status', 'active')->orderBy('nama_penerima')->get();
        $jenisBarangs = JenisBarang::where('status', 'active')->get();
        $tujuanKirims = MasterTujuanKirim::where('status', 'active')->orderBy('nama_tujuan')->get();
        $tujuanKegiatanUtamas = TujuanKegiatanUtama::where('aktif', true)->orderBy('ke')->get();

        // Get distinct ukuran kontainer from stock kontainer
        $ukuranKontainers = StockKontainer::select('ukuran')
            ->distinct()
            ->whereNotNull('ukuran')
            ->where('ukuran', '!=', '')
            ->orderBy('ukuran')
            ->pluck('ukuran')
            ->toArray();

        return view('orders-batam.edit', compact('orderBatam', 'terms', 'pengirims', 'penerimas', 'jenisBarangs', 'tujuanKirims', 'tujuanKegiatanUtamas', 'ukuranKontainers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $orderBatam = OrderBatam::findOrFail($id);

        $request->validate([
            'nomor_order' => 'required|string|unique:order_batams,nomor_order,' . $id,
            'tanggal_order' => 'required|date',
            'tujuan_kirim_id' => 'required|exists:master_tujuan_kirim,id',
            'tujuan_ambil_id' => 'required|exists:tujuan_kegiatan_utamas,id',
            'penerima' => 'nullable|string|max:255',
            'penerima_id' => 'nullable|exists:penerimas,id',
            'alamat_penerima' => 'nullable|string',
            'kontak_penerima' => 'nullable|string|max:255',
            'size_kontainer' => 'required|string|max:255',
            'unit_kontainer' => 'required|integer|min:1',
            'tipe_kontainer' => 'required|in:fcl,lcl,cargo,fcl_plus',
            'tanggal_pickup' => 'nullable|date',
            'satuan' => 'nullable|string|in:kg,ton,m3,unit,pcs,dus,karung,kontainer',
            'no_tiket_do' => 'nullable|string|max:255',
            'term_id' => 'nullable|exists:terms,id',
            'pengirim_id' => 'nullable|exists:pengirims,id',
            'jenis_barang_id' => 'nullable|exists:jenis_barangs,id',
            'status' => 'required|in:draft,confirmed,processing,completed,cancelled',
            'catatan' => 'nullable|string',
            'ftz03_option' => 'nullable|in:exclude,include,none',
            'sppb_option' => 'nullable|in:exclude,include,none',
            'buruh_bongkar_option' => 'nullable|in:exclude,include,none',
        ]);

        $data = $request->all();

        // Get tujuan kirim name from database
        $tujuanKirim = MasterTujuanKirim::find($request->tujuan_kirim_id);
        $data['tujuan_kirim'] = $tujuanKirim ? $tujuanKirim->nama_tujuan : '';

        // Get tujuan ambil name from database
        $tujuanAmbil = TujuanKegiatanUtama::find($request->tujuan_ambil_id);
        $data['tujuan_ambil'] = $tujuanAmbil ? $tujuanAmbil->ke : '';

        // Convert radio button options to boolean fields
        $data['exclude_ftz03'] = $request->ftz03_option === 'exclude';
        $data['include_ftz03'] = $request->ftz03_option === 'include';
        $data['exclude_sppb'] = $request->sppb_option === 'exclude';
        $data['include_sppb'] = $request->sppb_option === 'include';
        $data['exclude_buruh_bongkar'] = $request->buruh_bongkar_option === 'exclude';
        $data['include_buruh_bongkar'] = $request->buruh_bongkar_option === 'include';

        // Store the ID fields for relationships
        $data['tujuan_kirim_id'] = $request->tujuan_kirim_id;
        $data['tujuan_ambil_id'] = $request->tujuan_ambil_id;

        // Remove the radio button fields from data (but keep the ID fields)
        unset($data['ftz03_option'], $data['sppb_option'], $data['buruh_bongkar_option']);

        // Handle changes to unit_kontainer field for outstanding tracking
        // Always sync units with unit_kontainer for consistency
        $data['units'] = $request->unit_kontainer;

        if ($request->unit_kontainer != $orderBatam->units) {
            $oldUnits = $orderBatam->units ?? 0;
            $newUnits = $request->unit_kontainer;
            $processedUnits = $oldUnits - ($orderBatam->sisa ?? 0);

            // Update sisa based on new units and already processed units
            $data['sisa'] = max(0, $newUnits - $processedUnits);

            // Recalculate completion percentage
            if ($newUnits > 0) {
                $data['completion_percentage'] = min(100, ($processedUnits / $newUnits) * 100);

                // Update outstanding status
                if ($processedUnits >= $newUnits) {
                    $data['outstanding_status'] = 'completed';
                    $data['completed_at'] = now();
                } elseif ($processedUnits > 0) {
                    $data['outstanding_status'] = 'partial';
                } else {
                    $data['outstanding_status'] = 'pending';
                }
            }

            // Log the change in processing history
            $history = json_decode($orderBatam->processing_history ?? '[]', true);
            $history[] = [
                'action' => 'units_updated',
                'old_units' => $oldUnits,
                'new_units' => $newUnits,
                'user_id' => Auth::id(),
                'timestamp' => now()->toDateTimeString(),
                'notes' => 'Units updated via order edit (synced with unit_kontainer)'
            ];
            $data['processing_history'] = json_encode($history);
        }

        // Set penerima string field from penerimas table
        if (!empty($data['penerima_id'])) {
            $penerimaObj = Penerima::find($data['penerima_id']);
            $data['penerima'] = $penerimaObj ? $penerimaObj->nama_penerima : null;
        }

        $orderBatam->update($data);

        // Auto-sync related Surat Jalan data
        $this->syncSuratJalanData($orderBatam);

        return redirect()->route('orders-batam.index')->with('success', 'Order berhasil diperbarui. Data surat jalan terkait juga telah disinkronkan.');
    }

    /**
     * Sync related SuratJalan data when Order is updated
     */
    private function syncSuratJalanData(OrderBatam $orderBatam)
    {
        $suratJalans = $orderBatam->suratJalans;
        
        if ($suratJalans->isEmpty()) {
            return;
        }

        // Prepare sync data based on updated order
        $syncData = [];

        // Only update fields that exist in both Order and SuratJalan
        if ($orderBatam->pengirim && $orderBatam->pengirim->nama_pengirim) {
            $syncData['pengirim'] = $orderBatam->pengirim->nama_pengirim;
        }

        if ($orderBatam->tujuan_ambil) {
            $syncData['tujuan_pengambilan'] = $orderBatam->tujuan_ambil;
        }

        if ($orderBatam->tujuan_kirim) {
            $syncData['tujuan_pengiriman'] = $orderBatam->tujuan_kirim;
        }

        if ($orderBatam->tipe_kontainer) {
            $syncData['tipe_kontainer'] = $orderBatam->tipe_kontainer;
        }

        if ($orderBatam->size_kontainer) {
            $syncData['size'] = $orderBatam->size_kontainer;
        }

        if ($orderBatam->unit_kontainer) {
            $syncData['jumlah_kontainer'] = $orderBatam->unit_kontainer;
        }

        if ($orderBatam->jenisBarang && $orderBatam->jenisBarang->id) {
            $syncData['jenis_barang'] = $orderBatam->jenisBarang->id;
        }

        if ($orderBatam->term && $orderBatam->term->id) {
            $syncData['term'] = $orderBatam->term->id;
        }

        // Only update if there's data to sync
        if (!empty($syncData)) {
            // Add metadata about the sync
            $syncData['updated_at'] = now();
            
            // Update all related surat jalans
            $orderBatam->suratJalans()->update($syncData);
            
            // Log the sync activity
            Log::info('SuratJalan data synced', [
                'order_id' => $orderBatam->id,
                'surat_jalan_count' => $suratJalans->count(),
                'synced_fields' => array_keys($syncData),
                'user_id' => Auth::id()
            ]);
        }
    }

    /**
     * Download template CSV untuk import orders
     */
    public function downloadTemplate()
    {
        $filename = 'template_orders_' . date('Y-m-d') . '.csv';
        
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
                'nomor_order',
                'tanggal_order',
                'pengirim_id',
                'nama_pengirim',
                'term_id', 
                'nama_status',
                'jenis_barang_id',
                'nama_jenis_barang',
                'tujuan_ambil',
                'tujuan_kirim',
                'tipe_kontainer',
                'size_kontainer',
                'unit_kontainer',
                'no_tiket_do',
                'jumlah_barang',
                'berat_barang',
                'keterangan',
                'status'
            ];
            
            fputcsv($file, $header);
            
            // Example data rows
            $exampleData = [
                [
                    'ORD-' . date('Ymd') . '-001',
                    date('Y-m-d'),
                    '1',
                    'PT CONTOH PENGIRIM',
                    '1',
                    'COD',
                    '1', 
                    'Elektronik',
                    'Jakarta Utara',
                    'Surabaya',
                    'kontainer',
                    '20',
                    'ft',
                    'TKT001',
                    '100',
                    '1000',
                    'Barang elektronik untuk toko',
                    'draft'
                ],
                [
                    'ORD-' . date('Ymd') . '-002',
                    date('Y-m-d'),
                    '2',
                    'PT CONTOH LAINNYA',
                    '2',
                    'Credit 30',
                    '2',
                    'Makanan',
                    'Bandung',
                    'Medan',
                    'cargo',
                    '',
                    '',
                    'TKT002',
                    '50',
                    '500',
                    'Produk makanan ringan',
                    'confirmed'
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $orderBatam = OrderBatam::findOrFail($id);
        $orderBatam->delete();

        return redirect()->route('orders-batam.index')->with('success', 'Order berhasil dihapus.');
    }
}
