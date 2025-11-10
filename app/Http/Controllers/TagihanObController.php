<?php

namespace App\Http\Controllers;

use App\Models\TagihanOb;
use App\Models\Bl;
use App\Models\MasterPricelistOb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TagihanObController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('tagihan-ob-view');
        
        $tagihanOb = TagihanOb::with(['bl', 'creator'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);

        return view('tagihan-ob.index', compact('tagihanOb'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('tagihan-ob-create');
        
        $bls = Bl::orderBy('nomor_bl')->get();
        $pricelist = MasterPricelistOb::where('status', 'active')->get();
        
        return view('tagihan-ob.create', compact('bls', 'pricelist'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('tagihan-ob-create');
        
        $validated = $request->validate([
            'kapal' => 'required|string|max:255',
            'voyage' => 'required|string|max:255',
            'nomor_kontainer' => 'required|string|max:255',
            'nama_supir' => 'required|string|max:255',
            'barang' => 'required|string|max:255',
            'status_kontainer' => 'required|in:full,empty',
            'bl_id' => 'nullable|exists:bls,id',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $tagihan = new TagihanOb();
            $tagihan->fill($validated);
            $tagihan->created_by = Auth::id();
            
            // Calculate biaya from pricelist
            $biaya = $tagihan->calculateBiayaFromPricelist();
            $tagihan->biaya = $biaya;
            
            $tagihan->save();

            return redirect()->route('tagihan-ob.index')
                           ->with('success', 'Tagihan OB berhasil dibuat');
                           
        } catch (\Exception $e) {
            Log::error('Error creating TagihanOb: ' . $e->getMessage());
            return back()->withInput()
                        ->with('error', 'Gagal membuat tagihan OB: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TagihanOb $tagihanOb)
    {
        $this->authorize('tagihan-ob-view');
        
        $tagihanOb->load(['bl', 'creator']);
        
        return view('tagihan-ob.show', compact('tagihanOb'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TagihanOb $tagihanOb)
    {
        $this->authorize('tagihan-ob-update');
        
        $bls = Bl::orderBy('nomor_bl')->get();
        $pricelist = MasterPricelistOb::where('status', 'active')->get();
        
        return view('tagihan-ob.edit', compact('tagihanOb', 'bls', 'pricelist'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TagihanOb $tagihanOb)
    {
        $this->authorize('tagihan-ob-update');
        
        $validated = $request->validate([
            'kapal' => 'required|string|max:255',
            'voyage' => 'required|string|max:255',
            'nomor_kontainer' => 'required|string|max:255',
            'nama_supir' => 'required|string|max:255',
            'barang' => 'required|string|max:255',
            'status_kontainer' => 'required|in:full,empty',
            'bl_id' => 'nullable|exists:bls,id',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $tagihanOb->fill($validated);
            
            // Recalculate biaya if needed
            $biaya = $tagihanOb->calculateBiayaFromPricelist();
            $tagihanOb->biaya = $biaya;
            
            $tagihanOb->save();

            return redirect()->route('tagihan-ob.index')
                           ->with('success', 'Tagihan OB berhasil diupdate');
                           
        } catch (\Exception $e) {
            Log::error('Error updating TagihanOb: ' . $e->getMessage());
            return back()->withInput()
                        ->with('error', 'Gagal mengupdate tagihan OB: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TagihanOb $tagihanOb)
    {
        $this->authorize('tagihan-ob-delete');
        
        try {
            $tagihanOb->delete();
            
            return redirect()->route('tagihan-ob.index')
                           ->with('success', 'Tagihan OB berhasil dihapus');
                           
        } catch (\Exception $e) {
            Log::error('Error deleting TagihanOb: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus tagihan OB: ' . $e->getMessage());
        }
    }

    /**
     * Create tagihan OB from OB Muat action
     */
    public function createFromObMuat(Request $request)
    {
        $this->authorize('tagihan-ob-create');
        
        $validated = $request->validate([
            'bl_id' => 'required|exists:bls,id',
            'kegiatan' => 'required|string|in:tarik isi,tarik kosong'
        ]);

        try {
            $bl = Bl::findOrFail($validated['bl_id']);
            
            // Determine status kontainer based on kegiatan
            $statusKontainer = $validated['kegiatan'] === 'tarik isi' ? 'full' : 'empty';
            
            $tagihan = new TagihanOb();
            $tagihan->kapal = $bl->kapal ?? '';
            $tagihan->voyage = $bl->voyage ?? '';
            $tagihan->nomor_kontainer = $bl->nomor_kontainer ?? '';
            $tagihan->nama_supir = $bl->nama_supir ?? '';
            $tagihan->barang = $bl->barang ?? '';
            $tagihan->status_kontainer = $statusKontainer;
            $tagihan->bl_id = $bl->id;
            $tagihan->created_by = Auth::id();
            
            // Calculate biaya from pricelist
            $biaya = $tagihan->calculateBiayaFromPricelist();
            $tagihan->biaya = $biaya;
            
            $tagihan->save();

            return response()->json([
                'success' => true,
                'message' => 'Tagihan OB berhasil dibuat dari OB Muat',
                'data' => $tagihan
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error creating TagihanOb from OB Muat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat tagihan OB: ' . $e->getMessage()
            ], 500);
        }
    }
}