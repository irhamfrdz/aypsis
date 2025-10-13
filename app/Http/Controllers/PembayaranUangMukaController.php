<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Karyawan;
use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Models\PembayaranUangMuka;
use App\Models\NomorTerakhir;
use App\Models\MasterKegiatan;
use App\Models\Mobil;

class PembayaranUangMukaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Query pembayaran Uang Muka dengan relationships
        $query = PembayaranUangMuka::with(['kasBankAkun', 'pembuatPembayaran', 'penyetujuPembayaran', 'masterKegiatan'])
                               ->orderBy('tanggal_pembayaran', 'desc');        // Filter berdasarkan nomor pembayaran
        if ($request->filled('nomor_pembayaran')) {
            $query->where('nomor_pembayaran', 'like', '%' . $request->nomor_pembayaran . '%');
        }

        // Filter berdasarkan kegiatan
        if ($request->filled('kegiatan')) {
            $query->whereHas('masterKegiatan', function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->kegiatan . '%');
            });
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

        $pembayaranList = $query->paginate(20);

        // Ambil data karyawan supir untuk dropdown pencarian
        $supirList = Karyawan::whereRaw('LOWER(divisi) = ?', ['supir'])
                            ->where('status', 'active')
                            ->orderBy('nama_lengkap')
                            ->get();

        return view('pembayaran-uang-muka.index', [
            'title' => 'Pembayaran Uang Muka',
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

        // Ambil data kegiatan dengan type "uang muka" dan status aktif
        $kegiatanList = MasterKegiatan::where('type', 'uang muka')
                                     ->where('status', 'aktif')
                                     ->orderBy('nama_kegiatan')
                                     ->get();

        // Ambil data mobil untuk dropdown KIR & STNK
        $mobilList = Mobil::orderBy('plat')->get();

        // Ambil data karyawan untuk dropdown penerima (KIR & STNK)
        $karyawanList = Karyawan::where('status', 'active')
                               ->orderBy('nama_lengkap')
                               ->get();

        return view('pembayaran-uang-muka.create', [
            'title' => 'Create Pembayaran Uang Muka',
            'supirList' => $supirList,
            'kasBankList' => $kasBankList,
            'kegiatanList' => $kegiatanList,
            'mobilList' => $mobilList,
            'karyawanList' => $karyawanList
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

        // Get next running number from master nomor terakhir
        $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'nomor_pembayaran')->first();

        if (!$nomorTerakhir) {
            return [
                'success' => false,
                'message' => 'Modul nomor_pembayaran tidak ditemukan di master nomor terakhir'
            ];
        }

        // Loop untuk mencari nomor yang belum digunakan
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $nextNumber = $nomorTerakhir->nomor_terakhir + 1 + $attempt;
            $sequence = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            $nomorPembayaran = "{$kodeBank}-{$bulan}-{$tahun}-{$sequence}";

            // Check if nomor already exists
            $exists = \App\Models\PembayaranUangMuka::where('nomor_pembayaran', $nomorPembayaran)->exists();

            if (!$exists) {
                // Nomor unik ditemukan
                if ($actualGenerate) {
                    // Update counter jika ini actual generate (bukan preview)
                    $nomorTerakhir->update(['nomor_terakhir' => $nextNumber]);
                }

                return [
                    'success' => true,
                    'nomor_pembayaran' => $nomorPembayaran,
                    'preview' => !$actualGenerate
                ];
            }

            $attempt++;
        } while ($attempt < $maxAttempts);

        // Jika tidak bisa generate nomor unik
        return [
            'success' => false,
            'message' => 'Tidak dapat generate nomor unik setelah ' . $maxAttempts . ' percobaan'
        ];
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

        // Validasi dasar terlebih dahulu
        $basicValidated = $request->validate([
            'nomor_pembayaran' => 'nullable|string|max:255',
            'tanggal_pembayaran' => 'required|date',
            'kas_bank' => 'required|exists:akun_coa,id',
            'jenis_transaksi' => 'required|in:debit,kredit',
            'kegiatan' => 'required|exists:master_kegiatans,id',
            'keterangan' => 'nullable|string'
        ]);

        // Ambil info kegiatan untuk menentukan jenis validasi
        $kegiatan = MasterKegiatan::find($basicValidated['kegiatan']);
        $kegiatanText = $kegiatan ? strtolower($kegiatan->nama_kegiatan) : '';

        // Validasi kondisional berdasarkan kegiatan
        $conditionalRules = [];

        if (strpos($kegiatanText, 'kir') !== false && strpos($kegiatanText, 'stnk') !== false) {
            // Untuk KIR & STNK
            $conditionalRules = [
                'mobil_id' => 'required|exists:mobils,id',
                'penerima_id' => 'required|exists:karyawans,id',
                'jumlah_mobil' => 'required|numeric|min:0',
            ];
        } elseif (strpos($kegiatanText, 'muat') !== false || strpos($kegiatanText, 'bongkar') !== false) {
            // Untuk OB Muat/Bongkar
            $conditionalRules = [
                'supir' => 'required|array|min:1',
                'supir.*' => 'required|exists:karyawans,id',
                'jumlah' => 'required|array|min:1',
                'jumlah.*' => 'required|numeric|min:0',
            ];
        } else {
            // Untuk Amprahan dan kegiatan lainnya
            $conditionalRules = [
                'penerima_id' => 'required|exists:karyawans,id',
                'jumlah_penerima' => 'required|numeric|min:0',
            ];
        }

        // Gabungkan validasi dasar dan kondisional
        $allRules = array_merge([
            'nomor_pembayaran' => 'nullable|string|max:255',
            'tanggal_pembayaran' => 'required|date',
            'kas_bank' => 'required|exists:akun_coa,id',
            'jenis_transaksi' => 'required|in:debit,kredit',
            'kegiatan' => 'required|exists:master_kegiatans,id',
            'mobil_id' => 'nullable|exists:mobils,id',
            'penerima_id' => 'nullable|exists:karyawans,id',
            'supir' => 'nullable|array',
            'supir.*' => 'nullable|exists:karyawans,id',
            'jumlah' => 'nullable|array',
            'jumlah.*' => 'nullable|numeric|min:0',
            'jumlah_mobil' => 'nullable|numeric|min:0',
            'jumlah_penerima' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string'
        ], $conditionalRules);

        $validated = $request->validate($allRules);

        try {
            DB::beginTransaction();

            // Generate nomor pembayaran jika kosong atau sudah ada
            $nomorPembayaran = $validated['nomor_pembayaran'];
            if (!$nomorPembayaran) {
                $generateResult = $this->generateUniqueNomor($validated['kas_bank'], true);
                if (!$generateResult['success']) {
                    throw new \Exception($generateResult['message']);
                }
                $nomorPembayaran = $generateResult['nomor_pembayaran'];
            } else {
                // Check if nomor already exists
                $exists = PembayaranUangMuka::where('nomor_pembayaran', $nomorPembayaran)->exists();
                if ($exists) {
                    $generateResult = $this->generateUniqueNomor($validated['kas_bank'], true);
                    if (!$generateResult['success']) {
                        throw new \Exception('Nomor pembayaran sudah digunakan dan gagal generate nomor baru: ' . $generateResult['message']);
                    }
                    $nomorPembayaran = $generateResult['nomor_pembayaran'];
                }
            }

            // Hitung total pembayaran
            $totalPembayaran = 0;
            $jumlahPerSupirData = [];
            $supirIds = [];

            // Check jika ada mobil_id (untuk KIR & STNK)
            if (!empty($validated['mobil_id'])) {
                $totalPembayaran = floatval($validated['jumlah_mobil'] ?? 0);
                $supirIds = []; // Array kosong untuk KIR & STNK
            } elseif (!empty($validated['jumlah_penerima'])) {
                // Logic untuk penerima (Amprahan dan kegiatan lainnya)
                $totalPembayaran = floatval($validated['jumlah_penerima']);
                $supirIds = []; // Array kosong untuk kegiatan penerima
            } elseif (!empty($validated['supir']) && is_array($validated['supir'])) {
                // Logic untuk supir (OB Muat/Bongkar)
                foreach ($validated['supir'] as $supirId) {
                    $jumlah = floatval($validated['jumlah'][$supirId] ?? 0);
                    $jumlahPerSupirData[$supirId] = $jumlah;
                    $totalPembayaran += $jumlah;
                }
                $supirIds = $validated['supir'];
            } else {
                // Fallback jika tidak ada data supir, mobil, atau penerima
                $totalPembayaran = 0;
                $supirIds = [];
            }

            // Simpan pembayaran Uang Muka
            $pembayaran = PembayaranUangMuka::create([
                'nomor_pembayaran' => $nomorPembayaran,
                'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
                'kas_bank_id' => $validated['kas_bank'],
                'jenis_transaksi' => $validated['jenis_transaksi'],
                'kegiatan' => $validated['kegiatan'],
                'mobil_id' => $validated['mobil_id'] ?? null,
                'penerima_id' => $validated['penerima_id'] ?? null,
                'supir_ids' => $supirIds, // JSON array atau null
                'jumlah_per_supir' => $jumlahPerSupirData, // JSON object dengan supir_id => jumlah
                'total_pembayaran' => $totalPembayaran,
                'keterangan' => $validated['keterangan'],
                'status' => 'uang_muka_belum_terpakai', // Default status
                'dibuat_oleh' => Auth::id(),
                'disetujui_oleh' => Auth::id(),
                'tanggal_persetujuan' => now(),
            ]);

            // Pencatatan akuntansi
            $this->recordAccountingEntries($pembayaran, $validated, $totalPembayaran);

            DB::commit();

            $message = "Pembayaran Uang Muka berhasil dibuat dengan nomor: {$nomorPembayaran}. ";

            if (!empty($validated['mobil_id'])) {
                // Untuk KIR & STNK
                $mobil = Mobil::find($validated['mobil_id']);
                $message .= "Mobil: {$mobil->plat}. ";

                if (!empty($validated['penerima_id'])) {
                    $penerima = Karyawan::find($validated['penerima_id']);
                    $message .= "Penerima: {$penerima->nama_lengkap}. ";
                }
            } elseif (!empty($validated['jumlah_penerima'])) {
                // Untuk Amprahan dan kegiatan lainnya dengan penerima
                if (!empty($validated['penerima_id'])) {
                    $penerima = Karyawan::find($validated['penerima_id']);
                    $message .= "Penerima: {$penerima->nama_lengkap}. ";
                }
            } elseif (!empty($validated['supir'])) {
                // Untuk OB Muat/Bongkar dengan supir
                $jumlahSupir = count($validated['supir']);
                $message .= "Total supir: {$jumlahSupir}. ";
            }

            $message .= "Total pembayaran: Rp " . number_format($totalPembayaran, 0, ',', '.') . ".";

            return redirect()->route('pembayaran-uang-muka.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withErrors([
                'error' => 'Gagal menyimpan pembayaran Uang Muka: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pembayaran = PembayaranUangMuka::with(['kasBankAkun', 'pembuatPembayaran', 'penyetujuPembayaran'])
                                      ->findOrFail($id);

        // Get supir list from supir_ids
        $supirList = Karyawan::whereIn('id', $pembayaran->supir_ids ?? [])->get();

        return view('pembayaran-uang-muka.show', [
            'title' => 'Detail Pembayaran Uang Muka',
            'pembayaran' => $pembayaran,
            'supirList' => $supirList,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pembayaran = PembayaranUangMuka::findOrFail($id);

        // Check if already used
        if ($pembayaran->isUsed()) {
            return redirect()->route('pembayaran-uang-muka.index')
                           ->with('error', 'Tidak dapat mengedit Uang Muka yang sudah terpakai.');
        }

        // Ambil data karyawan yang mempunyai divisi supir
        $supirList = Karyawan::whereRaw('LOWER(divisi) = ?', ['supir'])
                            ->where('status', 'active')
                            ->orderBy('nama_lengkap')
                            ->get();

        // Ambil data akun kas/bank dari COA
        $kasBankList = Coa::where('tipe_akun', 'Kas/Bank')
                          ->orderBy('nomor_akun')
                          ->get();

        // Ambil data kegiatan dengan type "uang muka" dan status aktif
        $kegiatanList = MasterKegiatan::where('type', 'uang muka')
                                     ->where('status', 'aktif')
                                     ->orderBy('nama_kegiatan')
                                     ->get();

        // Ambil data mobil untuk dropdown KIR & STNK
        $mobilList = Mobil::orderBy('plat')->get();

        // Ambil data karyawan untuk dropdown penerima (KIR & STNK)
        $karyawanList = Karyawan::where('status', 'active')
                               ->orderBy('nama_lengkap')
                               ->get();

        return view('pembayaran-uang-muka.edit', [
            'title' => 'Edit Pembayaran Uang Muka',
            'pembayaran' => $pembayaran,
            'supirList' => $supirList,
            'kasBankList' => $kasBankList,
            'kegiatanList' => $kegiatanList,
            'mobilList' => $mobilList,
            'karyawanList' => $karyawanList
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pembayaran = PembayaranUangMuka::findOrFail($id);

        // Check if already used
        if ($pembayaran->isUsed()) {
            return redirect()->route('pembayaran-uang-muka.index')
                           ->with('error', 'Tidak dapat mengupdate Uang Muka yang sudah terpakai.');
        }

        // Pre-process jumlah data
        $jumlahData = $request->input('jumlah', []);
        if (!is_array($jumlahData)) {
            return back()->withErrors(['jumlah' => 'Data jumlah harus berupa array'])->withInput();
        }

        $cleanJumlahData = [];
        foreach ($jumlahData as $supirId => $jumlah) {
            $cleanJumlahData[$supirId] = is_numeric($jumlah) ? floatval($jumlah) : 0;
        }

        $request->merge(['jumlah' => $cleanJumlahData]);

        // Get kegiatan untuk menentukan validasi yang tepat
        $kegiatan = MasterKegiatan::find($request->input('kegiatan'));

        // Tentukan rules validasi berdasarkan kegiatan
        $validationRules = [
            'nomor_pembayaran' => 'required|string|max:255|unique:pembayaran_uang_muka,nomor_pembayaran,' . $id,
            'tanggal_pembayaran' => 'required|date',
            'kas_bank' => 'required|exists:akun_coa,id',
            'jenis_transaksi' => 'required|in:debit,kredit',
            'kegiatan' => 'required|exists:master_kegiatans,id',
            'keterangan' => 'nullable|string'
        ];

        if ($kegiatan && strtolower($kegiatan->nama) === 'uang muka kir & stnk') {
            // Untuk KIR & STNK: wajib mobil, tidak butuh supir
            $validationRules['mobil_id'] = 'required|exists:mobils,id';
            $validationRules['jumlah_mobil'] = 'required|numeric|min:0';
            $validationRules['penerima_id'] = 'required|exists:karyawans,id';
        } elseif ($kegiatan && (stripos($kegiatan->nama, 'ob muat') !== false || stripos($kegiatan->nama, 'ob bongkar') !== false)) {
            // Untuk OB Muat/Bongkar: wajib supir
            $validationRules['supir'] = 'required|array|min:1';
            $validationRules['supir.*'] = 'required|exists:karyawans,id';
            $validationRules['jumlah'] = 'required|array|min:1';
            $validationRules['jumlah.*'] = 'required|numeric|min:0';
        } elseif ($kegiatan && (stripos($kegiatan->nama, 'amprahan') !== false || $kegiatan->type === 'lainnya')) {
            // Untuk Amprahan dan Lainnya: wajib penerima
            $validationRules['penerima_id'] = 'required|exists:karyawans,id';
            $validationRules['jumlah_penerima'] = 'required|numeric|min:0';
        } else {
            // Default: untuk kegiatan supir biasa
            $validationRules['supir'] = 'required|array|min:1';
            $validationRules['supir.*'] = 'required|exists:karyawans,id';
            $validationRules['jumlah'] = 'required|array|min:1';
            $validationRules['jumlah.*'] = 'required|numeric|min:0';
        }

        // Validasi input dengan rules yang sudah ditentukan
        $validated = $request->validate($validationRules);

        try {
            DB::beginTransaction();

            // Hitung total pembayaran
            $totalPembayaran = 0;
            $jumlahPerSupirData = [];
            $supirIds = [];

            // Check jika ada mobil_id (untuk KIR & STNK)
            if (!empty($validated['mobil_id'])) {
                $totalPembayaran = floatval($validated['jumlah_mobil'] ?? 0);
                $supirIds = []; // Array kosong untuk KIR & STNK
            } elseif (!empty($validated['jumlah_penerima'])) {
                // Logic untuk penerima (Amprahan dan kegiatan lainnya)
                $totalPembayaran = floatval($validated['jumlah_penerima']);
                $supirIds = []; // Array kosong untuk kegiatan penerima
            } else {
                // Logic untuk supir (OB Muat/Bongkar)
                foreach ($validated['supir'] as $supirId) {
                    $jumlah = floatval($validated['jumlah'][$supirId] ?? 0);
                    $jumlahPerSupirData[$supirId] = $jumlah;
                    $totalPembayaran += $jumlah;
                }
                $supirIds = $validated['supir'];
            }

            // Update pembayaran Uang Muka
            $pembayaran->update([
                'nomor_pembayaran' => $validated['nomor_pembayaran'],
                'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
                'kas_bank_id' => $validated['kas_bank'],
                'jenis_transaksi' => $validated['jenis_transaksi'],
                'kegiatan' => $validated['kegiatan'],
                'mobil_id' => $validated['mobil_id'] ?? null,
                'penerima_id' => $validated['penerima_id'] ?? null,
                'supir_ids' => $supirIds,
                'jumlah_per_supir' => $jumlahPerSupirData,
                'total_pembayaran' => $totalPembayaran,
                'keterangan' => $validated['keterangan'],
            ]);

            DB::commit();

            $jumlahSupir = count($validated['supir']);
            $message = "Pembayaran Uang Muka berhasil diupdate. ";
            $message .= "Nomor: {$validated['nomor_pembayaran']}. ";
            $message .= "Kegiatan: {$validated['kegiatan']}. ";
            $message .= "Total supir: {$jumlahSupir}. ";
            $message .= "Total pembayaran: Rp " . number_format($totalPembayaran, 0, ',', '.') . ".";

            return redirect()->route('pembayaran-uang-muka.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withErrors([
                'error' => 'Gagal mengupdate pembayaran Uang Muka: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $pembayaran = PembayaranUangMuka::findOrFail($id);

            // Check if already used
            if ($pembayaran->isUsed()) {
                return redirect()->route('pembayaran-uang-muka.index')
                               ->with('error', 'Tidak dapat menghapus Uang Muka yang sudah terpakai.');
            }

            $nomorPembayaran = $pembayaran->nomor_pembayaran;
            $pembayaran->delete();

            return redirect()->route('pembayaran-uang-muka.index')
                           ->with('success', "Pembayaran Uang Muka {$nomorPembayaran} berhasil dihapus.");

        } catch (\Exception $e) {
            return redirect()->route('pembayaran-uang-muka.index')
                           ->with('error', 'Gagal menghapus pembayaran Uang Muka: ' . $e->getMessage());
        }
    }

    /**
     * Record accounting entries for pembayaran uang muka
     */
    private function recordAccountingEntries($pembayaran, $validated, $totalPembayaran)
    {
        // Get kegiatan info
        $kegiatan = MasterKegiatan::find($validated['kegiatan']);
        $kegiatanText = $kegiatan ? $kegiatan->nama_kegiatan : '';

        // Get kas/bank COA
        $kasBankCoa = Coa::find($validated['kas_bank']);

        if (!$kasBankCoa) {
            throw new \Exception('COA Kas/Bank tidak ditemukan');
        }

        // Untuk jenis transaksi KREDIT - uang keluar dari kas/bank
        if ($validated['jenis_transaksi'] === 'kredit') {
            // Update saldo kas/bank terlebih dahulu
            $kasBankCoa->decrement('saldo', $totalPembayaran);
            $kasBankCoa->refresh(); // Refresh model untuk mendapatkan saldo terbaru

            // 1. KREDIT pada akun kas/bank (mengurangi saldo)
            $this->createLedgerEntry(
                $kasBankCoa->id,
                $pembayaran->nomor_pembayaran,
                $pembayaran->tanggal_pembayaran,
                'Pembayaran Uang Muka - ' . $kegiatanText,
                0, // debet
                $totalPembayaran, // kredit
                $kasBankCoa->saldo // saldo terbaru setelah decrement
            );

            // 2. DEBIT pada COA uang muka yang sesuai
            $this->debitUangMukaCoa($pembayaran, $kegiatanText, $totalPembayaran);
        }

        // Untuk jenis transaksi DEBIT - uang masuk ke kas/bank
        if ($validated['jenis_transaksi'] === 'debit') {
            // Update saldo kas/bank terlebih dahulu
            $kasBankCoa->increment('saldo', $totalPembayaran);
            $kasBankCoa->refresh(); // Refresh model untuk mendapatkan saldo terbaru

            // 1. DEBIT pada akun kas/bank (menambah saldo)
            $this->createLedgerEntry(
                $kasBankCoa->id,
                $pembayaran->nomor_pembayaran,
                $pembayaran->tanggal_pembayaran,
                'Penerimaan Uang Muka - ' . $kegiatanText,
                $totalPembayaran, // debet
                0, // kredit
                $kasBankCoa->saldo // saldo terbaru setelah increment
            );

            // 2. KREDIT pada COA uang muka yang sesuai
            $this->creditUangMukaCoa($pembayaran, $kegiatanText, $totalPembayaran);
        }
    }

    /**
     * Create debit entry for uang muka COA
     */
    private function debitUangMukaCoa($pembayaran, $kegiatanText, $amount)
    {
        $coaCode = null;
        $coaName = null;

        // Tentukan COA berdasarkan kegiatan
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
            // Kegiatan lainnya -> COA Uang Muka Umum
            $coaCode = '1150009';
            $coaName = 'Uang Muka';
        }

        // Cari atau buat COA
        $uangMukaCoa = Coa::where('nomor_akun', $coaCode)->first();

        if (!$uangMukaCoa) {
            // Buat COA baru jika tidak ada
            $uangMukaCoa = Coa::create([
                'nomor_akun' => $coaCode,
                'nama_akun' => $coaName,
                'tipe_akun' => 'Asset',
                'saldo' => 0,
                'status' => 'Aktif'
            ]);
        }

        // Update saldo COA uang muka terlebih dahulu
        $uangMukaCoa->increment('saldo', $amount);
        $uangMukaCoa->refresh(); // Refresh model untuk mendapatkan saldo terbaru

        // DEBIT uang muka COA (menambah aset)
        $this->createLedgerEntry(
            $uangMukaCoa->id,
            $pembayaran->nomor_pembayaran,
            $pembayaran->tanggal_pembayaran,
            'Pembayaran Uang Muka - ' . $kegiatanText,
            $amount, // debet
            0, // kredit
            $uangMukaCoa->saldo // saldo terbaru setelah increment
        );
    }

    /**
     * Create credit entry for uang muka COA
     */
    private function creditUangMukaCoa($pembayaran, $kegiatanText, $amount)
    {
        $coaCode = null;
        $coaName = null;

        // Tentukan COA berdasarkan kegiatan
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
            // Kegiatan lainnya -> COA Uang Muka Umum
            $coaCode = '1150009';
            $coaName = 'Uang Muka';
        }

        // Cari atau buat COA
        $uangMukaCoa = Coa::where('nomor_akun', $coaCode)->first();

        if (!$uangMukaCoa) {
            // Buat COA baru jika tidak ada
            $uangMukaCoa = Coa::create([
                'nomor_akun' => $coaCode,
                'nama_akun' => $coaName,
                'tipe_akun' => 'Asset',
                'saldo' => 0,
                'status' => 'Aktif'
            ]);
        }

        // Update saldo COA uang muka terlebih dahulu
        $uangMukaCoa->decrement('saldo', $amount);
        $uangMukaCoa->refresh(); // Refresh model untuk mendapatkan saldo terbaru

        // KREDIT uang muka COA (mengurangi aset)
        $this->createLedgerEntry(
            $uangMukaCoa->id,
            $pembayaran->nomor_pembayaran,
            $pembayaran->tanggal_pembayaran,
            'Penerimaan Uang Muka - ' . $kegiatanText,
            0, // debet
            $amount, // kredit
            $uangMukaCoa->saldo // saldo terbaru setelah decrement
        );
    }

    /**
     * Create ledger entry
     */
    private function createLedgerEntry($coaId, $nomorTransaksi, $tanggal, $keterangan, $debet, $kredit, $saldoBaru)
    {
        // Create entry in coa_transactions table
        \App\Models\CoaTransaction::create([
            'coa_id' => $coaId,
            'tanggal_transaksi' => $tanggal,
            'nomor_referensi' => $nomorTransaksi,
            'jenis_transaksi' => 'Uang Muka',
            'keterangan' => $keterangan,
            'debit' => $debet,
            'kredit' => $kredit,
            'saldo' => $saldoBaru,
            'created_by' => Auth::id(),
        ]);

        // Log for debugging
        Log::info('Accounting Entry Created', [
            'coa_id' => $coaId,
            'nomor_referensi' => $nomorTransaksi,
            'tanggal_transaksi' => $tanggal,
            'keterangan' => $keterangan,
            'debit' => $debet,
            'kredit' => $kredit,
            'saldo_baru' => $saldoBaru,
        ]);
    }
}
