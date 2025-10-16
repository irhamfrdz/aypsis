<?php

namespace App\Http\Controllers;

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Jobs\RunCreateNextPeriode;
use App\Models\MasterPricelistSewaKontainer;

class DaftarTagihanKontainerSewaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Re-enable automatic periode creation with proper logic
        // This will create periods based on container duration like in CSV
        try {
            if (!Cache::has('tagihan:create-next-periode:lock')) {
                // dispatch a queued job so the work runs asynchronously
                RunCreateNextPeriode::dispatch();
                // prevent re-dispatch for 60 minutes
                Cache::put('tagihan:create-next-periode:lock', true, now()->addMinutes(60));
            }
        } catch (\Throwable $e) {
            // keep index working even if job dispatch fails; log could be added here
        }

        $query = DaftarTagihanKontainerSewa::query();

        // Exclude GROUP_SUMMARY records from main listing
        $query->where('nomor_kontainer', 'NOT LIKE', 'GROUP_SUMMARY_%')
              ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_TEMPLATE%');

        // Handle search functionality with group-based search
        if ($request->filled('q')) {
            $searchTerm = $request->input('q');

            // Find all containers that match the search term
            $matchingContainers = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')
                ->whereNotNull('group')
                ->where('group', '!=', '')
                ->get();

            if ($matchingContainers->isNotEmpty()) {
                // Collect all unique groups from matching containers
                $groups = $matchingContainers->pluck('group')->unique()->toArray();

                // Search by all these groups to show all containers in the related groups
                $query->whereIn('group', $groups);
            } else {
                // Otherwise, do regular search
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('vendor', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('group', 'LIKE', '%' . $searchTerm . '%');
                });
            }
        }

        // Handle vendor filter
        if ($request->filled('vendor')) {
            $query->where('vendor', $request->input('vendor'));
        }

        // Handle size filter
        if ($request->filled('size')) {
            $query->where('size', $request->input('size'));
        }

        // Handle periode filter
        if ($request->filled('periode')) {
            $query->where('periode', $request->input('periode'));
        }

        // Handle status filter (ongoing/selesai)
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'ongoing') {
                $query->whereNull('tanggal_akhir');
            } elseif ($status === 'selesai') {
                $query->whereNotNull('tanggal_akhir');
            }
        }

        // Handle status pranota filter
        if ($request->filled('status_pranota')) {
            $statusPranota = $request->input('status_pranota');
            if ($statusPranota === 'null') {
                // Filter untuk tagihan yang belum masuk pranota
                $query->whereNull('status_pranota');
            } else {
                // Filter untuk status pranota spesifik
                $query->where('status_pranota', $statusPranota);
            }
        }

        // Apply basic ordering and pagination directly at database level for better performance
        $query->orderBy('nomor_kontainer')
              ->orderBy('periode');

        // Use database-level pagination instead of collection filtering for better performance
        $perPage = 25; // Increase per page to reduce pagination requests
        $tagihans = $query->paginate($perPage);

        // Get filter options with caching for better performance
        $vendors = Cache::remember('tagihan_vendors', 300, function() {
            return DaftarTagihanKontainerSewa::distinct()
                ->whereNotNull('vendor')
                ->where('vendor', '!=', '')
                ->pluck('vendor')
                ->sort()
                ->values();
        });

        $sizes = Cache::remember('tagihan_sizes', 300, function() {
            return DaftarTagihanKontainerSewa::distinct()
                ->whereNotNull('size')
                ->where('size', '!=', '')
                ->pluck('size')
                ->sort()
                ->values();
        });

        $periodes = Cache::remember('tagihan_periodes', 300, function() {
            return DaftarTagihanKontainerSewa::distinct()
                ->whereNotNull('periode')
                ->pluck('periode')
                ->sort()
                ->values();
        });

        // Status options
        $statusOptions = [
            'ongoing' => 'Container Ongoing',
            'selesai' => 'Container Selesai'
        ];

        return view('daftar-tagihan-kontainer-sewa.index', compact('tagihans', 'vendors', 'sizes', 'periodes', 'statusOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('daftar-tagihan-kontainer-sewa.create');
    }



    /**
     * Show the form for creating a new group.
     */
    public function createGroup()
    {
        // Get all tagihan that don't have a group yet
        $tagihans = DaftarTagihanKontainerSewa::where(function($query) {
            $query->whereNull('group')
                  ->orWhere('group', '');
        })
        ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_SUMMARY_%')
        ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_TEMPLATE%')
        ->orderBy('vendor')
        ->orderBy('nomor_kontainer')
        ->get();

        return view('daftar-tagihan-kontainer-sewa.create-group', compact('tagihans'));
    }

    /**
     * Store a newly created group in storage.
     */
    public function storeGroup(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
            'selected_containers' => 'required|array|min:1',
            'selected_containers.*' => 'required|integer|exists:daftar_tagihan_kontainer_sewa,id',
        ]);

        // Additional validation: Check if selected containers are available (not already in another group)
        $selectedContainers = DaftarTagihanKontainerSewa::whereIn('id', $request->selected_containers)->get();
        $containersWithGroups = $selectedContainers->filter(function($container) {
            return !empty($container->group);
        });

        if ($containersWithGroups->isNotEmpty()) {
            $containerNumbers = $containersWithGroups->pluck('nomor_kontainer')->join(', ');
            return response()->json([
                'success' => false,
                'message' => 'Beberapa kontainer sudah memiliki group: ' . $containerNumbers . '. Pilih kontainer yang belum memiliki group.'
            ], 422);
        }

        $validated = $request->all();

        DB::beginTransaction();
        try {
            // Get selected containers to extract common values
            $selectedContainers = DaftarTagihanKontainerSewa::whereIn('id', $validated['selected_containers'])->get();

            // Use values from the first container as defaults for the group
            $firstContainer = $selectedContainers->first();
            $groupPeriode = $firstContainer ? $firstContainer->periode : now()->format('Y-m');

            // Update selected containers to assign them to the group
            DaftarTagihanKontainerSewa::whereIn('id', $validated['selected_containers'])
                ->update([
                    'group' => $validated['group_name'],
                    'updated_at' => now(),
                ]);

            // Create a group summary record
            $groupRecord = DaftarTagihanKontainerSewa::create([
                'group' => $validated['group_name'],
                'vendor' => 'GROUP',
                'size' => 'GROUP',
                'periode' => $groupPeriode,
                'nomor_kontainer' => 'GROUP_SUMMARY_' . $validated['group_name'],
                'tanggal_harga_awal' => now()->format('Y-m-d'),
                'tanggal_harga_akhir' => now()->addDays(30)->format('Y-m-d'),
                'tarif' => 0,
                'dpp' => 0,
                'dpp_nilai_lain' => 0,
                'ppn' => 0,
                'pph' => 0,
                'grand_total' => 0,
                'status_pembayaran' => 'belum_dibayar',
                'keterangan' => 'Group summary untuk ' . $validated['group_name'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Group '{$validated['group_name']}' berhasil dibuat dengan " . count($validated['selected_containers']) . " kontainer",
                'group_id' => $groupRecord->id,
                'container_count' => count($validated['selected_containers'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat group: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request);

        // Fill missing numeric fields with 0 to avoid null numeric issues
        foreach (['dpp','ppn','pph','grand_total'] as $n) {
            if (!isset($data[$n]) || $data[$n] === null || $data[$n] === '') {
                $data[$n] = 0;
            }
        }

        // Compute dpp_nilai_lain from dpp if not provided explicitly
        if (!isset($data['dpp_nilai_lain']) || $data['dpp_nilai_lain'] === null || $data['dpp_nilai_lain'] === '') {
            $data['dpp_nilai_lain'] = round((float)($data['dpp'] ?? 0) * 11 / 12, 2);
        }

        // Compute ppn from dpp_nilai_lain if not provided
        if (!isset($data['ppn']) || $data['ppn'] === null || $data['ppn'] === '') {
            $data['ppn'] = round((float)($data['dpp_nilai_lain'] ?? 0) * 0.12, 2);
        }

        // Compute pph from dpp (2%) if not provided explicitly
        if (!isset($data['pph']) || $data['pph'] === null || $data['pph'] === '') {
            $data['pph'] = round((float)($data['dpp'] ?? 0) * 0.02, 2);
        }

        // Compute grand_total from components if not provided explicitly
        if (!isset($data['grand_total']) || $data['grand_total'] === null || $data['grand_total'] === '') {
            $data['grand_total'] = round((float)($data['dpp'] ?? 0) + (float)($data['ppn'] ?? 0) - (float)($data['pph'] ?? 0), 2);
        }

        DaftarTagihanKontainerSewa::create($data);

        return redirect()->route('daftar-tagihan-kontainer-sewa.index')->with('success', 'Tagihan kontainer berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DaftarTagihanKontainerSewa $tagihan)
    {
        $item = $tagihan;
        return view('daftar-tagihan-kontainer-sewa.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DaftarTagihanKontainerSewa $tagihan)
    {
        $item = $tagihan;
        return view('daftar-tagihan-kontainer-sewa.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DaftarTagihanKontainerSewa $tagihan)
    {
        $data = $this->validateData($request);

        // Log untuk debugging
        \Log::info('Update Tagihan - Data received:', [
            'dpp' => $request->input('dpp'),
            'ppn' => $request->input('ppn'),
            'pph' => $request->input('pph'),
            'grand_total' => $request->input('grand_total'),
        ]);

        // Pastikan field numerik adalah angka, bukan null atau string kosong
        foreach (['dpp','ppn','pph','grand_total','dpp_nilai_lain'] as $field) {
            if (!isset($data[$field]) || $data[$field] === null || $data[$field] === '') {
                $data[$field] = 0;
            } else {
                // Pastikan nilai adalah numeric
                $data[$field] = (float) $data[$field];
            }
        }

        // PENTING: Jangan auto-calculate jika user sudah input nilai
        // Auto-calculate hanya untuk field yang benar-benar kosong (nilai 0)
        
        // Auto-calculate DPP Nilai Lain hanya jika = 0 dan DPP ada nilai
        if ($data['dpp_nilai_lain'] == 0 && $data['dpp'] > 0) {
            $data['dpp_nilai_lain'] = round($data['dpp'] * 11 / 12, 2);
        }

        // Auto-calculate PPN hanya jika = 0 dan DPP Nilai Lain ada nilai
        if ($data['ppn'] == 0 && $data['dpp_nilai_lain'] > 0) {
            $data['ppn'] = round($data['dpp_nilai_lain'] * 0.12, 2);
        }

        // Auto-calculate PPH hanya jika = 0 dan DPP ada nilai
        if ($data['pph'] == 0 && $data['dpp'] > 0) {
            $data['pph'] = round($data['dpp'] * 0.02, 2);
        }

        // Auto-calculate Grand Total hanya jika = 0
        if ($data['grand_total'] == 0) {
            $data['grand_total'] = round($data['dpp'] + $data['ppn'] - $data['pph'], 2);
        }

        \Log::info('Update Tagihan - Data after processing:', $data);

        $tagihan->update($data);

        return redirect()->route('daftar-tagihan-kontainer-sewa.index')->with('success', 'Tagihan kontainer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DaftarTagihanKontainerSewa $tagihan)
    {
        $tagihan->delete();
        return redirect()->route('daftar-tagihan-kontainer-sewa.index')->with('success', 'Tagihan kontainer berhasil dihapus.');
    }

    /**
     * Validate incoming request data.
     */
    protected function validateData(Request $request): array
    {
        return $request->validate([
            'vendor' => 'required|string|max:255',
            'nomor_kontainer' => 'required|string|max:100',
            'size' => 'nullable|string|max:50',
            'group' => 'nullable|string|max:100',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'periode' => 'nullable|integer|min:1',
            'masa' => 'nullable|string|max:255',
            'tarif' => 'nullable|string|max:50', // Changed from numeric to string - tarif can be "harian" or "bulanan"
            'adjustment' => 'nullable|numeric',
            'dpp' => 'nullable|numeric',
            'dpp_nilai_lain' => 'nullable|numeric',
            'ppn' => 'nullable|numeric',
            'pph' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric',
            'status' => 'nullable|string|max:50',
        ]);
    }

    /**
     * Update adjustment value for specific tagihan.
     */
    public function updateAdjustment(Request $request, $id)
    {
        // Validate the adjustment value
        $request->validate([
            'adjustment' => 'required|numeric|between:-999999999.99,999999999.99',
        ]);

        try {
            $tagihan = DaftarTagihanKontainerSewa::findOrFail($id);

            // Store old values for logging
            $oldAdjustment = $tagihan->adjustment ?? 0;
            $newAdjustment = $request->input('adjustment');

            // Update the adjustment
            $tagihan->adjustment = $newAdjustment;

            // Recalculate related values based on adjusted DPP
            $originalDpp = (float)($tagihan->dpp ?? 0);
            $adjustedDpp = $originalDpp + $newAdjustment;

            // Recalculate PPN (11%)
            $ppnRate = 0.11;
            $tagihan->ppn = $adjustedDpp * $ppnRate;

            // Recalculate PPH (2% - adjust rate as needed)
            $pphRate = 0.02;
            $tagihan->pph = $adjustedDpp * $pphRate;

            // Recalculate DPP Nilai Lain based on adjusted DPP
            $tagihan->dpp_nilai_lain = round($adjustedDpp * 11/12, 2);

            // Recalculate Grand Total: DPP + PPN - PPH (tanpa DPP Nilai Lain)
            $tagihan->grand_total = $adjustedDpp + $tagihan->ppn - $tagihan->pph;

            $tagihan->save();

            // Log the change for audit purposes
            Log::info("Adjustment updated for tagihan ID {$id}", [
                'container' => $tagihan->nomor_kontainer,
                'old_adjustment' => $oldAdjustment,
                'new_adjustment' => $newAdjustment,
                'adjusted_dpp' => $adjustedDpp,
                'new_dpp_nilai_lain' => $tagihan->dpp_nilai_lain,
                'new_ppn' => $tagihan->ppn,
                'new_pph' => $tagihan->pph,
                'new_grand_total' => $tagihan->grand_total,
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Adjustment berhasil diperbarui dan nilai terkait telah dihitung ulang',
                    'data' => [
                        'id' => $tagihan->id,
                        'adjustment' => $tagihan->adjustment,
                        'adjusted_dpp' => $adjustedDpp,
                        'dpp_nilai_lain' => $tagihan->dpp_nilai_lain,
                        'ppn' => $tagihan->ppn,
                        'pph' => $tagihan->pph,
                        'grand_total' => $tagihan->grand_total,
                        'formatted_adjustment' => 'Rp ' . number_format((float)$tagihan->adjustment, 0, '.', ','),
                        'formatted_dpp_nilai_lain' => 'Rp ' . number_format((float)$tagihan->dpp_nilai_lain, 0, '.', ','),
                        'formatted_ppn' => 'Rp ' . number_format((float)$tagihan->ppn, 0, '.', ','),
                        'formatted_pph' => 'Rp ' . number_format((float)$tagihan->pph, 0, '.', ','),
                        'formatted_grand_total' => 'Rp ' . number_format((float)$tagihan->grand_total, 0, '.', ','),
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Adjustment berhasil diperbarui dan nilai terkait telah dihitung ulang');

        } catch (\Exception $e) {
            Log::error("Failed to update adjustment for tagihan ID {$id}", [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui adjustment: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal memperbarui adjustment: ' . $e->getMessage());
        }
    }

    /**
     * Update adjustment note
     */
    public function updateAdjustmentNote(Request $request, $id)
    {
        try {
            // Validate request
            $request->validate([
                'adjustment_note' => 'nullable|string|max:500',
            ]);

            // Find the record
            $tagihan = DaftarTagihanKontainerSewa::find($id);
            if (!$tagihan) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data tagihan tidak ditemukan'
                    ], 404);
                }
                return redirect()->back()->with('error', 'Data tagihan tidak ditemukan');
            }

            // Update adjustment note
            $tagihan->adjustment_note = $request->adjustment_note;
            $tagihan->save();

            // Log the change
            Log::info("Adjustment note updated for tagihan ID {$id}", [
                'adjustment_note' => $request->adjustment_note,
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Alasan adjustment berhasil diperbarui',
                    'data' => [
                        'id' => $tagihan->id,
                        'adjustment_note' => $tagihan->adjustment_note,
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Alasan adjustment berhasil diperbarui');

        } catch (\Exception $e) {
            Log::error("Failed to update adjustment note for tagihan ID {$id}", [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui alasan adjustment: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal memperbarui alasan adjustment: ' . $e->getMessage());
        }
    }

    /**
     * Update vendor invoice information
     */
    public function updateVendorInfo(Request $request, $id)
    {
        try {
            // Validate request
            $request->validate([
                'invoice_vendor' => 'nullable|string|max:100',
                'tanggal_vendor' => 'nullable|date',
            ]);

            // Find the record
            $tagihan = DaftarTagihanKontainerSewa::find($id);
            if (!$tagihan) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data tagihan tidak ditemukan'
                    ], 404);
                }
                return redirect()->back()->with('error', 'Data tagihan tidak ditemukan');
            }

            // Update vendor info
            $tagihan->invoice_vendor = $request->invoice_vendor;
            $tagihan->tanggal_vendor = $request->tanggal_vendor;
            $tagihan->save();

            // Log the change
            Log::info("Vendor info updated for tagihan ID {$id}", [
                'invoice_vendor' => $request->invoice_vendor,
                'tanggal_vendor' => $request->tanggal_vendor,
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Informasi vendor berhasil diperbarui',
                    'data' => [
                        'id' => $tagihan->id,
                        'invoice_vendor' => $tagihan->invoice_vendor,
                        'tanggal_vendor' => $tagihan->tanggal_vendor,
                        'formatted_tanggal_vendor' => $tagihan->tanggal_vendor ? $tagihan->tanggal_vendor->format('d-M-Y') : null,
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Informasi vendor berhasil diperbarui');

        } catch (\Exception $e) {
            Log::error("Failed to update vendor info for tagihan ID {$id}", [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui informasi vendor: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal memperbarui informasi vendor: ' . $e->getMessage());
        }
    }

    /**
     * Update group information
     */
    public function updateGroupInfo(Request $request, $id)
    {
        try {
            // Validate request
            $request->validate([
                'group' => 'nullable|string|max:50',
            ]);

            // Find the record
            $tagihan = DaftarTagihanKontainerSewa::find($id);
            if (!$tagihan) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data tagihan tidak ditemukan'
                    ], 404);
                }
                return redirect()->back()->with('error', 'Data tagihan tidak ditemukan');
            }

            // Update group info
            $tagihan->group = $request->group;
            $tagihan->save();

            // Log the change
            Log::info("Group info updated for tagihan ID {$id}", [
                'group' => $request->group,
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Informasi group berhasil diperbarui',
                    'data' => [
                        'id' => $tagihan->id,
                        'group' => $tagihan->group,
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Informasi group berhasil diperbarui');

        } catch (\Exception $e) {
            Log::error("Failed to update group info for tagihan ID {$id}", [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui informasi group: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal memperbarui informasi group: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete selected items
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:daftar_tagihan_kontainer_sewa,id'
        ]);

        $count = DaftarTagihanKontainerSewa::whereIn('id', $request->ids)->delete();

        return redirect()->back()
                        ->with('success', "{$count} data tagihan kontainer berhasil dihapus.");
    }

    /**
     * Masukan ke pranota - update status to indicate items are added to pranota
     */
    public function masukanKePranota(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:daftar_tagihan_kontainer_sewa,id',
            'tanggal_pranota' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Get selected tagihan items
            $tagihanItems = DaftarTagihanKontainerSewa::whereIn('id', $request->ids)->get();

            if ($tagihanItems->isEmpty()) {
                throw new \Exception('Tidak ada tagihan yang ditemukan dengan ID yang dipilih');
            }

            // Generate nomor pranota with format: PTK + 1 digit cetakan + 2 digit tahun + 2 digit bulan + 6 digit running number
            $nomorCetakan = 1; // Fixed value since input removed
            $tanggalPranota = Carbon::parse($request->tanggal_pranota);
            $tahun = $tanggalPranota->format('y'); // 2 digit year
            $bulan = $tanggalPranota->format('m'); // 2 digit month

            // Running number: count pranota in current month + 1
            $runningNumber = str_pad(
                \App\Models\Pranota::whereYear('created_at', $tanggalPranota->year)
                    ->whereMonth('created_at', $tanggalPranota->month)
                    ->count() + 1,
                6, '0', STR_PAD_LEFT
            );

            $noInvoice = "PTK{$nomorCetakan}{$tahun}{$bulan}{$runningNumber}";

            // Create pranota
            $pranota = \App\Models\Pranota::create([
                'no_invoice' => $noInvoice,
                'total_amount' => 0, // Will be calculated and updated below
                'keterangan' => $request->keterangan ?: 'Pranota untuk ' . count($request->ids) . ' tagihan kontainer sewa',
                'status' => 'unpaid',
                'tagihan_ids' => $request->ids,
                'jumlah_tagihan' => count($request->ids),
                'tanggal_pranota' => $request->tanggal_pranota,
                'due_date' => $tanggalPranota->addDays(30)->format('Y-m-d')
            ]);

            // Update total amount using model method
            $pranota->updateTotalAmount();

            // Update tagihan items to mark them as included in pranota
            DaftarTagihanKontainerSewa::whereIn('id', $request->ids)
                ->update([
                    'status_pranota' => 'included',
                    'pranota_id' => $pranota->id,
                    'updated_at' => Carbon::now()
                ]);

            DB::commit();

            $message = 'Pranota berhasil dibuat dengan nomor: ' . $pranota->no_invoice .
                      ' untuk ' . count($request->ids) . ' tagihan (Total: Rp ' . number_format($pranota->total_amount ?? 0, 2, ',', '.') . ')';

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'pranota_id' => $pranota->id,
                    'pranota_no' => $pranota->no_invoice
                ]);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat pranota: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal membuat pranota: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update status for selected items
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:daftar_tagihan_kontainer_sewa,id',
            'status_pembayaran' => 'required|in:belum_dibayar,sudah_dibayar'
        ]);

        $count = DaftarTagihanKontainerSewa::whereIn('id', $request->ids)
                                  ->update(['status_pembayaran' => $request->status_pembayaran]);

        return redirect()->back()
                        ->with('success', "Status pembayaran {$count} data tagihan berhasil diperbarui.");
    }

    /**
     * Get all existing groups with their container counts
     */
    public function getGroups()
    {
        try {
            // Get all unique groups that are not null/empty and not GROUP_SUMMARY or GROUP_TEMPLATE
            $groups = DaftarTagihanKontainerSewa::whereNotNull('group')
                ->where('group', '!=', '')
                ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_SUMMARY_%')
                ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_TEMPLATE%')
                ->select('group')
                ->selectRaw('COUNT(*) as count')
                ->selectRaw('MIN(created_at) as created_at')
                ->groupBy('group')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($group) {
                    return [
                        'name' => $group->group,
                        'count' => $group->count,
                        'created_at' => $group->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'groups' => $groups
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting groups: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data group'
            ], 500);
        }
    }

    /**
     * Delete selected groups (remove group assignment from containers)
     */
    public function deleteGroups(Request $request)
    {
        $request->validate([
            'group_names' => 'required|array|min:1',
            'group_names.*' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $deletedGroups = [];
            $totalContainers = 0;

            foreach ($request->group_names as $groupName) {
                // Count containers in this group before deletion
                $containerCount = DaftarTagihanKontainerSewa::where('group', $groupName)->count();

                if ($containerCount > 0) {
                    // Remove group assignment (set group to null)
                    DaftarTagihanKontainerSewa::where('group', $groupName)
                        ->update(['group' => null]);

                    $deletedGroups[] = $groupName;
                    $totalContainers += $containerCount;
                }
            }

            DB::commit();

            $message = count($deletedGroups) === 1
                ? "Group '{$deletedGroups[0]}' berhasil dihapus. {$totalContainers} kontainer dikembalikan ke status individual."
                : count($deletedGroups) . " group berhasil dihapus. {$totalContainers} kontainer dikembalikan ke status individual.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_groups' => $deletedGroups,
                'total_containers' => $totalContainers
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting groups: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus group: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ungroupContainers(Request $request)
    {
        $request->validate([
            'container_ids' => 'required|array|min:1',
            'container_ids.*' => 'required|integer|exists:daftar_tagihan_kontainer_sewa,id'
        ]);

        try {
            DB::beginTransaction();

            // Update selected containers to remove group assignment (set group to null)
            $updatedCount = DaftarTagihanKontainerSewa::whereIn('id', $request->container_ids)
                ->whereNotNull('group') // Only update containers that actually have a group
                ->update(['group' => null]);

            DB::commit();

            $message = $updatedCount === 1
                ? "1 kontainer berhasil dikembalikan ke status individual."
                : "{$updatedCount} kontainer berhasil dikembalikan ke status individual.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'ungrouped_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error ungrouping containers: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus group: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show import page
     */
    public function importPage()
    {
        return view('daftar-tagihan-kontainer-sewa.import');
    }

    /**
     * Handle CSV import (basic import endpoint)
     */
    public function importCsv(Request $request)
    {
        // Validate file upload
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('csv_file');

            // Use default import options for basic import
            $options = [
                'validate_only' => false,
                'skip_duplicates' => true,
                'update_existing' => false,
            ];

            // Process the CSV import using existing method
            $results = $this->processCsvImport($file, $options);

            // Log the import activity
            Log::info('CSV Import completed', [
                'user_id' => Auth::id(),
                'filename' => $file->getClientOriginalName(),
                'imported' => $results['imported_count'],
                'errors' => count($results['errors']),
            ]);

            // Handle response based on request type
            if ($request->expectsJson()) {
                return response()->json($results);
            }

            // Redirect with success/error messages
            if ($results['success']) {
                return redirect()
                    ->route('daftar-tagihan-kontainer-sewa.index')
                    ->with('success', "Import berhasil! {$results['imported_count']} data berhasil diimpor.");
            } else {
                return redirect()
                    ->back()
                    ->withErrors(['csv_file' => 'Import gagal: ' . implode(', ', $results['errors'])])
                    ->withInput();
            }

        } catch (\Exception $e) {
            Log::error('Import CSV failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return redirect()
                ->back()
                ->withErrors(['csv_file' => 'Terjadi kesalahan saat import: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Export data tagihan to CSV
     */
    public function export(Request $request)
    {
        try {
            $query = DaftarTagihanKontainerSewa::query();

            // Exclude GROUP_SUMMARY records
            $query->where('nomor_kontainer', 'NOT LIKE', 'GROUP_SUMMARY_%')
                  ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_TEMPLATE%');

            // Apply same filters as index page
            if ($request->filled('vendor')) {
                $query->where('vendor', $request->input('vendor'));
            }

            if ($request->filled('size')) {
                $query->where('size', $request->input('size'));
            }

            if ($request->filled('periode')) {
                $query->where('periode', $request->input('periode'));
            }

            if ($request->filled('status')) {
                $status = $request->input('status');
                if ($status === 'ongoing') {
                    $query->whereNull('tanggal_akhir');
                } elseif ($status === 'selesai') {
                    $query->whereNotNull('tanggal_akhir');
                }
            }

            if ($request->filled('status_pranota')) {
                $statusPranota = $request->input('status_pranota');
                if ($statusPranota === 'null') {
                    $query->whereNull('status_pranota');
                } else {
                    $query->where('status_pranota', $statusPranota);
                }
            }

            // Apply search if provided
            if ($request->filled('q')) {
                $searchTerm = $request->input('q');
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('vendor', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('group', 'LIKE', '%' . $searchTerm . '%');
                });
            }

            $query->orderBy('nomor_kontainer')->orderBy('periode');

            // Get all matching records
            $tagihans = $query->get();

            $filename = 'export_tagihan_kontainer_sewa_' . date('Y-m-d_His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($tagihans) {
                $file = fopen('php://output', 'w');

                // Add BOM for UTF-8
                fputs($file, "\xEF\xBB\xBF");

                // Write header row
                fputcsv($file, [
                    'Group',
                    'Vendor',
                    'Nomor Kontainer',
                    'Size',
                    'Tanggal Awal',
                    'Tanggal Akhir',
                    'Periode',
                    'Masa',
                    'Tarif',
                    'Status',
                    'DPP',
                    'Adjustment',
                    'DPP Nilai Lain',
                    'PPN',
                    'PPH',
                    'Grand Total',
                    'Status Pranota',
                    'Pranota ID'
                ], ';');

                // Write data rows
                foreach ($tagihans as $tagihan) {
                    fputcsv($file, [
                        $tagihan->group ?? '',
                        $tagihan->vendor ?? '',
                        $tagihan->nomor_kontainer ?? '',
                        $tagihan->size ?? '',
                        $tagihan->tanggal_awal ? Carbon::parse($tagihan->tanggal_awal)->format('d-m-Y') : '',
                        $tagihan->tanggal_akhir ? Carbon::parse($tagihan->tanggal_akhir)->format('d-m-Y') : '',
                        $tagihan->periode ?? '',
                        $tagihan->masa ?? '',
                        $tagihan->tarif ?? '',
                        $tagihan->status ?? '',
                        $tagihan->dpp ?? 0,
                        $tagihan->adjustment ?? 0,
                        $tagihan->dpp_nilai_lain ?? 0,
                        $tagihan->ppn ?? 0,
                        $tagihan->pph ?? 0,
                        $tagihan->grand_total ?? 0,
                        $tagihan->status_pranota ?? '',
                        $tagihan->pranota_id ?? ''
                    ], ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error exporting data: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }

    /**
     * Export template for import (CSV format)
     */
    public function exportTemplate(Request $request)
    {
        try {
            $format = $request->get('format', 'standard'); // standard or dpe
            $filename = 'template_import_tagihan_kontainer_sewa_' . $format . '_' . date('Y-m-d') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($format) {
                $file = fopen('php://output', 'w');

                // Add BOM for UTF-8
                fputs($file, "\xEF\xBB\xBF");

                if ($format === 'dpe') {
                    // DPE Format Template (Auto Group Format)
                    fputcsv($file, [
                        'vendor',
                        'nomor_kontainer',
                        'size',
                        'group',
                        'tanggal_awal',
                        'tanggal_akhir',
                        'periode',
                        'tarif',
                        'status'
                    ], ';');

                    // Sample data for DPE format
                    fputcsv($file, [
                        'DPE',
                        'CCLU3836629',
                        '20',
                        '',
                        '2025-01-21',
                        '2025-02-20',
                        '1',
                        'Bulanan',
                        'Tersedia'
                    ], ';');

                    fputcsv($file, [
                        'DPE',
                        'CCLU3836629',
                        '20',
                        '',
                        '2025-02-21',
                        '2025-03-20',
                        '2',
                        'Bulanan',
                        'Tersedia'
                    ], ';');

                    fputcsv($file, [
                        'DPE',
                        'CBHU4077764',
                        '20',
                        '',
                        '2025-01-21',
                        '2025-02-20',
                        '1',
                        'Bulanan',
                        'Tersedia'
                    ], ';');

                    fputcsv($file, [
                        'DPE',
                        'RXTU4540180',
                        '40',
                        '',
                        '2025-03-04',
                        '2025-04-03',
                        '1',
                        'Bulanan',
                        'Tersedia'
                    ], ';');
                } else {
                    // Standard Format Template
                    fputcsv($file, [
                        'vendor',
                        'nomor_kontainer',
                        'size',
                        'group',
                        'tanggal_awal',
                        'tanggal_akhir',
                        'periode',
                        'tarif',
                        'status'
                    ], ';');

                    // Write sample data
                    fputcsv($file, [
                        'ZONA',
                        'ZONA001234',
                        '20',
                        'GROUP001',
                        '2024-01-01',
                        '2024-01-31',
                        '1',
                        'Bulanan',
                        'ongoing'
                    ], ';');

                    fputcsv($file, [
                        'DPE',
                        'DPE567890',
                        '40',
                        'GROUP002',
                        '2024-01-01',
                        '2024-01-31',
                        '1',
                        'Bulanan',
                        'selesai'
                    ], ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error generating export template: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Gagal mengunduh template: ' . $e->getMessage());
        }
    }

    /**
     * Process import file (CSV format)
     */
    public function processImport(Request $request)
    {
        // Validate request
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max, CSV only
            'validate_only' => 'boolean',
            'skip_duplicates' => 'boolean',
            'update_existing' => 'boolean',
        ]);

        try {
            $file = $request->file('import_file');

            // Prepare import options
            $options = [
                'validate_only' => $request->boolean('validate_only'),
                'skip_duplicates' => $request->boolean('skip_duplicates'),
                'update_existing' => $request->boolean('update_existing'),
            ];

            // Process CSV import
            $results = $this->processCsvImport($file, $options);

            // Log import activity
            Log::info('Import processed', [
                'user_id' => Auth::id(),
                'filename' => $file->getClientOriginalName(),
                'results' => $results,
            ]);

            // Return JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json($results);
            }

            // Handle redirect for non-AJAX requests
            if ($results['success']) {
                $message = $options['validate_only']
                    ? 'Validasi berhasil. Data siap untuk diimport.'
                    : "Import berhasil. {$results['imported_count']} data berhasil diimport";

                if ($results['updated_count'] > 0) {
                    $message .= ", {$results['updated_count']} data berhasil diupdate";
                }

                if ($results['skipped_count'] > 0) {
                    $message .= ", {$results['skipped_count']} data diskip (duplikat)";
                }

                return redirect()->route('daftar-tagihan-kontainer-sewa.index')->with('success', $message);
            } else {
                $errorMessage = 'Import gagal. ' . count($results['errors']) . ' error ditemukan.';
                return redirect()->back()->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('Import failed', [
                'user_id' => Auth::id(),
                'filename' => $request->file('import_file')->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import gagal: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    /**
     * Process CSV import
     */
    private function processCsvImport($file, $options)
    {
        $results = [
            'success' => true,
            'imported_count' => 0,
            'updated_count' => 0,
            'skipped_count' => 0,
            'errors' => [],
            'warnings' => [],
            'validate_only' => $options['validate_only'],
        ];

        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            throw new \Exception('Tidak dapat membaca file CSV');
        }

        $headers = [];
        $rowNumber = 0;

        // Detect CSV delimiter
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            $rowNumber++;

            try {
                // First row is header
                if ($rowNumber === 1) {
                    $headers = array_map('trim', $row);

                    // Remove BOM from the first header if present
                    if (!empty($headers[0])) {
                        $headers[0] = str_replace("\xEF\xBB\xBF", "", $headers[0]); // Remove UTF-8 BOM
                        $headers[0] = preg_replace('/^\x{FEFF}/u', '', $headers[0]); // Remove Unicode BOM
                    }

                    // Clean all headers from BOM and excess whitespace
                    $headers = array_map(function($header) {
                        // Remove BOM characters
                        $cleaned = str_replace("\xEF\xBB\xBF", "", $header);
                        $cleaned = preg_replace('/^\x{FEFF}/u', '', $cleaned);
                        $cleaned = preg_replace('/^[\x{FEFF}\x{EF}\x{BB}\x{BF}]+/u', '', $cleaned);
                        // Trim whitespace and remove quotes
                        $cleaned = trim($cleaned);
                        $cleaned = trim($cleaned, '"\''); // Remove surrounding quotes
                        return $cleaned;
                    }, $headers);

                    // Map headers dari format CSV Anda ke format sistem
                    $headerMapping = [
                        'Group' => 'group',
                        'Vendor' => 'vendor',
                        'Kontainer' => 'nomor_kontainer',
                        'Nomor Kontainer' => 'nomor_kontainer', // Support "Nomor Kontainer" variant
                        'Size' => 'size',
                        'Awal' => 'tanggal_awal',
                        'Tanggal Awal' => 'tanggal_awal', // Support "Tanggal Awal" variant
                        'Akhir' => 'tanggal_akhir',
                        'Tanggal Akhir' => 'tanggal_akhir', // Support "Tanggal Akhir" variant
                        'Ukuran' => 'size',
                        'Harga' => 'tarif',
                        'Tarif' => 'tarif',
                        'Periode' => 'periode_input',
                        'Masa' => 'masa',
                        'Status' => 'status_type',
                        'Hari' => 'hari',
                        'DPP' => 'dpp',
                        'Adjustment' => 'adjustment',
                        'DPP Nilai Lain' => 'dpp_nilai_lain',
                        'PPN' => 'ppn',
                        'PPH' => 'pph',
                        'Grand Total' => 'grand_total',
                        'Status Pranota' => 'status_pranota',
                        'Pranota ID' => 'pranota_id',
                        'Keterangan' => 'keterangan',
                        'QTY Disc' => 'qty_disc',
                        'Pembulatan' => 'pembulatan',
                        'No.InvoiceVendor' => 'no_invoice_vendor',
                        'Tgl.InvVendor' => 'tgl_invoice_vendor',
                        'No.Bank' => 'no_bank',
                        'Tgl.Bank' => 'tgl_bank'
                    ];

                    // Check if this CSV has the expected DPE format or standard format
                    // Clean headers for BOM and check
                    $cleanedHeaders = array_map(function($header) {
                        return preg_replace('/^[\x{FEFF}\x{EF}\x{BB}\x{BF}]+/u', '', $header);
                    }, $headers);

                    // Check for DPE format with columns: Group, Kontainer, Awal, Akhir, Ukuran
                    // Support both "Kontainer" and "Nomor Kontainer" variants
                    $dpeExpectedHeaders = ['Group', 'Kontainer', 'Awal', 'Akhir', 'Ukuran'];
                    $dpeExpectedHeadersVariant = ['Group', 'Nomor Kontainer', 'Tanggal Awal', 'Tanggal Akhir', 'Size'];
                    $dpeExpectedHeadersVariant2 = ['Group', 'Vendor', 'Nomor Kontainer', 'Size', 'Tanggal Awal', 'Tanggal Akhir'];
                    $hasDpeFormat = count(array_intersect($dpeExpectedHeaders, $cleanedHeaders)) >= 3 ||
                                   count(array_intersect($dpeExpectedHeadersVariant, $cleanedHeaders)) >= 3 ||
                                   count(array_intersect($dpeExpectedHeadersVariant2, $cleanedHeaders)) >= 4;

                    // Check for standard format with columns: vendor, nomor_kontainer, size, tanggal_awal, tanggal_akhir
                    $standardRequiredHeaders = ['vendor', 'nomor_kontainer', 'size', 'tanggal_awal', 'tanggal_akhir'];
                    $hasStandardFormat = count(array_intersect($standardRequiredHeaders, $cleanedHeaders)) >= 5;

                    if (!$hasDpeFormat && !$hasStandardFormat) {
                        throw new \Exception('Header tidak sesuai format. Expected: ' . implode(', ', $standardRequiredHeaders) .
                                          ' atau format DPE: ' . implode(', ', $dpeExpectedHeadersVariant2) .
                                          '. Headers yang ditemukan: ' . implode(', ', $cleanedHeaders));
                    }
                    continue;
                }

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Map row data to associative array using cleaned headers
                $data = [];
                foreach ($headers as $index => $header) {
                    $value = isset($row[$index]) ? trim($row[$index]) : '';
                    // Clean BOM from values as well
                    $value = str_replace("\xEF\xBB\xBF", "", $value);
                    $value = preg_replace('/^\x{FEFF}/u', '', $value);

                    // Headers are already cleaned during header processing
                    $data[$header] = $value;
                }

                // Clean and validate data
                $cleanedData = $this->cleanImportData($data, $rowNumber, $headers);

                // Check for duplicates
                $existing = $this->findExistingRecord($cleanedData);

                if ($existing) {
                    if ($options['skip_duplicates'] && !$options['update_existing']) {
                        $results['skipped_count']++;
                        $results['warnings'][] = "Baris {$rowNumber}: Data sudah ada (Kontainer: {$cleanedData['nomor_kontainer']}, Periode: {$cleanedData['periode']}) - diskip";
                        continue;
                    } elseif ($options['update_existing']) {
                        if (!$options['validate_only']) {
                            $existing->update($cleanedData);
                        }
                        $results['updated_count']++;
                        continue;
                    }
                }

                // If validation only, don't save
                if (!$options['validate_only']) {
                    DaftarTagihanKontainerSewa::create($cleanedData);
                }

                $results['imported_count']++;

            } catch (\Exception $e) {
                $results['errors'][] = [
                    'row' => $rowNumber,
                    'message' => $e->getMessage(),
                    'data' => $row
                ];
                $results['success'] = false;
            }
        }

        fclose($handle);

        $results['total_processed'] = $results['imported_count'] + $results['updated_count'] + $results['skipped_count'];

        return $results;
    }

    /**
     * Clean and validate import data
     */
    private function cleanImportData($data, $rowNumber, $headers = [])
    {
        // Detect if this is DPE format CSV - check for cleaned headers
        $cleanedHeaders = array_map(function($header) {
            return preg_replace('/^[\x{FEFF}\x{EF}\x{BB}\x{BF}]+/u', '', $header);
        }, $headers);

        // Check for DPE format (has 'Group' and 'Kontainer'/'Nomor Kontainer' columns)
        $isDpeFormat = in_array('Group', $cleanedHeaders) &&
                      (in_array('Kontainer', $cleanedHeaders) || in_array('Nomor Kontainer', $cleanedHeaders));

        if ($isDpeFormat) {
            // Handle DPE format (Group, Kontainer, Awal, Akhir, Ukuran, Harga)
            $cleaned = $this->cleanDpeFormatData($data, $rowNumber);
        } else {
            // Handle standard format (vendor, nomor_kontainer, size, tanggal_awal, tanggal_akhir)
            $vendor = $this->cleanVendor($data['vendor'] ?? '');
            $size = $this->cleanSize($data['size'] ?? '');

            // Parse tanggal
            $tanggalAwal = $this->parseDate($data['tanggal_awal'] ?? '');
            $tanggalAkhir = $this->parseDate($data['tanggal_akhir'] ?? '');

            // Ambil periode dari CSV sebagai nomor urut periode
            $periodeFromCsv = isset($data['periode']) ? (int)trim($data['periode']) : 1;

            // FIXED: Hitung jumlah hari dari tanggal untuk perhitungan DPP
            $jumlahHariUntukDpp = 0;
            if ($tanggalAwal && $tanggalAkhir) {
                $startDate = \Carbon\Carbon::parse($tanggalAwal);
                $endDate = \Carbon\Carbon::parse($tanggalAkhir);
                $jumlahHariUntukDpp = $startDate->diffInDays($endDate) + 1;
            }

            // Gunakan periode dari CSV untuk field 'periode' (nomor urut)
            // Tapi gunakan jumlah hari untuk perhitungan DPP
            $jumlahHari = $periodeFromCsv;

            // Get tarif type from CSV (Bulanan/Harian)
            $tarifText = trim($data['tarif'] ?? '');
            $tarifType = strtolower($tarifText);

            // Determine tarif_nominal (tarif per hari numerik) - CHECK MASTER PRICELIST FIRST
            $tarifNominal = 0;

            // FIXED: Parse tarif type from CSV first to determine which pricelist to use
            $csvTarifType = null;
            if (in_array(strtolower($tarifText), ['bulanan', 'monthly'])) {
                $csvTarifType = 'bulanan';
            } else if (in_array(strtolower($tarifText), ['harian', 'daily'])) {
                $csvTarifType = 'harian';
            }

            // First priority: Check master pricelist - prioritize matching tarif type from CSV
            $masterPricelist = null;

            if ($csvTarifType) {
                // Try to find pricelist that matches CSV tarif type
                $masterPricelist = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)
                    ->where('vendor', $vendor)
                    ->where('tarif', $csvTarifType)
                    ->first();
            }

            // If no matching tarif type found, get any pricelist for this vendor/size
            if (!$masterPricelist) {
                $masterPricelist = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)
                    ->where('vendor', $vendor)
                    ->first();
            }

            if ($masterPricelist) {
                // Use tarif type from CSV if specified, otherwise from master pricelist
                $tarifType = $csvTarifType ?: strtolower($masterPricelist->tarif);

                if ($tarifType === 'bulanan') {
                    // For monthly rate, store the monthly amount and mark it
                    $tarifNominal = $masterPricelist->harga; // Will be used as DPP directly, not multiplied by days
                    $isBulanan = true;
                } else {
                    // For daily rate, use as daily tariff
                    $tarifNominal = $masterPricelist->harga; // Will be multiplied by days later
                    $isBulanan = false;
                }
            } else {
                // Fallback: Use default tarif if no master pricelist found (daily rates)
                if ($vendor === 'DPE') {
                    $tarifNominal = ($size == '20') ? 25000 : 35000;
                } else if ($vendor === 'ZONA') {
                    $tarifNominal = ($size == '20') ? 20000 : 30000;
                }
                $isBulanan = false; // Fallback is always daily rate
                $tarifType = 'harian'; // Set explicit tarif type for fallback
            }

            // If tarif in CSV is a number, use that as tarif_nominal
            if (is_numeric($tarifText)) {
                $tarifNominal = $this->cleanNumber($tarifText);
                $tarifText = 'Custom'; // Mark as custom tarif
            } else if (!in_array($tarifType ?? '', ['bulanan', 'harian']) && !in_array(strtolower($tarifText), ['bulanan', 'harian', 'monthly', 'daily', ''])) {
                // Try to parse as number if not empty and not bulanan/harian
                $numericValue = $this->cleanNumber($tarifText);
                if ($numericValue > 0) {
                    $tarifNominal = $numericValue;
                    $tarifText = 'Custom';
                }
            }

            // Normalize tarif text using the determined tarif type
            if (($tarifType ?? '') === 'bulanan' || in_array(strtolower($tarifText), ['bulanan', 'monthly'])) {
                $tarifText = 'Bulanan';
            } else if (($tarifType ?? '') === 'harian' || in_array(strtolower($tarifText), ['harian', 'daily'])) {
                $tarifText = 'Harian';
            }

            $cleaned = [
                'vendor' => $vendor,
                'nomor_kontainer' => strtoupper(trim($data['nomor_kontainer'] ?? '')),
                'size' => $size,
                'tanggal_awal' => $tanggalAwal,
                'tanggal_akhir' => $tanggalAkhir,
                'tarif' => $tarifText, // Store text: "Bulanan" or "Harian"
                'periode' => $jumlahHari, // Nomor urut periode dari CSV
                'group' => trim($data['group'] ?? ''),
                'status' => $this->cleanStatus($data['status'] ?? 'ongoing'),
                'status_pranota' => null,
                'pranota_id' => null,
                // Store tarifNominal and jumlah hari aktual untuk perhitungan DPP
                '_tarif_for_calculation' => $tarifNominal,
                '_jumlah_hari_for_dpp' => $jumlahHariUntukDpp, // Jumlah hari aktual untuk DPP
                '_is_bulanan' => $isBulanan ?? false, // Mark if this is monthly rate
            ];
        }

        // Common validation and processing
        // Validate vendor field (should be set by cleanDpeFormatData for DPE format)
        if (empty($cleaned['vendor'])) {
            throw new \Exception('Vendor wajib diisi');
        }

        if (empty($cleaned['nomor_kontainer'])) {
            throw new \Exception('Nomor kontainer wajib diisi');
        }

        if (empty($cleaned['tanggal_awal']) || empty($cleaned['tanggal_akhir'])) {
            throw new \Exception('Tanggal awal dan tanggal akhir wajib diisi');
        }

        // Validate tanggal
        $startDate = Carbon::parse($cleaned['tanggal_awal']);
        $endDate = Carbon::parse($cleaned['tanggal_akhir']);

        if ($endDate->lt($startDate)) {
            throw new \Exception('Tanggal akhir tidak boleh lebih awal dari tanggal awal');
        }

        // Jika periode belum diset (dari cleanImportData), hitung dari tanggal sebagai fallback
        if (!isset($cleaned['periode']) || $cleaned['periode'] == 0) {
            $cleaned['periode'] = $startDate->diffInDays($endDate) + 1;
        }

        // Format masa: Tampilkan range tanggal (tanggal awal - tanggal akhir)
        $cleaned['masa'] = $startDate->format('j M Y') . ' - ' . $endDate->format('j M Y');

        // Remove empty group
        if (empty($cleaned['group']) || $cleaned['group'] === '-') {
            $cleaned['group'] = null;
        }

        // Calculate financial data (skip if DPE format with existing financial data)
        if (!$isDpeFormat || (!isset($cleaned['dpp']) || $cleaned['dpp'] == 0)) {
            $financialData = $this->calculateFinancialData($cleaned);
            $cleaned = array_merge($cleaned, $financialData);
        } else {
            // For DPE format with existing financial data, ensure dpp_nilai_lain is set
            if (!isset($cleaned['dpp_nilai_lain']) || $cleaned['dpp_nilai_lain'] == 0) {
                $cleaned['dpp_nilai_lain'] = round(($cleaned['dpp'] ?? 0) * 11/12, 2);
            }
        }

        // Remove temporary calculation keys before validation and saving
        if (isset($cleaned['_tarif_for_calculation'])) {
            unset($cleaned['_tarif_for_calculation']);
        }
        if (isset($cleaned['_is_bulanan'])) {
            unset($cleaned['_is_bulanan']);
        }

        // Validate business rules
        $this->validateBusinessRules($cleaned, $rowNumber, $isDpeFormat);

        return $cleaned;
    }

    /**
     * Clean DPE format data
     */
    private function cleanDpeFormatData($data, $rowNumber)
    {
        // Helper function to get value with possible BOM in key
        $getValue = function($key) use ($data) {
            // First try exact match
            if (isset($data[$key])) {
                return $data[$key];
            }

            // Then try with possible BOM variations
            $bomVariations = [
                "\xEF\xBB\xBF" . $key,    // UTF-8 BOM
                "\u{FEFF}" . $key,        // Unicode BOM (if supported)
            ];

            foreach ($bomVariations as $bomKey) {
                if (isset($data[$bomKey])) {
                    return $data[$bomKey];
                }
            }

            // Finally search through all keys for one that ends with our target
            foreach (array_keys($data) as $dataKey) {
                // Remove any BOM characters and check if it matches
                $cleanKey = preg_replace('/^[\x{FEFF}\x{EF}\x{BB}\x{BF}]+/u', '', $dataKey);
                if ($cleanKey === $key) {
                    return $data[$dataKey];
                }

                // Also check if original key ends with target (loose matching)
                if (strlen($dataKey) >= strlen($key) && substr($dataKey, -strlen($key)) === $key) {
                    return $data[$dataKey];
                }
            }

            return '';
        };

        // Parse data dari format DPE CSV
        // Determine vendor - bisa dari kolom 'vendor' atau 'Vendor', jika kosong default DPE
        $vendor = $getValue('Vendor') ?: ($getValue('vendor') ?: 'DPE');
        // Clean empty vendor (jika hanya whitespace atau kosong)
        $vendor = trim($vendor);
        if (empty($vendor)) {
            $vendor = 'DPE'; // Default ke DPE jika kosong
        }

        // Get size - support "Ukuran", "Size", "size"
        $size = $this->cleanSize($getValue('Size') ?: ($getValue('Ukuran') ?: $getValue('size')));

        // Get tarif text from 'Tarif' column (Bulanan/Harian)
        $tarifText = $getValue('Tarif') ?: $getValue('tarif');
        $tarifText = trim($tarifText);

        // Normalize tarif text to Bulanan/Harian
        $tarifLower = strtolower($tarifText);
        if (in_array($tarifLower, ['bulanan', 'monthly', 'bulan'])) {
            $tarifText = 'Bulanan';
        } else if (in_array($tarifLower, ['harian', 'daily', 'hari'])) {
            $tarifText = 'Harian';
        } else if (empty($tarifText)) {
            // Default ke Bulanan jika kosong
            $tarifText = 'Bulanan';
        }
        // If it's already "Bulanan" or "Harian", keep as is

        // Get periode from CSV (Periode column)
        $periodeValue = $getValue('Periode') ?: $getValue('periode');
        $periode = !empty($periodeValue) ? (int)trim($periodeValue) : 0;

        $cleaned = [
            'vendor' => strtoupper(trim($vendor)),
            'nomor_kontainer' => strtoupper(trim($getValue('Nomor Kontainer') ?: ($getValue('Kontainer') ?: $getValue('nomor_kontainer')))),
            'size' => $size,
            'tanggal_awal' => $this->parseDpeDate($getValue('Tanggal Awal') ?: ($getValue('Awal') ?: $getValue('tanggal_awal'))),
            'tanggal_akhir' => $this->parseDpeDate($getValue('Tanggal Akhir') ?: ($getValue('Akhir') ?: $getValue('tanggal_akhir'))),
            'tarif' => $tarifText, // Text: Bulanan atau Harian
            'periode' => $periode, // Periode dari CSV
            'group' => trim($getValue('Group') ?: $getValue('group')),
            'status' => $this->cleanDpeStatus($getValue('Status') ?: $getValue('status') ?: 'ongoing'),
            'status_pranota' => null,
            'pranota_id' => null,
            'keterangan' => trim($getValue('Keterangan')),
        ];

        // Ambil nilai finansial dari CSV jika tersedia
        $dppValue = $getValue('DPP');
        if (!empty($dppValue)) {
            $cleaned['dpp'] = $this->cleanDpeNumber($dppValue);
        } else {
            $cleaned['dpp'] = 0;
        }

        $adjustmentValue = trim($getValue('Adjustment') ?: $getValue('adjustment'));
        if (!empty($adjustmentValue)) {
            $cleaned['adjustment'] = $this->cleanDpeNumber($adjustmentValue);
        } else {
            $cleaned['adjustment'] = 0;
        }

        // Hitung DPP yang sudah disesuaikan dengan adjustment
        $adjustedDpp = $cleaned['dpp'] + $cleaned['adjustment'];

        // Ambil PPN dari CSV, jika tidak ada hitung dari DPP (11%)
        $ppnValue = $getValue('PPN') ?: $getValue('ppn');
        if (!empty($ppnValue)) {
            $cleaned['ppn'] = $this->cleanDpeNumber($ppnValue);
        } else {
            // Hitung PPN 11% dari adjusted DPP
            $cleaned['ppn'] = round($adjustedDpp * 0.11, 2);
        }

        // Ambil PPH dari CSV, jika tidak ada hitung dari DPP (2%)
        $pphValue = $getValue('PPH') ?: $getValue('pph');
        if (!empty($pphValue)) {
            $cleaned['pph'] = $this->cleanDpeNumber($pphValue);
        } else {
            // Hitung PPH 2% dari adjusted DPP
            $cleaned['pph'] = round($adjustedDpp * 0.02, 2);
        }

        // Ambil Grand Total dari CSV, jika tidak ada hitung dari komponen
        $grandTotalValue = $getValue('Grand Total') ?: $getValue('grand_total');
        if (!empty($grandTotalValue)) {
            $cleaned['grand_total'] = $this->cleanDpeNumber($grandTotalValue);
        } else {
            // Hitung Grand Total = DPP + PPN - PPH
            $cleaned['grand_total'] = round($adjustedDpp + $cleaned['ppn'] - $cleaned['pph'], 2);
        }

        // Ambil DPP Nilai Lain dari CSV atau hitung (11/12 dari adjusted DPP)
        $dppNilaiLainValue = $getValue('DPP Nilai Lain') ?: $getValue('dpp_nilai_lain');
        if (!empty($dppNilaiLainValue)) {
            $cleaned['dpp_nilai_lain'] = $this->cleanDpeNumber($dppNilaiLainValue);
        } else {
            // Hitung DPP Nilai Lain (11/12 dari adjusted DPP)
            $cleaned['dpp_nilai_lain'] = round($adjustedDpp * 11 / 12, 2);
        }

        // Data tambahan dari DPE
        $noInvoiceVendor = $getValue('No.InvoiceVendor');
        if (!empty($noInvoiceVendor)) {
            $cleaned['no_invoice_vendor'] = trim($noInvoiceVendor);
        }

        $tglInvVendor = $getValue('Tgl.InvVendor');
        if (!empty($tglInvVendor)) {
            $cleaned['tgl_invoice_vendor'] = $this->parseDpeDate($tglInvVendor);
        }

        $noBank = $getValue('No.Bank');
        if (!empty($noBank)) {
            $cleaned['no_bank'] = trim($noBank);
        }

        $tglBank = $getValue('Tgl.Bank');
        if (!empty($tglBank)) {
            $cleaned['tgl_bank'] = $this->parseDpeDate($tglBank);
        }

        return $cleaned;
    }

    /**
     * Helper methods for DPE data cleaning
     */
    private function parseDpeDate($date)
    {
        if (empty($date) || trim($date) === '') {
            return null;
        }

        try {
            // Handle DPE date formats: "21-01-2025", "21/01/2025", "30 Jan 25", etc.
            $date = trim($date);

            // Format: "21-01-2025" or "21/01/2025" (DD-MM-YYYY or DD/MM/YYYY)
            if (preg_match('/(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})/', $date, $matches)) {
                $day = $matches[1];
                $month = $matches[2];
                $year = $matches[3];
                return Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
            }

            // Format: "30 Jan 25"
            if (preg_match('/(\d{1,2})\s+(\w{3})\s+(\d{2})/', $date, $matches)) {
                $year = '20' . $matches[3];
                $monthMap = [
                    'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04',
                    'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08',
                    'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12'
                ];
                $month = $monthMap[$matches[2]] ?? '01';
                return Carbon::createFromDate($year, $month, $matches[1])->format('Y-m-d');
            }

            // Try Carbon parse as last resort
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Exception("Format tanggal tidak valid: {$date}");
        }
    }

    private function cleanDpeNumber($value)
    {
        if (empty($value) || trim($value) === '' || trim($value) === '-') {
            return 0;
        }

        // Handle negative values with comma format
        $value = trim($value);
        $isNegative = false;

        if (strpos($value, '-') !== false) {
            $isNegative = true;
            $value = str_replace('-', '', $value);
        }

        // Remove currency symbols, spaces, and formatting
        $cleaned = preg_replace('/[^\d.,]/', '', $value);
        $cleaned = str_replace(',', '', $cleaned); // Remove thousands separator

        $result = (float) $cleaned;
        return $isNegative ? -$result : $result;
    }

    private function cleanDpeStatus($status)
    {
        $status = strtolower(trim($status));

        if (in_array($status, ['bulanan', 'monthly'])) {
            return 'ongoing'; // Map bulanan ke ongoing
        }

        if (in_array($status, ['harian', 'daily'])) {
            return 'ongoing'; // Map harian ke ongoing
        }

        if (in_array($status, ['tersedia', 'available', 'ongoing', 'active'])) {
            return 'ongoing';
        }

        if (in_array($status, ['selesai', 'completed', 'done', 'finished'])) {
            return 'selesai';
        }

        return 'ongoing';
    }

    /**
     * Helper methods for data cleaning
     */
    private function cleanVendor($vendor)
    {
        $vendor = strtoupper(trim($vendor));

        if (in_array($vendor, ['ZONA', 'PT ZONA', 'PT. ZONA'])) {
            return 'ZONA';
        }

        if (in_array($vendor, ['DPE', 'PT DPE', 'PT. DPE'])) {
            return 'DPE';
        }

        return $vendor;
    }

    private function cleanSize($size)
    {
        $size = trim($size);

        if (in_array($size, ['20', '20ft', '20 ft', '20\''])) {
            return '20';
        }

        if (in_array($size, ['40', '40ft', '40 ft', '40\''])) {
            return '40';
        }

        return (string) $size;
    }

    private function cleanStatus($status)
    {
        $status = strtolower(trim($status));

        if (in_array($status, ['ongoing', 'active', 'aktif', 'berjalan', 'tersedia', 'available'])) {
            return 'ongoing';
        }

        if (in_array($status, ['selesai', 'completed', 'done', 'finished'])) {
            return 'selesai';
        }

        return 'ongoing';
    }

    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Handle various date formats
            $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'Y/m/d'];

            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $date)->format('Y-m-d');
                } catch (\Exception $e) {
                    continue;
                }
            }

            // Try Carbon parse as last resort
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Exception("Format tanggal tidak valid: {$date}");
        }
    }

    private function cleanNumber($value)
    {
        if (empty($value)) {
            return 0;
        }

        // Remove currency symbols and formatting
        $cleaned = preg_replace('/[^\d.,\-]/', '', $value);
        $cleaned = str_replace(',', '.', $cleaned);

        return (float) $cleaned;
    }

    private function findExistingRecord($data)
    {
        return DaftarTagihanKontainerSewa::where('nomor_kontainer', $data['nomor_kontainer'])
            ->where('periode', $data['periode'])
            ->first();
    }

    private function calculateFinancialData($data)
    {
        // Use temporary calculation value if available, otherwise get from master pricelist
        $tarifNominal = $data['_tarif_for_calculation'] ?? 0;
        $isBulanan = $data['_is_bulanan'] ?? false;

        // FIXED: Gunakan jumlah hari aktual untuk perhitungan DPP, bukan nomor periode
        $jumlahHariUntukDpp = $data['_jumlah_hari_for_dpp'] ?? $data['periode'];

        // Get master pricelist if available and no tarif provided
        if ($tarifNominal == 0) {
            $masterPricelist = MasterPricelistSewaKontainer::where('ukuran_kontainer', $data['size'])
                ->where('vendor', $data['vendor'])
                ->first();

            if ($masterPricelist) {
                $tarifNominal = $masterPricelist->harga; // Fixed: use harga column, not tarif
                $isBulanan = (strtolower($masterPricelist->tarif) === 'bulanan');
            }
        }

        // If still no tarif, use default based on vendor and size (daily rates)
        if ($tarifNominal == 0) {
            if ($data['vendor'] === 'DPE') {
                $tarifNominal = ($data['size'] == '20') ? 25000 : 35000;
            } else if ($data['vendor'] === 'ZONA') {
                $tarifNominal = ($data['size'] == '20') ? 20000 : 30000;
            }
            $isBulanan = false; // Default rates are daily
        }

        // Calculate DPP based on rate type (monthly vs daily)
        if ($isBulanan) {
            // For monthly rate: DPP = monthly rate (not multiplied by days)
            $dpp = $tarifNominal;
        } else {
            // For daily rate: DPP = daily rate × actual days
            $dpp = $tarifNominal * $jumlahHariUntukDpp;
        }

        // Calculate PPN (11% of DPP)
        $ppn = $dpp * 0.11;

        // Calculate PPH (2% of DPP)
        $pph = $dpp * 0.02;

        // Grand Total = DPP + PPN - PPH
        $grand_total = $dpp + $ppn - $pph;

        // Preserve existing adjustment if already set, otherwise set to 0
        $adjustment = isset($data['adjustment']) ? $data['adjustment'] : 0;

        return [
            'dpp' => $dpp,
            'adjustment' => $adjustment,
            'dpp_nilai_lain' => 0,
            'ppn' => $ppn,
            'pph' => $pph,
            'grand_total' => $grand_total,
        ];
    }

    private function validateBusinessRules($data, $rowNumber, $isDpeFormat = false)
    {
        // Check vendor
        if (!in_array($data['vendor'], ['ZONA', 'DPE'])) {
            throw new \Exception("Vendor tidak didukung: {$data['vendor']}. Harus ZONA atau DPE");
        }

        // Check size
        if (!in_array($data['size'], ['20', '40'])) {
            throw new \Exception("Ukuran kontainer tidak valid: {$data['size']}. Harus 20 atau 40");
        }

        // Check periode (jumlah hari)
        if (isset($data['periode']) && $data['periode'] > 365) {
            throw new \Exception("Periode terlalu lama ({$data['periode']} hari). Maksimal 365 hari");
        }

        // Check container number
        if (strlen($data['nomor_kontainer']) < 4) {
            throw new \Exception("Nomor kontainer terlalu pendek: {$data['nomor_kontainer']}");
        }
    }
}
