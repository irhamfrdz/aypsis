<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Karyawan;
use App\Models\Coa;
use App\Models\RealisasiUangMuka;
use App\Models\PembayaranUangMuka;
use App\Models\Mobil;
use App\Models\NomorTerakhir;
use App\Models\CoaTransaction;
use Illuminate\Support\Facades\Log;

class RealisasiUangMukaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Query realisasi uang muka dengan relationships
        $query = RealisasiUangMuka::with(['kasBankAkun', 'pembuatPembayaran', 'penyetujuPembayaran'])
                                 ->orderBy('tanggal_pembayaran', 'desc');

        // Filter berdasarkan nomor pembayaran
        if ($request->filled('nomor_pembayaran')) {
            $query->where('nomor_pembayaran', 'like', '%' . $request->nomor_pembayaran . '%');
        }

        // Filter berdasarkan supir (search dalam JSON array)
        if ($request->filled('supir')) {
            $supirId = $request->supir;
            $query->whereJsonContains('supir_ids', $supirId);
        }

        // Filter berdasarkan tanggal pembayaran
        if ($request->filled('tanggal_pembayaran')) {
            $query->whereDate('tanggal_pembayaran', $request->tanggal_pembayaran);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $realisasiList = $query->paginate(20);

        // Ambil data karyawan supir untuk dropdown pencarian
        $supirList = Karyawan::whereRaw('LOWER(divisi) = ?', ['supir'])
                            ->where('status', 'active')
                            ->orderBy('nama_lengkap')
                            ->get();

        return view('realisasi-uang-muka.index', [
            'title' => 'Realisasi Uang Muka',
            'realisasiList' => $realisasiList,
            'supirList' => $supirList
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil data karyawan yang mempunyai divisi supir
        $supirList = Karyawan::whereRaw('LOWER(divisi) = ?', ['supir'])
                            ->where('status', 'active') // hanya karyawan aktif
                            ->orderBy('nama_lengkap')
                            ->get();

        // Ambil semua data karyawan aktif untuk penerima (Amprahan, Solar, dll)
        $karyawanList = Karyawan::where('status', 'active')
                               ->orderBy('nama_lengkap')
                               ->get();

        // Ambik data mobil
        $mobilList = Mobil::orderBy('nomor_polisi')->get();

        // Ambil data akun kas/bank dari COA
        $kasBankList = Coa::where('tipe_akun', 'Kas/Bank')
                          ->orderBy('nomor_akun')
                          ->get();

        // Get Uang Muka yang belum direalisasi
        $uangMukaBelumRealisasiList = PembayaranUangMuka::where('status', 'uang_muka_belum_terpakai')
                                  ->with(['penerima', 'mobil'])
                                  ->orderBy('tanggal_pembayaran', 'desc')
                                  ->get();

        // Enrich Uang Muka data dengan nama supir
        foreach ($uangMukaBelumRealisasiList as $uangMuka) {
            // Ensure supir_ids is an array (handle double-encoded JSON from old data)
            $supirIds = $uangMuka->getAttributes()['supir_ids'] ?? null;
            if (is_string($supirIds)) {
                // Try to decode once
                $decoded = json_decode($supirIds, true);
                // If still a string after first decode, decode again (double-encoded)
                if (is_string($decoded)) {
                    $decoded = json_decode($decoded, true);
                }
                $uangMuka->supir_ids = is_array($decoded) ? $decoded : [];
            } elseif (is_null($supirIds) || !is_array($uangMuka->supir_ids)) {
                $uangMuka->supir_ids = [];
            }
            
            // Get supir names AFTER ensuring supir_ids is array
            $uangMuka->supir_names = $uangMuka->supirList()->pluck('nama_lengkap')->toArray();
            
            // Ensure jumlah_per_supir is an array (handle double-encoded JSON)
            $jumlahPerSupir = $uangMuka->getAttributes()['jumlah_per_supir'] ?? null;
            if (is_string($jumlahPerSupir)) {
                // Try to decode once
                $decoded = json_decode($jumlahPerSupir, true);
                // If still a string after first decode, decode again (double-encoded)
                if (is_string($decoded)) {
                    $decoded = json_decode($decoded, true);
                }
                $uangMuka->jumlah_per_supir = is_array($decoded) ? $decoded : [];
            } elseif (is_null($jumlahPerSupir) || !is_array($uangMuka->jumlah_per_supir)) {
                $uangMuka->jumlah_per_supir = [];
            }
        }

        return view('realisasi-uang-muka.create', [
            'title' => 'Tambah Realisasi Uang Muka',
            'supirList' => $supirList,
            'karyawanList' => $karyawanList,
            'mobilList' => $mobilList,
            'kasBankList' => $kasBankList,
            'uangMukaBelumRealisasiList' => $uangMukaBelumRealisasiList
        ]);
    }

    /**
     * Generate nomor pembayaran preview (tidak increment nomor terakhir)
     */
    public function generateNomor(Request $request)
    {
        try {
            return response()->json($this->generateUniqueNomor($request->kas_bank_id));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Force generate new nomor pembayaran (will increment counter)
     */
    public function forceGenerateNomor(Request $request)
    {
        try {
            return response()->json($this->generateUniqueNomor($request->kas_bank_id, true));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique nomor pembayaran dengan increment counter
     */
    private function generateUniqueNomor($kasBankId, $actualGenerate = false)
    {
        $today = now();
        $tahun = $today->format('y'); // 2 digit year
        $bulan = $today->format('m'); // 2 digit month

        // Get COA info untuk kode bank
        $coa = \App\Models\Coa::find($kasBankId);
        if (!$coa) {
            return [
                'success' => false,
                'message' => 'Bank/Kas tidak ditemukan'
            ];
        }

        // Ambil kode_nomor dari COA sebagai kode bank
        $kodeBank = $coa->kode_nomor ?? '000';

        // Get next running number from master nomor terakhir dengan lock untuk thread safety
        $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'nomor_pembayaran')
            ->lockForUpdate()
            ->first();

        if (!$nomorTerakhir) {
            return [
                'success' => false,
                'message' => 'Modul nomor_pembayaran tidak ditemukan di master nomor terakhir'
            ];
        }

        // Loop untuk mencari nomor yang belum digunakan
        $maxAttempts = 20; // Increased attempts
        $attempt = 0;

        do {
            $nextNumber = $nomorTerakhir->nomor_terakhir + 1 + $attempt;
            $sequence = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            $nomorPembayaran = "{$kodeBank}-{$bulan}-{$tahun}-{$sequence}";

            // Check if nomor already exists in related payment tables
            $existsInPembayaran = \App\Models\PembayaranUangMuka::where('nomor_pembayaran', $nomorPembayaran)->exists();
            $existsInRealisasi = \App\Models\RealisasiUangMuka::where('nomor_pembayaran', $nomorPembayaran)->exists();

            // Check pembayaran_obs table (OB payments) if it exists
            $existsInPembayaranObs = false;
            try {
                $existsInPembayaranObs = DB::table('pembayaran_obs')->where('nomor_pembayaran', $nomorPembayaran)->exists();
            } catch (\Exception $e) {
                // If table doesn't exist, just continue
                Log::info('pembayaran_obs table not found or accessible, skipping check');
            }

            if (!$existsInPembayaran && !$existsInRealisasi && !$existsInPembayaranObs) {
                // Nomor unik ditemukan
                if ($actualGenerate) {
                    // Update counter jika ini actual generate (bukan preview)
                    // Use the highest number we found to avoid conflicts
                    $finalNumber = $nextNumber;
                    $nomorTerakhir->update(['nomor_terakhir' => $finalNumber]);

                    Log::info('RealisasiUangMuka Generate Nomor - Success:', [
                        'nomor_pembayaran' => $nomorPembayaran,
                        'next_number' => $finalNumber,
                        'attempt' => $attempt + 1
                    ]);
                }

                return [
                    'success' => true,
                    'nomor_pembayaran' => $nomorPembayaran,
                    'preview' => !$actualGenerate
                ];
            }

            $attempt++;
        } while ($attempt < $maxAttempts);

        // Jika tidak bisa generate nomor unik, log for debugging
        Log::error('RealisasiUangMuka Generate Nomor - Failed:', [
            'kas_bank_id' => $kasBankId,
            'kode_bank' => $kodeBank,
            'current_nomor_terakhir' => $nomorTerakhir->nomor_terakhir,
            'max_attempts' => $maxAttempts
        ]);

        return [
            'success' => false,
            'message' => "Tidak dapat generate nomor unik setelah {$maxAttempts} percobaan. Silakan coba lagi atau hubungi administrator."
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug: Return debug response if in debug mode (FIRST thing to check!)
        if ($request->has('debug_mode')) {
            try {
                // Analyze input for debugging
                $input = $request->all();

                $penerimaFields = [];
                $jumlahKaryawanFields = [];

                foreach ($input as $key => $value) {
                    if (strpos($key, 'penerima') === 0) {
                        $penerimaFields[$key] = $value;
                    } elseif (strpos($key, 'jumlah_karyawan') === 0) {
                        $jumlahKaryawanFields[$key] = $value;
                    }
                }

                // Get activity info for debugging
                $kegiatan = null;
                $isMobilKegiatan = false;
                $isSupirKegiatan = false;
                $isPenerimaKegiatan = false;

                if (isset($input['kegiatan'])) {
                    $kegiatan = MasterKegiatan::find($input['kegiatan']);
                    if ($kegiatan) {
                        $isMobilKegiatan = $this->isMobilBasedActivity($kegiatan);
                        $isSupirKegiatan = $this->isSupirBasedActivity($kegiatan);
                        $isPenerimaKegiatan = !$isMobilKegiatan && !$isSupirKegiatan;
                    }
                }

                $debugResponse = [
                    'status' => 'debug',
                    'message' => 'RealisasiUangMukaController reached successfully',
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user() ? Auth::user()->name : 'No user',
                    'permissions' => Auth::user() ? Auth::user()->getAllPermissions()->pluck('name') : [],
                    'method' => $request->method(),
                    'route' => $request->route() ? $request->route()->getName() : 'No route',
                    'url' => $request->url(),
                    'middleware' => $request->route() ? $request->route()->middleware() : [],
                    'activity_analysis' => [
                        'kegiatan_id' => $kegiatan ? $kegiatan->id : null,
                        'kegiatan_nama' => $kegiatan ? $kegiatan->nama_kegiatan : null,
                        'is_mobil_based' => $isMobilKegiatan,
                        'is_supir_based' => $isSupirKegiatan,
                        'is_penerima_based' => $isPenerimaKegiatan
                    ],
                    'form_analysis' => [
                        'penerima_fields_count' => count($penerimaFields),
                        'jumlah_karyawan_fields_count' => count($jumlahKaryawanFields),
                        'penerima_fields' => $penerimaFields,
                        'jumlah_karyawan_fields' => $jumlahKaryawanFields,
                        'validation_should_pass' => count($penerimaFields) > 0 && count($jumlahKaryawanFields) > 0
                    ],
                    'basic_validation' => [
                        'has_kegiatan' => isset($input['kegiatan']),
                        'has_nomor_pembayaran' => isset($input['nomor_pembayaran']),
                        'has_tanggal_pembayaran' => isset($input['tanggal_pembayaran']),
                        'has_kas_bank' => isset($input['kas_bank']),
                        'has_jenis_transaksi' => isset($input['jenis_transaksi'])
                    ],
                    'input_count' => count($input)
                ];

                // Log debug response to Laravel log file for easy copying
                Log::info('RealisasiUangMuka Debug Response:', $debugResponse);

                return response()->json($debugResponse);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'debug_error',
                    'message' => 'Error in debug mode: ' . $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Debug: Log semua input yang diterima
        Log::info('RealisasiUangMuka Store - Input Data:', $request->all());

        // Determine input type based on what is present
        $isMobilInput = $request->has('mobil') && is_array($request->mobil) && count($request->mobil) > 0;
        $isSupirInput = $request->has('supir') && is_array($request->supir) && count($request->supir) > 0;
        $isPenerimaInput = $request->has('penerima') && is_array($request->penerima) && count($request->penerima) > 0;

        Log::info('RealisasiUangMuka Store - Input Analysis:', [
            'isMobilInput' => $isMobilInput,
            'isSupirInput' => $isSupirInput,
            'isPenerimaInput' => $isPenerimaInput
        ]);

        // Base validation rules
        $validationRules = [
            'nomor_pembayaran' => 'required|string|max:255|unique:realisasi_uang_muka,nomor_pembayaran',
            'tanggal_pembayaran' => 'required|date',
            'kas_bank' => 'required|exists:akun_coa,id',
            'jenis_transaksi' => 'required|in:debit,kredit',
            'keterangan' => 'nullable|string',
            'pembayaran_uang_muka_id' => 'nullable|exists:pembayaran_uang_muka,id',
            'nomor_voyage' => 'nullable|string'
        ];

        // Conditional validation based on input type
        if ($isMobilInput) {
            $validationRules['mobil'] = 'required|array|min:1';
            $validationRules['mobil.*'] = 'required|exists:mobils,id';
            $validationRules['jumlah_mobil'] = 'required|array|min:1';
            $validationRules['jumlah_mobil.*'] = 'required|numeric|min:0';
        } else if ($isSupirInput) {
            $validationRules['supir'] = 'required|array|min:1';
            $validationRules['supir.*'] = 'required|exists:karyawans,id';
            $validationRules['jumlah'] = 'required|array|min:1';
            $validationRules['jumlah.*'] = 'required|numeric|min:0';
        } else if ($isPenerimaInput) {
            $validationRules['penerima'] = 'required|array|min:1';
            $validationRules['penerima.*'] = 'required|exists:karyawans,id';
            $validationRules['jumlah_karyawan'] = 'required|array|min:1';
            $validationRules['jumlah_karyawan.*'] = 'required|numeric|min:0';
        } else {
            return back()->withErrors(['error' => 'Harap pilih minimal satu supir, penerima, atau mobil'])->withInput();
        }

        Log::info('RealisasiUangMuka Store - Validation Rules:', $validationRules);

        try {
            $validated = $request->validate($validationRules);
            Log::info('RealisasiUangMuka Store - Validated Data:', $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('RealisasiUangMuka Store - Validation Failed:', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            // Generate nomor pembayaran jika kosong
            $nomorPembayaran = $validated['nomor_pembayaran'];
            if (!$nomorPembayaran) {
                $generateResult = $this->generateUniqueNomor($validated['kas_bank'], true);
                if (!$generateResult['success']) {
                    throw new \Exception($generateResult['message']);
                }
                $nomorPembayaran = $generateResult['nomor_pembayaran'];
            }

            // Process data based on input type
            $totalPembayaran = 0;
            $itemIds = [];
            $jumlahPerItemData = [];
            $keteranganPerItemData = [];
            $itemType = $isMobilInput ? 'mobil' : ($isSupirInput ? 'supir' : 'penerima');

            if ($isMobilInput) {
                // Process mobil data for KIR & STNK
                foreach ($validated['mobil'] as $mobilId) {
                    if (isset($validated['jumlah_mobil'][$mobilId])) {
                        $jumlah = floatval($validated['jumlah_mobil'][$mobilId]);
                        if ($jumlah > 0) {
                            $jumlahPerItemData[$mobilId] = $jumlah;
                            $itemIds[] = $mobilId;
                            $totalPembayaran += $jumlah;

                            // Save individual keterangan if exists
                            if (isset($request->input('keterangan_mobil')[$mobilId])) {
                                $keteranganPerItemData[$mobilId] = $request->input('keterangan_mobil')[$mobilId];
                            }
                        }
                    }
                }

                if (empty($itemIds)) {
                    return back()->withErrors(['mobil' => 'Harap pilih minimal satu mobil dengan jumlah realisasi > 0'])->withInput();
                }
            } else if ($isSupirInput) {
                // Process supir data for OB Muat/Bongkar
                foreach ($validated['supir'] as $supirId) {
                    if (isset($validated['jumlah'][$supirId])) {
                        $jumlah = floatval($validated['jumlah'][$supirId]);
                        if ($jumlah > 0) {
                            $jumlahPerItemData[$supirId] = $jumlah;
                            $itemIds[] = $supirId;
                            $totalPembayaran += $jumlah;

                            // Save individual keterangan if exists
                            if (isset($request->input('keterangan')[$supirId])) {
                                $keteranganPerItemData[$supirId] = $request->input('keterangan')[$supirId];
                            }
                        }
                    }
                }

                if (empty($itemIds)) {
                    return back()->withErrors(['supir' => 'Harap pilih minimal satu supir dengan jumlah realisasi > 0'])->withInput();
                }
            } else {
                // Process penerima data for Amprahan, Solar, Lain-lain
                foreach ($validated['penerima'] as $penerimaId) {
                    if (isset($validated['jumlah_karyawan'][$penerimaId])) {
                        $jumlah = floatval($validated['jumlah_karyawan'][$penerimaId]);
                        if ($jumlah > 0) {
                            $jumlahPerItemData[$penerimaId] = $jumlah;
                            $itemIds[] = $penerimaId;
                            $totalPembayaran += $jumlah;

                            // Save individual keterangan if exists
                            if (isset($request->input('keterangan_karyawan')[$penerimaId])) {
                                $keteranganPerItemData[$penerimaId] = $request->input('keterangan_karyawan')[$penerimaId];
                            }
                        }
                    }
                }

                if (empty($itemIds)) {
                    return back()->withErrors(['penerima' => 'Harap pilih minimal satu penerima dengan jumlah realisasi > 0'])->withInput();
                }
            }

            // Pastikan totalPembayaran adalah float
            $totalPembayaran = floatval($totalPembayaran);

            // Get DP amount - DIFFERENT logic for voyage vs uang muka
            $dpAmount = 0;
            $voyageNumber = $request->input('nomor_voyage');
            
            // Scenario 1: OB Activity with Voyage - get DP from tagihan_ob
            if ($isSupirInput && $voyageNumber) {
                Log::info('Getting DP from tagihan_ob for voyage', ['voyage' => $voyageNumber]);
                
                // Get DP from tagihan_ob for selected supir (sum both muat and bongkar)
                foreach ($itemIds as $supirId) {
                    // Get supir nama_lengkap
                    $supir = \App\Models\Karyawan::find($supirId);
                    if ($supir) {
                        // Get total DP from tagihan_ob for this supir and voyage (both muat and bongkar)
                        $totalDp = DB::table('tagihan_ob')
                            ->where('voyage', $voyageNumber)
                            ->whereRaw('LOWER(nama_supir) = ?', [strtolower($supir->nama_lengkap)])
                            ->sum('dp');
                        
                        $dpAmount += floatval($totalDp);
                        Log::info("DP from tagihan_ob for supir {$supirId} ({$supir->nama_lengkap}): {$totalDp}");
                    }
                }
            }
            // Scenario 2: Using Uang Muka - get DP from pembayaran_uang_muka
            else if ($validated['pembayaran_uang_muka_id']) {
                Log::info('Getting DP from pembayaran_uang_muka', ['uang_muka_id' => $validated['pembayaran_uang_muka_id']]);
                
                $uangMuka = PembayaranUangMuka::find($validated['pembayaran_uang_muka_id']);
                if ($uangMuka) {
                    // Hitung DP hanya untuk item yang dipilih saat ini
                    $jumlahPerSupirUangMuka = $uangMuka->jumlah_per_supir; // JSON object
                    
                    // Log untuk debug
                    Log::info('Uang Muka Data:', [
                        'uang_muka_id' => $uangMuka->id,
                        'jumlah_per_supir_raw' => $jumlahPerSupirUangMuka,
                        'is_array' => is_array($jumlahPerSupirUangMuka),
                        'is_string' => is_string($jumlahPerSupirUangMuka),
                        'selected_item_ids' => $itemIds
                    ]);
                    
                    // Decode jika masih string JSON
                    if (is_string($jumlahPerSupirUangMuka)) {
                        $jumlahPerSupirUangMuka = json_decode($jumlahPerSupirUangMuka, true);
                    }
                    
                    if ($jumlahPerSupirUangMuka && is_array($jumlahPerSupirUangMuka)) {
                        foreach ($itemIds as $itemId) {
                            // Coba cari dengan key string dan integer
                            $dpValue = $jumlahPerSupirUangMuka[$itemId] ?? 
                                      $jumlahPerSupirUangMuka[strval($itemId)] ?? 
                                      null;
                            
                            if ($dpValue !== null) {
                                $dpAmount += floatval($dpValue);
                                Log::info("DP Found for item {$itemId}: {$dpValue}");
                            } else {
                                Log::warning("No DP found for item {$itemId}");
                            }
                        }
                    }
                }
            }

            Log::info('Total DP Amount calculated:', ['dpAmount' => $dpAmount, 'source' => $voyageNumber ? 'tagihan_ob' : 'uang_muka']);

            // Hitung selisih yang harus dibayar (realisasi - DP)
            $selisihPembayaran = $totalPembayaran - $dpAmount;
            
            Log::info('Payment Calculation:', [
                'totalPembayaran' => $totalPembayaran,
                'dpAmount' => $dpAmount,
                'selisihPembayaran' => $selisihPembayaran
            ]);

            // Simpan realisasi uang muka
            $realisasi = RealisasiUangMuka::create([
                'nomor_pembayaran' => $nomorPembayaran,
                'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
                'kas_bank_id' => $validated['kas_bank'],
                'jenis_transaksi' => $validated['jenis_transaksi'],
                'supir_ids' => $itemIds,
                'jumlah_per_supir' => $jumlahPerItemData,
                'keterangan_per_supir' => $keteranganPerItemData,
                'total_realisasi' => $totalPembayaran,
                'total_pembayaran' => $selisihPembayaran,
                'keterangan' => $validated['keterangan'],
                'pembayaran_uang_muka_id' => $validated['pembayaran_uang_muka_id'],
                'dp_amount' => $dpAmount,
                'item_type' => $itemType,
                'status' => 'approved',
                'dibuat_oleh' => Auth::id(),
                'disetujui_oleh' => Auth::id(),
                'tanggal_persetujuan' => now(),
            ]);

            // Update status Uang Muka jika ada yang dipilih
            if ($validated['pembayaran_uang_muka_id']) {
                $uangMuka = PembayaranUangMuka::find($validated['pembayaran_uang_muka_id']);
                if ($uangMuka) {
                    $uangMuka->markAsTerpakai();
                }
            }

            // Pencatatan akuntansi untuk realisasi uang muka
            $this->recordRealisasiAccountingEntries($realisasi, $validated, $totalPembayaran, $dpAmount);

            DB::commit();

            $jumlahItem = count($itemIds);
            $itemLabel = $isMobilInput ? 'mobil' : ($isSupirInput ? 'supir' : 'penerima');
            $message = "Realisasi Uang Muka berhasil dibuat dengan nomor: {$nomorPembayaran}. ";
            $message .= "Total {$itemLabel}: {$jumlahItem}. ";
            $message .= "Total realisasi: Rp " . number_format($totalPembayaran, 0, ',', '.') . ".";
            if ($dpAmount > 0) {
                $message .= " Terkait dengan Uang Muka: Rp " . number_format($dpAmount, 0, ',', '.') . ".";
            }

            return redirect()->route('realisasi-uang-muka.index')
                            ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();

            // Log error lengkap untuk debugging
            Log::error('RealisasiUangMuka Store - Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal membuat realisasi uang muka: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('realisasi-uang-muka.show', [
            'title' => 'Detail Realisasi Uang Muka',
            'id' => $id
        ]);
    }

    /**
     * Print the specified realisasi uang muka.
     */
    public function print(string $id)
    {
        $realisasi = RealisasiUangMuka::with([
            'kasBankAkun',
            'pembuatPembayaran',
            'penyetujuPembayaran',
            'masterKegiatan',
            'pembayaranUangMuka'
        ])->findOrFail($id);

        // Get item data based on item_type
        $itemList = [];
        if ($realisasi->item_type === 'mobil') {
            $itemList = Mobil::whereIn('id', $realisasi->supir_ids ?? [])->get();
        } else {
            // For both 'supir' and 'penerima' types, get from Karyawan
            $itemList = Karyawan::whereIn('id', $realisasi->supir_ids ?? [])->get();
        }

        // Get Uang Muka data - now with proper relationship
        $uangMukaData = $realisasi->pembayaranUangMuka;

        // If no direct relationship found, try to find by matching criteria and auto-link
        if (!$uangMukaData && $realisasi->dp_amount > 0) {
            // Strategy 1: Try to find by exact amount match and overlapping supir_ids
            if (!empty($realisasi->supir_ids) && is_array($realisasi->supir_ids)) {
                $uangMukaData = PembayaranUangMuka::where('total_pembayaran', $realisasi->dp_amount)
                    ->where('jenis_transaksi', 'uang_muka')
                    ->whereIn('status', ['approved', 'pending', 'uang_muka_belum_terpakai', 'uang_muka_terpakai'])
                    ->where(function($query) use ($realisasi) {
                        foreach ($realisasi->supir_ids as $supirId) {
                            $query->orWhereJsonContains('supir_ids', (int)$supirId);
                        }
                    })
                    ->orderBy('tanggal_pembayaran', 'desc')
                    ->first();
            }

            // Strategy 2: If not found, try by amount and similar activity
            if (!$uangMukaData && $realisasi->kegiatan) {
                $uangMukaData = PembayaranUangMuka::where('total_pembayaran', $realisasi->dp_amount)
                    ->where('jenis_transaksi', 'uang_muka')
                    ->where('kegiatan', $realisasi->kegiatan)
                    ->whereIn('status', ['approved', 'pending', 'uang_muka_belum_terpakai', 'uang_muka_terpakai'])
                    ->orderBy('tanggal_pembayaran', 'desc')
                    ->first();
            }

            // Strategy 3: Last resort - find by amount only, closest date
            if (!$uangMukaData) {
                $uangMukaData = PembayaranUangMuka::where('total_pembayaran', $realisasi->dp_amount)
                    ->where('jenis_transaksi', 'uang_muka')
                    ->whereIn('status', ['approved', 'pending', 'uang_muka_belum_terpakai', 'uang_muka_terpakai'])
                    ->where('tanggal_pembayaran', '<=', $realisasi->tanggal_pembayaran)
                    ->orderBy('tanggal_pembayaran', 'desc')
                    ->first();
            }

            // If we found uang muka data but no relationship exists, save it for future use
            if ($uangMukaData && !$realisasi->pembayaran_uang_muka_id) {
                try {
                    $realisasi->update(['pembayaran_uang_muka_id' => $uangMukaData->id]);
                    // Refresh the relationship
                    $realisasi->load('pembayaranUangMuka');
                } catch (\Exception $e) {
                    // Silent fail - continue with display
                }
            }
        }

        return view('realisasi-uang-muka.print', [
            'title' => 'Print Realisasi Uang Muka',
            'realisasi' => $realisasi,
            'itemList' => $itemList,
            'uangMukaData' => $uangMukaData
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Ambil data karyawan yang mempunyai divisi supir
        $supirList = Karyawan::whereRaw('LOWER(divisi) = ?', ['supir'])
                            ->where('status', 'active') // hanya karyawan aktif
                            ->orderBy('nama_lengkap')
                            ->get();

        // Ambil data akun kas/bank dari COA
        $kasBankList = Coa::where('tipe_akun', 'Kas/Bank')
                          ->orderBy('nomor_akun')
                          ->get();

        return view('realisasi-uang-muka.edit', [
            'title' => 'Edit Realisasi Uang Muka',
            'id' => $id,
            'supirList' => $supirList,
            'kasBankList' => $kasBankList
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi input
        $request->validate([
            'nomor_pembayaran' => 'required|string|max:255',
            'tanggal_pembayaran' => 'required|date',
            'kas_bank' => 'required|exists:akun_coa,id',
            'jenis_transaksi' => 'required|in:debit,kredit',
            'supir' => 'required|array|min:1',
            'supir.*' => 'required|exists:karyawans,id',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string'
        ]);

        return redirect()->route('realisasi-uang-muka.index')
                        ->with('success', 'Realisasi Uang Muka berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return redirect()->route('realisasi-uang-muka.index')
                        ->with('success', 'Realisasi Uang Muka berhasil dihapus.');
    }

    /**
     * Approve realisasi uang muka
     */
    public function approve(string $id)
    {
        return redirect()->back()
                        ->with('success', 'Realisasi Uang Muka berhasil diapprove.');
    }

    /**
     * Reject realisasi uang muka
     */
    public function reject(Request $request, string $id)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:500'
        ]);

        return redirect()->back()
                        ->with('success', 'Realisasi Uang Muka berhasil ditolak.');
    }

    /**
     * Record accounting entries for realisasi uang muka
     */
    private function recordRealisasiAccountingEntries($realisasi, $validated, $totalRealisasi, $dpAmount)
    {
        // Get kas/bank COA
        $kasBankCoa = \App\Models\Coa::find($validated['kas_bank']);
        if (!$kasBankCoa) {
            throw new \Exception('COA Kas/Bank tidak ditemukan untuk realisasi');
        }

        // Get kegiatan info
        $kegiatan = \App\Models\MasterKegiatan::find($validated['kegiatan']);
        $kegiatanText = $kegiatan ? $kegiatan->nama_kegiatan : '';

        // Hitung selisih
        $selisih = $totalRealisasi - $dpAmount;

        // Entry 1: Pencatatan berdasarkan selisih
        if ($selisih > 0) {
            // Realisasi lebih besar dari uang muka - tambahan pembayaran keluar
            $kasBankCoa->decrement('saldo', $selisih);
            $kasBankCoa->refresh();

            $this->createLedgerEntry(
                $kasBankCoa->id,
                $realisasi->nomor_pembayaran,
                $realisasi->tanggal_pembayaran,
                'Realisasi Uang Muka (Tambahan Pembayaran) - ' . $kegiatanText,
                0, // debet
                $selisih, // kredit (uang keluar)
                $kasBankCoa->saldo
            );
        } elseif ($selisih < 0) {
            // Realisasi lebih kecil dari uang muka - sisa uang muka dikembalikan ke kas
            $sisaUangMuka = abs($selisih);
            $kasBankCoa->increment('saldo', $sisaUangMuka);
            $kasBankCoa->refresh();

            $this->createLedgerEntry(
                $kasBankCoa->id,
                $realisasi->nomor_pembayaran,
                $realisasi->tanggal_pembayaran,
                'Pengembalian Sisa Uang Muka - ' . $kegiatanText,
                $sisaUangMuka, // debet (uang masuk kembali)
                0, // kredit
                $kasBankCoa->saldo
            );

            // Log informasi pengembalian sisa uang muka
            Log::info("Sisa Uang Muka Dikembalikan", [
                'nomor_realisasi' => $realisasi->nomor_pembayaran,
                'kegiatan' => $kegiatanText,
                'uang_muka_original' => $dpAmount,
                'realisasi_actual' => $totalRealisasi,
                'sisa_dikembalikan' => $sisaUangMuka,
                'kas_bank_akun' => $kasBankCoa->nama_akun
            ]);
        }
        // Jika selisih = 0, tidak perlu entry tambahan

        // Entry 2: KREDIT pada COA Uang Muka dengan nominal DP
        if ($dpAmount > 0) {
            $this->creditUangMukaCoaForRealisasi($realisasi, $kegiatanText, $dpAmount);
        }
    }

    /**
     * Create credit entry for uang muka COA on realisasi
     */
    private function creditUangMukaCoaForRealisasi($realisasi, $kegiatanText, $dpAmount)
    {
        $coaCode = null;
        $coaName = null;

        // Tentukan COA berdasarkan kegiatan (setiap kegiatan sudah punya COA spesifik)
        if (stripos($kegiatanText, 'kir') !== false && stripos($kegiatanText, 'stnk') !== false) {
            // KIR & STNK -> COA Uang Muka STNK
            $coaCode = '1150007';
            $coaName = 'Uang Muka STNK';
        } elseif (stripos($kegiatanText, 'ob bongkar') !== false ||
                  (stripos($kegiatanText, 'bongkar') !== false && stripos($kegiatanText, 'muat') === false)) {
            // OB Bongkar -> COA Uang Muka OB Bongkar
            $coaCode = '1150010';
            $coaName = 'Uang Muka OB Bongkar';
        } elseif (stripos($kegiatanText, 'ob muat') !== false ||
                  (stripos($kegiatanText, 'muat') !== false && stripos($kegiatanText, 'bongkar') === false)) {
            // OB Muat -> COA Uang Muka OB Muat
            $coaCode = '1150011';
            $coaName = 'Uang Muka OB Muat';
        } elseif (stripos($kegiatanText, 'amprahan') !== false) {
            // Amprahan -> COA Uang Muka Amprahan
            $coaCode = '1150012';
            $coaName = 'Uang Muka Amprahan';
        } else {
            // Semua kegiatan sudah memiliki COA spesifik, jika tidak cocok log untuk debugging
            \Log::warning('Kegiatan tidak dikenali untuk mapping COA: ' . $kegiatanText);
            // Gunakan COA default untuk keamanan
            $coaCode = '1150009';
            $coaName = 'Uang Muka';
        }

        // Cari atau buat COA
        $uangMukaCoa = \App\Models\Coa::where('nomor_akun', $coaCode)->first();

        if (!$uangMukaCoa) {
            // Buat COA baru jika tidak ada
            $uangMukaCoa = \App\Models\Coa::create([
                'nomor_akun' => $coaCode,
                'nama_akun' => $coaName,
                'tipe_akun' => 'Asset',
                'saldo' => 0,
                'status' => 'Aktif'
            ]);
        }

        // Update saldo COA uang muka (KREDIT = mengurangi aset)
        $uangMukaCoa->decrement('saldo', $dpAmount);
        $uangMukaCoa->refresh();

        // KREDIT uang muka COA (mengurangi aset uang muka)
        $this->createLedgerEntry(
            $uangMukaCoa->id,
            $realisasi->nomor_pembayaran,
            $realisasi->tanggal_pembayaran,
            'Realisasi Uang Muka - ' . $kegiatanText,
            0, // debet
            $dpAmount, // kredit
            $uangMukaCoa->saldo
        );
    }

    /**
     * Create ledger entry using CoaTransaction model
     */
    private function createLedgerEntry($coaId, $nomorPembayaran, $tanggal, $keterangan, $debet, $kredit, $saldo)
    {
        // Tentukan jenis transaksi berdasarkan nilai debet/kredit
        $jenisTransaksi = $debet > 0 ? 'debit' : 'kredit';

        // Create entry in CoaTransaction table
        CoaTransaction::create([
            'coa_id' => $coaId,
            'tanggal_transaksi' => $tanggal,
            'nomor_referensi' => $nomorPembayaran,
            'jenis_transaksi' => $jenisTransaksi,
            'keterangan' => $keterangan,
            'debit' => $debet,
            'kredit' => $kredit,
            'saldo' => $saldo,
            'created_by' => Auth::id()
        ]);

        // Log the journal entry for debugging
        Log::info("Journal Entry Created", [
            'coa_id' => $coaId,
            'tanggal' => $tanggal,
            'nomor_referensi' => $nomorPembayaran,
            'keterangan' => $keterangan,
            'debet' => $debet,
            'kredit' => $kredit,
            'saldo' => $saldo,
            'jenis_transaksi' => $jenisTransaksi
        ]);
    }

    /**
     * Get list of voyages for OB activities
     */
    public function getVoyageList(Request $request)
    {
        try {
            $kegiatan = $request->input('kegiatan'); // 'muat' or 'bongkar'

            $query = DB::table('tagihan_ob')
                ->whereNotNull('voyage')
                ->where('voyage', '!=', '');

            // Filter by kegiatan if provided
            if ($kegiatan && in_array(strtolower($kegiatan), ['muat', 'bongkar'])) {
                $query->where('kegiatan', strtolower($kegiatan));
            }

            $voyages = $query->select('voyage')
                ->distinct()
                ->orderBy('voyage', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $voyages->pluck('voyage')->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading voyage list: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get supir list by voyage for OB activities
     */
    public function getSupirByVoyage(Request $request)
    {
        try {
            $voyage = $request->input('voyage');
            $kegiatan = $request->input('kegiatan'); // 'muat' or 'bongkar'

            if (!$voyage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voyage parameter is required'
                ], 400);
            }

            // Build query to get supir list with total tagihan from tagihan_ob
            // Note: tagihan_ob uses nama_supir (string) not supir_id
            $query = DB::table('tagihan_ob')
                ->where('voyage', $voyage)
                ->whereNotNull('nama_supir')
                ->where('nama_supir', '!=', '');

            // Filter by kegiatan if provided
            if ($kegiatan && in_array(strtolower($kegiatan), ['muat', 'bongkar'])) {
                $query->where('kegiatan', strtolower($kegiatan));
            }

            $tagihanData = $query->select(
                    'nama_supir',
                    DB::raw('SUM(biaya) as total_tagihan'),
                    DB::raw('SUM(COALESCE(dp, 0)) as total_dp'),
                    DB::raw('COUNT(*) as jumlah_kontainer')
                )
                ->groupBy('nama_supir')
                ->orderBy('nama_supir')
                ->get();

            // Map nama_supir to supir_id from karyawans table
            $supirList = [];
            foreach ($tagihanData as $tagihan) {
                // Find supir by name in karyawans table
                $supir = DB::table('karyawans')
                    ->whereRaw('LOWER(nama_lengkap) = ?', [strtolower($tagihan->nama_supir)])
                    ->whereRaw('LOWER(divisi) = ?', ['supir'])
                    ->where('status', 'active')
                    ->first();

                if ($supir) {
                    $supirList[] = [
                        'supir_id' => (int) $supir->id,
                        'nama_supir' => $tagihan->nama_supir,
                        'total_tagihan' => (float) $tagihan->total_tagihan,
                        'jumlah_kontainer' => (int) $tagihan->jumlah_kontainer,
                        'dp_dibayar' => (float) $tagihan->total_dp
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $supirList,
                'filter' => [
                    'voyage' => $voyage,
                    'kegiatan' => $kegiatan
                ],
                'debug' => [
                    'total_tagihan_records' => $tagihanData->count(),
                    'matched_supir' => count($supirList)
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getSupirByVoyage: ' . $e->getMessage(), [
                'voyage' => $request->input('voyage'),
                'kegiatan' => $request->input('kegiatan'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading supir data: ' . $e->getMessage()
            ], 500);
        }
    }
}
