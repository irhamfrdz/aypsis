<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NaikKapal;
use App\Models\Prospek;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NaikKapalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = NaikKapal::with(['prospek', 'createdBy']);
        
        // Filter by kapal and voyage if provided
        if ($request->filled('kapal_id') && $request->filled('no_voyage')) {
            $kapal = \App\Models\MasterKapal::find($request->kapal_id);
            if ($kapal) {
                $query->where('nama_kapal', $kapal->nama_kapal)
                      ->where('no_voyage', $request->no_voyage);
            }
        }
        
        // Additional filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('tanggal_muat')) {
            $query->whereDate('tanggal_muat', $request->tanggal_muat);
        }
        
        $naikKapals = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get selected kapal info for display
        $selectedKapal = null;
        if ($request->filled('kapal_id')) {
            $selectedKapal = \App\Models\MasterKapal::find($request->kapal_id);
        }

        return view('naik-kapal.index', compact('naikKapals', 'selectedKapal'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not implemented - create functionality not needed
        return redirect()->route('naik-kapal.index')
            ->with('info', 'Fitur tambah data naik kapal tidak tersedia.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Not implemented - store functionality not needed
        return redirect()->route('naik-kapal.index')
            ->with('info', 'Fitur tambah data naik kapal tidak tersedia.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $naikKapal = NaikKapal::with(['prospek', 'createdBy', 'updatedBy'])->findOrFail($id);
        return view('naik-kapal.show', compact('naikKapal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $naikKapal = NaikKapal::findOrFail($id);
        $prospeks = Prospek::where('status', 'aktif')
            ->orWhere('id', $naikKapal->prospek_id)
            ->orderBy('tanggal', 'desc')
            ->get();
            
        return view('naik-kapal.edit', compact('naikKapal', 'prospeks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $naikKapal = NaikKapal::findOrFail($id);
        
        $request->validate([
            'prospek_id' => 'required|exists:prospek,id',
            'nomor_kontainer' => 'required|string|max:255',
            'tanggal_muat' => 'required|date',
            'jam_muat' => 'nullable|date_format:H:i',
            'nama_kapal' => 'required|string|max:255',
            'status' => 'required|in:menunggu,dimuat,selesai,batal'
        ]);

        DB::beginTransaction();
        try {
            // Get prospek data
            $prospek = Prospek::findOrFail($request->prospek_id);
            
            $naikKapal->update([
                'prospek_id' => $request->prospek_id,
                'nomor_kontainer' => $request->nomor_kontainer,
                'no_seal' => $request->no_seal,
                'tipe_kontainer' => $request->tipe_kontainer,
                'ukuran_kontainer' => $request->ukuran_kontainer,
                'nama_kapal' => $request->nama_kapal,
                'no_voyage' => $request->no_voyage,
                'pelabuhan_asal' => $request->pelabuhan_asal,
                'pelabuhan_tujuan' => $request->pelabuhan_tujuan,
                'tanggal_muat' => $request->tanggal_muat,
                'jam_muat' => $request->jam_muat,
                'total_volume' => round($prospek->total_volume ?? 0, 3),
                'total_tonase' => round($prospek->total_ton ?? 0, 3),
                'kuantitas' => $prospek->kuantitas ?? 0,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
                'updated_by' => Auth::id()
            ]);

            // Update prospek status if naik kapal is completed
            if ($request->status == 'selesai') {
                $prospek->update(['status' => 'sudah_muat']);
            } elseif ($request->status == 'batal') {
                $prospek->update(['status' => 'aktif']);
            }

            DB::commit();
            
            return redirect()->route('naik-kapal.index')
                ->with('success', 'Data naik kapal berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $naikKapal = NaikKapal::findOrFail($id);
            
            DB::beginTransaction();
            
            // Reset prospek status if naik kapal was completed
            if ($naikKapal->status == 'selesai') {
                $naikKapal->prospek->update(['status' => 'aktif']);
            }
            
            $naikKapal->delete();
            
            DB::commit();
            
            return redirect()->route('naik-kapal.index')
                ->with('success', 'Data naik kapal berhasil dihapus.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
