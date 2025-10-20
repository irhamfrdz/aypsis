<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterTerminal;
use App\Models\MasterKapal;
use App\Models\Kontainer;
use App\Models\GateIn;
use App\Models\GateInPetikemas;
use App\Models\GateInAktivitas;
use App\Models\PricelistGateIn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GateInController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('gate-in-view');

        $gateIns = GateIn::with(['terminal', 'kapal', 'kontainers'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('gate-in.index', compact('gateIns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('gate-in-create');

        // Get master data for dropdowns
        $terminals = MasterTerminal::where('status', 'aktif')->orderBy('nama_terminal')->get();
        $kapals = MasterKapal::where('status', 'aktif')->orderBy('nama_kapal')->get();

        // Get unique pelabuhan from pricelist_gate_ins
        $pelabuhans = PricelistGateIn::select('pelabuhan')
            ->where('status', 'aktif')
            ->distinct()
            ->orderBy('pelabuhan')
            ->pluck('pelabuhan')
            ->toArray();

        // Get unique kegiatan from pricelist_gate_ins
        $kegiatans = PricelistGateIn::select('kegiatan')
            ->where('status', 'aktif')
            ->distinct()
            ->orderBy('kegiatan')
            ->pluck('kegiatan')
            ->toArray();

        // Get unique gudang from pricelist_gate_ins
        $gudangs = PricelistGateIn::select('gudang')
            ->where('status', 'aktif')
            ->whereNotNull('gudang')
            ->where('gudang', '!=', '')
            ->distinct()
            ->orderBy('gudang')
            ->pluck('gudang')
            ->toArray();

        // Get unique kontainer from pricelist_gate_ins
        $kontainerOptions = PricelistGateIn::select('kontainer')
            ->where('status', 'aktif')
            ->whereNotNull('kontainer')
            ->where('kontainer', '!=', '')
            ->distinct()
            ->orderBy('kontainer')
            ->pluck('kontainer')
            ->toArray();

        // Get unique muatan from pricelist_gate_ins
        $muatans = PricelistGateIn::select('muatan')
            ->where('status', 'aktif')
            ->whereNotNull('muatan')
            ->where('muatan', '!=', '')
            ->distinct()
            ->orderBy('muatan')
            ->pluck('muatan')
            ->toArray();

        // Get kontainers yang sudah checkpoint supir dan belum gate in
        $kontainers = Kontainer::where('status_checkpoint_supir', 'selesai')
            ->where('status_gate_in', '!=', 'selesai')
            ->orderBy('nomor_seri_gabungan')
            ->get();

        return view('gate-in.create', compact('terminals', 'kapals', 'pelabuhans', 'kegiatans', 'gudangs', 'kontainerOptions', 'muatans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('gate-in-create');

        $request->validate([
            'nomor_gate_in' => 'required|string|max:50|unique:gate_ins,nomor_gate_in',
            'pelabuhan' => 'required|string|max:255',
            'kegiatan' => 'required|string|max:255',
            'gudang' => 'required|string|max:255',
            'kontainer' => 'required|string|max:255',
            'muatan' => 'required|string|max:255',
            'kapal_id' => 'required|exists:master_kapals,id',
            'kontainer_ids' => 'required|array|min:1',
            'kontainer_ids.*' => 'required|integer|exists:surat_jalans,id',
            'keterangan' => 'nullable|string|max:500'
        ], [
            'nomor_gate_in.required' => 'Nomor Gate In wajib diisi.',
            'nomor_gate_in.unique' => 'Nomor Gate In sudah digunakan, silakan gunakan nomor lain.',
            'nomor_gate_in.max' => 'Nomor Gate In maksimal 50 karakter.',
            'pelabuhan.required' => 'Pelabuhan wajib dipilih.',
            'kegiatan.required' => 'Kegiatan wajib dipilih.',
            'gudang.required' => 'Gudang wajib dipilih.',
            'kontainer.required' => 'Kontainer wajib dipilih.',
            'muatan.required' => 'Muatan wajib dipilih.',
            'kapal_id.required' => 'Kapal wajib dipilih.',
            'kapal_id.exists' => 'Kapal yang dipilih tidak valid.',
            'kontainer_ids.required' => 'Pilih minimal satu kontainer.',
            'kontainer_ids.min' => 'Pilih minimal satu kontainer.',
            'kontainer_ids.*.required' => 'Data kontainer tidak boleh kosong.',
            'kontainer_ids.*.integer' => 'Data kontainer tidak valid.',
            'kontainer_ids.*.exists' => 'Surat jalan yang dipilih tidak ditemukan.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.'
        ]);

        DB::beginTransaction();
        try {
            // Create Gate In record
            $gateIn = GateIn::create([
                'nomor_gate_in' => $request->nomor_gate_in,
                'pelabuhan' => $request->pelabuhan,
                'kegiatan' => $request->kegiatan,
                'gudang' => $request->gudang,
                'kontainer' => $request->kontainer,
                'muatan' => $request->muatan,
                'kapal_id' => $request->kapal_id,
                'tanggal_gate_in' => now(),
                'user_id' => Auth::id(),
                'keterangan' => $request->keterangan,
                'status' => 'aktif'
            ]);

            // Log data yang diterima
            Log::info('Gate In store - surat_jalan IDs diterima:', ['count' => count($request->kontainer_ids), 'ids' => $request->kontainer_ids]);

            // Update surat jalan yang dipilih dengan gate_in_id
            $updateCount = 0;
            $errorMessages = [];

            foreach ($request->kontainer_ids as $suratJalanId) {
                // Ambil data surat jalan
                $suratJalan = DB::table('surat_jalans')->where('id', $suratJalanId)->first();

                if (!$suratJalan) {
                    $errorMessages[] = "Surat jalan dengan ID {$suratJalanId} tidak ditemukan";
                    continue;
                }

                // Validasi apakah surat jalan sudah checkpoint
                if ($suratJalan->status !== 'sudah_checkpoint') {
                    $errorMessages[] = "Surat jalan {$suratJalan->no_surat_jalan} belum melalui checkpoint supir";
                    continue;
                }

                if (!$suratJalan->no_kontainer) {
                    $errorMessages[] = "Surat jalan {$suratJalan->no_surat_jalan} tidak memiliki nomor kontainer";
                    continue;
                }

                // Update surat jalan dengan gate_in_id dan status gate in
                $updated = DB::table('surat_jalans')->where('id', $suratJalanId)->update([
                    'gate_in_id' => $gateIn->id,
                    'status_gate_in' => 'selesai',
                    'tanggal_gate_in' => now(),
                    'updated_at' => now()
                ]);

                if ($updated) {
                    $updateCount++;
                    Log::info('Surat jalan ' . $suratJalan->no_surat_jalan . ' dengan kontainer ' . $suratJalan->no_kontainer . ' linked ke Gate In ID ' . $gateIn->id);

                    // Opsional: Jika ada tabel kontainers dan ingin sync data juga
                    try {
                        $kontainer = Kontainer::where('nomor_seri_gabungan', $suratJalan->no_kontainer)->first();
                        if ($kontainer) {
                            $kontainer->update([
                                'gate_in_id' => $gateIn->id,
                                'status_gate_in' => 'selesai',
                                'tanggal_gate_in' => now()
                            ]);
                            Log::info('Kontainer ' . $suratJalan->no_kontainer . ' juga diupdate di tabel kontainers');
                        }
                    } catch (\Exception $e) {
                        // Jika gagal update kontainer, tidak perlu error karena data utama di surat_jalans
                        Log::warning('Gagal update tabel kontainers untuk nomor: ' . $suratJalan->no_kontainer . '. Error: ' . $e->getMessage());
                    }
                } else {
                    $errorMessages[] = "Gagal mengupdate surat jalan {$suratJalan->no_surat_jalan}";
                }
            }

            // Jika tidak ada kontainer yang berhasil diupdate, rollback dan return error
            if ($updateCount === 0) {
                DB::rollback();
                $errorMessage = 'Tidak ada kontainer yang berhasil ditambahkan ke Gate In. ';
                $errorMessage .= implode('; ', $errorMessages);

                return back()->withInput()
                    ->with('error', $errorMessage);
            }

            Log::info('Gate In created with ID ' . $gateIn->id . ' - Updated ' . $updateCount . ' kontainer records');

            // Process multiple biaya for specific kegiatan like RECEIVING
            $this->processMultipleBiaya($gateIn, $request->kontainer_ids);

            DB::commit();

            $successMessage = 'Gate In berhasil dibuat dengan nomor: ' . $gateIn->nomor_gate_in . ' (' . $updateCount . ' kontainer berhasil ditambahkan)';

            // Tambahkan warning jika ada error pada beberapa kontainer
            if (!empty($errorMessages)) {
                $successMessage .= '. Peringatan: ' . implode('; ', $errorMessages);
            }

            return redirect()->route('gate-in.show', $gateIn)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gate In store error: ' . $e->getMessage());
            Log::error('Gate In store stack trace: ' . $e->getTraceAsString());

            // Buat pesan error yang lebih informatif
            $errorMessage = 'Gagal menyimpan Gate In. ';

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $errorMessage .= 'Nomor Gate In sudah digunakan, silakan gunakan nomor lain.';
            } elseif (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                $errorMessage .= 'Data yang dipilih tidak valid atau sudah dihapus.';
            } elseif (strpos($e->getMessage(), 'Connection refused') !== false) {
                $errorMessage .= 'Koneksi database bermasalah, silakan coba lagi.';
            } else {
                $errorMessage .= 'Detail error: ' . $e->getMessage();
            }

            return back()->withInput()
                ->with('error', $errorMessage)
                ->withErrors(['general' => 'Silakan periksa kembali data yang diinput dan coba lagi.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(GateIn $gateIn)
    {
        $this->authorize('gate-in-view');

        // Load relasi yang benar - suratJalans bukan kontainers, plus aktivitas dan petikemas
        $gateIn->load(['terminal', 'kapal', 'suratJalans', 'user', 'aktivitas', 'petikemas']);

        return view('gate-in.show', compact('gateIn'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GateIn $gateIn)
    {
        $this->authorize('gate-in-update');

        $terminals = MasterTerminal::where('status', 'aktif')->orderBy('nama_terminal')->get();
        $kapals = MasterKapal::where('status', 'aktif')->orderBy('nama_kapal')->get();

        // Get kontainers yang sudah checkpoint supir (termasuk yang sudah terpilih di gate in ini)
        $kontainers = Kontainer::where(function($query) use ($gateIn) {
            $query->where('status_checkpoint_supir', 'selesai')
                  ->where(function($q) use ($gateIn) {
                      $q->where('status_gate_in', '!=', 'selesai')
                        ->orWhere('gate_in_id', $gateIn->id);
                  });
        })->orderBy('nomor_seri_gabungan')->get();

        $gateIn->load(['kontainers']);

        return view('gate-in.edit', compact('gateIn', 'terminals', 'kapals', 'kontainers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GateIn $gateIn)
    {
        $this->authorize('gate-in-update');

        $request->validate([
            'kapal_id' => 'required|exists:master_kapals,id',
            'kontainer_ids' => 'required|array|min:1',
            'kontainer_ids.*' => 'exists:kontainers,id',
            'keterangan' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            // Update Gate In record
            $gateIn->update([
                'kapal_id' => $request->kapal_id,
                'keterangan' => $request->keterangan
            ]);

            // Reset kontainer yang sebelumnya terkait dengan gate in ini
            Kontainer::where('gate_in_id', $gateIn->id)->update([
                'gate_in_id' => null,
                'status_gate_in' => 'pending',
                'tanggal_gate_in' => null
            ]);

            // Update kontainer baru yang dipilih
            foreach ($request->kontainer_ids as $kontainerId) {
                $kontainer = Kontainer::find($kontainerId);
                $kontainer->update([
                    'gate_in_id' => $gateIn->id,
                    'status_gate_in' => 'selesai',
                    'tanggal_gate_in' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('gate-in.show', $gateIn)
                ->with('success', 'Gate In berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui Gate In: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GateIn $gateIn)
    {
        $this->authorize('gate-in-delete');

        DB::beginTransaction();
        try {
            // Reset status kontainer yang terkait
            Kontainer::where('gate_in_id', $gateIn->id)->update([
                'gate_in_id' => null,
                'status_gate_in' => 'pending',
                'tanggal_gate_in' => null
            ]);

            // Soft delete gate in record
            $gateIn->update(['status' => 'nonaktif']);

            DB::commit();

            return redirect()->route('gate-in.index')
                ->with('success', 'Gate In berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat menghapus Gate In: ' . $e->getMessage());
        }
    }

    /**
     * Get kontainers by filters via AJAX
     */
    public function getKontainers(Request $request)
    {
        $query = Kontainer::where('status_checkpoint_supir', 'selesai')
            ->where('status_gate_in', '!=', 'selesai');

        if ($request->kapal_id) {
            $query->where('kapal_id', $request->kapal_id);
        }

        $kontainers = $query->orderBy('nomor_seri_gabungan')->get();

        return response()->json($kontainers);
    }

    /**
     * Get kontainers from surat jalan by filters via AJAX
     */
    public function getKontainersSuratJalan(Request $request)
    {
        try {
            $kontainerSize = $request->query('kontainer_size');

            Log::info('getKontainersSuratJalan called', ['kontainer_size' => $kontainerSize]);

            // Query surat jalan yang sudah approved dan belum gate in
            $query = DB::table('surat_jalans as sj')
                ->whereIn('sj.status', ['approved', 'fully_approved', 'completed', 'sudah_checkpoint'])
                ->whereNotNull('sj.no_kontainer')
                ->where('sj.no_kontainer', '!=', '')
                ->whereNull('sj.gate_in_id') // Belum pernah gate in
                ->select(
                    'sj.id',
                    'sj.no_surat_jalan',
                    'sj.no_kontainer as nomor_kontainer',
                    'sj.size',
                    'sj.jumlah_kontainer',
                    'sj.tujuan_pengiriman',
                    'sj.supir as supir_nama',
                    'sj.no_plat as plat_nomor',
                    'sj.tipe_kontainer',
                    'sj.no_seal',
                    'sj.kegiatan as kegiatan_surat_jalan'
                );

            // Filter by kontainer size if provided
            if ($kontainerSize) {
                $query->where('sj.size', $kontainerSize);
                Log::info('Filtering by kontainer size:', ['size' => $kontainerSize]);
            }

            $kontainers = $query->orderBy('sj.no_surat_jalan')->get();

            Log::info('Found kontainers:', ['count' => $kontainers->count(), 'size_filter' => $kontainerSize]);

            return response()->json($kontainers);
        } catch (\Exception $e) {
            Log::error('getKontainersSuratJalan error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Add kontainer to gate in
     */
    public function addKontainer(Request $request, GateIn $gateIn)
    {
        $this->authorize('gate-in-update');

        $request->validate([
            'kontainer_id' => 'required|exists:kontainers,id'
        ]);

        $kontainer = Kontainer::find($request->kontainer_id);

        // Check if kontainer already in this gate in
        if ($gateIn->kontainers()->where('kontainer_id', $kontainer->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kontainer sudah ada dalam gate in ini'
            ]);
        }

        // Check if kontainer has required checkpoint
        if ($kontainer->status_checkpoint_supir !== 'selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Kontainer belum selesai checkpoint supir'
            ]);
        }

        // Add kontainer to gate in
        $gateIn->kontainers()->attach($kontainer->id, [
            'waktu_masuk' => null,
            'waktu_keluar' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Update kontainer
        $kontainer->update([
            'gate_in_id' => $gateIn->id,
            'status_gate_in' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kontainer berhasil ditambahkan'
        ]);
    }

    /**
     * Remove kontainer from gate in
     */
    public function removeKontainer(Request $request, GateIn $gateIn)
    {
        $this->authorize('gate-in-update');

        $request->validate([
            'kontainer_id' => 'required|exists:kontainers,id'
        ]);

        $kontainer = Kontainer::find($request->kontainer_id);

        // Remove from gate in
        $gateIn->kontainers()->detach($kontainer->id);

        // Update kontainer
        $kontainer->update([
            'gate_in_id' => null,
            'status_gate_in' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kontainer berhasil dihapus'
        ]);
    }

    /**
     * Update gate in status
     */
    public function updateStatus(Request $request, GateIn $gateIn)
    {
        $this->authorize('gate-in-update');

        $request->validate([
            'status' => 'required|in:aktif,selesai,dibatalkan'
        ]);

        $gateIn->update([
            'status' => $request->status,
            'waktu_keluar' => $request->status === 'selesai' ? now() : $gateIn->waktu_keluar
        ]);

        // Update kontainer status if gate in is completed
        if ($request->status === 'selesai') {
            $gateIn->kontainers()->where('status_gate_in', '!=', 'keluar')->update([
                'status_gate_in' => 'keluar'
            ]);
        }

        return redirect()->route('gate-in.show', $gateIn)->with('success', 'Status gate in berhasil diupdate');
    }


    /**
     * Get gudang options based on selected kegiatan
     */
    public function getGudangByKegiatan(Request $request)
    {
        $kegiatan = $request->query('kegiatan');

        if (!$kegiatan) {
            return response()->json(['gudangs' => []]);
        }

        $gudangs = PricelistGateIn::where('kegiatan', $kegiatan)
            ->where('status', 'aktif')
            ->whereNotNull('gudang')
            ->where('gudang', '!=', '')
            ->select('gudang')
            ->distinct()
            ->orderBy('gudang')
            ->pluck('gudang')
            ->toArray();

        return response()->json(['gudangs' => $gudangs]);
    }

    /**
     * Get kontainer options based on selected kegiatan
     */
    public function getKontainerByKegiatan(Request $request)
    {
        $kegiatan = $request->query('kegiatan');

        if (!$kegiatan) {
            return response()->json(['kontainers' => []]);
        }

        $kontainers = PricelistGateIn::where('kegiatan', $kegiatan)
            ->where('status', 'aktif')
            ->whereNotNull('kontainer')
            ->where('kontainer', '!=', '')
            ->select('kontainer')
            ->distinct()
            ->orderBy('kontainer')
            ->pluck('kontainer')
            ->toArray();

        return response()->json(['kontainers' => $kontainers]);
    }

    /**
     * Get muatan options based on selected kegiatan
     */
    public function getMuatanByKegiatan(Request $request)
    {
        $kegiatan = $request->query('kegiatan');

        if (!$kegiatan) {
            return response()->json(['muatans' => []]);
        }

        $muatans = PricelistGateIn::where('kegiatan', $kegiatan)
            ->where('status', 'aktif')
            ->whereNotNull('muatan')
            ->where('muatan', '!=', '')
            ->select('muatan')
            ->distinct()
            ->orderBy('muatan')
            ->pluck('muatan')
            ->toArray();

        return response()->json(['muatans' => $muatans]);
    }

    /**
     * Calculate total cost based on selected inputs with multiple biaya support
     */
    public function calculateTotal(Request $request)
    {
        try {
            $pelabuhan = $request->query('pelabuhan');
            $kegiatan = $request->query('kegiatan');
            $gudang = $request->query('gudang');
            $kontainer = $request->query('kontainer');
            $muatan = $request->query('muatan');
            $selectedKontainers = $request->query('kontainer_ids', []);

            // If no kontainer selected, return 0
            if (empty($selectedKontainers)) {
                return response()->json([
                    'total' => 0,
                    'formatted_total' => 'Rp 0',
                    'breakdown' => [],
                    'kontainer_count' => 0,
                    'aktivitas_details' => []
                ]);
            }

            $kontainerCount = count($selectedKontainers);

            // Check if this kegiatan has multiple biaya
            $kegiatanWithMultipleBiaya = ['RECEIVING', 'DISCHARGE', 'LOADING'];

            if (in_array($kegiatan, $kegiatanWithMultipleBiaya)) {
                return $this->calculateMultipleBiayaTotal($pelabuhan, $kegiatan, $gudang, $kontainer, $muatan, $kontainerCount);
            }

            // Single biaya calculation (existing logic)
            return $this->calculateSingleBiayaTotal($pelabuhan, $kegiatan, $gudang, $kontainer, $muatan, $kontainerCount);

        } catch (\Exception $e) {
            Log::error('Calculate total error:', ['message' => $e->getMessage()]);
            return response()->json([
                'total' => 0,
                'formatted_total' => 'Error dalam perhitungan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate total for multiple biaya (like RECEIVING with LOLO, HAULAGE, ADMINISTRASI)
     */
    private function calculateMultipleBiayaTotal($pelabuhan, $kegiatan, $gudang, $kontainer, $muatan, $kontainerCount)
    {
        $biayaMapping = [
            'RECEIVING' => ['ADMINISTRASI', 'HAULAGE', 'LOLO', 'MASA 1A'],
            'DISCHARGE' => ['ADMINISTRASI', 'HAULAGE', 'LOLO', 'STEVEDORING'],
            'LOADING' => ['ADMINISTRASI', 'HAULAGE', 'LOLO', 'STUFFING']
        ];

        $biayaList = $biayaMapping[$kegiatan] ?? [];
        $aktivitasDetails = [];
        $totalKeseluruhan = 0;

        foreach ($biayaList as $biaya) {
            // Get tarif from pricelist_gate_ins
            // For exact match first, then try with nullable fields for ADMINISTRASI
            $pricelist = PricelistGateIn::where('status', 'aktif')
                ->where('pelabuhan', $pelabuhan)
                ->where('kegiatan', $kegiatan)
                ->where('biaya', $biaya)
                ->where('gudang', $gudang)
                ->where('kontainer', $kontainer)
                ->where('muatan', $muatan)
                ->first();

            // If not found and this is ADMINISTRASI, try with nullable kontainer/muatan
            if (!$pricelist && $biaya === 'ADMINISTRASI') {
                $pricelist = PricelistGateIn::where('status', 'aktif')
                    ->where('pelabuhan', $pelabuhan)
                    ->where('kegiatan', $kegiatan)
                    ->where('biaya', $biaya)
                    ->where('gudang', $gudang)
                    ->where(function($query) use ($kontainer, $muatan) {
                        $query->where(function($q) use ($kontainer, $muatan) {
                            // Exact match
                            $q->where('kontainer', $kontainer)->where('muatan', $muatan);
                        })->orWhere(function($q) {
                            // Nullable fields for general ADMINISTRASI
                            $q->whereNull('kontainer')->whereNull('muatan');
                        });
                    })
                    ->first();
            }

            if ($pricelist) {
                // For ADMINISTRASI: box=1, itm=1 (charged once regardless of kontainer count)
                // For others: box=kontainer_count, itm=1 (charged per kontainer)
                $box = $biaya === 'ADMINISTRASI' ? 1 : $kontainerCount;
                $itm = 1;
                $total = $box * $itm * $pricelist->tarif;
                $totalKeseluruhan += $total;

                $aktivitasDetails[] = [
                    'aktivitas' => $biaya,
                    's_t_s' => $biaya === 'ADMINISTRASI' ? '0/-/-' : "{$kontainer}/DRY/F",
                    'box' => $box,
                    'itm' => $itm,
                    'tarif' => $pricelist->tarif,
                    'formatted_tarif' => 'Rp ' . number_format((float)$pricelist->tarif, 0, ',', '.'),
                    'total' => $total,
                    'formatted_total' => 'Rp ' . number_format($total, 0, ',', '.')
                ];
            } else {
                Log::warning("Pricelist tidak ditemukan untuk biaya: {$biaya}");
            }
        }

        // Calculate grand total with tax calculations
        $subtotal = $totalKeseluruhan;
        
        // Calculate materai (10,000 for transactions above 5 million)
        $materai = $subtotal > 5000000 ? 10000 : 0;
        
        // Calculate PPN (11% added to subtotal)
        $ppn = $subtotal * 0.11;
        $totalWithPPN = $subtotal + $ppn;
        
        // Calculate PPH (2% reduced from total)
        $pph = $totalWithPPN * 0.02;
        $grandTotal = $totalWithPPN - $pph + $materai;

        return response()->json([
            'total' => $grandTotal,
            'formatted_total' => 'Rp ' . number_format($grandTotal, 0, ',', '.'),
            'subtotal' => $subtotal,
            'formatted_subtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
            'ppn' => $ppn,
            'formatted_ppn' => 'Rp ' . number_format($ppn, 0, ',', '.'),
            'pph' => $pph,
            'formatted_pph' => 'Rp ' . number_format($pph, 0, ',', '.'),
            'materai' => $materai,
            'formatted_materai' => 'Rp ' . number_format($materai, 0, ',', '.'),
            'kontainer_count' => $kontainerCount,
            'aktivitas_details' => $aktivitasDetails,
            'breakdown' => [
                'pelabuhan' => $pelabuhan,
                'kegiatan' => $kegiatan,
                'gudang' => $gudang,
                'kontainer' => $kontainer,
                'muatan' => $muatan
            ],
            'is_multiple_biaya' => true
        ]);
    }

    /**
     * Calculate total for single biaya (existing logic)
     */
    private function calculateSingleBiayaTotal($pelabuhan, $kegiatan, $gudang, $kontainer, $muatan, $kontainerCount)
    {
        // Find matching pricelist
        $pricelistQuery = PricelistGateIn::where('status', 'aktif');

        if ($pelabuhan) {
            $pricelistQuery->where('pelabuhan', $pelabuhan);
        }
        if ($kegiatan) {
            $pricelistQuery->where('kegiatan', $kegiatan);
        }
        if ($gudang) {
            $pricelistQuery->where('gudang', $gudang);
        }
        if ($kontainer) {
            $pricelistQuery->where('kontainer', $kontainer);
        }
        if ($muatan) {
            $pricelistQuery->where('muatan', $muatan);
        }

        $pricelist = $pricelistQuery->first();

        if (!$pricelist) {
            return response()->json([
                'total' => 0,
                'formatted_total' => 'Tarif tidak ditemukan',
                'breakdown' => [],
                'kontainer_count' => $kontainerCount,
                'aktivitas_details' => [],
                'error' => 'Kombinasi input tidak ditemukan dalam pricelist',
                'is_multiple_biaya' => false
            ]);
        }

        // Calculate total
        $unitPrice = $pricelist->tarif ?? 0;
        $total = $unitPrice * $kontainerCount;

        return response()->json([
            'total' => $total,
            'formatted_total' => 'Rp ' . number_format($total, 0, ',', '.'),
            'unit_price' => $unitPrice,
            'formatted_unit_price' => 'Rp ' . number_format($unitPrice, 0, ',', '.'),
            'kontainer_count' => $kontainerCount,
            'aktivitas_details' => [],
            'breakdown' => [
                'pelabuhan' => $pelabuhan,
                'kegiatan' => $kegiatan,
                'gudang' => $gudang,
                'kontainer' => $kontainer,
                'muatan' => $muatan
            ],
            'is_multiple_biaya' => false
        ]);
    }

    /**
     * Generate nomor gate in
     */
    private function generateNomorGateIn()
    {
        $prefix = 'GI';
        $tahun = date('Y');
        $bulan = date('m');

        // Get last number for current month/year
        $lastGateIn = GateIn::where('nomor_gate_in', 'like', $prefix . $tahun . $bulan . '%')
            ->orderBy('nomor_gate_in', 'desc')
            ->first();

        if ($lastGateIn) {
            $lastNumber = (int) substr($lastGateIn->nomor_gate_in, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $tahun . $bulan . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Process multiple biaya for specific kegiatan like RECEIVING
     */
    private function processMultipleBiaya(GateIn $gateIn, array $kontainerIds)
    {
        try {
            // Only process for specific kegiatan that have multiple biaya
            $kegiatanWithMultipleBiaya = ['RECEIVING', 'DISCHARGE', 'LOADING'];

            if (!in_array($gateIn->kegiatan, $kegiatanWithMultipleBiaya)) {
                Log::info("Kegiatan {$gateIn->kegiatan} tidak memerlukan multiple biaya processing");
                return;
            }

            Log::info("Processing multiple biaya for kegiatan: {$gateIn->kegiatan}");

            // Get all aktivitas/biaya for this kegiatan from pricelist
            $aktivitasList = $this->getAktivitasForKegiatan($gateIn);

            if (empty($aktivitasList)) {
                Log::warning("Tidak ada aktivitas ditemukan untuk kegiatan {$gateIn->kegiatan}");
                return;
            }

            // Get kontainer details from surat jalans
            $kontainerDetails = $this->getKontainerDetails($kontainerIds);

            // Create aktivitas details
            foreach ($aktivitasList as $aktivitas) {
                $this->createAktivitasDetail($gateIn, $aktivitas, $kontainerDetails);
            }

            // Create petikemas details
            foreach ($kontainerDetails as $kontainer) {
                $this->createPetikemas($gateIn, $kontainer, $aktivitasList);
            }

            Log::info("Multiple biaya processing completed for Gate In ID: {$gateIn->id}");

        } catch (\Exception $e) {
            Log::error("Error processing multiple biaya: " . $e->getMessage());
            // Don't throw exception to prevent transaction rollback
        }
    }

    /**
     * Get aktivitas/biaya list for specific kegiatan
     */
    private function getAktivitasForKegiatan(GateIn $gateIn)
    {
        $biayaMapping = [
            'RECEIVING' => ['ADMINISTRASI', 'HAULAGE', 'LOLO', 'MASA 1A'],
            'DISCHARGE' => ['ADMINISTRASI', 'HAULAGE', 'LOLO', 'STEVEDORING'],
            'LOADING' => ['ADMINISTRASI', 'HAULAGE', 'LOLO', 'STUFFING']
        ];

        $biayaList = $biayaMapping[$gateIn->kegiatan] ?? [];
        $aktivitasList = [];

        foreach ($biayaList as $biaya) {
            // Get tarif from pricelist_gate_ins
            $pricelist = PricelistGateIn::where('status', 'aktif')
                ->where('pelabuhan', $gateIn->pelabuhan)
                ->where('kegiatan', $gateIn->kegiatan)
                ->where('biaya', $biaya)
                ->where('gudang', $gateIn->gudang)
                ->where('kontainer', $gateIn->kontainer)
                ->where('muatan', $gateIn->muatan)
                ->first();

            // If not found and this is ADMINISTRASI, try with nullable kontainer/muatan
            if (!$pricelist && $biaya === 'ADMINISTRASI') {
                $pricelist = PricelistGateIn::where('status', 'aktif')
                    ->where('pelabuhan', $gateIn->pelabuhan)
                    ->where('kegiatan', $gateIn->kegiatan)
                    ->where('biaya', $biaya)
                    ->where('gudang', $gateIn->gudang)
                    ->where(function($query) use ($gateIn) {
                        $query->where(function($q) use ($gateIn) {
                            // Exact match
                            $q->where('kontainer', $gateIn->kontainer)->where('muatan', $gateIn->muatan);
                        })->orWhere(function($q) {
                            // Nullable fields for general ADMINISTRASI
                            $q->whereNull('kontainer')->whereNull('muatan');
                        });
                    })
                    ->first();
            }

            if ($pricelist) {
                $aktivitasList[] = [
                    'aktivitas' => $biaya,
                    'tarif' => $pricelist->tarif,
                    's_t_s' => $biaya === 'ADMINISTRASI' ? '0/-/-' : "{$gateIn->kontainer}/DRY/F"
                ];
            } else {
                Log::warning("Pricelist tidak ditemukan untuk biaya: {$biaya}");
            }
        }

        return $aktivitasList;
    }

    /**
     * Get kontainer details from surat jalans
     */
    private function getKontainerDetails(array $kontainerIds)
    {
        return DB::table('surat_jalans')
            ->whereIn('id', $kontainerIds)
            ->select('id', 'no_kontainer', 'size', 'tipe_kontainer')
            ->get()
            ->toArray();
    }

    /**
     * Create aktivitas detail record
     */
    private function createAktivitasDetail(GateIn $gateIn, array $aktivitas, array $kontainerDetails)
    {
        $kontainerCount = count($kontainerDetails);

        // For ADMINISTRASI, box=1, itm=1 regardless of kontainer count
        // For others, box=kontainer count, itm=1
        $box = $aktivitas['aktivitas'] === 'ADMINISTRASI' ? 1 : $kontainerCount;
        $itm = 1;
        $total = $box * $itm * $aktivitas['tarif'];

        GateInAktivitas::create([
            'gate_in_id' => $gateIn->id,
            'aktivitas' => $aktivitas['aktivitas'],
            's_t_s' => $aktivitas['s_t_s'],
            'box' => $box,
            'itm' => $itm,
            'tarif' => $aktivitas['tarif'],
            'total' => $total
        ]);

        Log::info("Created aktivitas: {$aktivitas['aktivitas']} with total: {$total}");
    }

    /**
     * Create petikemas detail record
     */
    private function createPetikemas(GateIn $gateIn, $kontainer, array $aktivitasList)
    {
        // Calculate total estimasi biaya for this petikemas
        $estimasiBiaya = 0;
        foreach ($aktivitasList as $aktivitas) {
            if ($aktivitas['aktivitas'] !== 'ADMINISTRASI') {
                $estimasiBiaya += $aktivitas['tarif'];
            }
        }

        // Add portion of ADMINISTRASI cost
        $administrasiAktivitas = collect($aktivitasList)->firstWhere('aktivitas', 'ADMINISTRASI');
        if ($administrasiAktivitas) {
            $kontainerCount = count($this->getKontainerDetails([$kontainer->id]));
            $estimasiBiaya += $administrasiAktivitas['tarif'] / $kontainerCount;
        }

        GateInPetikemas::create([
            'gate_in_id' => $gateIn->id,
            'no_petikemas' => $kontainer->no_kontainer,
            's_t_s' => "{$kontainer->size}/DRY/F",
            'estimasi' => now()->toDateString(),
            'estimasi_biaya' => $estimasiBiaya
        ]);

        Log::info("Created petikemas: {$kontainer->no_kontainer} with estimasi: {$estimasiBiaya}");
    }
}
