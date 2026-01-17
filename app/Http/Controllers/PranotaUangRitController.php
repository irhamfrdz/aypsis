<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PranotaUangRit;
use App\Models\PranotaUangRitSupirDetail;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PranotaUangRitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Default to last 30 days if no date range is provided
        if (!($request->filled('start_date') && $request->filled('end_date'))) {
            $request->merge([
                'start_date' => now()->subDays(30)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d'),
            ]);
        }

        $query = PranotaUangRit::with(['suratJalan', 'creator', 'approver']);

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_pranota', 'like', "%{$search}%")
                  ->orWhere('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir_nama', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range (use pranota table's 'tanggal' column)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        // Filter by supir
        if ($request->filled('supir')) {
            $query->where('supir_nama', 'like', "%{$request->supir}%");
        }

        $pranotaUangRits = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('pranota-uang-rit.index', compact('pranotaUangRits'));
    }

    /**
     * Show the date selection page for creating a new pranota.
     */
    public function selectDate(Request $request)
    {
        // Provide existing values if available
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        return view('pranota-uang-rit.select-date', compact('start_date', 'end_date'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Default to last 30 days for create if date range is not provided
        $startDate = request()->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = request()->input('end_date', now()->format('Y-m-d'));

        // Validate date inputs
        if (request()->filled('start_date') && request()->filled('end_date')) {
            try {
                $startCheck = \Carbon\Carbon::parse($startDate)->startOfDay();
                $endCheck = \Carbon\Carbon::parse($endDate)->endOfDay();
                if ($startCheck->gt($endCheck)) {
                    return redirect()->route('pranota-uang-rit.select-date')->withInput()->with('error', 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.');
                }
            } catch (\Exception $e) {
                return redirect()->route('pranota-uang-rit.select-date')->withInput()->with('error', 'Format tanggal tidak valid.');
            }
        }
        // Apply date range filter first for better performance and consistency
        $startDateObj = null;
        $endDateObj = null;
        if (!empty($startDate) && !empty($endDate)) {
            try {
                $startDateObj = \Carbon\Carbon::parse($startDate)->startOfDay();
                $endDateObj = \Carbon\Carbon::parse($endDate)->endOfDay();
                
                // Logging for debugging
                Log::info('PranotaUangRit::create date filters', [
                    'start' => $startDateObj->toDateString(), 
                    'end' => $endDateObj->toDateString(),
                    'start_input' => $startDate,
                    'end_input' => $endDate
                ]);
            } catch (\Exception $e) {
                Log::error('Date parsing failed', ['start' => $startDate, 'end' => $endDate, 'error' => $e->getMessage()]);
                // If parsing fails, use default last 30 days
                $startDateObj = \Carbon\Carbon::now()->subDays(30)->startOfDay();
                $endDateObj = \Carbon\Carbon::now()->endOfDay();
            }
        }

        // Get available surat jalans that haven't been processed for pranota uang rit
        // Only include surat jalans that use 'menggunakan_rit' and status pembayaran uang rit 'belum_dibayar'
        $baseQuery = SuratJalan::with(['tandaTerima', 'approvals'])->where(function($q) {
            // Include surat jalan which have been approved or already passed checkpoint / have tanda terima
            $q->where('status', 'approved')
              ->orWhere('status', 'sudah_checkpoint')
              ->orWhere('status', 'active')
              ->orWhereNotNull('tanggal_checkpoint')
              ->orWhereHas('tandaTerima')
              ->orWhereHas('approvals', function($sub) {
                  $sub->where('status', 'approved');
              });
        })
            ->where('rit', 'menggunakan_rit') // Filter only surat jalan yang menggunakan rit
            ->where('status_pembayaran_uang_rit', SuratJalan::STATUS_UANG_RIT_BELUM_DIBAYAR) // Filter yang belum dibayar
            ->whereNotIn('id', function($query) {
                $query->select('surat_jalan_id')
                    ->from('pranota_uang_rits')
                    ->whereNotNull('surat_jalan_id')
                    ->whereNotIn('status', ['cancelled']);
            })
            // Include surat jalan that:
            // - Have a supir/kenek checkpoint (tanggal_checkpoint), OR
            // - Have a Tanda Terima record, OR
            // - Are bongkaran with tanggal tanda terima, OR
            // - Are approved (don't strictly require checkpoint)
            ->where(function($q) {
                $q->whereNotNull('tanggal_checkpoint')
                  ->orWhereHas('tandaTerima')
                  ->orWhere(function($subQ) {
                      // Surat jalan bongkaran yang sudah memilih tanggal tanda terima
                      $subQ->where('kegiatan', 'bongkaran')
                           ->whereNotNull('tanggal_tanda_terima');
                  })
                  ->orWhere('status', 'approved'); // Tambahan: surat jalan yang sudah approved bisa langsung muncul
            });

        // Apply date range filter to base query BEFORE any cloning - filter by tanggal tanda terima
        if ($startDateObj && $endDateObj) {
            $baseQuery->where(function($q) use ($startDateObj, $endDateObj) {
                // Filter berdasarkan tanggal dari berbagai sumber (OR conditions)
                $q->where(function($subQ) use ($startDateObj, $endDateObj) {
                    // 1. Tanggal dari relasi tandaTerima (untuk surat jalan non-bongkaran seperti pengiriman, muat, dll)
                    $subQ->whereHas('tandaTerima', function($ttQuery) use ($startDateObj, $endDateObj) {
                        $ttQuery->where(\DB::raw('DATE(tanggal)'), '>=', $startDateObj->toDateString())
                                ->where(\DB::raw('DATE(tanggal)'), '<=', $endDateObj->toDateString());
                    });
                })
                ->orWhere(function($subQ) use ($startDateObj, $endDateObj) {
                    // 2. Tanggal tanda terima untuk kegiatan bongkaran (kolom langsung di tabel surat_jalans)
                    $subQ->where('kegiatan', 'bongkaran')
                         ->whereNotNull('tanggal_tanda_terima')
                         ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '>=', $startDateObj->toDateString())
                         ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '<=', $endDateObj->toDateString());
                })
                ->orWhere(function($subQ) use ($startDateObj, $endDateObj) {
                    // 3. Filter berdasarkan tanggal checkpoint
                    $subQ->whereNotNull('tanggal_checkpoint')
                         ->where(\DB::raw('DATE(tanggal_checkpoint)'), '>=', $startDateObj->toDateString())
                         ->where(\DB::raw('DATE(tanggal_checkpoint)'), '<=', $endDateObj->toDateString());
                })
                ->orWhere(function($subQ) use ($startDateObj, $endDateObj) {
                    // 4. Filter berdasarkan tanggal surat jalan (untuk yang approved tapi belum ada checkpoint/tanda terima)
                    $subQ->where('status', 'approved')
                         ->where(\DB::raw('DATE(tanggal_surat_jalan)'), '>=', $startDateObj->toDateString())
                         ->where(\DB::raw('DATE(tanggal_surat_jalan)'), '<=', $endDateObj->toDateString());
                });
            });
            
            // Log the actual SQL query for debugging
            $sqlQuery = $baseQuery->toSql();
            $bindings = $baseQuery->getBindings();
            
            Log::info('Applied date filter to base query (tanggal tanda terima)', [
                'start_filter' => $startDateObj->toDateString(),
                'end_filter' => $endDateObj->toDateString(),
                'filter_type' => 'tanggal_tanda_terima (relasi + kolom bongkaran + fallback checkpoint)',
                'sql_query_preview' => str_replace('?', "'%s'", $sqlQuery),
                'bindings_count' => count($bindings)
            ]);
        }

        $baseQuery->orderBy('created_at', 'desc');

        // Now calculate statistics based on the filtered base query
        $baseQueryBeforeDate = SuratJalan::with(['tandaTerima', 'approvals'])->where(function($q) {
            // Include surat jalan which have been approved or already passed checkpoint / have tanda terima
            $q->where('status', 'approved')
              ->orWhere('status', 'sudah_checkpoint')
              ->orWhere('status', 'active')
              ->orWhereNotNull('tanggal_checkpoint')
              ->orWhereHas('tandaTerima')
              ->orWhereHas('approvals', function($sub) {
                  $sub->where('status', 'approved');
              });
        })
            ->where('rit', 'menggunakan_rit')
            ->where('status_pembayaran_uang_rit', SuratJalan::STATUS_UANG_RIT_BELUM_DIBAYAR)
            ->whereNotIn('id', function($query) {
                $query->select('surat_jalan_id')
                    ->from('pranota_uang_rits')
                    ->whereNotNull('surat_jalan_id')
                    ->whereNotIn('status', ['cancelled']);
            })
            ->where(function($q) {
                $q->whereNotNull('tanggal_checkpoint')
                  ->orWhereHas('tandaTerima')
                  ->orWhere(function($subQ) {
                      // Surat jalan bongkaran yang sudah memilih tanggal tanda terima
                      $subQ->where('kegiatan', 'bongkaran')
                           ->whereNotNull('tanggal_tanda_terima');
                  });
            });
        
        $countBeforeDate = $baseQueryBeforeDate->count();
        $countAfterDate = (clone $baseQuery)->count();
        
        Log::info('Date filtering impact', [
            'count_before_date_filter' => $countBeforeDate,
            'count_after_date_filter' => $countAfterDate,
            'date_filter_applied' => ($startDateObj && $endDateObj),
            'start_date' => $startDateObj ? $startDateObj->toDateString() : null,
            'end_date' => $endDateObj ? $endDateObj->toDateString() : null
        ]);
        
        $eligibleCount = (clone $baseQuery)->count();
        $pranotaUsedCount = (clone $baseQuery)->whereIn('id', function($subQuery) {
            $subQuery->select('surat_jalan_id')->from('pranota_uang_rits')->whereNotNull('surat_jalan_id')->whereNotIn('status', ['cancelled']);
        })->count();
        
        // Get final surat jalans (no need to reapply filters, they're already in baseQuery)
        $suratJalans = (clone $baseQuery)->get();
        $finalFilteredCount = $suratJalans->count();

        // Get examples for debugging
        $eligibleExamples = (clone $baseQuery)->take(10)->get(['id', 'no_surat_jalan', 'supir', 'status', 'tanggal_checkpoint', 'tanggal_surat_jalan']);
        $excludedByPranotaExamples = (clone $baseQuery)->whereIn('id', function($subQuery) {
            $subQuery->select('surat_jalan_id')->from('pranota_uang_rits')->whereNotNull('surat_jalan_id')->whereNotIn('status', ['cancelled']);
        })->take(10)->get(['id', 'no_surat_jalan', 'supir', 'status', 'tanggal_checkpoint', 'tanggal_surat_jalan']);
        $excludedByPaymentExamples = (clone $baseQuery)->where('status_pembayaran_uang_rit', '!=', SuratJalan::STATUS_UANG_RIT_BELUM_DIBAYAR)->take(10)->get(['id', 'no_surat_jalan', 'supir', 'status', 'status_pembayaran_uang_rit', 'tanggal_surat_jalan']);
        
        // Pass the start and end dates to the view so UI shows selected range explicitly
        $viewStartDate = $startDate;
        $viewEndDate = $endDate;

        Log::info('Final Surat Jalans for Pranota: ' . $suratJalans->count());
        Log::info('Date filtering applied', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_obj' => $startDateObj ? $startDateObj->toDateString() : null,
            'end_obj' => $endDateObj ? $endDateObj->toDateString() : null,
            'eligible_count' => $eligibleCount,
            'final_count' => $finalFilteredCount
        ]);
        
        // Debug: Show some sample data with dates
        if ($suratJalans->count() > 0) {
            $sample = $suratJalans->first();
            Log::info('Sample Surat Jalan: ', [
                'id' => $sample->id,
                'no_surat_jalan' => $sample->no_surat_jalan,
                'tanggal_surat_jalan' => $sample->tanggal_surat_jalan,
                'status' => $sample->status,
                'rit' => $sample->rit,
                'supir_nama' => $sample->supir_nama,
                'kenek_nama' => $sample->kenek_nama
            ]);
            
            // Log ALL dates to see if filtering is working
            $allDates = $suratJalans->pluck('tanggal_surat_jalan', 'no_surat_jalan')->toArray();
            Log::info('ALL Surat Jalan dates returned:', $allDates);
        }

        // Compute extra diagnostics: SJs that have Tanda Terima but are excluded from final list
        $suratJalanIncludedIds = $suratJalans->pluck('id')->toArray();
        $excludedByTandaTerimaExamples = (clone $baseQuery)
            ->whereHas('tandaTerima')
            ->whereNotIn('id', $suratJalanIncludedIds)
            ->take(10)
            ->get(['id', 'no_surat_jalan', 'supir', 'status', 'rit', 'status_pembayaran_uang_rit', 'tanggal_surat_jalan']);

        // TAMBAHAN: Ambil juga data dari SuratJalanBongkaran yang sudah ada tanda terima
        $suratJalanBongkarans = collect();
        if ($startDateObj && $endDateObj) {
            $queryBongkaran = SuratJalanBongkaran::with(['tandaTerima'])
                ->whereHas('tandaTerima', function($query) use ($startDateObj, $endDateObj) {
                    $query->where(\DB::raw('DATE(tanggal_tanda_terima)'), '>=', $startDateObj->toDateString())
                          ->where(\DB::raw('DATE(tanggal_tanda_terima)'), '<=', $endDateObj->toDateString());
                })
                ->where('lokasi', 'Jakarta') // Filter: hanya lokasi Jakarta
                ->where(function($q) {
                    // Filter: rit = menggunakan_rit ATAU rit is NULL (default dianggap menggunakan rit)
                    $q->where('rit', 'menggunakan_rit')
                      ->orWhereNull('rit');
                })
                ->where(function($q) {
                    // Filter: status_pembayaran_uang_rit = belum_bayar ATAU NULL (belum ada pembayaran)
                    $q->where('status_pembayaran_uang_rit', 'belum_bayar')
                      ->orWhereNull('status_pembayaran_uang_rit');
                })
                ->whereNotIn('id', function($query) {
                    $query->select('surat_jalan_bongkaran_id')
                        ->from('pranota_uang_rits')
                        ->whereNotNull('surat_jalan_bongkaran_id')
                        ->whereNotIn('status', ['cancelled']);
                });

            $suratJalanBongkarans = $queryBongkaran->orderBy('created_at', 'desc')->get();

            Log::info('Surat Jalan Bongkaran Query', [
                'date_range' => $startDateObj->toDateString() . ' to ' . $endDateObj->toDateString(),
                'count' => $suratJalanBongkarans->count(),
                'sql' => $queryBongkaran->toSql(),
            ]);
            
            if ($suratJalanBongkarans->count() > 0) {
                Log::info('Sample Bongkaran Data:', [
                    'first' => [
                        'id' => $suratJalanBongkarans->first()->id,
                        'nomor' => $suratJalanBongkarans->first()->nomor_surat_jalan,
                        'rit' => $suratJalanBongkarans->first()->rit,
                        'status_pembayaran' => $suratJalanBongkarans->first()->status_pembayaran_uang_rit,
                        'has_tanda_terima' => $suratJalanBongkarans->first()->tandaTerima ? 'yes' : 'no',
                        'tanggal_tt' => $suratJalanBongkarans->first()->tandaTerima ? $suratJalanBongkarans->first()->tandaTerima->tanggal_tanda_terima : null,
                    ]
                ]);
            }
        }

        return view('pranota-uang-rit.create', compact('suratJalans', 'suratJalanBongkarans', 'eligibleCount', 'pranotaUsedCount', 'finalFilteredCount', 'eligibleExamples', 'excludedByPranotaExamples', 'excludedByPaymentExamples', 'excludedByTandaTerimaExamples', 'viewStartDate', 'viewEndDate'));
    }

    /**
     * Show selection page for uang jalan
     */
    public function selectUangJalan(Request $request)
    {
        // Build base eligibility query
        $baseQuery = SuratJalan::with(['tandaTerima', 'approvals'])->where(function($q) {
            $q->where('status', 'approved')
              ->orWhere('status', 'sudah_checkpoint')
              ->orWhere('status', 'active')
              ->orWhereNotNull('tanggal_checkpoint')
              ->orWhereHas('tandaTerima')
              ->orWhereHas('approvals', function($sub) {
                  $sub->where('status', 'approved');
              });
        });

        // Clone base query for counting and further filtering
        $query = (clone $baseQuery)->where('rit', 'menggunakan_rit') // Filter only surat jalan yang menggunakan rit
            ->where('status_pembayaran_uang_rit', SuratJalan::STATUS_UANG_RIT_BELUM_DIBAYAR) // Filter yang belum dibayar
            ->whereNotIn('id', function($subQuery) {
                $subQuery->select('surat_jalan_id')
                    ->from('pranota_uang_rits')
                    ->whereNotNull('surat_jalan_id')
                    ->whereNotIn('status', ['cancelled']);
            })
            // Only include surat jalan that already have a supir checkpoint OR have a Tanda Terima record
            ->where(function($q) {
                $q->whereNotNull('tanggal_checkpoint')
                  ->orWhereHas('tandaTerima');
            });

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir_nama', 'like', "%{$search}%");
            });
        }

        // Filter by date range using whereDate for robust date comparison
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        if ($startDate && $endDate) {
            try {
                $startDateObj = \Carbon\Carbon::parse($startDate)->startOfDay();
                $endDateObj = \Carbon\Carbon::parse($endDate)->endOfDay();
                if ($startDateObj->gt($endDateObj)) {
                    return redirect()->route('pranota-uang-rit.select-uang-jalan')
                            ->withInput()
                            ->with('error', 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.');
                }
                $query->whereDate('tanggal_surat_jalan', '>=', $startDateObj->toDateString())
                      ->whereDate('tanggal_surat_jalan', '<=', $endDateObj->toDateString());
            } catch (\Exception $e) {
                // Invalid date format, ignore filter and show all but return an error message
                return redirect()->route('pranota-uang-rit.select-uang-jalan')->withInput()->with('error', 'Format tanggal tidak valid.');
            }
        }

        // Filter by supir
        if ($request->filled('supir')) {
            $query->where('supir_nama', 'like', "%{$request->supir}%");
        }

        // Compute counts for debugging/help messages
        $eligibleCount = (clone $baseQuery)->count();
        $pranotaUsedCount = (clone $baseQuery)->whereIn('id', function($subQuery) {
            $subQuery->select('surat_jalan_id')->from('pranota_uang_rits')->whereNotNull('surat_jalan_id')->whereNotIn('status', ['cancelled']);
        })->count();
        $finalFilteredCount = (clone $query)->count();

        $eligibleExamples = (clone $baseQuery)->take(10)->get(['id', 'no_surat_jalan', 'supir', 'status', 'tanggal_checkpoint']);
        $excludedByPranotaExamples = (clone $baseQuery)->whereIn('id', function($subQuery) {
            $subQuery->select('surat_jalan_id')->from('pranota_uang_rits')->whereNotNull('surat_jalan_id')->whereNotIn('status', ['cancelled']);
        })->take(10)->get(['id', 'no_surat_jalan', 'supir', 'status', 'tanggal_checkpoint']);
        $excludedByPaymentExamples = (clone $baseQuery)->where('status_pembayaran_uang_rit', '!=', SuratJalan::STATUS_UANG_RIT_BELUM_DIBAYAR)->take(10)->get(['id', 'no_surat_jalan', 'supir', 'status', 'status_pembayaran_uang_rit']);

        $suratJalans = $query->orderBy('created_at', 'desc')->paginate(20);

        // Additional diagnostics: SJs that have a tanda terima but are excluded from the paginated results
        $includedIds = $suratJalans->pluck('id')->toArray();
        $excludedByTandaTerimaExamples = (clone $baseQuery)
            ->whereHas('tandaTerima')
            ->whereNotIn('id', $includedIds)
            ->take(10)
            ->get(['id', 'no_surat_jalan', 'supir', 'status', 'rit', 'status_pembayaran_uang_rit']);

        return view('pranota-uang-rit.select-uang-jalan', compact('suratJalans', 'eligibleCount', 'pranotaUsedCount', 'finalFilteredCount', 'eligibleExamples', 'excludedByPranotaExamples', 'excludedByPaymentExamples', 'excludedByTandaTerimaExamples'));
    }

    /**
     * Create pranota from selected uang jalan
     */
    public function createFromSelection(Request $request)
    {
        $request->validate([
            'surat_jalan_ids' => 'required|array|min:1',
            'surat_jalan_ids.*' => 'exists:surat_jalans,id',
        ]);

        $suratJalans = SuratJalan::whereIn('id', $request->surat_jalan_ids)
            ->where(function($q) {
                $q->where('status', 'approved')
                  ->orWhere('status', 'sudah_checkpoint')
                  ->orWhere('status', 'active')
                  ->orWhereNotNull('tanggal_checkpoint')
                  ->orWhereHas('tandaTerima')
                  ->orWhereHas('approvals', function($sub) {
                      $sub->where('status', 'approved');
                  });
            })
            ->where(function($q) {
                $q->whereNotNull('tanggal_checkpoint')
                  ->orWhereHas('tandaTerima');
            })
            ->get();

        if ($suratJalans->isEmpty()) {
            return redirect()->route('pranota-uang-rit.select-uang-jalan')
                ->with('error', 'Data surat jalan tidak ditemukan atau tidak valid.');
        }

        if ($suratJalans->count() < count($request->surat_jalan_ids)) {
            return redirect()->route('pranota-uang-rit.select-uang-jalan')
                ->with('error', 'Beberapa surat jalan tidak valid atau tidak memenuhi kriteria (checkpoint supir atau Tanda Terima).');
        }

        return view('pranota-uang-rit.create-from-selection', compact('suratJalans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'surat_jalan_data' => 'sometimes|array',
            'surat_jalan_data.*.selected' => 'required',
            'surat_jalan_data.*.no_surat_jalan' => 'required|string|max:255',
            'surat_jalan_data.*.supir_nama' => 'required|string|max:255',
            'surat_jalan_data.*.kenek_nama' => 'nullable|string|max:255',
            'surat_jalan_data.*.uang_rit_supir' => 'required|numeric|min:0',
            'surat_jalan_bongkaran_data' => 'sometimes|array',
            'surat_jalan_bongkaran_data.*.selected' => 'required',
            'surat_jalan_bongkaran_data.*.no_surat_jalan' => 'required|string|max:255',
            'surat_jalan_bongkaran_data.*.supir_nama' => 'required|string|max:255',
            'surat_jalan_bongkaran_data.*.uang_rit_supir' => 'required|numeric|min:0',
            'supir_details' => 'sometimes|array', // Data hutang, tabungan, dan BPJS per supir
            'supir_details.*.hutang' => 'nullable|numeric|min:0',
            'supir_details.*.tabungan' => 'nullable|numeric|min:0',
            'supir_details.*.bpjs' => 'nullable|numeric|min:0',
        ], [
            'surat_jalan_data.required' => 'Silakan pilih minimal satu surat jalan.',
            'surat_jalan_data.min' => 'Silakan pilih minimal satu surat jalan.',
        ]);

        DB::beginTransaction();
        try {
            // Filter hanya data yang dipilih untuk regular surat jalan
            $selectedData = [];
            if ($request->has('surat_jalan_data')) {
                $selectedData = array_filter($request->surat_jalan_data, function($item) {
                    return isset($item['selected']) && $item['selected'];
                });
            }

            // Filter hanya data yang dipilih untuk surat jalan bongkaran
            $selectedBongkaranData = [];
            if ($request->has('surat_jalan_bongkaran_data')) {
                $selectedBongkaranData = array_filter($request->surat_jalan_bongkaran_data, function($item) {
                    return isset($item['selected']) && $item['selected'];
                });
            }

            if (empty($selectedData) && empty($selectedBongkaranData)) {
                return back()->withErrors(['surat_jalan_data' => 'Silakan pilih minimal satu surat jalan.'])->withInput();
            }

            // Generate nomor pranota DALAM transaksi yang sama
            $nomorPranota = $this->generateNomorPranota();
            
            // Hitung total per supir dan total keseluruhan
            $supirTotals = [];
            $totalUangSupirKeseluruhan = 0.0; // Initialize as float
            $totalHutangKeseluruhan = 0.0;
            $totalTabunganKeseluruhan = 0.0;
            $totalBpjsKeseluruhan = 0.0;

            // Debug: Log the selected data
            Log::info('Selected Data for Pranota Creation:', $selectedData);

            // Hitung total uang supir per supir
            foreach ($selectedData as $suratJalanId => $data) {
                $supirNama = $data['supir_nama'];
                $uangSupir = floatval($data['uang_rit_supir'] ?? 0); // Ensure it's a number
                
                Log::info("Processing Regular Supir: {$supirNama}, Uang Supir: {$uangSupir}");
                
                if (!isset($supirTotals[$supirNama])) {
                    $supirTotals[$supirNama] = [
                        'total_uang_supir' => 0.0,
                        'jumlah_rit' => 0,
                        'hutang' => 0.0,
                        'tabungan' => 0.0,
                        'bpjs' => 0.0,
                    ];
                }
                
                $supirTotals[$supirNama]['total_uang_supir'] += $uangSupir;
                $supirTotals[$supirNama]['jumlah_rit'] += 1;
                $totalUangSupirKeseluruhan += $uangSupir;
            }

            // Hitung total uang supir per supir untuk bongkaran
            foreach ($selectedBongkaranData as $suratJalanBongkaranId => $data) {
                $supirNama = $data['supir_nama'];
                $uangSupir = floatval($data['uang_rit_supir'] ?? 0);
                
                Log::info("Processing Bongkaran Supir: {$supirNama}, Uang Supir: {$uangSupir}");
                
                if (!isset($supirTotals[$supirNama])) {
                    $supirTotals[$supirNama] = [
                        'total_uang_supir' => 0.0,
                        'jumlah_rit' => 0,
                        'hutang' => 0.0,
                        'tabungan' => 0.0,
                        'bpjs' => 0.0,
                    ];
                }
                
                $supirTotals[$supirNama]['total_uang_supir'] += $uangSupir;
                $supirTotals[$supirNama]['jumlah_rit'] += 1;
                $totalUangSupirKeseluruhan += $uangSupir;
            }

            Log::info("Total Uang Supir Keseluruhan after calculation: {$totalUangSupirKeseluruhan}");

            // Ambil data hutang, tabungan, dan BPJS dari frontend
            $supirDetails = $request->input('supir_details', []);
            Log::info('Supir Details from request:', $supirDetails);
            
            foreach ($supirDetails as $supirNama => $details) {
                if (isset($supirTotals[$supirNama])) {
                    $supirTotals[$supirNama]['hutang'] = floatval($details['hutang'] ?? 0);
                    $supirTotals[$supirNama]['tabungan'] = floatval($details['tabungan'] ?? 0);
                    $supirTotals[$supirNama]['bpjs'] = floatval($details['bpjs'] ?? 0);
                    
                    $totalHutangKeseluruhan += $supirTotals[$supirNama]['hutang'];
                    $totalTabunganKeseluruhan += $supirTotals[$supirNama]['tabungan'];
                    $totalBpjsKeseluruhan += $supirTotals[$supirNama]['bpjs'];
                }
            }

            $grandTotalBersih = $totalUangSupirKeseluruhan - $totalHutangKeseluruhan - $totalTabunganKeseluruhan - $totalBpjsKeseluruhan;
            
            Log::info("Final calculations - Total Uang: {$totalUangSupirKeseluruhan}, Total Hutang: {$totalHutangKeseluruhan}, Total Tabungan: {$totalTabunganKeseluruhan}, Total BPJS: {$totalBpjsKeseluruhan}, Grand Total: {$grandTotalBersih}");

            // Buat SATU pranota untuk SEMUA surat jalan yang dipilih
            $grandTotalValue = $totalUangSupirKeseluruhan - $totalHutangKeseluruhan - $totalTabunganKeseluruhan - $totalBpjsKeseluruhan;
            
            // Gabungkan informasi dari semua surat jalan
            $allNoSuratJalan = [];
            $allSupirNama = [];
            $allKenekNama = [];
            $allNoPlat = [];
            $firstSuratJalanId = null;
            $firstSuratJalanBongkaranId = null;
            
            foreach ($selectedData as $suratJalanId => $data) {
                if ($firstSuratJalanId === null) {
                    $firstSuratJalanId = $suratJalanId; // Gunakan ID surat jalan pertama sebagai referensi
                }
                $allNoSuratJalan[] = $data['no_surat_jalan'];
                if (!in_array($data['supir_nama'], $allSupirNama)) {
                    $allSupirNama[] = $data['supir_nama'];
                }
                if (!empty($data['kenek_nama']) && !in_array($data['kenek_nama'], $allKenekNama)) {
                    $allKenekNama[] = $data['kenek_nama'];
                }
            }

            foreach ($selectedBongkaranData as $suratJalanBongkaranId => $data) {
                if ($firstSuratJalanBongkaranId === null) {
                    $firstSuratJalanBongkaranId = $suratJalanBongkaranId;
                }
                $allNoSuratJalan[] = $data['no_surat_jalan'] . ' (Bongkaran)';
                if (!in_array($data['supir_nama'], $allSupirNama)) {
                    $allSupirNama[] = $data['supir_nama'];
                }
            }
            
            // Gabungkan menjadi string
            $combinedNoSuratJalan = implode(', ', $allNoSuratJalan);
            $combinedSupirNama = implode(', ', $allSupirNama);
            $combinedKenekNama = implode(', ', array_filter($allKenekNama));
            
            Log::info("Creating SINGLE Pranota with values - Nomor: {$nomorPranota}, Total Uang: {$totalUangSupirKeseluruhan}, Total Hutang: {$totalHutangKeseluruhan}, Total Tabungan: {$totalTabunganKeseluruhan}, Total BPJS: {$totalBpjsKeseluruhan}, Grand Total: {$grandTotalValue}");
            
            // Buat satu record pranota untuk semua surat jalan
            $pranotaUangRit = PranotaUangRit::create([
                'no_pranota' => $nomorPranota,
                'tanggal' => $request->tanggal,
                'surat_jalan_id' => $firstSuratJalanId, // Referensi ke surat jalan pertama (bisa null jika hanya bongkaran)
                'surat_jalan_bongkaran_id' => $firstSuratJalanBongkaranId, // Referensi ke surat jalan bongkaran pertama
                'no_surat_jalan' => $combinedNoSuratJalan,
                'supir_nama' => $combinedSupirNama,
                'kenek_nama' => $combinedKenekNama ?: null,
                'uang_rit_supir' => $totalUangSupirKeseluruhan,
                'total_rit' => $totalUangSupirKeseluruhan,
                'total_uang' => $totalUangSupirKeseluruhan,
                'total_hutang' => $totalHutangKeseluruhan,
                'total_tabungan' => $totalTabunganKeseluruhan,
                'total_bpjs' => $totalBpjsKeseluruhan,
                'grand_total_bersih' => $grandTotalValue,
                'keterangan' => $request->keterangan,
                'status' => PranotaUangRit::STATUS_DRAFT,
                'created_by' => Auth::id(),
            ]);

            $createdPranota = [$pranotaUangRit];

            // Update status pembayaran uang rit pada SEMUA surat jalan yang dipilih
            foreach ($selectedData as $suratJalanId => $data) {
                $suratJalan = SuratJalan::find($suratJalanId);
                if ($suratJalan) {
                    $suratJalan->update([
                        'status_pembayaran_uang_rit' => SuratJalan::STATUS_UANG_RIT_SUDAH_MASUK_PRANOTA
                    ]);
                }
            }

            // Update status pembayaran uang rit pada SEMUA surat jalan bongkaran yang dipilih
            foreach ($selectedBongkaranData as $suratJalanBongkaranId => $data) {
                $suratJalanBongkaran = SuratJalanBongkaran::find($suratJalanBongkaranId);
                if ($suratJalanBongkaran) {
                    $suratJalanBongkaran->update([
                        'status_pembayaran_uang_rit' => 'lunas' // Gunakan nilai enum yang sesuai dengan tabel bongkaran
                    ]);
                }
            }

            // Simpan detail per supir
            foreach ($supirTotals as $supirNama => $totals) {
                PranotaUangRitSupirDetail::create([
                    'no_pranota' => $nomorPranota,
                    'supir_nama' => $supirNama,
                    'jumlah_rit' => intval($totals['jumlah_rit']),
                    'total_uang_supir' => floatval($totals['total_uang_supir']),
                    'hutang' => floatval($totals['hutang']),
                    'tabungan' => floatval($totals['tabungan']),
                    'bpjs' => floatval($totals['bpjs']),
                    // grand_total akan dihitung otomatis di model
                ]);
            }

            // Simpan detail per surat jalan (untuk tracking)
            foreach ($selectedData as $suratJalanId => $data) {
                // Buat record detail surat jalan jika ada model untuk itu
                // Atau bisa menggunakan tabel pivot/relation table
                Log::info("Pranota {$nomorPranota} includes Surat Jalan: {$data['no_surat_jalan']} - {$data['supir_nama']} - Rp " . number_format($data['uang_rit_supir']));
            }

            DB::commit();
            
            $jumlahSuratJalan = count($selectedData);
            $jumlahSupir = count($supirTotals);
            $message = "Pranota Uang Rit {$nomorPranota} berhasil dibuat untuk {$jumlahSuratJalan} surat jalan dengan {$jumlahSupir} supir!";
                
            return redirect()->route('pranota-uang-rit.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Pranota Uang Rit: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat Pranota Uang Rit: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PranotaUangRit $pranotaUangRit)
    {
        // Load necessary relationships
        $pranotaUangRit->load(['suratJalan', 'creator', 'updater', 'approver']);
        
        // Parse multiple surat jalan from combined field (since we now store combined data)
        $suratJalanNomors = explode(', ', $pranotaUangRit->no_surat_jalan);
        $suratJalanSupirs = explode(', ', $pranotaUangRit->supir_nama);
        
        // Create grouped pranota data from the combined stored data
        $groupedPranota = collect();
        foreach ($suratJalanNomors as $index => $nomor) {
            $supir = $suratJalanSupirs[$index] ?? $suratJalanSupirs[0];
            $groupedPranota->push((object)[
                'no_surat_jalan' => trim($nomor),
                'supir_nama' => trim($supir),
                'tanggal' => $pranotaUangRit->tanggal,
                'uang_rit_supir' => $pranotaUangRit->uang_rit_supir / count($suratJalanNomors), // Split evenly
            ]);
        }
            
        // Get supir details for this pranota
        $supirDetails = PranotaUangRitSupirDetail::where('no_pranota', $pranotaUangRit->no_pranota)
            ->orderBy('supir_nama')
            ->get();
            
        return view('pranota-uang-rit.show', compact('pranotaUangRit', 'groupedPranota', 'supirDetails'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PranotaUangRit $pranotaUangRit)
    {
        // Only allow editing if status is draft or submitted
        if (!in_array($pranotaUangRit->status, [PranotaUangRit::STATUS_DRAFT, PranotaUangRit::STATUS_SUBMITTED])) {
            return redirect()->route('pranota-uang-rit.index')
                ->with('error', 'Pranota Uang Rit dengan status ' . $pranotaUangRit->status_label . ' tidak dapat diedit.');
        }

        // Get surat jalan numbers from the combined field
        $suratJalanNomors = explode(', ', $pranotaUangRit->no_surat_jalan);
        
        // Get surat jalans that are part of this pranota
        $suratJalans = SuratJalan::whereIn('no_surat_jalan', $suratJalanNomors)
            ->orderBy('tanggal_surat_jalan', 'desc')
            ->get();
        
        // Get supir details for this pranota
        $supirDetails = PranotaUangRitSupirDetail::where('no_pranota', $pranotaUangRit->no_pranota)
            ->orderBy('supir_nama')
            ->get();

        return view('pranota-uang-rit.edit', compact('pranotaUangRit', 'suratJalans', 'supirDetails'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PranotaUangRit $pranotaUangRit)
    {
        // Only allow updating if status is draft or submitted
        if (!in_array($pranotaUangRit->status, [PranotaUangRit::STATUS_DRAFT, PranotaUangRit::STATUS_SUBMITTED])) {
            return redirect()->route('pranota-uang-rit.index')
                ->with('error', 'Pranota Uang Rit dengan status ' . $pranotaUangRit->status_label . ' tidak dapat diupdate.');
        }

        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'supir_details' => 'sometimes|array',
            'supir_details.*.hutang' => 'nullable|numeric|min:0',
            'supir_details.*.tabungan' => 'nullable|numeric|min:0',
            'supir_details.*.bpjs' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Update supir details
            $supirDetails = $request->input('supir_details', []);
            $totalHutangKeseluruhan = 0.0;
            $totalTabunganKeseluruhan = 0.0;
            $totalBpjsKeseluruhan = 0.0;
            
            foreach ($supirDetails as $supirNama => $details) {
                $hutang = floatval($details['hutang'] ?? 0);
                $tabungan = floatval($details['tabungan'] ?? 0);
                $bpjs = floatval($details['bpjs'] ?? 0);
                
                // Update detail per supir
                PranotaUangRitSupirDetail::where('no_pranota', $pranotaUangRit->no_pranota)
                    ->where('supir_nama', $supirNama)
                    ->update([
                        'hutang' => $hutang,
                        'tabungan' => $tabungan,
                        'bpjs' => $bpjs,
                        // grand_total akan dihitung otomatis di model
                    ]);
                
                $totalHutangKeseluruhan += $hutang;
                $totalTabunganKeseluruhan += $tabungan;
                $totalBpjsKeseluruhan += $bpjs;
            }
            
            // Calculate new grand total
            $grandTotalBersih = $pranotaUangRit->total_uang - $totalHutangKeseluruhan - $totalTabunganKeseluruhan - $totalBpjsKeseluruhan;
            
            // Update pranota
            $pranotaUangRit->update([
                'tanggal' => $request->tanggal,
                'total_hutang' => $totalHutangKeseluruhan,
                'total_tabungan' => $totalTabunganKeseluruhan,
                'total_bpjs' => $totalBpjsKeseluruhan,
                'grand_total_bersih' => $grandTotalBersih,
                'keterangan' => $request->keterangan,
                'updated_by' => Auth::id(),
            ]);

            Log::info('Pranota Uang Rit updated', [
                'pranota_id' => $pranotaUangRit->id,
                'no_pranota' => $pranotaUangRit->no_pranota,
                'updated_by' => Auth::user()->name,
            ]);

            DB::commit();
            return redirect()->route('pranota-uang-rit.index')
                ->with('success', 'Pranota Uang Rit berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating Pranota Uang Rit: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui Pranota Uang Rit: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PranotaUangRit $pranotaUangRit)
    {
        // Only allow deletion if status is draft
        if ($pranotaUangRit->status !== PranotaUangRit::STATUS_DRAFT) {
            return redirect()->route('pranota-uang-rit.index')
                ->with('error', 'Hanya Pranota Uang Rit dengan status Draft yang dapat dihapus.');
        }

        try {
            // Ambil no_surat_jalan untuk mencari surat jalan yang terkait
            $noSuratJalanList = explode(', ', $pranotaUangRit->no_surat_jalan);
            
            // Kembalikan status surat jalan biasa ke 'belum_dibayar'
            SuratJalan::whereIn('no_surat_jalan', $noSuratJalanList)
                ->where('status_pembayaran_uang_rit', SuratJalan::STATUS_UANG_RIT_SUDAH_MASUK_PRANOTA)
                ->update([
                    'status_pembayaran_uang_rit' => SuratJalan::STATUS_UANG_RIT_BELUM_DIBAYAR
                ]);

            // Kembalikan status surat jalan bongkaran ke 'belum_bayar'
            SuratJalanBongkaran::whereIn('nomor_surat_jalan', $noSuratJalanList)
                ->where('status_pembayaran_uang_rit', 'lunas')
                ->update([
                    'status_pembayaran_uang_rit' => 'belum_bayar'
                ]);

            // Hapus pranota
            $pranotaUangRit->delete();

            Log::info('Pranota Uang Rit deleted and surat jalan status reset', [
                'pranota_id' => $pranotaUangRit->id,
                'no_pranota' => $pranotaUangRit->no_pranota,
                'no_surat_jalan_list' => $noSuratJalanList,
                'deleted_by' => Auth::user()->name,
            ]);

            return redirect()->route('pranota-uang-rit.index')
                ->with('success', 'Pranota Uang Rit berhasil dihapus dan status surat jalan telah dikembalikan!');

        } catch (\Exception $e) {
            Log::error('Error deleting Pranota Uang Rit: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus Pranota Uang Rit: ' . $e->getMessage());
        }
    }

    /**
     * Submit pranota for approval
     */
    public function submit(PranotaUangRit $pranotaUangRit)
    {
        if ($pranotaUangRit->status !== PranotaUangRit::STATUS_DRAFT) {
            return redirect()->route('pranota-uang-rit.index')
                ->with('error', 'Hanya Pranota Uang Rit dengan status Draft yang dapat disubmit.');
        }

        try {
            $pranotaUangRit->update([
                'status' => PranotaUangRit::STATUS_SUBMITTED,
                'updated_by' => Auth::id(),
            ]);

            // Update status surat jalan
            if ($pranotaUangRit->suratJalan) {
                $pranotaUangRit->suratJalan->update([
                    'status_pembayaran_uang_rit' => SuratJalan::STATUS_UANG_RIT_PRANOTA_SUBMITTED
                ]);
            }

            Log::info('Pranota Uang Rit submitted', [
                'pranota_id' => $pranotaUangRit->id,
                'no_pranota' => $pranotaUangRit->no_pranota,
                'submitted_by' => Auth::user()->name,
            ]);

            return redirect()->route('pranota-uang-rit.index')
                ->with('success', 'Pranota Uang Rit berhasil disubmit untuk approval!');

        } catch (\Exception $e) {
            Log::error('Error submitting Pranota Uang Rit: ' . $e->getMessage());
            return back()->with('error', 'Gagal submit Pranota Uang Rit: ' . $e->getMessage());
        }
    }

    /**
     * Approve pranota
     */
    public function approve(PranotaUangRit $pranotaUangRit)
    {
        if ($pranotaUangRit->status !== PranotaUangRit::STATUS_SUBMITTED) {
            return redirect()->route('pranota-uang-rit.index')
                ->with('error', 'Hanya Pranota Uang Rit dengan status Submitted yang dapat diapprove.');
        }

        try {
            $pranotaUangRit->update([
                'status' => PranotaUangRit::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            // Update status surat jalan
            if ($pranotaUangRit->suratJalan) {
                $pranotaUangRit->suratJalan->update([
                    'status_pembayaran_uang_rit' => SuratJalan::STATUS_UANG_RIT_PRANOTA_APPROVED
                ]);
            }

            Log::info('Pranota Uang Rit approved', [
                'pranota_id' => $pranotaUangRit->id,
                'no_pranota' => $pranotaUangRit->no_pranota,
                'approved_by' => Auth::user()->name,
            ]);

            return redirect()->route('pranota-uang-rit.index')
                ->with('success', 'Pranota Uang Rit berhasil diapprove!');

        } catch (\Exception $e) {
            Log::error('Error approving Pranota Uang Rit: ' . $e->getMessage());
            return back()->with('error', 'Gagal approve Pranota Uang Rit: ' . $e->getMessage());
        }
    }

    /**
     * Mark pranota as paid
     */
    public function markAsPaid(Request $request, PranotaUangRit $pranotaUangRit)
    {
        if ($pranotaUangRit->status !== PranotaUangRit::STATUS_APPROVED) {
            return redirect()->route('pranota-uang-rit.index')
                ->with('error', 'Hanya Pranota Uang Rit dengan status Approved yang dapat dimark sebagai Paid.');
        }

        $request->validate([
            'tanggal_bayar' => 'required|date',
        ]);

        try {
            $pranotaUangRit->update([
                'status' => PranotaUangRit::STATUS_PAID,
                'tanggal_bayar' => $request->tanggal_bayar,
                'updated_by' => Auth::id(),
            ]);

            // Update status surat jalan menjadi dibayar
            if ($pranotaUangRit->suratJalan) {
                $pranotaUangRit->suratJalan->update([
                    'status_pembayaran_uang_rit' => SuratJalan::STATUS_UANG_RIT_DIBAYAR
                ]);
            }

            Log::info('Pranota Uang Rit marked as paid', [
                'pranota_id' => $pranotaUangRit->id,
                'no_pranota' => $pranotaUangRit->no_pranota,
                'tanggal_bayar' => $request->tanggal_bayar,
                'updated_by' => Auth::user()->name,
            ]);

            return redirect()->route('pranota-uang-rit.index')
                ->with('success', 'Pranota Uang Rit berhasil dimark sebagai Paid!');

        } catch (\Exception $e) {
            Log::error('Error marking Pranota Uang Rit as paid: ' . $e->getMessage());
            return back()->with('error', 'Gagal mark Pranota Uang Rit sebagai Paid: ' . $e->getMessage());
        }
    }

    /**
     * Generate nomor pranota otomatis
     * Format: PUR-MM-YY-XXXXXX
     * - PUR: 3 digit kode modul 
     * - MM: 2 digit bulan
     * - YY: 2 digit tahun
     * - XXXXXX: 6 digit nomor terakhir dari master nomor terakhir modul PUR
     */
    private function generateNomorPranota()
    {
        $date = now();
        $bulan = $date->format('m'); // 2 digit bulan
        $tahun = $date->format('y'); // 2 digit tahun
        
        // Cari nomor tertinggi yang sudah ada di database untuk format bulan-tahun ini
        $lastExisting = PranotaUangRit::where('no_pranota', 'like', "PUR-{$bulan}-{$tahun}-%")
            ->orderBy('no_pranota', 'desc')
            ->first();
            
        $lastNumber = 0;
        if ($lastExisting) {
            // Extract nomor dari format PUR-MM-YY-XXXXXX
            $parts = explode('-', $lastExisting->no_pranota);
            if (count($parts) >= 4) {
                $lastNumber = (int) end($parts);
            }
        }
        
        // Lock record untuk mencegah race condition
        $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'PUR')->lockForUpdate()->first();

        if (!$nomorTerakhir) {
            // Buat record baru, sinkron dengan database yang sudah ada
            $nomorBaru = max($lastNumber + 1, 1);
            \App\Models\NomorTerakhir::create([
                'modul' => 'PUR',
                'nomor_terakhir' => $nomorBaru,
                'keterangan' => 'Auto generated Pranota Uang Rit number'
            ]);
        } else {
            // Update dengan nomor yang lebih tinggi dari existing atau nomor_terakhir
            $nomorBaru = max($nomorTerakhir->nomor_terakhir + 1, $lastNumber + 1);
            $nomorTerakhir->update(['nomor_terakhir' => $nomorBaru]);
        }

        // Format 6 digit nomor urut
        $sequence = str_pad($nomorBaru, 6, '0', STR_PAD_LEFT);
        $nomorPranota = "PUR-{$bulan}-{$tahun}-{$sequence}";
        
        // Double check - jika masih ada duplicate, increment lagi
        while (PranotaUangRit::where('no_pranota', $nomorPranota)->exists()) {
            $nomorBaru++;
            $sequence = str_pad($nomorBaru, 6, '0', STR_PAD_LEFT);
            $nomorPranota = "PUR-{$bulan}-{$tahun}-{$sequence}";
            
            // Update nomor_terakhir dengan nomor yang benar
            if ($nomorTerakhir) {
                $nomorTerakhir->update(['nomor_terakhir' => $nomorBaru]);
            }
        }
        
        Log::info("Generated Pranota Number: {$nomorPranota}");
        return $nomorPranota;
    }

    /**
     * Print ritasi supir format
     */
    public function print(PranotaUangRit $pranotaUangRit)
    {
        // Load necessary relationships
        $pranotaUangRit->load(['suratJalan', 'creator', 'updater', 'approver']);
        
        // Parse multiple surat jalan from combined field (since we now store combined data)
        $suratJalanNomors = explode(', ', $pranotaUangRit->no_surat_jalan);
        $suratJalanSupirs = explode(', ', $pranotaUangRit->supir_nama);
        
        // Create grouped pranota data from the combined stored data
        $groupedPranota = collect();
        foreach ($suratJalanNomors as $index => $nomor) {
            $supir = $suratJalanSupirs[$index] ?? $suratJalanSupirs[0];
            $groupedPranota->push((object)[
                'no_surat_jalan' => trim($nomor),
                'supir_nama' => trim($supir),
                'tanggal' => $pranotaUangRit->tanggal,
                'uang_rit_supir' => $pranotaUangRit->uang_rit_supir / count($suratJalanNomors), // Split evenly
            ]);
        }
            
        // Get supir details for this pranota
        $supirDetails = PranotaUangRitSupirDetail::where('no_pranota', $pranotaUangRit->no_pranota)
            ->orderBy('supir_nama')
            ->get();
            
        return view('pranota-uang-rit.print', compact('pranotaUangRit', 'groupedPranota', 'supirDetails'));
    }

    /**
     * Export single pranota to Excel
     */
    public function exportSingle(PranotaUangRit $pranotaUangRit)
    {
        // Load necessary relationships
        $pranotaUangRit->load(['suratJalan', 'creator', 'updater', 'approver']);
        
        // Get supir details for this pranota with karyawan data
        $supirDetails = PranotaUangRitSupirDetail::where('no_pranota', $pranotaUangRit->no_pranota)
            ->orderBy('supir_nama')
            ->get();

        // Get NIK from karyawans table for each supir
        foreach ($supirDetails as $detail) {
            $karyawan = \App\Models\Karyawan::where('nama', $detail->supir_nama)->first();
            $detail->nik = $karyawan ? $karyawan->nik : '-';
        }

        // Create Excel file using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'RITASI SUPIR');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Pranota info
        $sheet->setCellValue('A3', 'GAJI SUPIR TGL: ' . $pranotaUangRit->tanggal->format('d M Y'));
        $sheet->setCellValue('I3', $pranotaUangRit->no_pranota);
        $sheet->getStyle('I3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Detail supir headers
        $row = 5;
        $sheet->setCellValue('A' . $row, 'No');
        $sheet->setCellValue('B' . $row, 'NIK');
        $sheet->setCellValue('C' . $row, 'Supir');
        $sheet->setCellValue('D' . $row, 'Rit');
        $sheet->setCellValue('E' . $row, 'Total Uang');
        $sheet->setCellValue('F' . $row, 'Hutang');
        $sheet->setCellValue('G' . $row, 'Tabungan');
        $sheet->setCellValue('H' . $row, 'BPJS');
        $sheet->setCellValue('I' . $row, 'Grand Total');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ]
        ];
        $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($headerStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);   // No
        $sheet->getColumnDimension('B')->setWidth(8);   // NIK
        $sheet->getColumnDimension('C')->setWidth(28);  // Supir
        $sheet->getColumnDimension('D')->setWidth(6);   // Rit
        $sheet->getColumnDimension('E')->setWidth(15);  // Total Uang
        $sheet->getColumnDimension('F')->setWidth(12);  // Hutang
        $sheet->getColumnDimension('G')->setWidth(12);  // Tabungan
        $sheet->getColumnDimension('H')->setWidth(10);  // BPJS
        $sheet->getColumnDimension('I')->setWidth(15);  // Grand Total

        // Fill data
        $row++;
        $no = 1;
        $totalRit = 0;
        $totalUang = 0;
        $totalHutang = 0;
        $totalTabungan = 0;
        $totalBpjs = 0;
        $totalGrand = 0;

        foreach ($supirDetails as $detail) {
            $rit = $detail->total_uang_supir > 0 ? round($detail->total_uang_supir / 85000) : 0;
            
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $detail->nik ?? '-');
            $sheet->setCellValue('C' . $row, strtoupper($detail->supir_nama));
            $sheet->setCellValue('D' . $row, $rit);
            $sheet->setCellValue('E' . $row, $detail->total_uang_supir);
            $sheet->setCellValue('F' . $row, $detail->hutang);
            $sheet->setCellValue('G' . $row, $detail->tabungan);
            $sheet->setCellValue('H' . $row, $detail->bpjs);
            $sheet->setCellValue('I' . $row, $detail->grand_total);

            // Format currency
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('#,##0');

            // Center align for No, NIK, Rit
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Right align for currency columns
            $sheet->getStyle('E' . $row . ':I' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            // Add border to data rows
            $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);

            $totalRit += $rit;
            $totalUang += $detail->total_uang_supir;
            $totalHutang += $detail->hutang;
            $totalTabungan += $detail->tabungan;
            $totalBpjs += $detail->bpjs;
            $totalGrand += $detail->grand_total;

            $row++;
            $no++;
        }

        // Total row
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->setCellValue('D' . $row, $totalRit);
        $sheet->setCellValue('E' . $row, $totalUang);
        $sheet->setCellValue('F' . $row, $totalHutang);
        $sheet->setCellValue('G' . $row, $totalTabungan);
        $sheet->setCellValue('H' . $row, $totalBpjs);
        $sheet->setCellValue('I' . $row, $totalGrand);

        // Format total row
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getStyle('A' . $row . ':I' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':I' . $row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F5F5F5');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E' . $row . ':I' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Create writer and download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'RITASI_SUPIR_' . $pranotaUangRit->no_pranota . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export selected surat jalan to Excel
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'selected_data' => 'required|json'
        ]);

        $selectedData = json_decode($request->selected_data, true);

        if (empty($selectedData)) {
            return back()->with('error', 'Tidak ada surat jalan yang dipilih.');
        }

        // Create Excel file using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'No. Surat Jalan');
        $sheet->setCellValue('C1', 'Nama Supir');
        $sheet->setCellValue('D1', 'Tanggal Checkpoint');
        $sheet->setCellValue('E1', 'Pengirim');
        $sheet->setCellValue('F1', 'Tujuan Pengambilan');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);

        // Fill data
        $row = 2;
        foreach ($selectedData as $index => $data) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $data['no_surat_jalan'] ?? '-');
            $sheet->setCellValue('C' . $row, $data['supir'] ?? '-');
            
            // Format tanggal dengan prioritas: checkpoint -> tanda terima -> tanda terima bongkaran
            $tanggalDisplay = '-';
            
            // Coba ambil dari tanggal checkpoint dulu
            if (!empty($data['tanggal_checkpoint'])) {
                try {
                    $tanggalDisplay = \Carbon\Carbon::parse($data['tanggal_checkpoint'])->format('d/m/Y');
                } catch (\Exception $e) {
                    $tanggalDisplay = '-';
                }
            }
            
            // Jika checkpoint tidak ada, coba tanda terima
            if ($tanggalDisplay === '-' && !empty($data['tanggal_tanda_terima'])) {
                try {
                    $tanggalDisplay = \Carbon\Carbon::parse($data['tanggal_tanda_terima'])->format('d/m/Y');
                } catch (\Exception $e) {
                    $tanggalDisplay = '-';
                }
            }
            
            // Jika masih tidak ada, coba tanda terima bongkaran
            if ($tanggalDisplay === '-' && !empty($data['tanggal_tanda_terima_bongkaran'])) {
                try {
                    $tanggalDisplay = \Carbon\Carbon::parse($data['tanggal_tanda_terima_bongkaran'])->format('d/m/Y');
                } catch (\Exception $e) {
                    $tanggalDisplay = '-';
                }
            }
            
            $sheet->setCellValue('D' . $row, $tanggalDisplay);
            $sheet->setCellValue('E' . $row, $data['pengirim'] ?? '-');
            $sheet->setCellValue('F' . $row, $data['tujuan_pengambilan'] ?? '-');
            
            // Add border to data rows
            $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
            
            $row++;
        }

        // Add border to header
        $sheet->getStyle('A1:F1')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ]
        ]);

        // Create writer and download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Surat_Jalan_Pranota_' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
