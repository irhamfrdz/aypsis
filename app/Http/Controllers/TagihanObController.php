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
    public function index(Request $request)
    {
        $this->authorize('tagihan-ob-view');
        
        // Check if kapal and voyage are provided
        if (!$request->has(['kapal', 'voyage'])) {
            return $this->selectKapalVoyage();
        }
        
        $tagihanOb = TagihanOb::with(['bl', 'creator'])
                        ->where('kapal', $request->kapal)
                        ->where('voyage', $request->voyage)
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);

        return view('tagihan-ob.index', compact('tagihanOb'))
                ->with('selectedKapal', $request->kapal)
                ->with('selectedVoyage', $request->voyage);
    }

    /**
     * Show the form for selecting kapal and voyage.
     */
    public function selectKapalVoyage()
    {
        $this->authorize('tagihan-ob-view');
        
        // Ambil data kapal dan voyage dari pergerakan kapal
        $pergerakanKapals = \App\Models\PergerakanKapal::whereNotNull('voyage')
                                                       ->where('voyage', '!=', '')
                                                       ->orderBy('nama_kapal')
                                                       ->orderBy('voyage')
                                                       ->get();

        // Group voyage by kapal name
        $voyageByKapal = $pergerakanKapals->groupBy('nama_kapal')->map(function($group) {
            return $group->pluck('voyage')->unique()->sort()->values();
        });

        return view('tagihan-ob.select-kapal-voyage', compact('voyageByKapal'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('tagihan-ob-create');
        
        $bls = Bl::orderBy('nomor_bl')->get();
        $pricelist = MasterPricelistOb::where('status', 'active')->get();
        
        // Pre-fill kapal and voyage if provided
        $prefilledKapal = $request->get('kapal');
        $prefilledVoyage = $request->get('voyage');
        
        return view('tagihan-ob.create', compact('bls', 'pricelist', 'prefilledKapal', 'prefilledVoyage'));
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

    /**
     * Update a specific field of the tagihan ob via AJAX.
     */
    public function updateField(Request $request, TagihanOb $tagihanOb)
    {
        $this->authorize('tagihan-ob-update');
        
        try {
            $field = $request->input('field');
            $value = $request->input('value');
            
            // Debug logging
            Log::info('UpdateField Debug', [
                'field' => $field,
                'raw_value' => $value,
                'value_type' => gettype($value),
                'request_all' => $request->all()
            ]);
            
            // Validate allowed fields
            $allowedFields = ['nama_supir', 'nomor_kontainer', 'biaya'];
            if (!in_array($field, $allowedFields)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Field tidak diizinkan untuk diubah.'
                ], 400);
            }
            
            // Field-specific validation and casting
            if ($field === 'nama_supir') {
                $request->validate([
                    'value' => 'required|string|max:255'
                ]);
                $finalValue = trim($value);
            } elseif ($field === 'nomor_kontainer') {
                $request->validate([
                    'value' => 'required|string|max:255'
                ]);
                $finalValue = trim($value);
            } elseif ($field === 'biaya') {
                $request->validate([
                    'value' => 'required|numeric|min:0'
                ]);
                // Ensure we store as integer/float correctly
                $finalValue = floatval($value);
                
                Log::info('Biaya validation passed', [
                    'original_value' => $value,
                    'final_value' => $finalValue,
                    'final_value_type' => gettype($finalValue)
                ]);
            } else {
                $finalValue = $value;
            }
            
            // Update the field
            $tagihanOb->{$field} = $finalValue;
            $tagihanOb->save();
            
            Log::info('Field updated in database', [
                'field' => $field,
                'old_value' => $tagihanOb->getOriginal($field),
                'new_value' => $tagihanOb->{$field}
            ]);
            
            // Format response value for display
            $formattedValue = $finalValue;
            if ($field === 'biaya') {
                $formattedValue = number_format($finalValue, 0, ',', '.');
            }
            
            Log::info('Tagihan OB field updated via inline edit', [
                'tagihan_ob_id' => $tagihanOb->id,
                'field' => $field,
                'old_value' => $tagihanOb->getOriginal($field),
                'new_value' => $finalValue,
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui.',
                'formatted_value' => $formattedValue,
                'raw_value' => $finalValue
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating tagihan ob field: ' . $e->getMessage(), [
                'tagihan_ob_id' => $tagihanOb->id,
                'field' => $field,
                'value' => $value
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.'
            ], 500);
        }
    }
}