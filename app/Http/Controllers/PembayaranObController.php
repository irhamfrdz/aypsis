<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Karyawan;
use App\Models\Coa;
use App\Models\PembayaranOb;
use App\Models\NomorTerakhir;
use App\Models\NaikKapal;
use App\Models\Bl;

class PembayaranObController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Query pembayaran OB dengan relationships
        $query = PembayaranOb::with(['kasBankAkun', 'pembuatPembayaran', 'penyetujuPembayaran'])
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

        // Filter berdasarkan tanggal dari
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_pembayaran', '>=', $request->tanggal_dari);
        }

        // Filter berdasarkan tanggal sampai
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_pembayaran', '<=', $request->tanggal_sampai);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pembayaranList = $query->paginate(20);

        // Ambil data karyawan supir untuk dropdown pencarian
        $supirList = Karyawan::whereRaw('LOWER(divisi) = ?', ['supir'])
                            ->where('status', 'active')
                            ->orderBy('nama_lengkap')
                            ->get();

        return view('pembayaran-dp-ob.index', [
            'title' => 'Pembayaran DP OB',
            'pembayaranList' => $pembayaranList,
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

        // Ambil data akun kas/bank dari COA
        $kasBankList = Coa::where('tipe_akun', 'Kas/Bank')
                          ->orderBy('nomor_akun')
                          ->get();

                // Get Uang Muka yang belum terpakai
        $uangMukaBelumTerpakaiList = \App\Models\PembayaranUangMuka::where('status', 'uang_muka_belum_terpakai')
                                  ->orderBy('tanggal_pembayaran', 'desc')
                                  ->get();

        // Enrich Uang Muka data dengan nama supir
        foreach ($uangMukaBelumTerpakaiList as $uangMuka) {
            $uangMuka->supir_names = $uangMuka->supirList()->pluck('nama_lengkap')->toArray();
        }

        return view('pembayaran-dp-ob.create', [
            'title' => 'Tambah Pembayaran DP OB',
            'supirList' => $supirList,
            'kasBankList' => $kasBankList,
            'uangMukaBelumTerpakaiList' => $uangMukaBelumTerpakaiList
        ]);
    }

    /**
     * Get voyage list based on kegiatan (Muat or Bongkar)
     */
    public function getVoyageList(Request $request)
    {
        try {
            $kegiatan = $request->input('kegiatan');

            if (!$kegiatan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kegiatan harus dipilih'
                ], 400);
            }

            $voyages = [];

            if ($kegiatan === 'Muat') {
                // Ambil dari table naik_kapal - distinct by no_voyage
                $voyages = NaikKapal::select('no_voyage', 'nama_kapal')
                    ->selectRaw('MIN(id) as id')
                    ->selectRaw('MAX(tanggal_muat) as tanggal_muat')
                    ->whereNotNull('no_voyage')
                    ->where('no_voyage', '!=', '')
                    ->groupBy('no_voyage', 'nama_kapal')
                    ->orderBy('tanggal_muat', 'desc')
                    ->get()
                    ->map(function($item) {
                        return [
                            'id' => $item->no_voyage, // Gunakan no_voyage sebagai value
                            'no_voyage' => $item->no_voyage,
                            'nama_kapal' => $item->nama_kapal
                        ];
                    });
            } elseif ($kegiatan === 'Bongkar') {
                // Ambil dari table bls - distinct by no_voyage
                $voyages = Bl::select('no_voyage', 'nama_kapal')
                    ->selectRaw('MIN(id) as id')
                    ->selectRaw('MAX(created_at) as created_at')
                    ->whereNotNull('no_voyage')
                    ->where('no_voyage', '!=', '')
                    ->groupBy('no_voyage', 'nama_kapal')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($item) {
                        return [
                            'id' => $item->no_voyage, // Gunakan no_voyage sebagai value
                            'no_voyage' => $item->no_voyage,
                            'nama_kapal' => $item->nama_kapal
                        ];
                    });
            }

            return response()->json([
                'success' => true,
                'voyages' => $voyages,
                'kegiatan' => $kegiatan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data voyage: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate nomor pembayaran preview (tidak increment nomor terakhir)
     */
    public function generateNomor(Request $request)
    {
        try {
            $today = now();
            $tahun = $today->format('y'); // 2 digit year
            $bulan = $today->format('m'); // 2 digit month

            // Get COA info untuk kode bank
            $coa = \App\Models\Coa::find($request->kas_bank_id);
            if (!$coa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank/Kas tidak ditemukan'
                ], 404);
            }

            // Ambil kode_nomor dari COA sebagai kode bank
            $kodeBank = $coa->kode_nomor ?? '000';

            // Get next running number from master nomor terakhir (preview only, don't increment)
            $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'nomor_pembayaran')->first();

            if (!$nomorTerakhir) {
                return response()->json([
                    'success' => false,
                    'message' => 'Modul nomor_pembayaran tidak ditemukan di master nomor terakhir'
                ], 404);
            }

            $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
            $sequence = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $nomorPembayaran = "{$kodeBank}-{$bulan}-{$tahun}-{$sequence}";

            return response()->json([
                'success' => true,
                'nomor_pembayaran' => $nomorPembayaran,
                'preview' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Pre-process jumlah data to ensure it's clean
        $jumlahData = $request->input('jumlah', []);
        if (!is_array($jumlahData)) {
            return back()->withErrors(['jumlah' => 'Data jumlah harus berupa array'])->withInput();
        }

        // Clean jumlah data - remove any non-numeric values
        $cleanJumlahData = [];
        foreach ($jumlahData as $supirId => $jumlah) {
            $cleanJumlahData[$supirId] = is_numeric($jumlah) ? floatval($jumlah) : 0;
        }

        // Replace jumlah in request
        $request->merge(['jumlah' => $cleanJumlahData]);

        // Validasi input
        $validated = $request->validate([
            'nomor_pembayaran' => 'required|string|max:255|unique:pembayaran_obs,nomor_pembayaran',
            'tanggal_pembayaran' => 'required|date',
            'kas_bank' => 'required|exists:akun_coa,id',
            'jenis_transaksi' => 'required|in:debit,kredit',
            'kegiatan' => 'nullable|string|max:255',
            'nomor_voyage' => 'nullable|string|max:255',
            'supir' => 'required|array|min:1',
            'supir.*' => 'required|exists:karyawans,id',
            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'pembayaran_uang_muka_id' => 'nullable|exists:pembayaran_uang_muka,id'
        ]);

        try {
            DB::beginTransaction();

            // Generate nomor pembayaran jika kosong
            $nomorPembayaran = $validated['nomor_pembayaran'];
            if (!$nomorPembayaran) {
                $nomorPembayaran = PembayaranOb::generateNomorPembayaran($validated['kas_bank']);
            }

            // Hitung total pembayaran dari semua supir
            $subtotalPembayaran = 0;
            $jumlahPerSupirData = [];

            foreach ($validated['supir'] as $supirId) {
                $jumlah = floatval($validated['jumlah'][$supirId] ?? 0);
                $jumlahPerSupirData[$supirId] = $jumlah;
                $subtotalPembayaran += $jumlah;
            }

            // Pastikan subtotalPembayaran adalah float
            $subtotalPembayaran = floatval($subtotalPembayaran);
            $totalPembayaran = $subtotalPembayaran;

            // Kurangi dengan Uang Muka jika ada yang dipilih
            $uangMukaAmount = 0;
            $pembayaranUangMukaId = $validated['pembayaran_uang_muka_id'] ?? null;
            
            if ($pembayaranUangMukaId) {
                $uangMuka = \App\Models\PembayaranUangMuka::find($pembayaranUangMukaId);
                if ($uangMuka) {
                    $uangMukaAmount = floatval($uangMuka->total_pembayaran);
                    $totalPembayaran = $subtotalPembayaran - $uangMukaAmount;
                    // Pastikan total tidak negatif
                    $totalPembayaran = max(0, $totalPembayaran);
                }
            }

            // Simpan pembayaran OB
            $pembayaran = PembayaranOb::create([
                'nomor_pembayaran' => $nomorPembayaran,
                'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
                'kas_bank_id' => $validated['kas_bank'],
                'jenis_transaksi' => $validated['jenis_transaksi'],
                'kegiatan' => $validated['kegiatan'] ?? null,
                'nomor_voyage' => $validated['nomor_voyage'] ?? null,
                'supir_ids' => $validated['supir'], // JSON array
                'jumlah_per_supir' => $jumlahPerSupirData, // JSON object dengan supir_id => jumlah
                'subtotal_pembayaran' => $subtotalPembayaran, // Subtotal sebelum dikurangi Uang Muka
                'uang_muka_amount' => $uangMukaAmount, // Jumlah Uang Muka yang digunakan
                'dp_amount' => $subtotalPembayaran, // Jumlah DP sama dengan subtotal
                'total_pembayaran' => $totalPembayaran, // Total sudah dikurangi Uang Muka
                'keterangan' => $validated['keterangan'] ?? null,
                'pembayaran_uang_muka_id' => $pembayaranUangMukaId,
                'status' => 'approved', // Langsung approved untuk OB
                'dibuat_oleh' => Auth::id(),
                'disetujui_oleh' => Auth::id(),
                'tanggal_persetujuan' => now(),
            ]);

            // Update status Uang Muka jika ada yang dipilih
            if ($pembayaranUangMukaId) {
                $uangMuka = \App\Models\PembayaranUangMuka::find($pembayaranUangMukaId);
                if ($uangMuka) {
                    $uangMuka->markAsTerpakai();
                }
            }

            DB::commit();

            $jumlahSupir = count($validated['supir']);
            $message = "Pembayaran OB berhasil dibuat dengan nomor: {$nomorPembayaran}. ";
            $message .= "Total supir: {$jumlahSupir}. ";
            if ($uangMukaAmount > 0) {
                $message .= "Total awal: Rp " . number_format($subtotalPembayaran, 0, ',', '.') . ". ";
                $message .= "Uang Muka digunakan: Rp " . number_format($uangMukaAmount, 0, ',', '.') . ". ";
                $message .= "Total setelah Uang Muka: Rp " . number_format($totalPembayaran, 0, ',', '.') . ".";
            } else {
                $message .= "Total pembayaran: Rp " . number_format($totalPembayaran, 0, ',', '.') . ".";
            }

            return redirect()->route('pembayaran-ob.index')
                            ->with('success', $message);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            
            $errorMessage = 'Gagal menyimpan data pembayaran OB ke database. ';
            
            // Detect specific database errors
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $errorMessage .= 'Nomor pembayaran sudah digunakan. Silakan generate nomor baru.';
            } elseif (str_contains($e->getMessage(), 'foreign key constraint')) {
                $errorMessage .= 'Data terkait (Kas/Bank atau Supir) tidak valid.';
            } elseif (str_contains($e->getMessage(), 'Data too long')) {
                $errorMessage .= 'Data yang diinput terlalu panjang.';
            } else {
                $errorMessage .= 'Error Database: ' . $e->getMessage();
            }
            
            return redirect()->back()
                           ->withInput()
                           ->withErrors(['database' => $errorMessage])
                           ->with('error', $errorMessage);
                           
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->withErrors($e->errors())
                           ->with('error', 'Validasi gagal. Periksa kembali data yang diinput.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            
            $errorMessage = 'Gagal menyimpan pembayaran OB. ';
            
            // Check for common errors
            if (str_contains($e->getMessage(), 'nomor_pembayaran')) {
                $errorMessage .= 'Terdapat masalah dengan nomor pembayaran. ';
            } elseif (str_contains($e->getMessage(), 'supir')) {
                $errorMessage .= 'Terdapat masalah dengan data supir. ';
            } elseif (str_contains($e->getMessage(), 'jumlah')) {
                $errorMessage .= 'Terdapat masalah dengan jumlah pembayaran. ';
            } elseif (str_contains($e->getMessage(), 'voyage')) {
                $errorMessage .= 'Terdapat masalah dengan nomor voyage. ';
            }
            
            $errorMessage .= 'Detail: ' . $e->getMessage();
            
            \Log::error('Pembayaran OB Store Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['_token'])
            ]);
            
            return redirect()->back()
                           ->withInput()
                           ->withErrors(['general' => $errorMessage])
                           ->with('error', $errorMessage);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pembayaran = PembayaranOb::with(['kasBankAkun', 'creator', 'updater'])
                                  ->findOrFail($id);

        return view('pembayaran-dp-ob.show', [
            'title' => 'Detail Pembayaran DP OB',
            'pembayaran' => $pembayaran
        ]);
    }

    /**
     * Print the specified pembayaran OB.
     */
    public function print(string $id)
    {
        $pembayaran = PembayaranOb::with(['kasBankAkun', 'pembuatPembayaran', 'penyetujuPembayaran'])
                                  ->findOrFail($id);

        // Get supir data
        $supirList = Karyawan::whereIn('id', $pembayaran->supir_ids ?? [])->get();

        // Get Uang Muka data if exists
        $uangMukaData = null;
        if ($pembayaran->pembayaran_uang_muka_id) {
            $uangMukaData = \App\Models\PembayaranUangMuka::find($pembayaran->pembayaran_uang_muka_id);
        }

        return view('pembayaran-ob.print', [
            'title' => 'Print Pembayaran OB',
            'pembayaran' => $pembayaran,
            'supirList' => $supirList,
            'uangMukaData' => $uangMukaData
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pembayaran = PembayaranOb::findOrFail($id);

        // Ambil data karyawan yang mempunyai divisi supir
        $supirList = Karyawan::whereRaw('LOWER(divisi) = ?', ['supir'])
                            ->where('status', 'active') // hanya karyawan aktif
                            ->orderBy('nama_lengkap')
                            ->get();

        // Ambil data akun kas/bank dari COA
        $kasBankList = Coa::where('tipe_akun', 'Kas/Bank')
                          ->orderBy('nomor_akun')
                          ->get();

        return view('pembayaran-dp-ob.edit', [
            'title' => 'Edit Pembayaran DP OB',
            'pembayaran' => $pembayaran,
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
            'kegiatan' => 'nullable|string|max:255',
            'nomor_voyage' => 'nullable|string|max:255',
            'supir' => 'required|array|min:1',
            'supir.*' => 'required|exists:karyawans,id',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string'
        ]);

        return redirect()->route('pembayaran-ob.index')
                        ->with('success', 'Pembayaran OB berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return redirect()->route('pembayaran-ob.index')
                        ->with('success', 'Pembayaran OB berhasil dihapus.');
    }

    /**
     * Generate nomor pembayaran otomatis
     * Format: 3 digit COA + 2 digit bulan + 2 digit tahun + 6 digit nomor terakhir
     */
    public function generateNomorPembayaran(Request $request)
    {
        try {
            // Ambil kas_bank_id dari request atau gunakan default
            $kasBankId = $request->input('kas_bank_id');

            // Jika tidak ada kas_bank_id, gunakan kas/bank pertama sebagai default
            if (!$kasBankId) {
                $defaultKasBank = Coa::where('tipe_akun', 'Kas/Bank')
                                    ->orderBy('nomor_akun')
                                    ->first();
                $kasBankId = $defaultKasBank ? $defaultKasBank->id : null;
            }

            $coaPrefix = 'KBJ'; // Default jika tidak ada COA

            if ($kasBankId) {
                $kasBank = Coa::find($kasBankId);
                if ($kasBank) {
                    if ($kasBank->kode_nomor) {
                        // Ambil kode_nomor dari COA (contoh: KBJ, BCA, MDR)
                        $coaPrefix = $kasBank->kode_nomor;
                    } else {
                        // Fallback: ambil 3 karakter pertama dari nomor_akun jika kode_nomor kosong
                        $coaPrefix = substr(str_replace('.', '', $kasBank->nomor_akun), 0, 3);
                    }
                }
            }

            // Format tanggal: 2 digit bulan + 2 digit tahun
            $today = now();
            $monthYear = $today->format('my'); // m = bulan 2 digit, y = tahun 2 digit

            // Ambil nomor terakhir dari master nomor_pembayaran
            $lastNumber = $this->getLastPaymentNumber('pembayaran_ob', $coaPrefix . $monthYear);
            $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);

            // Format final: KODE_COA + BULAN(2) + TAHUN(2) + NOMOR(6)
            $nomor = $coaPrefix . $monthYear . $nextNumber;

            // Update master nomor_terakhir untuk increment selanjutnya
            $nomorTerakhir = NomorTerakhir::where('modul', 'nomor_pembayaran')->first();
            if ($nomorTerakhir) {
                $nomorTerakhir->update(['nomor_terakhir' => $lastNumber + 1]);
            }

            return response()->json([
                'nomor_pembayaran' => $nomor,
                'format_info' => [
                    'coa_prefix' => $coaPrefix,
                    'month_year' => $monthYear,
                    'sequence' => $nextNumber,
                    'full_format' => "{$coaPrefix} + {$monthYear} + {$nextNumber}",
                    'explanation' => "COA: {$coaPrefix}, Bulan/Tahun: {$monthYear}, Urutan: {$nextNumber}"
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal generate nomor pembayaran',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get last payment number for sequence
     */
    private function getLastPaymentNumber($module = 'pembayaran_ob', $prefix = '')
    {
        try {
            // Ambil nomor terakhir dari tabel master nomor_terakhir
            // Menggunakan modul 'nomor_pembayaran' untuk semua jenis pembayaran
            $nomorTerakhir = NomorTerakhir::where('modul', 'nomor_pembayaran')->first();

            if ($nomorTerakhir && $nomorTerakhir->nomor_terakhir) {
                return (int) $nomorTerakhir->nomor_terakhir;
            }

            // Jika tidak ada record, mulai dari 0
            return 0;

        } catch (\Exception $e) {
            // Fallback: return 0 jika ada error
            logger('Error getting last payment number: ' . $e->getMessage());
            return 0;
        }
    }



    /**
     * Approve pembayaran OB
     */
    public function approve(string $id)
    {
        return redirect()->back()
                        ->with('success', 'Pembayaran OB berhasil diapprove.');
    }

    /**
     * Reject pembayaran OB
     */
    public function reject(Request $request, string $id)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:500'
        ]);

        return redirect()->back()
                        ->with('success', 'Pembayaran OB berhasil ditolak.');
    }
}
