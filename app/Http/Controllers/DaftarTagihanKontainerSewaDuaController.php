<?php

namespace App\Http\Controllers;

use App\Models\DaftarTagihanKontainerSewaDua;
use App\Models\Kontainer;
use App\Models\MasterPricelistSewaKontainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DaftarTagihanKontainerSewaDuaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DaftarTagihanKontainerSewaDua::query();

        // Exclude GROUP_SUMMARY records from main listing
        $query->where('nomor_kontainer', 'NOT LIKE', 'GROUP_SUMMARY_%')
              ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_TEMPLATE%');

        // Handle search functionality
        if ($request->filled('q')) {
            $searchTerm = trim($request->input('q'));
            $sanitizedSearch = preg_replace('/[^A-Za-z0-9]/', '', $searchTerm);

            $query->where(function ($q) use ($searchTerm, $sanitizedSearch) {
                $q->where('vendor', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('group', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('invoice_vendor', 'LIKE', '%' . $searchTerm . '%');

                if (!empty($sanitizedSearch)) {
                    $q->orWhereRaw("REPLACE(REPLACE(nomor_kontainer, '-', ''),' ', '') LIKE ?", ['%' . $sanitizedSearch . '%']);
                }
            });
        }

        // Handle filters
        if ($request->filled('vendor')) {
            $query->where('vendor', $request->input('vendor'));
        }
        if ($request->filled('size')) {
            $query->where('size', $request->input('size'));
        }
        if ($request->filled('periode')) {
            $query->where('periode', $request->input('periode'));
        }

        // Handle status filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'ongoing') {
                $query->whereNull('tanggal_akhir');
            } elseif ($status === 'selesai') {
                $query->whereNotNull('tanggal_akhir');
            }
        }

        // Ordering and Pagination
        $query->orderBy('nomor_kontainer')
              ->orderBy('periode');

        $tagihans = $query->paginate(25);

        // Filter Options
        $vendors = Cache::remember('tagihan_dua_vendors', 300, function() {
            return DaftarTagihanKontainerSewaDua::distinct()->whereNotNull('vendor')->pluck('vendor')->sort()->values();
        });
        $sizes = Cache::remember('tagihan_dua_sizes', 300, function() {
            return DaftarTagihanKontainerSewaDua::distinct()->whereNotNull('size')->pluck('size')->sort()->values();
        });
        $periodes = Cache::remember('tagihan_dua_periodes', 300, function() {
            return DaftarTagihanKontainerSewaDua::distinct()->whereNotNull('periode')->pluck('periode')->sort()->values();
        });

        // Status options
        $statusOptions = [
            'ongoing' => 'Container Ongoing',
            'selesai' => 'Container Selesai'
        ];

        return view('daftar-tagihan-kontainer-sewa-2.index', compact('tagihans', 'vendors', 'sizes', 'periodes', 'statusOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $containersData = Kontainer::select('nomor_seri_gabungan', 'vendor', 'ukuran')
            ->whereNotNull('nomor_seri_gabungan')
            ->whereNotNull('vendor')
            ->orderBy('nomor_seri_gabungan')
            ->get();
            
        $vendors = Kontainer::select('vendor')->distinct()->whereNotNull('vendor')->orderBy('vendor')->pluck('vendor');

        return view('daftar-tagihan-kontainer-sewa-2.create', compact('containersData', 'vendors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor' => 'required|string|max:255',
            'nomor_kontainer' => 'nullable|string|max:100',
            'nomor_kontainer_manual' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:50',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'periode' => 'nullable|integer|min:1',
            'tarif' => 'nullable|string|max:50',
            'dpp' => 'nullable|numeric',
            'ppn' => 'nullable|numeric',
            'pph' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric',
            'group' => 'nullable|string|max:100',
            'masa' => 'nullable|string|max:255',
            'adjustment' => 'nullable|numeric',
            'invoice_vendor' => 'nullable|string|max:255',
            'tanggal_invoice_vendor' => 'nullable|date',
        ]);

        // Handle nomor_kontainer
        if (empty($data['nomor_kontainer']) && !empty($data['nomor_kontainer_manual'])) {
            $data['nomor_kontainer'] = $data['nomor_kontainer_manual'];
        }
        unset($data['nomor_kontainer_manual']);

        if (empty($data['nomor_kontainer'])) {
            return back()->withErrors(['nomor_kontainer' => 'Nomor kontainer wajib diisi.'])->withInput();
        }

        // Auto-calculate logic (simplified from V1)
        $data['dpp'] = $data['dpp'] ?? 0;
        $data['dpp_nilai_lain'] = round($data['dpp'] * 11 / 12, 2);
        
        // PPN is 12% of DPP Nilai Lain
        $data['ppn'] = $data['ppn'] ?? round($data['dpp_nilai_lain'] * 0.12, 2);
        
        // PPH is 2% of DPP
        $data['pph'] = $data['pph'] ?? round($data['dpp'] * 0.02, 2);
        
        $data['grand_total'] = $data['grand_total'] ?? ($data['dpp'] + $data['ppn'] - $data['pph']);

        DaftarTagihanKontainerSewaDua::create($data);

        return redirect()->route('daftar-tagihan-kontainer-sewa-2.index')->with('success', 'Tagihan kontainer berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = DaftarTagihanKontainerSewaDua::findOrFail($id);
        return view('daftar-tagihan-kontainer-sewa-2.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = DaftarTagihanKontainerSewaDua::findOrFail($id);
        return view('daftar-tagihan-kontainer-sewa-2.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'vendor' => 'required|string|max:255',
            'nomor_kontainer' => 'required|string|max:100',
            'size' => 'nullable|string|max:50',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'periode' => 'nullable|integer|min:1',
            'tarif' => 'nullable|string|max:50',
            'dpp' => 'nullable|numeric',
            'ppn' => 'nullable|numeric',
            'pph' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric',
        ]);

        $item = DaftarTagihanKontainerSewaDua::findOrFail($id);
        $item->update($data);

        return redirect()->route('daftar-tagihan-kontainer-sewa-2.index')->with('success', 'Tagihan kontainer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = DaftarTagihanKontainerSewaDua::findOrFail($id);
        $item->delete();
        
        return redirect()->route('daftar-tagihan-kontainer-sewa-2.index')->with('success', 'Tagihan kontainer berhasil dihapus.');
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

        // Query pricelist
        $pr = null;
        if ($size) {
            $pr = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)
                ->where('vendor', $vendor)
                ->where(function($q) use ($periodStart){
                    $q->where('tanggal_harga_awal', '<=', $periodStart->toDateString())
                      ->where(function($q2) use ($periodStart){ $q2->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir','>=',$periodStart->toDateString()); });
                })->orderBy('tanggal_harga_awal','desc')->first();
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
            'harga_pricelist' => $pr ? (float)$pr->harga : null,
            'vendor' => $vendor,
            'size' => $size,
            'tanggal_berlaku' => $pr && $pr->tanggal_harga_awal ? Carbon::parse($pr->tanggal_harga_awal)->format('d M Y') : null,
        ]);
    }
    
    /**
     * Show the form for creating a new group.
     */
    public function createGroup()
    {
        // Get all tagihan that don't have a group yet
        $tagihans = DaftarTagihanKontainerSewaDua::where(function($query) {
            $query->whereNull('group')
                  ->orWhere('group', '');
        })
        ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_SUMMARY_%')
        ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_TEMPLATE%')
        ->orderBy('vendor')
        ->orderBy('nomor_kontainer')
        ->get();

        return view('daftar-tagihan-kontainer-sewa-2.create-group', compact('tagihans'));
    }

    /**
     * Store a newly created group in storage.
     */
    public function storeGroup(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
            'selected_containers' => 'required|array|min:1',
            'selected_containers.*' => 'required|integer|exists:daftar_tagihan_kontainer_sewa_dua,id',
        ]);

        $validated = $request->all();

        DB::beginTransaction();
        try {
            // Update selected containers
            DaftarTagihanKontainerSewaDua::whereIn('id', $validated['selected_containers'])
                ->update([
                    'group' => $validated['group_name'],
                    'updated_at' => now(),
                ]);

            // Create a group summary record
            $groupRecord = DaftarTagihanKontainerSewaDua::create([
                'group' => $validated['group_name'],
                'vendor' => 'GROUP',
                'nomor_kontainer' => 'GROUP_SUMMARY_' . $validated['group_name'],
                'dpp' => 0,
                'grand_total' => 0,
                'status' => 'ongoing'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Group '{$validated['group_name']}' berhasil dibuat.",
                'group_id' => $groupRecord->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat group: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function import()
    {
        return view('daftar-tagihan-kontainer-sewa-2.import');
    }

    public function export(Request $request)
    {
        $query = DaftarTagihanKontainerSewaDua::query();

        // Exclude GROUP_SUMMARY records
        $query->where('nomor_kontainer', 'NOT LIKE', 'GROUP_SUMMARY_%')
              ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_TEMPLATE%');

        // Apply same filters as index
        if ($request->filled('q')) {
            $searchTerm = trim($request->input('q'));
            $sanitizedSearch = preg_replace('/[^A-Za-z0-9]/', '', $searchTerm);

            $query->where(function ($q) use ($searchTerm, $sanitizedSearch) {
                $q->where('vendor', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('group', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('invoice_vendor', 'LIKE', '%' . $searchTerm . '%');

                if (!empty($sanitizedSearch)) {
                    $q->orWhereRaw("REPLACE(REPLACE(nomor_kontainer, '-', ''),' ', '') LIKE ?", ['%' . $sanitizedSearch . '%']);
                }
            });
        }

        if ($request->filled('vendor')) {
            $query->where('vendor', $request->input('vendor'));
        }
        if ($request->filled('size')) {
            $query->where('size', $request->input('size'));
        }
        if ($request->filled('periode')) {
            $query->where('periode', $request->input('periode'));
        }

        // Handle status filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'ongoing') {
                $query->whereNull('tanggal_akhir');
            } elseif ($status === 'selesai') {
                $query->whereNotNull('tanggal_akhir');
            }
        }

        // Ordering
        $query->orderBy('nomor_kontainer')
              ->orderBy('periode');

        $tagihans = $query->get();

        // Generate CSV
        $filename = 'daftar_tagihan_kontainer_sewa_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($tagihans) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row
            fputcsv($file, [
                'Vendor',
                'No. Kontainer',
                'Size',
                'Periode',
                'Tanggal Awal',
                'Tanggal Akhir',
                'Masa',
                'Tarif',
                'DPP',
                'DPP Nilai Lain',
                'Adjustment',
                'Invoice Vendor',
                'Tanggal Invoice Vendor',
                'PPN',
                'PPH',
                'Grand Total',
                'Group',
                'Status'
            ]);

            // Data rows
            foreach ($tagihans as $tagihan) {
                fputcsv($file, [
                    $tagihan->vendor,
                    $tagihan->nomor_kontainer,
                    $tagihan->size,
                    $tagihan->periode,
                    $tagihan->tanggal_awal ? date('d-m-Y', strtotime($tagihan->tanggal_awal)) : '',
                    $tagihan->tanggal_akhir ? date('d-m-Y', strtotime($tagihan->tanggal_akhir)) : '',
                    $tagihan->masa,
                    $tagihan->tarif,
                    $tagihan->dpp,
                    $tagihan->dpp_nilai_lain,
                    $tagihan->adjustment,
                    $tagihan->invoice_vendor,
                    $tagihan->tanggal_invoice_vendor ? date('d-m-Y', strtotime($tagihan->tanggal_invoice_vendor)) : '',
                    $tagihan->ppn,
                    $tagihan->pph,
                    $tagihan->grand_total,
                    $tagihan->group,
                    $tagihan->tanggal_akhir ? 'Selesai' : 'Ongoing'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function exportTemplate()
    {
        // Placeholder
        return response()->download(public_path('templates/template_tagihan_kontainer_sewa.xlsx'));
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:daftar_tagihan_kontainer_sewa_dua,id'
        ]);

        try {
            $deleted = DaftarTagihanKontainerSewaDua::whereIn('id', $request->ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deleted} tagihan kontainer.",
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tagihan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:daftar_tagihan_kontainer_sewa_dua,id',
            'status' => 'required|string|in:ongoing,selesai'
        ]);

        try {
            $status = $request->status;
            $updateData = [];
            
            if ($status === 'selesai') {
                $updateData['tanggal_akhir'] = now();
            } else {
                $updateData['tanggal_akhir'] = null;
            }
            
            $updated = DaftarTagihanKontainerSewaDua::whereIn('id', $request->ids)
                ->update($updateData);
            
            $statusLabel = $status === 'selesai' ? 'Selesai' : 'Ongoing';
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil mengubah status {$updated} tagihan kontainer menjadi {$statusLabel}.",
                'updated_count' => $updated
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk update status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status tagihan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ungroupContainers(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:daftar_tagihan_kontainer_sewa_dua,id'
        ]);

        try {
            $updated = DaftarTagihanKontainerSewaDua::whereIn('id', $request->ids)
                ->update(['group' => null]);
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil mengeluarkan {$updated} kontainer dari grup.",
                'updated_count' => $updated
            ]);
        } catch (\Exception $e) {
            Log::error('Ungroup containers error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengeluarkan kontainer dari grup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getGroups(Request $request)
    {
        try {
            $groups = DaftarTagihanKontainerSewaDua::whereNotNull('group')
                ->where('group', '!=', '')
                ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_SUMMARY_%')
                ->distinct()
                ->pluck('group')
                ->sort()
                ->values();
            
            return response()->json([
                'success' => true,
                'groups' => $groups
            ]);
        } catch (\Exception $e) {
            Log::error('Get groups error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data grup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteGroups(Request $request)
    {
        $request->validate([
            'groups' => 'required|array',
            'groups.*' => 'required|string'
        ]);

        try {
            DB::beginTransaction();
            
            // Remove group from containers
            $updated = DaftarTagihanKontainerSewaDua::whereIn('group', $request->groups)
                ->update(['group' => null]);
            
            // Delete group summary records
            $deleted = DaftarTagihanKontainerSewaDua::whereIn('group', $request->groups)
                ->where('nomor_kontainer', 'LIKE', 'GROUP_SUMMARY_%')
                ->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus " . count($request->groups) . " grup dan membebaskan {$updated} kontainer.",
                'updated_count' => $updated,
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete groups error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus grup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateInvoiceNumber(Request $request)
    {
        try {
            // Generate invoice number based on current date and count
            $prefix = 'INV-KS-' . date('Ym') . '-';
            $count = DaftarTagihanKontainerSewaDua::whereNotNull('invoice_vendor')
                ->where('invoice_vendor', 'LIKE', $prefix . '%')
                ->count();
            
            $nextNumber = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            $invoiceNumber = $prefix . $nextNumber;
            
            return response()->json([
                'success' => true,
                'invoice_number' => $invoiceNumber
            ]);
        } catch (\Exception $e) {
            Log::error('Generate invoice number error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));
            
            // Remove header row
            $header = array_shift($data);
            
            DB::beginTransaction();
            
            $imported = 0;
            $errors = [];
            
            foreach ($data as $index => $row) {
                try {
                    // Map CSV columns to database fields
                    // Adjust this mapping based on your CSV structure
                    DaftarTagihanKontainerSewaDua::create([
                        'vendor' => $row[0] ?? null,
                        'nomor_kontainer' => $row[1] ?? null,
                        'size' => $row[2] ?? null,
                        'periode' => $row[3] ?? null,
                        'tanggal_awal' => $row[4] ?? null,
                        'tanggal_akhir' => $row[5] ?? null,
                        'masa' => $row[6] ?? null,
                        'tarif' => $row[7] ?? null,
                        'dpp' => $row[8] ?? 0,
                        'dpp_nilai_lain' => $row[9] ?? 0,
                        'adjustment' => $row[10] ?? 0,
                        'invoice_vendor' => $row[11] ?? null,
                        'tanggal_invoice_vendor' => $row[12] ?? null,
                        'ppn' => $row[13] ?? 0,
                        'pph' => $row[14] ?? 0,
                        'grand_total' => $row[15] ?? 0,
                        'group' => $row[16] ?? null,
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil import {$imported} data.",
                'imported_count' => $imported,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import CSV error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal import CSV: ' . $e->getMessage()
            ], 500);
        }
    }
}
