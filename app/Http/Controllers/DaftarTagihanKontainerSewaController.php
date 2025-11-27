<?php

namespace App\Http\Controllers;

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\PranotaTagihanKontainerSewa;
use App\Models\NomorTerakhir;
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

        // Handle search functionality - show matching containers and all containers in the same group
        if ($request->filled('q')) {
            $searchTerm = trim($request->input('q'));

            // create a sanitized search to match container numbers regardless of hyphens or spaces
            $sanitizedSearch = preg_replace('/[^A-Za-z0-9]/', '', $searchTerm);

            // First, find all matching containers
                        $matchingContainers = DaftarTagihanKontainerSewa::where(function ($q) use ($searchTerm, $sanitizedSearch) {
                                $q->where('vendor', 'LIKE', '%' . $searchTerm . '%')
                                    ->orWhere('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')
                                    ->orWhere('group', 'LIKE', '%' . $searchTerm . '%')
                                    ->orWhere('invoice_vendor', 'LIKE', '%' . $searchTerm . '%');

                                // also allow numeric/alphanumeric searches that ignore hyphens/spaces
                                if (!empty($sanitizedSearch)) {
                                        $q->orWhereRaw("REPLACE(REPLACE(nomor_kontainer, '-', ''),' ', '') LIKE ?", ['%' . $sanitizedSearch . '%']);
                                }
                        })->get();

            // Collect all group names from matching containers
            $groupNames = $matchingContainers->where('group', '!=', null)
                                             ->where('group', '!=', '')
                                             ->pluck('group')
                                             ->unique()
                                             ->values();

            // Now apply the filter: show direct matches OR containers in the same groups
            $query->where(function ($q) use ($searchTerm, $groupNames, $sanitizedSearch) {
                // Show containers that directly match the search term
                $q->where(function ($subQ) use ($searchTerm) {
                    $subQ->where('vendor', 'LIKE', '%' . $searchTerm . '%')
                         ->orWhere('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')
                         ->orWhere('group', 'LIKE', '%' . $searchTerm . '%')
                         ->orWhere('invoice_vendor', 'LIKE', '%' . $searchTerm . '%');
                });

                // OR show all containers that belong to the same groups as matching containers
                if ($groupNames->isNotEmpty()) {
                    $q->orWhereIn('group', $groupNames);
                }
            });
            
            // If we have exact container matches, we prioritize them in ordering so they appear at the top
            $exactMatches = DaftarTagihanKontainerSewa::where('nomor_kontainer', '=', $searchTerm)
                ->orWhereRaw("REPLACE(REPLACE(nomor_kontainer, '-', ''),' ', '') = ?", [$sanitizedSearch])->pluck('id')->toArray();
            if (!empty($exactMatches)) {
                $query->orderByRaw(sprintf("FIELD(%s, %s)", 'id', implode(',', array_map('intval', $exactMatches))));
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
            } elseif ($statusPranota === 'belum_pranota') {
                // Filter untuk tagihan yang belum masuk pranota
                $query->whereNull('status_pranota');
            } elseif ($statusPranota === 'sudah_pranota') {
                // Filter untuk tagihan yang sudah masuk pranota
                $query->whereNotNull('status_pranota');
            } else {
                // Filter untuk status pranota spesifik
                $query->where('status_pranota', $statusPranota);
            }
        }

        // Handle status invoice filter
        if ($request->filled('status_invoice')) {
            $statusInvoice = $request->input('status_invoice');
            if ($statusInvoice === 'null') {
                // Filter untuk tagihan yang belum ada invoice
                $query->whereNull('invoice_id');
            } else {
                // Filter untuk status invoice spesifik - join dengan tabel invoices_kontainer_sewa
                $query->whereHas('invoice', function($q) use ($statusInvoice) {
                    $q->where('status', $statusInvoice);
                });
            }
        }

        // Handle nomor kontainer filter (for modal search)
        if ($request->filled('nomor_kontainer')) {
            $query->where('nomor_kontainer', 'LIKE', '%' . $request->input('nomor_kontainer') . '%');
        }

        // Handle available for pranota filter (exclude already in specific pranota)
        if ($request->filled('available_for_pranota') && $request->filled('exclude_pranota_id')) {
            $excludePranotaId = $request->input('exclude_pranota_id');
            
            // Get tagihan IDs that are already in the specified pranota
            $existingPranota = \App\Models\PranotaTagihanKontainerSewa::find($excludePranotaId);
            if ($existingPranota && !empty($existingPranota->tagihan_kontainer_sewa_ids)) {
                $excludeIds = $existingPranota->tagihan_kontainer_sewa_ids;
                $query->whereNotIn('id', $excludeIds);
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

        // Handle AJAX requests for modal selection (like from tambah kontainer modal)
        if ($request->ajax() || $request->wantsJson()) {
            $tagihanList = $tagihans->items(); // Get items from paginated result
            
            return response()->json([
                'success' => true,
                'tagihan' => $tagihanList,
                'pagination' => [
                    'current_page' => $tagihans->currentPage(),
                    'last_page' => $tagihans->lastPage(),
                    'per_page' => $tagihans->perPage(),
                    'total' => $tagihans->total()
                ]
            ]);
        }

        // Ensure exactMatches variable exists for highlighting search results in view
        if (!isset($exactMatches) || !is_array($exactMatches)) {
            $exactMatches = [];
        }

        return view('daftar-tagihan-kontainer-sewa.index', compact('tagihans', 'vendors', 'sizes', 'periodes', 'statusOptions', 'exactMatches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all containers with vendor information
        $containersData = \App\Models\Kontainer::select('nomor_seri_gabungan', 'vendor', 'ukuran')
            ->whereNotNull('nomor_seri_gabungan')
            ->whereNotNull('vendor')
            ->orderBy('nomor_seri_gabungan')
            ->get();

        // Get distinct vendors from kontainers table
        $vendors = \App\Models\Kontainer::select('vendor')
            ->distinct()
            ->whereNotNull('vendor')
            ->orderBy('vendor')
            ->pluck('vendor');

        return view('daftar-tagihan-kontainer-sewa.create', compact('containersData', 'vendors'));
    }

    /**
     * API: Get computed DPP and taxes based on selected vendor/size/tarif/date
     */
    public function getPricelistForDpp(Request $request)
    {
        $vendor = $request->input('vendor');
        $size = $request->input('size') ?: $request->input('ukuran_kontainer');
        $tarif = $request->input('tarif');
        $tanggal_awal = $request->input('tanggal_awal');
        $tanggal_akhir = $request->input('tanggal_akhir');
        $periode = $request->input('periode');

        // Convert dates
        try {
            $baseStart = $tanggal_awal ? Carbon::parse($tanggal_awal)->startOfDay() : Carbon::now()->startOfDay();
        } catch (\Exception $e) {
            $baseStart = Carbon::now()->startOfDay();
        }

        // If periode is provided, compute the start date for that periode
        if ($periode && is_numeric($periode) && $periode > 0) {
            $p = intval($periode);
            $periodStart = $baseStart->copy()->addMonthsNoOverflow($p-1);
        } else {
            $periodStart = $baseStart;
        }

        // Determine days in the period
        if ($periode && is_numeric($periode) && $periode > 0) {
            // Compute days for the given periode (from scripts logic)
            $periodStartLocal = $periodStart->copy();
            $periodEndLocal = $periodStartLocal->copy()->addMonthsNoOverflow(1)->subDay();
            if ($tanggal_akhir) {
                try {
                    $endCap = Carbon::parse($tanggal_akhir)->startOfDay();
                    if ($periodEndLocal->gt($endCap)) $periodEndLocal = $endCap;
                } catch (\Exception $e) {}
            }
            if ($periodEndLocal->lt($periodStartLocal)) $periodEndLocal = $periodStartLocal->copy();
            $daysInPeriod = $periodStartLocal->diffInDays($periodEndLocal) + 1;
        } else if ($tanggal_akhir) {
            try {
                $end = Carbon::parse($tanggal_akhir)->startOfDay();
                $daysInPeriod = $baseStart->diffInDays($end) + 1;
            } catch (\Exception $e) {
                $daysInPeriod = 1;
            }
        } else {
            $daysInPeriod = 1;
        }

        $fullMonthLen = $periodStart->copy()->endOfMonth()->day;

        // Query pricelist with the same logic as the import scripts
        $pr = null;
        if ($size) {
            $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)
                ->where('vendor', $vendor)
                ->where(function($q) use ($periodStart){
                    $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
                      ->where(function($q2) use ($periodStart){ $q2->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir','>=',$periodStart->toDateString()); });
                })->orderBy('tanggal_harga_awal','desc')->first();
        }
        if (!$pr && $size) {
            $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)
                ->where(function($q) use ($periodStart){
                    $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
                      ->where(function($q2) use ($periodStart){ $q2->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir','>=',$periodStart->toDateString()); });
                })->orderBy('tanggal_harga_awal','desc')->first();
        }
        if (!$pr && $size && $vendor) {
            $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)->where('vendor',$vendor)->orderBy('tanggal_harga_awal','desc')->first();
        }
        if (!$pr && $size) {
            $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)->orderBy('tanggal_harga_awal','desc')->first();
        }

        $dppComputed = 0.0;
        $tarifLabel = '';
        if ($pr) {
            $harga = (float)$pr->harga;
            $prTarif = strtolower((string)$pr->tarif);
            if (strpos($prTarif,'harian')!==false) {
                $dppComputed = round($harga * $daysInPeriod,2);
                $tarifLabel = 'Harian';
            } else {
                if ($daysInPeriod >= $fullMonthLen) {
                    $dppComputed = round($harga,2);
                    $tarifLabel = 'Bulanan';
                } else {
                    $dppComputed = round($harga * ($daysInPeriod/$fullMonthLen),2);
                    $tarifLabel = 'Harian';
                }
            }
        }

        $dpp_nilai_lain = round($dppComputed * 11/12,2);
        $ppn = round($dpp_nilai_lain * 0.12,2);
        $pph = round($dppComputed * 0.02,2);
        $grand_total = round($dppComputed + $ppn - $pph,2);

        return response()->json([
            'success' => true,
            'dpp' => $dppComputed,
            'dpp_nilai_lain' => $dpp_nilai_lain,
            'ppn' => $ppn,
            'pph' => $pph,
            'grand_total' => $grand_total,
            'tarif' => $tarifLabel,
            'pricelist' => $pr ? $pr->toArray() : null
        ]);
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
            'adjustment_note' => 'nullable|string|max:500',
        ]);

        try {
            $tagihan = DaftarTagihanKontainerSewa::findOrFail($id);

            // Store old values for logging
            $oldDpp = $tagihan->dpp ?? 0;
            $oldAdjustment = $tagihan->adjustment ?? 0;
            $oldGrandTotal = $tagihan->grand_total ?? 0;
            $newAdjustment = $request->input('adjustment');
            $newAdjustmentNote = $request->input('adjustment_note');

            // Update DPP by adding the NEW adjustment (not cumulative)
            // DPP = original DPP (minus old adjustment) + new adjustment
            $originalDppWithoutAdjustment = $oldDpp - $oldAdjustment;
            $tagihan->dpp = $originalDppWithoutAdjustment + $newAdjustment;
            
            // Keep the adjustment value for reference/history
            $tagihan->adjustment = $newAdjustment;
            $tagihan->adjustment_note = $newAdjustmentNote;

            // Recalculate taxes and totals using model method
            $tagihan->recalculateTaxes();

            // Save will trigger the boot method to calculate grand_total
            $tagihan->save();

            // Log the change for audit purposes
            Log::info("Adjustment updated for tagihan ID {$id}", [
                'container' => $tagihan->nomor_kontainer,
                'original_dpp' => $originalDppWithoutAdjustment,
                'old_adjustment' => $oldAdjustment,
                'new_adjustment' => $newAdjustment,
                'new_dpp' => $tagihan->dpp,
                'adjustment_note' => $newAdjustmentNote,
                'old_grand_total' => $oldGrandTotal,
                'new_ppn' => $tagihan->ppn,
                'new_pph' => $tagihan->pph,
                'new_grand_total' => $tagihan->grand_total,
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Adjustment berhasil diperbarui dan DPP telah dihitung ulang',
                    'data' => [
                        'id' => $tagihan->id,
                        'original_dpp' => $originalDppWithoutAdjustment,
                        'adjustment' => $tagihan->adjustment,
                        'new_dpp' => $tagihan->dpp,
                        'dpp_nilai_lain' => $tagihan->dpp_nilai_lain,
                        'ppn' => $tagihan->ppn,
                        'pph' => $tagihan->pph,
                        'grand_total' => $tagihan->grand_total,
                        'formatted_original_dpp' => 'Rp ' . number_format((float)$originalDppWithoutAdjustment, 0, '.', ','),
                        'formatted_adjustment' => ($newAdjustment >= 0 ? '+' : '') . 'Rp ' . number_format((float)$newAdjustment, 0, '.', ','),
                        'formatted_new_dpp' => 'Rp ' . number_format((float)$tagihan->dpp, 0, '.', ','),
                        'formatted_ppn' => 'Rp ' . number_format((float)$tagihan->ppn, 0, '.', ','),
                        'formatted_pph' => 'Rp ' . number_format((float)$tagihan->pph, 0, '.', ','),
                        'formatted_grand_total' => 'Rp ' . number_format((float)$tagihan->grand_total, 0, '.', ','),
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Adjustment berhasil diperbarui dan DPP telah dihitung ulang');

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
                PranotaTagihanKontainerSewa::whereYear('created_at', $tanggalPranota->year)
                    ->whereMonth('created_at', $tanggalPranota->month)
                    ->count() + 1,
                6, '0', STR_PAD_LEFT
            );

            $noInvoice = "PTK{$nomorCetakan}{$tahun}{$bulan}{$runningNumber}";

            // Create pranota
            $pranota = PranotaTagihanKontainerSewa::create([
                'no_invoice' => $noInvoice,
                'total_amount' => 0, // Will be calculated and updated below
                'keterangan' => $request->keterangan ?: 'Pranota untuk ' . count($request->ids) . ' tagihan kontainer sewa',
                'status' => 'unpaid',
                'tagihan_kontainer_sewa_ids' => $request->ids,
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
     * Generate invoice number with format: MS-MMYY-0000001
     */
    public function generateInvoiceNumber(Request $request)
    {
        try {
            return DB::transaction(function () {
                // Get or create nomor_terakhir record for MS module
                $nomorTerakhir = NomorTerakhir::where('modul', 'MS')->lockForUpdate()->first();
                
                if (!$nomorTerakhir) {
                    $nomorTerakhir = NomorTerakhir::create([
                        'modul' => 'MS',
                        'nomor_terakhir' => 1,
                        'keterangan' => 'Nomor Invoice Vendor Kontainer Sewa'
                    ]);
                    $runningNumber = 1;
                } else {
                    $runningNumber = $nomorTerakhir->nomor_terakhir + 1;
                    $nomorTerakhir->update(['nomor_terakhir' => $runningNumber]);
                }

                // Format: MS-MMYY-0000001
                $month = date('m'); // 2 digit month
                $year = date('y');  // 2 digit year
                $invoiceNumber = sprintf('MS-%s%s-%07d', $month, $year, $runningNumber);

                return response()->json([
                    'success' => true,
                    'invoice_number' => $invoiceNumber,
                    'running_number' => $runningNumber
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error generating invoice number: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor invoice',
                'error' => $e->getMessage()
            ], 500);
        }
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
     * Import CSV via modal (AJAX endpoint)
     */
    public function importCsvModal(Request $request)
    {
        // Validate file upload
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('import_file');

            // Get import options from request
            $options = [
                'validate_only' => false,
                'skip_duplicates' => $request->has('skip_duplicates'),
                'update_existing' => $request->has('update_existing'),
            ];

            // Process the CSV import
            $results = $this->processCsvImport($file, $options);

            // Log the import activity
            Log::info('CSV Import via modal completed', [
                'user_id' => Auth::id(),
                'filename' => $file->getClientOriginalName(),
                'imported' => $results['imported_count'],
                'updated' => $results['updated_count'],
                'skipped' => $results['skipped_count'],
                'errors' => count($results['errors']),
                'options' => $options,
            ]);

            // Return JSON response
            return response()->json([
                'success' => $results['success'],
                'message' => $results['success'] 
                    ? 'Import berhasil!' 
                    : 'Import gagal!',
                'imported_count' => $results['imported_count'],
                'updated_count' => $results['updated_count'],
                'skipped_count' => $results['skipped_count'],
                'errors' => $results['errors'],
                'warnings' => $results['warnings'],
            ]);

        } catch (\Exception $e) {
            Log::error('Import CSV via modal failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat import',
                'errors' => [
                    ['row' => 0, 'message' => $e->getMessage()]
                ],
                'imported_count' => 0,
                'updated_count' => 0,
                'skipped_count' => 0,
                'warnings' => [],
            ], 500);
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
                        'nomor_kontainer',
                        'vendor',
                        'size',
                        'tanggal_awal',
                        'tanggal_akhir',
                        'group',
                        'masa',
                        'tarif',
                        'dpp',
                        'adjustment',
                        'adjustment_note',
                        'nomor_bank',
                        'invoice_vendor',
                        'tanggal_vendor',
                        'dpp_nilai_lain',
                        'ppn',
                        'pph',
                        'grand_total',
                        'periode'
                    ], ';');

                    // Sample data for DPE format
                    fputcsv($file, [
                        'CCLU3836629',
                        'DPE',
                        '20',
                        '2025-01-21',
                        '2025-02-20',
                        '',
                        '1',
                        '35000',
                        '35000',
                        '0',
                        '',
                        'BCA123456',
                        'INV-DPE-001',
                        '2025-01-21',
                        '0',
                        '3850',
                        '350',
                        '38500',
                        '2025-01'
                    ], ';');

                    fputcsv($file, [
                        'CCLU3836629',
                        'DPE',
                        '20',
                        '2025-02-21',
                        '2025-03-20',
                        '',
                        '1',
                        '35000',
                        '35000',
                        '0',
                        '',
                        'BCA123456',
                        'INV-DPE-001',
                        '2025-02-21',
                        '0',
                        '3850',
                        '350',
                        '38500',
                        '2025-02'
                    ], ';');

                    fputcsv($file, [
                        'CBHU4077764',
                        'DPE',
                        '20',
                        '2025-01-21',
                        '2025-02-20',
                        '',
                        '1',
                        '35000',
                        '35000',
                        '0',
                        '',
                        'BCA123456',
                        'INV-DPE-002',
                        '2025-01-21',
                        '0',
                        '3850',
                        '350',
                        '38500',
                        '2025-01'
                    ], ';');

                    fputcsv($file, [
                        'RXTU4540180',
                        'DPE',
                        '40',
                        '2025-03-04',
                        '2025-04-03',
                        '',
                        '1',
                        '50000',
                        '50000',
                        '0',
                        '',
                        'BCA123456',
                        'INV-DPE-003',
                        '2025-03-04',
                        '0',
                        '5500',
                        '500',
                        '55000',
                        '2025-03'
                    ], ';');
                } else {
                    // Standard Format Template
                    fputcsv($file, [
                        'nomor_kontainer',
                        'vendor',
                        'size',
                        'tanggal_awal',
                        'tanggal_akhir',
                        'group',
                        'masa',
                        'tarif',
                        'dpp',
                        'adjustment',
                        'adjustment_note',
                        'nomor_bank',
                        'invoice_vendor',
                        'tanggal_vendor',
                        'dpp_nilai_lain',
                        'ppn',
                        'pph',
                        'grand_total',
                        'periode'
                    ], ';');

                    // Write sample data
                    fputcsv($file, [
                        'ZONA001234',
                        'ZONA',
                        '20',
                        '2024-01-01',
                        '2024-01-31',
                        'GROUP001',
                        '1',
                        '40000',
                        '40000',
                        '0',
                        '',
                        'BCA789012',
                        'INV-ZONA-001',
                        '2024-01-01',
                        '0',
                        '4400',
                        '400',
                        '44000',
                        '2024-01'
                    ], ';');

                    fputcsv($file, [
                        'DPE567890',
                        'DPE',
                        '40',
                        '2024-01-01',
                        '2024-01-31',
                        'GROUP002',
                        '1',
                        '50000',
                        '50000',
                        '0',
                        '',
                        'BCA789012',
                        'INV-DPE-004',
                        '2024-01-01',
                        '0',
                        '5500',
                        '500',
                        '55000',
                        '2024-01'
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
                        'nomor_kontainer' => 'nomor_kontainer',
                        'Size' => 'size',
                        'size' => 'size',
                        'Awal' => 'tanggal_awal',
                        'Tanggal Awal' => 'tanggal_awal', // Support "Tanggal Awal" variant
                        'tanggal_awal' => 'tanggal_awal',
                        'Akhir' => 'tanggal_akhir',
                        'Tanggal Akhir' => 'tanggal_akhir', // Support "Tanggal Akhir" variant
                        'tanggal_akhir' => 'tanggal_akhir',
                        'Ukuran' => 'size',
                        'Harga' => 'tarif',
                        'Tarif' => 'tarif',
                        'tarif' => 'tarif',
                        'Periode' => 'periode_input',
                        'periode' => 'periode_input',
                        'Masa' => 'masa',
                        'masa' => 'masa',
                        'Status' => 'status_type',
                        'Hari' => 'hari',
                        'DPP' => 'dpp',
                        'dpp' => 'dpp',
                        'Adjustment' => 'adjustment',
                        'adjustment' => 'adjustment',
                        'Adjustment Note' => 'adjustment_note',
                        'adjustment_note' => 'adjustment_note',
                        'Nomor Bank' => 'nomor_bank',
                        'nomor_bank' => 'nomor_bank',
                        'Invoice Vendor' => 'invoice_vendor',
                        'invoice_vendor' => 'invoice_vendor',
                        'Tanggal Vendor' => 'tanggal_vendor',
                        'tanggal_vendor' => 'tanggal_vendor',
                        'DPP Nilai Lain' => 'dpp_nilai_lain',
                        'dpp_nilai_lain' => 'dpp_nilai_lain',
                        'PPN' => 'ppn',
                        'ppn' => 'ppn',
                        'PPH' => 'pph',
                        'pph' => 'pph',
                        'Grand Total' => 'grand_total',
                        'grand_total' => 'grand_total',
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

            // Parse additional financial columns from CSV if provided
            $adjustment = isset($data['adjustment']) ? $this->cleanNumber($data['adjustment']) : 0;
            $adjustmentNote = isset($data['adjustment_note']) ? trim($data['adjustment_note']) : null;
            $nomorBank = isset($data['nomor_bank']) ? trim($data['nomor_bank']) : null;
            $invoiceVendor = isset($data['invoice_vendor']) ? trim($data['invoice_vendor']) : null;
            $tanggalVendor = isset($data['tanggal_vendor']) ? $this->parseDate($data['tanggal_vendor']) : null;
            $dppFromCsv = isset($data['dpp']) ? $this->cleanNumber($data['dpp']) : null;
            $dppNilaiLain = isset($data['dpp_nilai_lain']) ? $this->cleanNumber($data['dpp_nilai_lain']) : null;
            $ppn = isset($data['ppn']) ? $this->cleanNumber($data['ppn']) : null;
            $pph = isset($data['pph']) ? $this->cleanNumber($data['pph']) : null;
            $grandTotal = isset($data['grand_total']) ? $this->cleanNumber($data['grand_total']) : null;

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
                // Additional columns from CSV
                'adjustment' => $adjustment,
                'adjustment_note' => $adjustmentNote,
                'nomor_bank' => $nomorBank,
                'invoice_vendor' => $invoiceVendor,
                'tanggal_vendor' => $tanggalVendor,
                // Store tarifNominal and jumlah hari aktual untuk perhitungan DPP
                '_tarif_for_calculation' => $tarifNominal,
                '_jumlah_hari_for_dpp' => $jumlahHariUntukDpp, // Jumlah hari aktual untuk DPP
                '_is_bulanan' => $isBulanan ?? false, // Mark if this is monthly rate
                // Store financial data from CSV if provided (will override calculation)
                '_dpp_from_csv' => $dppFromCsv,
                '_dpp_nilai_lain_from_csv' => $dppNilaiLain,
                '_ppn_from_csv' => $ppn,
                '_pph_from_csv' => $pph,
                '_grand_total_from_csv' => $grandTotal,
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

        // Calculate financial data or use values from CSV
        $hasFinancialDataInCsv = isset($cleaned['_dpp_from_csv']) && $cleaned['_dpp_from_csv'] > 0;
        
        if ($hasFinancialDataInCsv) {
            // Use financial data from CSV
            $cleaned['dpp'] = $cleaned['_dpp_from_csv'];
            $cleaned['dpp_nilai_lain'] = $cleaned['_dpp_nilai_lain_from_csv'] ?? round($cleaned['dpp'] * 11/12, 2);
            $cleaned['ppn'] = $cleaned['_ppn_from_csv'] ?? round($cleaned['dpp'] * 0.11, 2);
            $cleaned['pph'] = $cleaned['_pph_from_csv'] ?? round($cleaned['dpp'] * 0.01, 2);
            $cleaned['grand_total'] = $cleaned['_grand_total_from_csv'] ?? ($cleaned['dpp'] + $cleaned['ppn']);
        } else if (!$isDpeFormat || (!isset($cleaned['dpp']) || $cleaned['dpp'] == 0)) {
            // Calculate financial data from tariff
            $financialData = $this->calculateFinancialData($cleaned);
            $cleaned = array_merge($cleaned, $financialData);
        } else {
            // For DPE format with existing financial data, ensure dpp_nilai_lain is set
            if (!isset($cleaned['dpp_nilai_lain']) || $cleaned['dpp_nilai_lain'] == 0) {
                $cleaned['dpp_nilai_lain'] = round(($cleaned['dpp'] ?? 0) * 11/12, 2);
            }
        }

        // Remove temporary calculation keys before validation and saving
        $temporaryKeys = [
            '_tarif_for_calculation',
            '_is_bulanan',
            '_jumlah_hari_for_dpp',
            '_dpp_from_csv',
            '_dpp_nilai_lain_from_csv',
            '_ppn_from_csv',
            '_pph_from_csv',
            '_grand_total_from_csv'
        ];
        
        foreach ($temporaryKeys as $key) {
            if (isset($cleaned[$key])) {
                unset($cleaned[$key]);
            }
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

        // Trim whitespace
        $date = trim($date);

        try {
            // Handle various date formats
            $formats = [
                'Y-m-d',      // 2023-06-07
                'd/m/Y',      // 07/06/2023
                'd-m-Y',      // 07-06-2023
                'm/d/Y',      // 06/07/2023
                'Y/m/d',      // 2023/06/07
                'd M y',      // 07 Jun 23
                'd M Y',      // 07 Jun 2023
                'd-M-y',      // 07-Jun-23
                'd-M-Y',      // 07-Jun-2023
            ];

            foreach ($formats as $format) {
                try {
                    $parsed = Carbon::createFromFormat($format, $date);
                    if ($parsed) {
                        return $parsed->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            // Try Carbon parse as last resort (handles many formats automatically)
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Exception("Format tanggal tidak valid: {$date}");
        }
    }

    private function cleanNumber($value)
    {
        // Trim whitespace first
        $value = trim($value);
        
        if (empty($value)) {
            return 0;
        }

        // Remove all spaces and currency symbols
        $cleaned = preg_replace('/\s+/', '', $value); // Remove all whitespace
        $cleaned = preg_replace('/[^\d.,\-]/', '', $cleaned); // Remove non-numeric except . , -
        
        // Handle different decimal separators
        // If has both comma and dot, assume dot is thousands separator (European format)
        if (strpos($cleaned, '.') !== false && strpos($cleaned, ',') !== false) {
            // Format: 1.234.567,89 (European) -> remove dots, replace comma with dot
            $cleaned = str_replace('.', '', $cleaned);
            $cleaned = str_replace(',', '.', $cleaned);
        } elseif (strpos($cleaned, ',') !== false) {
            // Only comma: could be decimal or thousands
            // If only one comma and digits after it <= 2, it's decimal
            $parts = explode(',', $cleaned);
            if (count($parts) == 2 && strlen($parts[1]) <= 2) {
                $cleaned = str_replace(',', '.', $cleaned); // Decimal separator
            } else {
                $cleaned = str_replace(',', '', $cleaned); // Thousands separator
            }
        } elseif (strpos($cleaned, '.') !== false) {
            // Only dot: could be decimal or thousands
            // If only one dot and digits after it <= 2, it's decimal
            $parts = explode('.', $cleaned);
            if (count($parts) == 2 && strlen($parts[1]) <= 2) {
                // Already using dot as decimal, keep it
            } else {
                $cleaned = str_replace('.', '', $cleaned); // Thousands separator
            }
        }

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
