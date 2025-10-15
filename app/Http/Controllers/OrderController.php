<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Term;
use App\Models\Pengirim;
use App\Models\JenisBarang;
use App\Models\MasterTujuanKirim;
use App\Models\TujuanKegiatanUtama;
use App\Models\StockKontainer;
use App\Models\NomorTerakhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['term', 'pengirim', 'jenisBarang']);

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

        $orders = $query->latest()->paginate(15);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $terms = Term::where('status', 'active')->get();
        $pengirims = Pengirim::where('status', 'active')->get();
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

        return view('orders.create', compact('terms', 'pengirims', 'jenisBarangs', 'tujuanKirims', 'tujuanKegiatanUtamas', 'ukuranKontainers', 'nextOrderNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_order' => 'required|string|unique:orders,nomor_order',
            'tanggal_order' => 'required|date',
            'tujuan_kirim_id' => 'required|exists:master_tujuan_kirim,id',
            'tujuan_ambil_id' => 'required|exists:tujuan_kegiatan_utamas,id',
            'size_kontainer' => 'required|string|max:255',
            'unit_kontainer' => 'required|integer|min:1',
            'units' => 'required|integer|min:1',
            'tipe_kontainer' => 'required|in:fcl,lcl,cargo,fcl_plus',
            'tanggal_pickup' => 'nullable|date',
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

        // Remove the radio button fields from data
        unset($data['ftz03_option'], $data['sppb_option'], $data['buruh_bongkar_option'], $data['tujuan_kirim_id'], $data['tujuan_ambil_id']);

        // Initialize outstanding tracking fields
        $data['sisa'] = $data['units']; // Initially, all units are remaining
        $data['outstanding_status'] = 'pending';
        $data['completion_percentage'] = 0.00;
        $data['processing_history'] = json_encode([]);

        // Update nomor terakhir for ODS if nomor_order follows ODS format
        if (strpos($request->nomor_order, 'ODS') === 0) {
            $this->updateNomorTerakhir($request->nomor_order);
        }

        Order::create($data);

        return redirect()->route('orders.index')->with('success', 'Order berhasil ditambahkan.');
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

        // Get or create nomor terakhir for ODS module
        $nomorTerakhir = NomorTerakhir::where('modul', 'ODS')->first();

        if (!$nomorTerakhir) {
            // Create ODS entry if not exists
            $nomorTerakhir = NomorTerakhir::create([
                'modul' => 'ODS',
                'nomor_terakhir' => 0,
                'keterangan' => 'Nomor order delivery system'
            ]);
        }

        // Increment running number
        $runningNumber = $nomorTerakhir->nomor_terakhir + 1;

        // Format: ODS + MM + YY + 6digit running number
        $orderNumber = 'ODS' . $month . $year . str_pad($runningNumber, 6, '0', STR_PAD_LEFT);

        return $orderNumber;
    }

    /**
     * AJAX endpoint to generate new order number
     */
    public function generateOrderNumber()
    {
        try {
            $orderNumber = $this->generateNextOrderNumber();

            return response()->json([
                'success' => true,
                'order_number' => $orderNumber
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
    private function updateNomorTerakhir($orderNumber)
    {
        // Extract running number from ODS format: ODS + MM + YY + 6digit
        if (preg_match('/^ODS\d{4}(\d{6})$/', $orderNumber, $matches)) {
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
        $order = Order::with(['term', 'pengirim', 'jenisBarang'])->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $order = Order::findOrFail($id);
        $terms = Term::where('status', 'active')->get();
        $pengirims = Pengirim::where('status', 'active')->get();
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

        return view('orders.edit', compact('order', 'terms', 'pengirims', 'jenisBarangs', 'tujuanKirims', 'tujuanKegiatanUtamas', 'ukuranKontainers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'nomor_order' => 'required|string|unique:orders,nomor_order,' . $id,
            'tanggal_order' => 'required|date',
            'tujuan_kirim_id' => 'required|exists:master_tujuan_kirim,id',
            'tujuan_ambil_id' => 'required|exists:tujuan_kegiatan_utamas,id',
            'size_kontainer' => 'required|string|max:255',
            'unit_kontainer' => 'required|integer|min:1',
            'units' => 'required|integer|min:1',
            'tipe_kontainer' => 'required|in:fcl,lcl,cargo,fcl_plus',
            'tanggal_pickup' => 'nullable|date',
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

        // Remove the radio button fields from data
        unset($data['ftz03_option'], $data['sppb_option'], $data['buruh_bongkar_option'], $data['tujuan_kirim_id'], $data['tujuan_ambil_id']);

        // Handle changes to units field for outstanding tracking
        if ($request->units != $order->units) {
            $oldUnits = $order->units ?? 0;
            $newUnits = $request->units;
            $processedUnits = $oldUnits - ($order->sisa ?? 0);

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
            $history = json_decode($order->processing_history ?? '[]', true);
            $history[] = [
                'action' => 'units_updated',
                'old_units' => $oldUnits,
                'new_units' => $newUnits,
                'user_id' => Auth::id(),
                'timestamp' => now()->toDateTimeString(),
                'notes' => 'Units updated via order edit'
            ];
            $data['processing_history'] = json_encode($history);
        }

        $order->update($data);

        return redirect()->route('orders.index')->with('success', 'Order berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order berhasil dihapus.');
    }
}
