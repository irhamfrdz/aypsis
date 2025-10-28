<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Stuffing;
use App\Models\TandaTerimaLcl;
use App\Models\Prospek;

class StuffingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Stuffing::with(['createdBy', 'lclItems']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_stuffing', 'like', "%{$search}%")
                  ->orWhere('nomor_kontainer', 'like', "%{$search}%")
                  ->orWhere('lokasi_stuffing', 'like', "%{$search}%")
                  ->orWhere('supervisor_stuffing', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Date filter
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_stuffing', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_stuffing', '<=', $request->end_date);
        }
        
        $stuffings = $query->latest('tanggal_stuffing')->paginate(15);
        
        // Statistics
        $stats = [
            'total' => Stuffing::count(),
            'draft' => Stuffing::where('status', 'draft')->count(),
            'in_progress' => Stuffing::where('status', 'in_progress')->count(),
            'completed' => Stuffing::where('status', 'completed')->count(),
        ];
        
        return view('stuffing.index', compact('stuffings', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get available LCL items that haven't been stuffed yet or are only in draft status
        $availableLcl = TandaTerimaLcl::where(function($query) {
                // LCL items that have never been stuffed
                $query->where('status_stuffing', 'belum stuffing')
                      ->orWhereNull('status_stuffing');
            })
            ->orWhere(function($query) {
                // LCL items that are in stuffings but only draft status
                $query->whereHas('stuffings', function($subQuery) {
                    $subQuery->where('status', 'draft');
                });
            })
            ->with(['jenisBarang', 'tujuanPengiriman', 'items', 'term'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('stuffing.create', compact('availableLcl'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_stuffing' => 'required|string|max:255|unique:stuffings',
            'tanggal_stuffing' => 'required|date',
            'nomor_kontainer' => 'required|string|max:255',
            'nomor_seal' => 'nullable|string|max:255',
            'tipe_kontainer' => 'required|in:20ft,40ft,40hc,45ft',
            'keterangan' => 'nullable|string|max:1000',
            'lcl_items' => 'required|array|min:1',
            'lcl_items.*' => 'exists:tanda_terima_lcl,id',
        ]);

        DB::transaction(function () use ($request) {
            // Create stuffing record
            $stuffing = Stuffing::create([
                'nomor_stuffing' => $request->nomor_stuffing,
                'tanggal_stuffing' => $request->tanggal_stuffing,
                'nomor_kontainer' => $request->nomor_kontainer,
                'nomor_seal' => $request->nomor_seal,
                'tipe_kontainer' => $request->tipe_kontainer,
                'keterangan' => $request->keterangan,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            // Collect data for prospek creation
            $lclItems = [];
            $totalVolume = 0;
            $totalWeight = 0;
            $pengirimList = [];
            $barangList = [];
            $tujuanList = [];

            // Attach LCL items to stuffing and update their container info
            foreach ($request->lcl_items as $lclId) {
                $stuffing->lclItems()->attach($lclId, [
                    'volume_stuffed' => $request->input("volume_stuffed.{$lclId}"),
                    'weight_stuffed' => $request->input("weight_stuffed.{$lclId}"),
                    'pieces_stuffed' => $request->input("pieces_stuffed.{$lclId}"),
                    'position_in_container' => $request->input("position_in_container.{$lclId}"),
                    'stuffing_notes' => $request->input("stuffing_notes.{$lclId}"),
                ]);
                
                // Get LCL item data
                $lcl = TandaTerimaLcl::with(['items', 'tujuanPengiriman'])->find($lclId);
                
                // Update LCL with container number and status
                $lcl->update([
                    'nomor_kontainer' => $request->nomor_kontainer,
                    'status_stuffing' => 'sudah stuffing'
                ]);

                // Collect data for prospek
                $lclItems[] = $lcl;
                if ($lcl->items) {
                    $totalVolume += $lcl->items->sum(function($item) {
                        return ($item->panjang ?? 0) * ($item->lebar ?? 0) * ($item->tinggi ?? 0);
                    });
                    $totalWeight += $lcl->items->sum('tonase') ?? 0;
                }
                
                // Collect unique senders, goods, and destinations
                if ($lcl->nama_pengirim && !in_array($lcl->nama_pengirim, $pengirimList)) {
                    $pengirimList[] = $lcl->nama_pengirim;
                }
                if ($lcl->nama_barang && !in_array($lcl->nama_barang, $barangList)) {
                    $barangList[] = $lcl->nama_barang;
                }
                if ($lcl->tujuanPengiriman && !in_array($lcl->tujuanPengiriman->nama_tujuan, $tujuanList)) {
                    $tujuanList[] = $lcl->tujuanPengiriman->nama_tujuan;
                }
            }

            // Create prospek record
            Prospek::create([
                'tanggal' => $request->tanggal_stuffing,
                'nama_supir' => null, // Will be filled later when shipping
                'barang' => implode(', ', $barangList),
                'pt_pengirim' => implode(', ', array_slice($pengirimList, 0, 3)) . (count($pengirimList) > 3 ? '...' : ''),
                'ukuran' => $request->tipe_kontainer,
                'tipe' => 'LCL',
                'no_surat_jalan' => $request->nomor_stuffing,
                'surat_jalan_id' => null,
                'nomor_kontainer' => $request->nomor_kontainer,
                'no_seal' => $request->nomor_seal,
                'tujuan_pengiriman' => implode(', ', $tujuanList),
                'nama_kapal' => null, // Will be filled later when vessel is assigned
                'keterangan' => $request->keterangan . "\n[Auto-generated from Stuffing: {$request->nomor_stuffing}]\nTotal Volume: {$totalVolume} m³\nTotal Weight: {$totalWeight} Ton\nTotal LCL Items: " . count($request->lcl_items),
                'status' => Prospek::STATUS_AKTIF,
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->route('stuffing.index')
                        ->with('success', 'Data stuffing berhasil dibuat dan nomor kontainer telah diupdate pada tanda terima LCL. Data prospek juga telah dibuat secara otomatis.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stuffing = Stuffing::with([
            'lclItems.jenisBarang',
            'lclItems.tujuanPengiriman',
            'lclItems.items',
            'createdBy',
            'updatedBy'
        ])->findOrFail($id);
        
        return view('stuffing.show', compact('stuffing'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $stuffing = Stuffing::with('lclItems')->findOrFail($id);
        
        // Get available LCL items (including those already attached to this stuffing)
        $availableLcl = TandaTerimaLcl::where(function($query) use ($stuffing) {
            $query->whereNull('nomor_kontainer')
                  ->orWhere('nomor_kontainer', $stuffing->nomor_kontainer);
        })->orderBy('tanggal_terima', 'desc')->get();
        
        return view('stuffing.edit', compact('stuffing', 'availableLcl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $stuffing = Stuffing::findOrFail($id);
        
        $request->validate([
            'nomor_stuffing' => 'required|string|max:255|unique:stuffings,nomor_stuffing,' . $id,
            'tanggal_stuffing' => 'required|date',
            'nomor_kontainer' => 'required|string|max:255',
            'tipe_kontainer' => 'required|in:20ft,40ft,40hc',
            'nomor_seal' => 'nullable|string|max:255',
            'catatan' => 'nullable|string|max:1000',
            'status' => 'required|in:draft,in_progress,completed,cancelled',
            'lcl_items' => 'nullable|array',
            'lcl_items.*' => 'exists:tanda_terima_lcl,id'
        ]);

        DB::beginTransaction();

        try {
            // Store old nomor_kontainer to check if it changed
            $oldNomorKontainer = $stuffing->nomor_kontainer;
            $newNomorKontainer = $request->nomor_kontainer;

            // Update stuffing record
            $stuffing->update([
                'nomor_stuffing' => $request->nomor_stuffing,
                'tanggal_stuffing' => $request->tanggal_stuffing,
                'nomor_kontainer' => $request->nomor_kontainer,
                'nomor_seal' => $request->nomor_seal,
                'tipe_kontainer' => $request->tipe_kontainer,
                'catatan' => $request->catatan,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);

            // Handle LCL items update
            if ($request->has('lcl_items') && is_array($request->lcl_items)) {
                // Reset old LCL items container number
                TandaTerimaLcl::where('nomor_kontainer', $oldNomorKontainer)
                    ->update(['nomor_kontainer' => null]);

                // Update new LCL items
                TandaTerimaLcl::whereIn('id', $request->lcl_items)
                    ->update(['nomor_kontainer' => $newNomorKontainer]);

                // Get updated LCL data for prospek
                $lclItems = TandaTerimaLcl::whereIn('id', $request->lcl_items)->get();
                
                if ($lclItems->isNotEmpty()) {
                    // Calculate totals
                    $totalVolume = $lclItems->sum('volume');
                    $totalBerat = $lclItems->sum('berat');
                    
                    // Get unique pengirim and tujuan
                    $pengirimList = $lclItems->pluck('pengirim')->unique()->filter()->implode(', ');
                    $jenisBarangList = $lclItems->pluck('jenis_barang')->unique()->filter()->implode(', ');
                    $tujuanList = $lclItems->pluck('tujuan')->unique()->filter()->implode(', ');

                    // Update existing prospek or create new one
                    $prospekData = [
                        'tanggal_prospek' => $request->tanggal_stuffing,
                        'pengirim' => $pengirimList ?: 'Multiple Senders',
                        'jenis_barang' => $jenisBarangList ?: 'Mixed Goods',
                        'tujuan' => $tujuanList ?: 'Multiple Destinations',
                        'volume' => $totalVolume,
                        'berat' => $totalBerat,
                        'nomor_kontainer' => $newNomorKontainer,
                        'keterangan' => "Updated from Stuffing #{$stuffing->id} - Total {$lclItems->count()} LCL items",
                        'updated_at' => now()
                    ];

                    // Find existing prospek by old container number or create new
                    $existingProspek = Prospek::where('nomor_kontainer', $oldNomorKontainer)->first();
                    
                    if ($existingProspek) {
                        $existingProspek->update($prospekData);
                    } else {
                        $prospekData['created_at'] = now();
                        Prospek::create($prospekData);
                    }
                }
            }

            DB::commit();

            return redirect()->route('stuffing.show', $stuffing)
                            ->with('success', 'Data stuffing berhasil diperbarui dan prospek telah disinkronkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Terjadi kesalahan saat memperbarui data stuffing: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $stuffing = Stuffing::findOrFail($id);
        
        DB::transaction(function () use ($stuffing) {
            // Reset LCL items status back to unstuffed and clear container number
            foreach ($stuffing->lclItems as $lcl) {
                $lcl->update([
                    'nomor_kontainer' => null,
                    'status_stuffing' => 'belum stuffing'
                ]);
            }
            
            // Delete related prospek record if exists
            Prospek::where('no_surat_jalan', $stuffing->nomor_stuffing)
                   ->where('nomor_kontainer', $stuffing->nomor_kontainer)
                   ->delete();
            
            // Detach LCL items
            $stuffing->lclItems()->detach();
            
            // Delete stuffing
            $stuffing->delete();
        });
        
        return redirect()->route('stuffing.index')
                        ->with('success', 'Data stuffing dan prospek terkait berhasil dihapus. Nomor kontainer pada tanda terima LCL telah direset.');
    }
}
