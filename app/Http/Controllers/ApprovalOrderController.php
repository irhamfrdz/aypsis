<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Penerima;
use App\Models\Term;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalOrderController extends Controller
{
    /**
     * Display a listing of approval orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['pengirim', 'jenisBarang', 'term', 'suratJalans', 'recipient']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_order', 'like', "%{$search}%")
                    ->orWhere('tujuan_ambil', 'like', "%{$search}%")
                    ->orWhere('tujuan_kirim', 'like', "%{$search}%")
                    ->orWhereHas('pengirim', function ($query) use ($search) {
                        $query->where('nama_pengirim', 'like', "%{$search}%");
                    })
                    ->orWhereHas('suratJalans', function ($query) use ($search) {
                        $query->where('no_surat_jalan', 'like', "%{$search}%")
                            ->orWhere('no_kontainer', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_order', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_order', '<=', $request->end_date);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Pagination
        $perPage = $request->get('per_page', 50);
        $orders = $query->paginate($perPage)->withQueryString();

        // Get last update time from cache
        $lastUpdate = Cache::get('last_tanda_terima_update');
        $lastUpdateStr = $lastUpdate ? Carbon::parse($lastUpdate)->format('H:i') : '--:--';

        return view('approval-order.index', compact('orders', 'lastUpdateStr'));
    }

    /**
     * Show the form for creating a new approval.
     */
    public function create()
    {
        // Get orders yang belum ada term-nya
        $orders = Order::with(['pengirim'])
            ->whereDoesntHave('term')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('approval-order.create', compact('orders'));
    }

    /**
     * Store a newly created approval (add term to order).
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'term_name' => 'required|string|max:255',
            'term_days' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Create term
            $term = Term::create([
                'name' => $request->term_name,
                'days' => $request->term_days,
                'description' => $request->description,
                'created_by' => Auth::id(),
            ]);

            // Update order with term
            $order = Order::findOrFail($request->order_id);
            $order->term_id = $term->id;
            $order->save();

            DB::commit();

            return redirect()->route('approval-order.index')
                ->with('success', 'Term berhasil ditambahkan ke Order');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menambahkan term: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified approval.
     */
    public function show($id)
    {
        $order = Order::with(['pengirim', 'term', 'jenisBarang', 'recipient', 'notifyParty'])
            ->findOrFail($id);

        return view('approval-order.show', compact('order'));
    }

    /**
     * Show the form for editing the specified approval.
     */
    public function edit($id)
    {
        $order = Order::with(['pengirim', 'jenisBarang', 'term'])->findOrFail($id);
        $terms = Term::orderBy('kode')->get();
        $penerimas = Penerima::where('status', 'active')->orderBy('nama_penerima')->get();
        $pengirims = \App\Models\Pengirim::where('status', 'active')->orderBy('nama_pengirim')->get();

        return view('approval-order.edit', compact('order', 'terms', 'penerimas', 'pengirims'));
    }

    /**
     * Update the specified approval.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'term_id' => 'required|exists:terms,id',
            'pengirim_id' => 'nullable|exists:pengirims,id',
            'alamat_pengirim' => 'nullable|string',
            'kontak_pengirim' => 'nullable|string|max:255',
            'penerima_id' => 'nullable|exists:penerimas,id',
            'notify_party_id' => 'nullable|exists:penerimas,id',
            'kontak_penerima' => 'nullable|string|max:255',
            'alamat_penerima' => 'nullable|string',
            'ftz03_option' => 'required|in:exclude,include,none',
            'sppb_option' => 'required|in:exclude,include,none',
            'buruh_bongkar_option' => 'required|in:exclude,include,none',
            'nama_barang_dimensi' => 'nullable|array',
            'jumlah_dimensi' => 'nullable|array',
            'satuan_dimensi' => 'nullable|array',
            'panjang' => 'nullable|array',
            'lebar' => 'nullable|array',
            'tinggi' => 'nullable|array',
            'meter_kubik' => 'nullable|array',
            'tonase' => 'nullable|array',
        ]);

        try {
            $order = Order::findOrFail($id);
            $order->term_id = $request->term_id;

            // Update Informasi Pengirim
            $order->pengirim_id = $request->pengirim_id;
            $order->alamat_pengirim = $request->alamat_pengirim;
            $order->kontak_pengirim = $request->kontak_pengirim;

            // Update Informasi Penerima
            $order->penerima_id = $request->penerima_id;
            $order->notify_party_id = $request->notify_party_id;

            if ($request->filled('penerima_id')) {
                $penerima = \App\Models\Penerima::find($request->penerima_id);
                $order->penerima = $penerima ? $penerima->nama_penerima : null;
            } else {
                $order->penerima = null;
            }

            $order->kontak_penerima = $request->kontak_penerima;
            $order->alamat_penerima = $request->alamat_penerima;

            // Update FTZ03
            $order->exclude_ftz03 = $request->ftz03_option == 'exclude';
            $order->include_ftz03 = $request->ftz03_option == 'include';

            // Update SPPB
            $order->exclude_sppb = $request->sppb_option == 'exclude';
            $order->include_sppb = $request->sppb_option == 'include';

            // Update Buruh Bongkar
            $order->exclude_buruh_bongkar = $request->buruh_bongkar_option == 'exclude';
            $order->include_buruh_bongkar = $request->buruh_bongkar_option == 'include';

            // Process Dimensi & Volume
            $dimensiItems = [];
            $totalJumlah = 0;
            $totalTonase = 0;
            $totalVolume = 0;
            $namaBarangList = [];

            if ($request->has('nama_barang_dimensi')) {
                foreach ($request->nama_barang_dimensi as $key => $val) {
                    // Check if row has any data
                    if (empty($val) && empty($request->panjang[$key]) && empty($request->lebar[$key]) && empty($request->tonase[$key])) {
                        continue;
                    }

                    $item = [
                        'nama_barang' => $val,
                        'jumlah' => $request->jumlah_dimensi[$key] ?? 0,
                        'satuan' => $request->satuan_dimensi[$key] ?? '',
                        'panjang' => $request->panjang[$key] ?? 0,
                        'lebar' => $request->lebar[$key] ?? 0,
                        'tinggi' => $request->tinggi[$key] ?? 0,
                        'meter_kubik' => $request->meter_kubik[$key] ?? 0,
                        'tonase' => $request->tonase[$key] ?? 0,
                    ];
                    $dimensiItems[] = $item;
                    $totalJumlah += (int) ($item['jumlah'] ?? 0);
                    $totalTonase += (float) ($item['tonase'] ?? 0);
                    $totalVolume += (float) ($item['meter_kubik'] ?? 0);
                    if (! empty($val)) {
                        $namaBarangList[] = $val;
                    }
                }
            }

            $order->dimensi_items = $dimensiItems;
            $order->jumlah = $totalJumlah;
            $order->tonase = $totalTonase;
            $order->meter_kubik = $totalVolume;
            $order->nama_barang = $namaBarangList;

            $order->save();

            // Sync with SuratJalan and TandaTerima
            $order->load(['suratJalans.tandaTerima', 'pengirim', 'term']);
            $namaPengirim = $order->pengirim->nama_pengirim ?? null;
            $alamatPengirim = $order->alamat_pengirim;
            $penerimaId = $order->penerima_id;
            $namaPenerima = $order->penerima;
            $alamatPenerima = $order->alamat_penerima;
            $termName = $order->term->nama_status ?? null;

            foreach ($order->suratJalans as $suratJalan) {
                // Update SuratJalan
                $suratJalan->update([
                    'pengirim' => $namaPengirim,
                    'alamat' => $alamatPengirim,
                    'penerima_id' => $penerimaId,
                    'alamat_penerima' => $alamatPenerima,
                    'term' => $order->term_id,
                ]);

                // Update TandaTerima
                if ($suratJalan->tandaTerima) {
                    $suratJalan->tandaTerima->update([
                        'pengirim' => $namaPengirim,
                        'alamat_pengirim' => $alamatPengirim,
                        'penerima' => $namaPenerima,
                        'alamat_penerima' => $alamatPenerima,
                        'jumlah' => $totalJumlah,
                        'tonase' => $totalTonase,
                        'meter_kubik' => $totalVolume,
                        'dimensi_items' => $dimensiItems,
                        'nama_barang' => $namaBarangList,
                        'satuan' => count($dimensiItems) > 0 ? ($dimensiItems[0]['satuan'] ?? null) : null,
                        'term' => $termName,
                    ]);
                }
            }

            return redirect()->route('approval-order.index')
                ->with('success', 'Data Order berhasil diupdate dan disinkronkan ke Tanda Terima');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengupdate data: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified approval.
     */
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->term_id = null;
            $order->save();

            return redirect()->route('approval-order.index')
                ->with('success', 'Term berhasil dihapus dari Order');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus term: '.$e->getMessage());
        }
    }

    /**
     * Approve the order.
     */
    public function approve($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->status = 'approved';
            $order->approved_by = Auth::id();
            $order->approved_at = Carbon::now();
            $order->save();

            return redirect()->route('approval-order.index')
                ->with('success', 'Order berhasil disetujui');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyetujui Order: '.$e->getMessage());
        }
    }

    /**
     * Reject the order.
     */
    public function reject($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->status = 'rejected';
            $order->rejected_by = Auth::id();
            $order->rejected_at = Carbon::now();
            $order->save();

            return redirect()->route('approval-order.index')
                ->with('success', 'Order berhasil ditolak');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menolak Order: '.$e->getMessage());
        }
    }

    /**
     * Update data penerima dan alamat pada tanda terima via Artisan Command
     */
    public function updateTandaTerima(Request $request)
    {
        try {
            $dryRun = $request->input('dry_run', false);

            // Prepare command arguments
            $arguments = ['--all' => true];
            if ($dryRun) {
                $arguments['--dry-run'] = true;
            }

            // Run artisan command and capture output
            Artisan::call('tanda-terima:update-penerima', $arguments);
            $output = Artisan::output();

            // Parse output to get statistics
            $totalOrders = 0;
            $totalTandaTerima = 0;
            $totalWithChanges = 0;
            $totalUpdated = 0;

            if (preg_match('/Ditemukan (\d+) order dengan data penerima/', $output, $matches)) {
                $totalOrders = (int) $matches[1];
            }

            if (preg_match('/Total Tanda Terima ditemukan: (\d+)/', $output, $matches)) {
                $totalTandaTerima = (int) $matches[1];
            }

            if (preg_match('/Total Tanda Terima dengan perubahan: (\d+)/', $output, $matches)) {
                $totalWithChanges = (int) $matches[1];
            }

            if (preg_match('/Total Tanda Terima yang akan diupdate: (\d+)/', $output, $matches)) {
                $totalWithChanges = (int) $matches[1]; // dry run
            }

            if (preg_match('/Total Tanda Terima berhasil diupdate: (\d+)/', $output, $matches)) {
                $totalUpdated = (int) $matches[1];
            }

            return response()->json([
                'success' => true,
                'message' => $dryRun ? 'Preview berhasil' : 'Update berhasil',
                'total_orders' => $totalOrders,
                'total_tanda_terima' => $totalTandaTerima,
                'total_with_changes' => $totalWithChanges,
                'total_updated' => $dryRun ? 0 : $totalUpdated,
                'output' => $output,
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating tanda terima: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }
}
