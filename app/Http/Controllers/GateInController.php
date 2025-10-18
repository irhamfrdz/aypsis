<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterTerminal;
use App\Models\MasterKapal;
use App\Models\MasterService;
use App\Models\Kontainer;
use App\Models\GateIn;
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

        $gateIns = GateIn::with(['terminal', 'kapal', 'service', 'kontainers.kontainer'])
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

        // Get kontainers yang sudah checkpoint supir dan belum gate in
        $kontainers = Kontainer::where('status_checkpoint_supir', 'selesai')
            ->where('status_gate_in', '!=', 'selesai')
            ->orderBy('nomor_seri_gabungan')
            ->get();

        return view('gate-in.create', compact('terminals', 'kapals'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('gate-in-create');

        $request->validate([
            'nomor_gate_in' => 'required|string|max:50|unique:gate_ins,nomor_gate_in',
            'terminal_id' => 'required|exists:master_terminals,id',
            'kapal_id' => 'required|exists:master_kapals,id',
            'kontainer_ids' => 'required|array|min:1',
            'kontainer_ids.*' => 'required|integer|exists:surat_jalans,id',
            'keterangan' => 'nullable|string|max:500'
        ], [
            'nomor_gate_in.required' => 'Nomor Gate In wajib diisi.',
            'nomor_gate_in.unique' => 'Nomor Gate In sudah digunakan, silakan gunakan nomor lain.',
            'nomor_gate_in.max' => 'Nomor Gate In maksimal 50 karakter.',
            'terminal_id.required' => 'Terminal wajib dipilih.',
            'terminal_id.exists' => 'Terminal yang dipilih tidak valid.',
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
            // Auto-assign service_id untuk gate in (ambil service yang aktif)
            $serviceId = MasterService::where('status', 'aktif')->value('id');
            if (!$serviceId) {
                throw new \Exception('Tidak ada service aktif yang tersedia. Hubungi administrator.');
            }

            $gateIn = GateIn::create([
                'nomor_gate_in' => $request->nomor_gate_in,
                'terminal_id' => $request->terminal_id,
                'kapal_id' => $request->kapal_id,
                'service_id' => $serviceId,
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

        // Load relasi yang benar - suratJalans bukan kontainers
        $gateIn->load(['terminal', 'kapal', 'service', 'suratJalans', 'user']);

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
        $services = MasterService::where('status', 'aktif')->orderBy('nama_service')->get();

        // Get kontainers yang sudah checkpoint supir (termasuk yang sudah terpilih di gate in ini)
        $kontainers = Kontainer::where(function($query) use ($gateIn) {
            $query->where('status_checkpoint_supir', 'selesai')
                  ->where(function($q) use ($gateIn) {
                      $q->where('status_gate_in', '!=', 'selesai')
                        ->orWhere('gate_in_id', $gateIn->id);
                  });
        })->orderBy('nomor_seri_gabungan')->get();

        $gateIn->load(['kontainers']);

        return view('gate-in.edit', compact('gateIn', 'terminals', 'kapals', 'services', 'kontainers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GateIn $gateIn)
    {
        $this->authorize('gate-in-update');

        $request->validate([
            'terminal_id' => 'required|exists:master_terminals,id',
            'kapal_id' => 'required|exists:master_kapals,id',
            'service_id' => 'required|exists:master_services,id',
            'kontainer_ids' => 'required|array|min:1',
            'kontainer_ids.*' => 'exists:kontainers,id',
            'keterangan' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            // Update Gate In record
            $gateIn->update([
                'terminal_id' => $request->terminal_id,
                'kapal_id' => $request->kapal_id,
                'service_id' => $request->service_id,
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

        if ($request->terminal_id) {
            $query->where('terminal_id', $request->terminal_id);
        }

        if ($request->kapal_id) {
            $query->where('kapal_id', $request->kapal_id);
        }

        if ($request->service_id) {
            $query->where('service_id', $request->service_id);
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
            Log::info('getKontainersSuratJalan called - loading all available kontainers');

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
                    'sj.no_seal'
                );

            $kontainers = $query->orderBy('sj.no_surat_jalan')->get();

            Log::info('Found kontainers:', ['count' => $kontainers->count()]);

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
            'terminal_id' => $gateIn->terminal_id,
            'service_id' => $gateIn->service_id,
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
            'terminal_id' => null,
            'service_id' => null,
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
}
