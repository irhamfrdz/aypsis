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
        $services = MasterService::where('status', 'aktif')->orderBy('nama_service')->get();

        // Get kontainers yang sudah checkpoint supir dan belum gate in
        $kontainers = Kontainer::where('status_checkpoint_supir', 'selesai')
            ->where('status_gate_in', '!=', 'selesai')
            ->orderBy('nomor_seri_gabungan')
            ->get();

        return view('gate-in.create', compact('terminals', 'kapals', 'services', 'kontainers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('gate-in-create');

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
            // Create Gate In record
            $gateIn = GateIn::create([
                'nomor_gate_in' => $this->generateNomorGateIn(),
                'terminal_id' => $request->terminal_id,
                'kapal_id' => $request->kapal_id,
                'service_id' => $request->service_id,
                'tanggal_gate_in' => now(),
                'user_id' => Auth::id(),
                'keterangan' => $request->keterangan,
                'status' => 'aktif'
            ]);

            // Update kontainer status and link to gate in
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
                ->with('success', 'Gate In berhasil dibuat dengan nomor: ' . $gateIn->nomor_gate_in);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan Gate In: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(GateIn $gateIn)
    {
        $this->authorize('gate-in-view');

        $gateIn->load(['terminal', 'kapal', 'service', 'kontainers.kontainer', 'user']);

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
