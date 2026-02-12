<?php

namespace App\Http\Controllers;

use App\Models\PranotaLembur;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use App\Models\NomorTerakhir;
use App\Models\MasterPricelistLembur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PranotaLemburController extends Controller
{
    /**
     * Display form to select date range
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->can('pranota-lembur-view')) {
            abort(403, 'Unauthorized');
        }

        return view('pranota-lembur.select-date');
    }

    /**
     * Display form to create pranota lembur with filtered data
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('pranota-lembur-create')) {
            abort(403, 'Unauthorized');
        }

        // Validasi required tanggal
        if (!$request->has('start_date') || !$request->has('end_date')) {
            return redirect()->route('pranota-lembur.index')
                ->with('error', 'Tanggal mulai dan tanggal akhir harus diisi');
        }

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Get surat jalan yang belum punya pranota lembur
        $suratJalans = SuratJalan::query()
            ->with('tandaTerima')
            ->where(function($q) {
                $q->where('lembur', true)
                  ->orWhere('nginap', true);
            })
            ->whereHas('tandaTerima', function($q) use ($startDate, $endDate) {
                $q->whereDate('tanggal', '>=', $startDate)
                  ->whereDate('tanggal', '<=', $endDate);
            })
            ->whereDoesntHave('pranotaLemburs')
            ->get();

        // Get surat jalan bongkaran yang belum punya pranota lembur
        $bongkarans = SuratJalanBongkaran::query()
            ->with('tandaTerima')
            ->where(function($q) {
                $q->where('lembur', true)
                  ->orWhere('nginap', true);
            })
            ->whereHas('tandaTerima', function($q) use ($startDate, $endDate) {
                $q->whereDate('tanggal_tanda_terima', '>=', $startDate)
                  ->whereDate('tanggal_tanda_terima', '<=', $endDate);
            })
            ->whereDoesntHave('pranotaLemburs')
            ->get();

        // Standardize properties
        $suratJalans->each(function($item) {
            $item->type_surat = 'Muat';
            $item->report_date = $item->tandaTerima ? $item->tandaTerima->tanggal : null;
        });

        $bongkarans->each(function($item) {
            $item->type_surat = 'Bongkaran';
            $item->no_surat_jalan = $item->nomor_surat_jalan;
            $item->report_date = $item->tandaTerima ? $item->tandaTerima->tanggal_tanda_terima : null;
        });

        // Merge collections
        $allSuratJalans = $suratJalans->concat($bongkarans)->sortBy('report_date')->values();

        // Get pricelist lembur
        $pricelistLemburs = MasterPricelistLembur::where('status', 'aktif')->get();

        // Generate nomor pranota
        $nomorTerakhir = NomorTerakhir::where('modul', 'PML')->first();
        $nextNumber = $nomorTerakhir ? $nomorTerakhir->nomor_terakhir + 1 : 1;
        $tahun = now()->format('y');
        $bulan = now()->format('m');
        $nomorCetakan = $request->input('nomor_cetakan', 1);
        $nomorPranotaDisplay = "PML{$nomorCetakan}{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return view('pranota-lembur.create', [
            'suratJalans' => $allSuratJalans,
            'pricelistLemburs' => $pricelistLemburs,
            'nomorPranotaDisplay' => $nomorPranotaDisplay,
            'nomorCetakan' => $nomorCetakan,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Store a newly created pranota lembur
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('pranota-lembur-create')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'tanggal_pranota' => 'required|date',
            'nomor_cetakan' => 'required|integer',
            'adjustment' => 'nullable|numeric',
            'alasan_adjustment' => 'nullable|string',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:muat,bongkaran',
            'items.*.id' => 'required|integer',
            'items.*.supir' => 'required|string',
            'items.*.no_plat' => 'required|string',
            'items.*.is_lembur' => 'required|boolean',
            'items.*.is_nginap' => 'required|boolean',
            'items.*.biaya_lembur' => 'nullable|numeric',
            'items.*.biaya_nginap' => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();

            // Generate nomor pranota
            $nomorTerakhir = NomorTerakhir::where('modul', 'PML')->first();
            if (!$nomorTerakhir) {
                $nomorTerakhir = NomorTerakhir::create([
                    'modul' => 'PML',
                    'nomor_terakhir' => 0
                ]);
            }

            $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
            $tahun = now()->format('y');
            $bulan = now()->format('m');
            $nomorCetakan = $validated['nomor_cetakan'];
            $nomorPranota = "PML{$nomorCetakan}{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            // Calculate total biaya
            $totalBiaya = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $biayaLembur = $item['is_lembur'] ? ($item['biaya_lembur'] ?? 0) : 0;
                $biayaNginap = $item['is_nginap'] ? ($item['biaya_nginap'] ?? 0) : 0;
                $totalItem = $biayaLembur + $biayaNginap;
                $totalBiaya += $totalItem;

                $itemsData[] = [
                    'type' => $item['type'],
                    'id' => $item['id'],
                    'supir' => $item['supir'],
                    'no_plat' => $item['no_plat'],
                    'is_lembur' => $item['is_lembur'],
                    'is_nginap' => $item['is_nginap'],
                    'biaya_lembur' => $biayaLembur,
                    'biaya_nginap' => $biayaNginap,
                    'total_biaya' => $totalItem,
                ];
            }

            $adjustment = $validated['adjustment'] ?? 0;
            $totalSetelahAdjustment = $totalBiaya + $adjustment;

            // Create pranota
            $pranota = PranotaLembur::create([
                'nomor_pranota' => $nomorPranota,
                'nomor_cetakan' => $nomorCetakan,
                'tanggal_pranota' => $validated['tanggal_pranota'],
                'total_biaya' => $totalBiaya,
                'adjustment' => $adjustment,
                'alasan_adjustment' => $validated['alasan_adjustment'],
                'total_setelah_adjustment' => $totalSetelahAdjustment,
                'catatan' => $validated['catatan'],
                'status' => PranotaLembur::STATUS_DRAFT,
                'created_by' => $user->id,
            ]);

            // Attach surat jalans
            foreach ($itemsData as $itemData) {
                $pivotData = [
                    'supir' => $itemData['supir'],
                    'no_plat' => $itemData['no_plat'],
                    'is_lembur' => $itemData['is_lembur'],
                    'is_nginap' => $itemData['is_nginap'],
                    'biaya_lembur' => $itemData['biaya_lembur'],
                    'biaya_nginap' => $itemData['biaya_nginap'],
                    'total_biaya' => $itemData['total_biaya'],
                ];

                if ($itemData['type'] === 'muat') {
                    $pranota->suratJalans()->attach($itemData['id'], $pivotData);
                } else {
                    $pranota->suratJalanBongkarans()->attach($itemData['id'], $pivotData);
                }
            }

            // Update nomor terakhir
            $nomorTerakhir->update(['nomor_terakhir' => $nextNumber]);

            DB::commit();

            return redirect()->route('pranota-lembur.show', $pranota->id)
                ->with('success', 'Pranota Lembur berhasil dibuat dengan nomor: ' . $nomorPranota);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Gagal membuat Pranota Lembur: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display list of pranota lembur
     */
    public function list(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('pranota-lembur-view')) {
            abort(403, 'Unauthorized');
        }

        $query = PranotaLembur::with(['creator', 'suratJalans', 'suratJalanBongkarans'])
            ->latest('tanggal_pranota');

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_pranota', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pranota', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pranota', '<=', $request->end_date);
        }

        $pranotas = $query->paginate(15)->appends($request->query());

        return view('pranota-lembur.list', [
            'pranotas' => $pranotas,
            'search' => $request->search ?? '',
            'status' => $request->status ?? '',
            'startDate' => $request->start_date ?? '',
            'endDate' => $request->end_date ?? '',
        ]);
    }

    /**
     * Display the specified pranota lembur
     */
    public function show(PranotaLembur $pranotaLembur)
    {
        $user = Auth::user();

        if (!$user->can('pranota-lembur-view')) {
            abort(403, 'Unauthorized');
        }

        $pranotaLembur->load([
            'creator',
            'updater',
            'approver',
            'suratJalans.tandaTerima',
            'suratJalanBongkarans.tandaTerima'
        ]);

        return view('pranota-lembur.show', compact('pranotaLembur'));
    }

    /**
     * Print pranota lembur
     */
    public function print(PranotaLembur $pranotaLembur)
    {
        $user = Auth::user();

        if (!$user->can('pranota-lembur-print')) {
            abort(403, 'Unauthorized');
        }

        $pranotaLembur->load([
            'creator',
            'approver',
            'suratJalans.tandaTerima',
            'suratJalanBongkarans.tandaTerima'
        ]);

        return view('pranota-lembur.print', compact('pranotaLembur'));
    }

    /**
     * Update status to submitted
     */
    public function submit(PranotaLembur $pranotaLembur)
    {
        $user = Auth::user();

        if (!$user->can('pranota-lembur-update')) {
            abort(403, 'Unauthorized');
        }

        if ($pranotaLembur->status !== PranotaLembur::STATUS_DRAFT) {
            return back()->with('error', 'Hanya pranota dengan status Draft yang bisa di-submit');
        }

        $pranotaLembur->update([
            'status' => PranotaLembur::STATUS_SUBMITTED,
            'updated_by' => $user->id,
        ]);

        return back()->with('success', 'Pranota Lembur berhasil di-submit');
    }

    /**
     * Approve pranota lembur
     */
    public function approve(PranotaLembur $pranotaLembur)
    {
        $user = Auth::user();

        if (!$user->can('pranota-lembur-approve')) {
            abort(403, 'Unauthorized');
        }

        if ($pranotaLembur->status !== PranotaLembur::STATUS_SUBMITTED) {
            return back()->with('error', 'Hanya pranota dengan status Submitted yang bisa di-approve');
        }

        $pranotaLembur->update([
            'status' => PranotaLembur::STATUS_APPROVED,
            'approved_by' => $user->id,
            'approved_at' => now(),
            'updated_by' => $user->id,
        ]);

        return back()->with('success', 'Pranota Lembur berhasil di-approve');
    }

    /**
     * Cancel pranota lembur
     */
    public function cancel(PranotaLembur $pranotaLembur)
    {
        $user = Auth::user();

        if (!$user->can('pranota-lembur-delete')) {
            abort(403, 'Unauthorized');
        }

        if (in_array($pranotaLembur->status, [PranotaLembur::STATUS_PAID])) {
            return back()->with('error', 'Pranota yang sudah dibayar tidak bisa dibatalkan');
        }

        try {
            DB::beginTransaction();

            $pranotaLembur->update([
                'status' => PranotaLembur::STATUS_CANCELLED,
                'updated_by' => $user->id,
            ]);

            DB::commit();

            return back()->with('success', 'Pranota Lembur berhasil dibatalkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan Pranota Lembur: ' . $e->getMessage());
        }
    }

    /**
     * Delete pranota lembur
     */
    public function destroy(PranotaLembur $pranotaLembur)
    {
        $user = Auth::user();

        if (!$user->can('pranota-lembur-delete')) {
            abort(403, 'Unauthorized');
        }

        if ($pranotaLembur->status === PranotaLembur::STATUS_PAID) {
            return back()->with('error', 'Pranota yang sudah dibayar tidak bisa dihapus');
        }

        try {
            DB::beginTransaction();

            // Detach all relationships
            $pranotaLembur->suratJalans()->detach();
            $pranotaLembur->suratJalanBongkarans()->detach();

            // Soft delete
            $pranotaLembur->delete();

            DB::commit();

            return redirect()->route('pranota-lembur.list')
                ->with('success', 'Pranota Lembur berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus Pranota Lembur: ' . $e->getMessage());
        }
    }
}
