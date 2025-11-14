<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PranotaOb;
use App\Models\PranotaObItem;
use App\Models\TagihanOb;
use App\Models\NomorTerakhir;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PranotaObController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PranotaOb::with(['creator', 'items.tagihanOb'])
            ->latest();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan periode
        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pranota', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('periode', 'like', "%{$search}%");
            });
        }

        $pranotaObs = $query->paginate(15)->withQueryString();
        
        return view('pranota-ob.index', compact('pranotaObs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Ambil tagihan OB yang belum ada di pranota
        $availableTagihanOb = TagihanOb::with('bl')
            ->whereDoesntHave('pranotaObItem')
            ->latest()
            ->get();

        // Group berdasarkan kapal dan voyage untuk memudahkan selection
        $groupedTagihan = $availableTagihanOb->groupBy(['kapal', 'voyage']);

        return view('pranota-ob.create', compact('availableTagihanOb', 'groupedTagihan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_pranota' => 'required|string|max:50|unique:pranota_obs,nomor_pranota',
            'tanggal_pranota' => 'required|date',
            'total_biaya' => 'required|numeric|min:0',
            'penyesuaian' => 'nullable|numeric',
            'keterangan' => 'nullable|string|max:1000',
            'tagihan_ids' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            // Parse tagihan IDs
            $tagihanIds = array_filter(explode(',', $request->tagihan_ids));
            
            if (empty($tagihanIds)) {
                throw new \Exception("Tidak ada tagihan OB yang dipilih.");
            }

            // Cek apakah ada tagihan OB yang sudah ada di pranota lain
            $existingPranota = TagihanOb::whereIn('id', $tagihanIds)
                ->whereHas('pranotaObItem')
                ->first();

            if ($existingPranota) {
                throw new \Exception("Tagihan OB {$existingPranota->nomor_kontainer} sudah ada di pranota lain.");
            }
            
            // Calculate grand total
            $totalBiaya = floatval($request->total_biaya);
            $penyesuaian = floatval($request->penyesuaian ?? 0);
            $grandTotal = $totalBiaya + $penyesuaian;

            // Buat pranota
            $pranota = PranotaOb::create([
                'nomor_pranota' => $request->nomor_pranota,
                'tanggal_pranota' => $request->tanggal_pranota,
                'total_biaya' => $totalBiaya,
                'penyesuaian' => $penyesuaian,
                'grand_total' => $grandTotal,
                'keterangan' => $request->keterangan,
                'created_by' => Auth::id(),
                'status' => 'draft'
            ]);

            // Tambahkan items
            foreach ($tagihanIds as $tagihanObId) {
                $tagihanOb = TagihanOb::find($tagihanObId);
                
                if ($tagihanOb) {
                    PranotaObItem::create([
                        'pranota_ob_id' => $pranota->id,
                        'tagihan_ob_id' => $tagihanObId,
                        'amount' => $tagihanOb->biaya
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('pranota-ob.show', $pranota)
                ->with('success', 'Pranota OB berhasil dibuat dengan nomor: ' . $request->nomor_pranota);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PranotaOb $pranotaOb)
    {
        $pranotaOb->load(['items.tagihanOb.bl', 'creator', 'approver']);
        
        return view('pranota-ob.show', compact('pranotaOb'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PranotaOb $pranotaOb)
    {
        // Hanya bisa edit jika status masih draft
        if ($pranotaOb->status !== 'draft') {
            return back()->with('error', 'Hanya pranota dengan status draft yang bisa diedit');
        }

        $pranotaOb->load('items.tagihanOb');
        
        // Ambil tagihan OB yang available (belum ada di pranota lain)
        $availableTagihanOb = TagihanOb::with('bl')
            ->whereDoesntHave('pranotaObItem', function ($query) use ($pranotaOb) {
                $query->where('pranota_ob_id', '!=', $pranotaOb->id);
            })
            ->latest()
            ->get();

        return view('pranota-ob.edit', compact('pranotaOb', 'availableTagihanOb'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PranotaOb $pranotaOb)
    {
        // Hanya bisa update jika status masih draft
        if ($pranotaOb->status !== 'draft') {
            return back()->with('error', 'Hanya pranota dengan status draft yang bisa diedit');
        }

        $request->validate([
            'tanggal_pranota' => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
            'periode' => 'nullable|string|max:20',
            'tagihan_ob_ids' => 'required|array|min:1',
            'tagihan_ob_ids.*' => 'exists:tagihan_ob,id'
        ]);

        try {
            DB::beginTransaction();

            // Cek apakah ada tagihan OB yang sudah ada di pranota lain (kecuali pranota ini)
            $existingPranota = TagihanOb::whereIn('id', $request->tagihan_ob_ids)
                ->whereHas('pranotaObItem', function ($query) use ($pranotaOb) {
                    $query->where('pranota_ob_id', '!=', $pranotaOb->id);
                })
                ->first();

            if ($existingPranota) {
                throw new \Exception("Tagihan OB {$existingPranota->nomor_kontainer} sudah ada di pranota lain.");
            }

            // Update pranota
            $pranotaOb->update([
                'tanggal_pranota' => $request->tanggal_pranota,
                'keterangan' => $request->keterangan,
                'periode' => $request->periode,
            ]);

            // Hapus items yang lama
            $pranotaOb->items()->delete();

            // Tambahkan items yang baru
            foreach ($request->tagihan_ob_ids as $tagihanObId) {
                $tagihanOb = TagihanOb::find($tagihanObId);
                
                PranotaObItem::create([
                    'pranota_ob_id' => $pranotaOb->id,
                    'tagihan_ob_id' => $tagihanObId,
                    'amount' => $tagihanOb->biaya
                ]);
            }

            // Recalculate total
            $pranotaOb->calculateTotal();

            DB::commit();

            return redirect()->route('pranota-ob.show', $pranotaOb)
                ->with('success', 'Pranota OB berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PranotaOb $pranotaOb)
    {
        // Hanya bisa hapus jika status draft atau cancelled
        if (!in_array($pranotaOb->status, ['draft', 'cancelled'])) {
            return back()->with('error', 'Hanya pranota dengan status draft atau cancelled yang bisa dihapus');
        }

        try {
            DB::beginTransaction();
            
            // Items akan terhapus otomatis karena cascade delete
            $pranotaOb->delete();
            
            DB::commit();

            return redirect()->route('pranota-ob.index')
                ->with('success', 'Pranota OB berhasil dihapus');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus pranota OB: ' . $e->getMessage());
        }
    }

    /**
     * Approve pranota OB
     */
    public function approve(PranotaOb $pranotaOb)
    {
        if ($pranotaOb->status !== 'pending') {
            return back()->with('error', 'Hanya pranota dengan status pending yang bisa disetujui');
        }

        $pranotaOb->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id()
        ]);

        return back()->with('success', 'Pranota OB berhasil disetujui');
    }

    /**
     * Submit pranota untuk approval
     */
    public function submit(PranotaOb $pranotaOb)
    {
        if ($pranotaOb->status !== 'draft') {
            return back()->with('error', 'Hanya pranota dengan status draft yang bisa diajukan');
        }

        if ($pranotaOb->items()->count() === 0) {
            return back()->with('error', 'Pranota harus memiliki minimal satu item');
        }

        $pranotaOb->update(['status' => 'pending']);

        return back()->with('success', 'Pranota OB berhasil diajukan untuk approval');
    }

    /**
     * Cancel pranota
     */
    public function cancel(PranotaOb $pranotaOb)
    {
        if ($pranotaOb->status === 'approved') {
            return back()->with('error', 'Pranota yang sudah disetujui tidak bisa dibatalkan');
        }

        $pranotaOb->update(['status' => 'cancelled']);

        return back()->with('success', 'Pranota OB berhasil dibatalkan');
    }

    /**
     * Print pranota
     */
    public function print(PranotaOb $pranotaOb)
    {
        if ($pranotaOb->status === 'draft') {
            return back()->with('error', 'Pranota dengan status draft tidak bisa dicetak');
        }

        $pranotaOb->load(['items.tagihanOb.bl', 'creator', 'approver']);
        
        return view('pranota-ob.print', compact('pranotaOb'));
    }

    /**
     * Get available tagihan OB for AJAX
     */
    public function getAvailableTagihanOb(Request $request)
    {
        $query = TagihanOb::with('bl')
            ->whereDoesntHave('pranotaObItem');

        // Filter berdasarkan kapal jika ada
        if ($request->filled('kapal')) {
            $query->where('kapal', $request->kapal);
        }

        // Filter berdasarkan voyage jika ada
        if ($request->filled('voyage')) {
            $query->where('voyage', $request->voyage);
        }

        $tagihanObs = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $tagihanObs->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nomor_kontainer' => $item->nomor_kontainer,
                    'kapal' => $item->kapal,
                    'voyage' => $item->voyage,
                    'nama_supir' => $item->nama_supir,
                    'barang' => $item->barang,
                    'biaya' => $item->biaya,
                    'formatted_biaya' => 'Rp ' . number_format($item->biaya, 0, ',', '.')
                ];
            })
        ]);
    }

    /**
     * Generate preview nomor pranota (without incrementing)
     */
    public function generateNomorPreview()
    {
        try {
            $nomorPranota = $this->generateNomorPranota(false);
            
            return response()->json([
                'success' => true,
                'nomor_pranota' => $nomorPranota
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate nomor pranota from master nomor terakhir
     * Format: KodeModul(3)-Bulan(2)-Tahun(2)-RunningNumber(6)
     * Contoh: PMS-11-25-000001
     */
    private function generateNomorPranota($increment = true)
    {
        $modul = 'PMS'; // Pranota Modul Shipping/OB
        
        // Get or create master nomor terakhir untuk modul PMS
        $masterNomor = NomorTerakhir::firstOrCreate(
            ['modul' => $modul],
            [
                'nomor_terakhir' => 0,
                'keterangan' => 'Nomor terakhir untuk Pranota OB (On Board)'
            ]
        );

        // Get current date components
        $now = now();
        $bulan = $now->format('m'); // 2 digit bulan (01-12)
        $tahun = $now->format('y'); // 2 digit tahun (25 untuk 2025)
        
        // Running number (6 digit)
        $runningNumber = $increment ? $masterNomor->nomor_terakhir + 1 : $masterNomor->nomor_terakhir + 1;
        $formattedNumber = str_pad($runningNumber, 6, '0', STR_PAD_LEFT);
        
        // Format final: KodeModul-Bulan-Tahun-RunningNumber
        $nomorPranota = "{$modul}-{$bulan}-{$tahun}-{$formattedNumber}";
        
        // Increment nomor terakhir if requested
        if ($increment) {
            $masterNomor->increment('nomor_terakhir');
        }
        
        return $nomorPranota;
    }
}
