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

    public function exportTemplate()
    {
        // Placeholder
        return response()->download(public_path('templates/template_tagihan_kontainer_sewa.xlsx'));
    }
}
