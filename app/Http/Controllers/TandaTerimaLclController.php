<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaLclItem;
use App\Models\TandaTerimaLclKontainerPivot;
use App\Models\Term;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use App\Models\MasterTujuanKirim;
use App\Models\MasterPengirimPenerima;
use App\Models\Karyawan;
use App\Models\Prospek;

class TandaTerimaLclController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Redirect to main index with LCL filter
        return redirect()->route('tanda-terima-tanpa-surat-jalan.index', ['tipe' => 'lcl']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $terms = Term::all();
        $masterTujuanKirims = MasterTujuanKirim::all();
        $masterPengirimPenerima = MasterPengirimPenerima::active()->get();
        // Ambil karyawan yang memiliki divisi 'supir'
        $supirs = Karyawan::where('divisi', 'supir')
            ->select('nama_lengkap as nama_supir', 'plat as no_plat')
            ->get();
        
        // Include all non-inactive containers (many records use 'available'/'rented' etc.)
        $kontainers = Kontainer::where('status', '!=', 'inactive')->get();
        $stockKontainers = StockKontainer::active()->get();
        $merged = [];
        foreach ($kontainers as $k) {
            $nomor = $k->nomor_kontainer;
            $merged[$nomor] = [
                'value' => $nomor,
                'label' => $nomor . ' (Kontainer)',
                'size' => $k->ukuran ?? null,
                'source' => 'kontainer',
                'status' => $k->status ?? null,
            ];
        }
        foreach ($stockKontainers as $s) {
            $nomor = $s->nomor_kontainer;
            if (!isset($merged[$nomor])) {
                $merged[$nomor] = [
                    'value' => $nomor,
                    'label' => $nomor . ' (Stock)',
                    'size' => $s->ukuran ?? null,
                    'source' => 'stock',
                    'status' => $s->status ?? null,
                ];
            }
        }
        $containerOptions = array_values($merged);

        return view('tanda-terima-tanpa-surat-jalan.create-lcl', compact(
            'terms', 
            'masterTujuanKirims',
            'masterPengirimPenerima',
            'supirs',
            'containerOptions'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_tanda_terima' => 'nullable|string|max:255|unique:tanda_terima_lcl,nomor_tanda_terima',
            'tanggal_tanda_terima' => 'required|date',
            'term_id' => 'required|exists:terms,id',
            'nama_penerima' => 'required|string|max:255',
            'pic_penerima' => 'nullable|string|max:255',
            'telepon_penerima' => 'nullable|string|max:50',
            'alamat_penerima' => 'required|string',
            'nama_pengirim' => 'required|string|max:255',
            'pic_pengirim' => 'nullable|string|max:255',
            'telepon_pengirim' => 'nullable|string|max:50',
            'alamat_pengirim' => 'required|string',
            'supir' => 'required|string|max:255',
            'no_plat' => 'required|string|max:255',
            'tujuan_pengiriman' => 'required|exists:master_tujuan_kirim,id',
            'nomor_kontainer' => 'nullable|string|max:255',
            'size_kontainer' => 'nullable|in:20ft,40ft,40hc,45ft',
            'nomor_seal' => 'nullable|string|max:255',
            'tipe_kontainer' => 'nullable|in:HC,STD,RF,OT,FR,Dry Container',
            'gambar_surat_jalan.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'nama_barang' => 'nullable|array',
            'nama_barang.*' => 'nullable|string|max:255',
            'jumlah' => 'nullable|array',
            'jumlah.*' => 'nullable|integer|min:0',
            'satuan' => 'nullable|array',
            'satuan.*' => 'nullable|string|max:50',
            'panjang' => 'nullable|array',
            'panjang.*' => 'nullable|numeric|min:0',
            'lebar' => 'nullable|array',
            'lebar.*' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|array',
            'tinggi.*' => 'nullable|numeric|min:0',
            'meter_kubik' => 'nullable|array',
            'meter_kubik.*' => 'nullable|numeric|min:0',
            'tonase' => 'nullable|array',
            'tonase.*' => 'nullable|numeric|min:0'
        ]);

        DB::transaction(function () use ($request) {
            // Handle image uploads
            $gambarPaths = [];
            if ($request->hasFile('gambar_surat_jalan')) {
                foreach ($request->file('gambar_surat_jalan') as $file) {
                    if ($file && $file->isValid()) {
                        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('tanda-terima-lcl/gambar-surat-jalan', $fileName, 'public');
                        $gambarPaths[] = $path;
                    }
                }
            }

            // Create main LCL record
            $tandaTerima = TandaTerimaLcl::create([
                'nomor_tanda_terima' => $request->nomor_tanda_terima,
                'tanggal_tanda_terima' => $request->tanggal_tanda_terima,
                'no_surat_jalan_customer' => $request->no_surat_jalan_customer,
                'term_id' => $request->term_id,
                'nama_penerima' => $request->nama_penerima,
                'pic_penerima' => $request->pic_penerima,
                'telepon_penerima' => $request->telepon_penerima,
                'alamat_penerima' => $request->alamat_penerima,
                'nama_pengirim' => $request->nama_pengirim,
                'pic_pengirim' => $request->pic_pengirim,
                'telepon_pengirim' => $request->telepon_pengirim,
                'alamat_pengirim' => $request->alamat_pengirim,
                'gambar_surat_jalan' => !empty($gambarPaths) ? $gambarPaths : null,
                'supir' => $request->supir,
                'no_plat' => $request->no_plat,
                'tujuan_pengiriman_id' => $request->tujuan_pengiriman,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            // Create dimension items from array fields
            if ($request->has('panjang') && is_array($request->panjang)) {
                $namaBarangArray = $request->nama_barang ?? [];
                $jumlahArray = $request->jumlah ?? [];
                $satuanArray = $request->satuan ?? [];
                $panjangArray = $request->panjang ?? [];
                $lebarArray = $request->lebar ?? [];
                $tinggiArray = $request->tinggi ?? [];
                $tonaseArray = $request->tonase ?? [];

                $count = max(
                    count($panjangArray),
                    count($lebarArray),
                    count($tinggiArray),
                    count($tonaseArray)
                );

                for ($i = 0; $i < $count; $i++) {
                    $panjang = isset($panjangArray[$i]) ? floatval($panjangArray[$i]) : null;
                    $lebar = isset($lebarArray[$i]) ? floatval($lebarArray[$i]) : null;
                    $tinggi = isset($tinggiArray[$i]) ? floatval($tinggiArray[$i]) : null;
                    $tonase = isset($tonaseArray[$i]) ? floatval($tonaseArray[$i]) : null;

                    // Calculate volume if dimensions are provided
                    $volume = null;
                    if ($panjang > 0 && $lebar > 0 && $tinggi > 0) {
                        $volume = $panjang * $lebar * $tinggi;
                    }

                    $namaBarang = isset($namaBarangArray[$i]) ? $namaBarangArray[$i] : null;
                    $jumlah = isset($jumlahArray[$i]) ? intval($jumlahArray[$i]) : null;
                    $satuan = isset($satuanArray[$i]) ? $satuanArray[$i] : null;

                    // Create item if at least one value is provided (including nama_barang, jumlah, or satuan)
                    if ($namaBarang || $jumlah || $satuan || $panjang || $lebar || $tinggi || $volume || $tonase) {
                        TandaTerimaLclItem::create([
                            'tanda_terima_lcl_id' => $tandaTerima->id,
                            'item_number' => $i + 1,
                            'nama_barang' => $namaBarang,
                            'jumlah' => $jumlah,
                            'satuan' => $satuan,
                            'panjang' => $panjang,
                            'lebar' => $lebar,
                            'tinggi' => $tinggi,
                            'meter_kubik' => $volume,
                            'tonase' => $tonase,
                        ]);
                    }
                }
            }

            // Removed auto-create prospek logic - will be handled separately when seal is assigned
        });

        return redirect()->route('tanda-terima-tanpa-surat-jalan.index', ['tipe' => 'lcl'])
                        ->with('success', 'Tanda Terima LCL berhasil dibuat. Silakan assign barang ke kontainer dan input nomor seal.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tandaTerima = TandaTerimaLcl::with([
            'term',
            'tujuanPengiriman', 
            'items',
            'createdBy',
            'updatedBy'
        ])->findOrFail($id);
        
        return view('tanda-terima-tanpa-surat-jalan.show-lcl', compact('tandaTerima'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tandaTerima = TandaTerimaLcl::with('items')->findOrFail($id);
        $terms = Term::all();
        $masterTujuanKirims = MasterTujuanKirim::all();
        $masterPengirimPenerima = MasterPengirimPenerima::where('status', 'active')->orderBy('nama')->get();
        // Ambil karyawan yang memiliki divisi 'supir'
        $supirs = Karyawan::where('divisi', 'supir')
            ->select('nama_lengkap as nama_supir', 'plat as no_plat')
            ->get();
        
        $kontainers = Kontainer::where('status', 'active')->get();
        $stockKontainers = StockKontainer::active()->get();
        $merged = [];
        foreach ($kontainers as $k) {
            $nomor = $k->nomor_kontainer;
            $merged[$nomor] = [
                'value' => $nomor,
                'label' => $nomor . ' (Kontainer)',
                'size' => $k->ukuran ?? null,
                'source' => 'kontainer',
                'status' => $k->status ?? null,
            ];
        }
        foreach ($stockKontainers as $s) {
            $nomor = $s->nomor_kontainer;
            if (!isset($merged[$nomor])) {
                $merged[$nomor] = [
                    'value' => $nomor,
                    'label' => $nomor . ' (Stock)',
                    'size' => $s->ukuran ?? null,
                    'source' => 'stock',
                    'status' => $s->status ?? null,
                ];
            }
        }
        $containerOptions = array_values($merged);

        return view('tanda-terima-tanpa-surat-jalan.edit-lcl', compact('tandaTerima', 'containerOptions', 'masterPengirimPenerima', 'terms', 'masterTujuanKirims', 'supirs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tandaTerima = TandaTerimaLcl::findOrFail($id);
        
        $request->validate([
            'nomor_tanda_terima' => 'nullable|string|max:255|unique:tanda_terima_lcl,nomor_tanda_terima,' . $id,
            'tanggal_tanda_terima' => 'required|date',
            'nama_penerima' => 'required|string|max:255',
            'pic_penerima' => 'nullable|string|max:255',
            'telepon_penerima' => 'nullable|string|max:50',
            'alamat_penerima' => 'required|string',
            'nama_pengirim' => 'required|string|max:255',
            'pic_pengirim' => 'nullable|string|max:255',
            'telepon_pengirim' => 'nullable|string|max:50',
            'alamat_pengirim' => 'required|string',
            'nama_barang' => 'nullable|array',
            'nama_barang.*' => 'nullable|string|max:255',
            'jumlah' => 'nullable|array',
            'jumlah.*' => 'nullable|numeric|min:0',
            'satuan' => 'nullable|array',
            'satuan.*' => 'nullable|string|max:50',
            'panjang' => 'nullable|array',
            'panjang.*' => 'nullable|numeric|min:0',
            'lebar' => 'nullable|array',
            'lebar.*' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|array',
            'tinggi.*' => 'nullable|numeric|min:0',
            'meter_kubik' => 'nullable|array',
            'meter_kubik.*' => 'nullable|numeric|min:0',
            'tonase' => 'nullable|array',
            'tonase.*' => 'nullable|numeric|min:0',
            'supir' => 'required|string|max:255',
            'no_plat' => 'required|string|max:255',
            'tipe_kontainer' => 'required|in:cargo,lcl',
            'nomor_seal' => 'nullable|string|max:255',
            'jenis_kontainer' => 'nullable|in:HC,STD,RF,OT,FR,Dry Container',
        ]);

        DB::transaction(function () use ($request, $tandaTerima) {
            // Update main record (without nama_barang - it's now handled in items)
            $tandaTerima->update([
                'nomor_tanda_terima' => $request->nomor_tanda_terima,
                'tanggal_tanda_terima' => $request->tanggal_tanda_terima,
                'no_surat_jalan_customer' => $request->no_surat_jalan_customer,
                'term_id' => $request->term_id,
                'nama_penerima' => $request->nama_penerima,
                'pic_penerima' => $request->pic_penerima,
                'telepon_penerima' => $request->telepon_penerima,
                'alamat_penerima' => $request->alamat_penerima,
                'nama_pengirim' => $request->nama_pengirim,
                'pic_pengirim' => $request->pic_pengirim,
                'telepon_pengirim' => $request->telepon_pengirim,
                'alamat_pengirim' => $request->alamat_pengirim,
                'kuantitas' => $request->kuantitas,
                'keterangan_barang' => $request->keterangan_barang,
                'tipe_kontainer' => $request->tipe_kontainer,
                'nomor_kontainer' => $request->tipe_kontainer === 'lcl' ? $request->nomor_kontainer : null,
                'size_kontainer' => $request->tipe_kontainer === 'lcl' ? $request->size_kontainer : null,
                'nomor_seal' => $request->nomor_seal,
                'jenis_kontainer' => $request->jenis_kontainer,
                'supir' => $request->supir,
                'no_plat' => $request->no_plat,
                'tujuan_pengiriman_id' => $request->master_tujuan_kirim_id,
                'updated_by' => Auth::id(),
            ]);

            // Update items - handle flat array format
            if ($request->has('nama_barang') && is_array($request->nama_barang)) {
                $namaBarangs = $request->nama_barang;
                $jumlahs = $request->jumlah ?? [];
                $satuans = $request->satuan ?? [];
                $panjangs = $request->panjang ?? [];
                $lebars = $request->lebar ?? [];
                $tinggis = $request->tinggi ?? [];
                $tonases = $request->tonase ?? [];
                $itemIds = $request->item_ids ?? [];
                
                $existingIds = [];
                
                foreach ($namaBarangs as $index => $namaBarang) {
                    // Check if at least one field has value
                    $hasData = !empty($namaBarang) || !empty($jumlahs[$index]) || !empty($satuans[$index]) 
                            || !empty($panjangs[$index]) || !empty($lebars[$index]) || !empty($tinggis[$index]) || !empty($tonases[$index]);
                    
                    if ($hasData) {
                        // Calculate volume if dimensions are provided
                        $volume = null;
                        if (!empty($panjangs[$index]) && !empty($lebars[$index]) && !empty($tinggis[$index])) {
                            $volume = $panjangs[$index] * $lebars[$index] * $tinggis[$index];
                        }
                        
                        if (isset($itemIds[$index]) && $itemIds[$index]) {
                            // Update existing item
                            $existingItem = TandaTerimaLclItem::find($itemIds[$index]);
                            if ($existingItem) {
                                $existingItem->update([
                                    'nama_barang' => $namaBarang ?: null,
                                    'jumlah' => $jumlahs[$index] ?? null,
                                    'satuan' => $satuans[$index] ?? null,
                                    'panjang' => $panjangs[$index] ?? null,
                                    'lebar' => $lebars[$index] ?? null,
                                    'tinggi' => $tinggis[$index] ?? null,
                                    'meter_kubik' => $volume,
                                    'tonase' => $tonases[$index] ?? null,
                                ]);
                                $existingIds[] = $existingItem->id;
                            }
                        } else {
                            // Create new item
                            $newItem = TandaTerimaLclItem::create([
                                'tanda_terima_lcl_id' => $tandaTerima->id,
                                'item_number' => $index + 1,
                                'nama_barang' => $namaBarang ?: null,
                                'jumlah' => $jumlahs[$index] ?? null,
                                'satuan' => $satuans[$index] ?? null,
                                'panjang' => $panjangs[$index] ?? null,
                                'lebar' => $lebars[$index] ?? null,
                                'tinggi' => $tinggis[$index] ?? null,
                                'meter_kubik' => $volume,
                                'tonase' => $tonases[$index] ?? null,
                            ]);
                            $existingIds[] = $newItem->id;
                        }
                    }
                }
                
                // Delete items that are no longer present
                if (!empty($existingIds)) {
                    $tandaTerima->items()->whereNotIn('id', $existingIds)->delete();
                } else {
                    // If no items, delete all
                    $tandaTerima->items()->delete();
                }
            }
        });

        return redirect()->route('tanda-terima-lcl.show', $tandaTerima->id)
                        ->with('success', 'Tanda Terima LCL berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tandaTerima = TandaTerimaLcl::findOrFail($id);
        
        DB::transaction(function () use ($tandaTerima) {
            $tandaTerima->items()->delete();
            $tandaTerima->delete();
        });
        
        return redirect()->route('tanda-terima-tanpa-surat-jalan.index', ['tipe' => 'lcl'])
                        ->with('success', 'Tanda Terima LCL berhasil dihapus.');
    }

    /**
     * Bulk export selected LCL records
     */
    public function bulkExport(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada item yang dipilih untuk export.');
        }

        $tandaTerimas = TandaTerimaLcl::with([
            'term',
            'tujuanPengiriman',
            'items'
        ])->whereIn('id', $ids)->get();

        // Generate CSV export
        $filename = 'tanda_terima_lcl_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($tandaTerimas) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'No. Tanda Terima',
                'Tanggal',
                'No. Surat Jalan Customer', 
                'Term',
                'Nama Penerima',
                'Alamat Penerima',
                'Nama Pengirim',
                'Alamat Pengirim',
                'Nama Barang',
                'Kuantitas',
                'Supir',
                'No. Plat',
                'Tujuan Pengiriman',
                'No. Kontainer',
                'Size Kontainer',
                'Total Volume (m³)',
                'Total Berat (Ton)',
                'Status'
            ]);

            foreach ($tandaTerimas as $tandaTerima) {
                $totalVolume = $tandaTerima->items->sum('meter_kubik');
                $totalBerat = $tandaTerima->items->sum('tonase');
                
                fputcsv($file, [
                    $tandaTerima->nomor_tanda_terima,
                    $tandaTerima->tanggal_tanda_terima->format('d/M/Y'),
                    $tandaTerima->no_surat_jalan_customer,
                    $tandaTerima->term->nama_status ?? '',
                    $tandaTerima->nama_penerima,
                    $tandaTerima->alamat_penerima,
                    $tandaTerima->nama_pengirim,
                    $tandaTerima->alamat_pengirim,
                    $tandaTerima->nama_barang,
                    $tandaTerima->kuantitas,
                    $tandaTerima->supir,
                    $tandaTerima->no_plat,
                    $tandaTerima->tujuanPengiriman->nama_tujuan ?? '',
                    $tandaTerima->nomor_kontainer,
                    $tandaTerima->size_kontainer,
                    number_format($totalVolume, 6),
                    number_format($totalBerat, 2),
                    $tandaTerima->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Validate container numbers for selected LCL records
     */
    public function validateContainers(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada item yang dipilih.'
            ]);
        }

        $tandaTerimas = TandaTerimaLcl::whereIn('id', $ids)
                                    ->select('id', 'nomor_kontainer', 'nomor_tanda_terima')
                                    ->get();

        // Check for items without container numbers
        $itemsWithoutContainer = $tandaTerimas->whereNull('nomor_kontainer')->where('nomor_kontainer', '');
        $hasNoContainer = $itemsWithoutContainer->isNotEmpty();

        // Check for different container numbers
        $uniqueContainers = $tandaTerimas->whereNotNull('nomor_kontainer')
                                        ->where('nomor_kontainer', '!=', '')
                                        ->pluck('nomor_kontainer')
                                        ->unique();
        
        $hasDifferentContainers = $uniqueContainers->count() > 1;

        $containerInfo = '';
        if ($hasDifferentContainers) {
            $containerInfo = "Nomor kontainer yang berbeda ditemukan:\n";
            foreach ($uniqueContainers as $container) {
                $items = $tandaTerimas->where('nomor_kontainer', $container);
                $containerInfo .= "- {$container}: " . $items->pluck('nomor_tanda_terima')->join(', ') . "\n";
            }
        }

        if ($hasNoContainer) {
            $containerInfo .= "\nItem tanpa nomor kontainer:\n";
            $containerInfo .= $itemsWithoutContainer->pluck('nomor_tanda_terima')->join(', ');
        }

        return response()->json([
            'success' => true,
            'has_different_containers' => $hasDifferentContainers,
            'has_no_container' => $hasNoContainer,
            'container_info' => $containerInfo,
            'unique_containers' => $uniqueContainers->values()
        ]);
    }

    /**
     * Assign container number and seal to selected LCL items
     */
    public function assignContainer(Request $request)
    {
        $request->validate([
            'nomor_kontainer' => 'required|string|max:255',
            'size_kontainer' => 'required|in:20ft,40ft,40hc,45ft',
            'nomor_seal' => 'nullable|string|max:255',
            'tipe_kontainer' => 'nullable|in:HC,STD,RF,OT,FR,Dry Container',
            'selected_ids' => 'required|string',
        ]);

        $ids = json_decode($request->input('selected_ids'), true);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada item yang dipilih.');
        }

        $prospekCreated = false;
        $prospekMessage = '';
        $shouldCreateProspek = !empty($request->nomor_seal);
        $nomorSeal = $request->nomor_seal;

        DB::transaction(function () use ($ids, $request, $shouldCreateProspek, $nomorSeal, &$prospekCreated, &$prospekMessage) {
            // Get current max urutan for this container
            $maxUrutan = \App\Models\KontainerTandaTerimaLcl::where('nomor_kontainer', $request->nomor_kontainer)
                ->max('urutan_dalam_kontainer') ?? 0;

            foreach ($ids as $index => $id) {
                $tandaTerima = TandaTerimaLcl::find($id);
                
                if (!$tandaTerima) continue;

                // Check if already has container assignment
                $existingKontainer = $tandaTerima->kontainerPivot()->first();
                
                if ($existingKontainer) {
                    // Update existing container pivot
                    $existingKontainer->update([
                        'nomor_kontainer' => $request->nomor_kontainer,
                        'urutan_dalam_kontainer' => $maxUrutan + $index + 1,
                        'catatan' => $request->tipe_kontainer ? "Tipe: {$request->tipe_kontainer}, Size: {$request->size_kontainer}" : "Size: {$request->size_kontainer}",
                    ]);
                } else {
                    // Create new container pivot
                    \App\Models\KontainerTandaTerimaLcl::create([
                        'tanda_terima_lcl_id' => $id,
                        'nomor_kontainer' => $request->nomor_kontainer,
                        'urutan_dalam_kontainer' => $maxUrutan + $index + 1,
                        'persentase_volume' => null, // Will be calculated later if needed
                        'catatan' => $request->tipe_kontainer ? "Tipe: {$request->tipe_kontainer}, Size: {$request->size_kontainer}" : "Size: {$request->size_kontainer}",
                    ]);
                }

                // Update main tanda terima record
                $tandaTerima->update([
                    'updated_by' => Auth::id(),
                    'updated_at' => now()
                ]);
            }

            // If seal is filled, automatically create prospek
            if ($shouldCreateProspek) {
                $this->createProspekFromLcl($ids, $prospekCreated, $prospekMessage);
                
                // Update prospek with seal number if created
                if ($prospekCreated && $nomorSeal) {
                    $prospek = Prospek::where('nomor_kontainer', $request->nomor_kontainer)
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    if ($prospek) {
                        $prospek->update(['no_seal' => $nomorSeal]);
                    }
                }
            }
        });

        $count = count($ids);
        $successMessage = "{$count} item berhasil dimasukkan ke kontainer {$request->nomor_kontainer}.";
        
        if ($prospekCreated) {
            $successMessage .= " " . $prospekMessage;
        }
        
        return redirect()->route('tanda-terima-tanpa-surat-jalan.index', ['tipe' => 'lcl'])
                        ->with('success', $successMessage);
    }

    /**
     * Bulk update seal information for selected LCL records
     */
    public function bulkSeal(Request $request)
    {
        $request->validate([
            'nomor_seal' => 'required|string|max:255',
            'tanggal_seal' => 'required|date',
            'ids' => 'required|string',
            'kirim_ke_prospek' => 'nullable|boolean'
        ]);

        $ids = json_decode($request->input('ids'), true);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada item yang dipilih.');
        }

        // Validate again to make sure containers are still consistent
        $tandaTerimas = TandaTerimaLcl::whereIn('id', $ids)->get();
        
        $uniqueContainers = $tandaTerimas->whereNotNull('nomor_kontainer')
                                        ->where('nomor_kontainer', '!=', '')
                                        ->pluck('nomor_kontainer')
                                        ->unique();
        
        if ($uniqueContainers->count() > 1) {
            return redirect()->back()->with('error', 'Item yang dipilih memiliki nomor kontainer yang berbeda.');
        }

        $itemsWithoutContainer = $tandaTerimas->whereNull('nomor_kontainer')->where('nomor_kontainer', '');
        if ($itemsWithoutContainer->isNotEmpty()) {
            return redirect()->back()->with('error', 'Ada item yang belum memiliki nomor kontainer.');
        }

        $prospekCreated = false;
        $prospekMessage = '';

        DB::transaction(function () use ($ids, $request, &$prospekCreated, &$prospekMessage) {
            // Update seal information
            TandaTerimaLcl::whereIn('id', $ids)->update([
                'nomor_seal' => $request->nomor_seal,
                'tanggal_seal' => $request->tanggal_seal,
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);

            // If kirim_ke_prospek is checked, create prospek entry
            if ($request->has('kirim_ke_prospek') && $request->kirim_ke_prospek) {
                $this->createProspekFromLcl($ids, $prospekCreated, $prospekMessage);
            }
        });

        $count = count($ids);
        $successMessage = "Nomor seal dan tanggal seal berhasil ditambahkan ke {$count} tanda terima LCL.";
        
        if ($prospekCreated) {
            $successMessage .= " " . $prospekMessage;
        }
        
        return redirect()->route('tanda-terima-tanpa-surat-jalan.index', ['tipe' => 'lcl'])
                        ->with('success', $successMessage);
    }

    /**
     * Bulk delete selected LCL records
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada item yang dipilih untuk dihapus.');
        }

        DB::transaction(function () use ($ids) {
            // Delete all items first
            TandaTerimaLclItem::whereHas('tandaTerima', function($query) use ($ids) {
                $query->whereIn('id', $ids);
            })->delete();
            
            // Then delete main records
            TandaTerimaLcl::whereIn('id', $ids)->delete();
        });

        $count = count($ids);
        return redirect()->route('tanda-terima-tanpa-surat-jalan.index', ['tipe' => 'lcl'])
                        ->with('success', "{$count} Tanda Terima LCL berhasil dihapus.");
    }

    /**
     * Bulk split selected LCL records - create new container with specified volume/weight
     */
    public function bulkSplit(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
            'tipe_kontainer' => 'required|in:lcl,cargo',
            'nomor_kontainer' => 'nullable|string|max:255',
            'size_kontainer' => 'nullable|in:20ft,40ft,40hc,45ft',
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'panjang' => 'required|numeric|min:0.01',
            'lebar' => 'required|numeric|min:0.01',
            'tinggi' => 'required|numeric|min:0.01',
            'volume' => 'required|numeric|min:0.001',
            'berat' => 'required|numeric|min:0.001',
            'kuantitas' => 'nullable|integer|min:1',
            'keterangan' => 'required|string|max:1000'
        ]);

        $containerNumbers = json_decode($request->input('ids'), true);
        
        if (empty($containerNumbers)) {
            return redirect()->back()->with('error', 'Tidak ada kontainer yang dipilih.');
        }

        \Log::info('Bulk Split Request', [
            'container_numbers' => $containerNumbers,
            'request_data' => $request->all()
        ]);

        // Get all tanda terima IDs from selected containers via pivot table
        $tandaTerimaIds = TandaTerimaLclKontainerPivot::whereIn('nomor_kontainer', $containerNumbers)
            ->pluck('tanda_terima_lcl_id')
            ->unique()
            ->toArray();

        \Log::info('Found tanda terima IDs', ['ids' => $tandaTerimaIds]);

        if (empty($tandaTerimaIds)) {
            return redirect()->back()->with('error', 'Tidak ada tanda terima ditemukan untuk kontainer yang dipilih.');
        }

        $splitVolume = $request->volume; // CBM (sudah dalam m³)
        $splitBeratTon = $request->berat; // Ton dari form (tidak perlu konversi lagi)
        $splitKuantitas = $request->kuantitas ?? 0;
        $processedCount = 0;
        
        DB::transaction(function () use ($tandaTerimaIds, $request, $splitVolume, $splitBeratTon, $splitKuantitas, &$processedCount) {
            
            foreach ($tandaTerimaIds as $originalId) {
                $originalTandaTerima = TandaTerimaLcl::with('items')->findOrFail($originalId);
                
                // Calculate current totals
                $currentVolume = $originalTandaTerima->items->sum('meter_kubik');
                $currentBeratTon = $originalTandaTerima->items->sum('tonase');
                
                // Check if we have enough volume and weight to split
                if ($currentVolume < $splitVolume) {
                    continue; // Skip this item if not enough volume
                }
                
                if ($currentBeratTon < $splitBeratTon) {
                    continue; // Skip this item if not enough weight (in ton)
                }
                
                // Generate new tanda terima number with suffix
                $newNomorTandaTerima = $originalTandaTerima->nomor_tanda_terima . '-SPLIT';
                
                // Create new LCL record for split container
                $newTandaTerima = TandaTerimaLcl::create([
                    'nomor_tanda_terima' => $newNomorTandaTerima,
                    'tanggal_tanda_terima' => $originalTandaTerima->tanggal_tanda_terima,
                    'no_surat_jalan_customer' => $originalTandaTerima->no_surat_jalan_customer,
                    'term_id' => $originalTandaTerima->term_id,
                    'nama_penerima' => $originalTandaTerima->nama_penerima,
                    'pic_penerima' => $originalTandaTerima->pic_penerima,
                    'telepon_penerima' => $originalTandaTerima->telepon_penerima,
                    'alamat_penerima' => $originalTandaTerima->alamat_penerima,
                    'nama_pengirim' => $originalTandaTerima->nama_pengirim,
                    'pic_pengirim' => $originalTandaTerima->pic_pengirim,
                    'telepon_pengirim' => $originalTandaTerima->telepon_pengirim,
                    'alamat_pengirim' => $originalTandaTerima->alamat_pengirim,
                    'nama_barang' => $request->nama_barang,
                    'kuantitas' => $request->jumlah,
                    'keterangan_barang' => $request->satuan,
                    'supir' => $originalTandaTerima->supir,
                    'no_plat' => $originalTandaTerima->no_plat,
                    'tujuan_pengiriman_id' => $originalTandaTerima->tujuan_pengiriman_id,
                    'tipe_kontainer' => $request->tipe_kontainer,
                    'nomor_kontainer' => $request->nomor_kontainer,
                    'size_kontainer' => $request->size_kontainer,
                    'status' => 'draft',
                    'created_by' => Auth::id(),
                ]);

                // Create single item for split container with specified dimensions
                TandaTerimaLclItem::create([
                    'tanda_terima_lcl_id' => $newTandaTerima->id,
                    'item_number' => 1,
                    'nama_barang' => $request->nama_barang,
                    'keterangan_barang' => $request->keterangan,
                    'panjang' => $request->panjang,
                    'lebar' => $request->lebar,
                    'tinggi' => $request->tinggi,
                    'meter_kubik' => $splitVolume,
                    'tonase' => $splitBeratTon,
                ]);
                
                // Update original tanda terima - reduce volume and weight
                $remainingVolume = $currentVolume - $splitVolume;
                $remainingBeratTon = $currentBeratTon - $splitBeratTon;
                $remainingKuantitas = $splitKuantitas > 0 ? max(0, $originalTandaTerima->kuantitas - $splitKuantitas) : $originalTandaTerima->kuantitas;
                
                // Update original items - jika hanya ada satu item, langsung kurangi
                $itemCount = $originalTandaTerima->items->count();
                
                if ($itemCount == 1) {
                    // Jika hanya satu item, langsung kurangi volume dan berat
                    $singleItem = $originalTandaTerima->items->first();
                    
                    // Pastikan remaining volume dan berat tidak negatif
                    $newVolume = max(0, round($remainingVolume, 3));
                    $newTonase = max(0, round($remainingBeratTon, 3));
                    
                    \Log::info("Single Item Direct Update", [
                        'item_id' => $singleItem->id,
                        'old_volume' => $singleItem->meter_kubik,
                        'new_volume' => $newVolume,
                        'old_tonase' => $singleItem->tonase,
                        'new_tonase' => $newTonase,
                        'calculation_check' => [
                            'current_volume' => $currentVolume,
                            'split_volume' => $splitVolume,
                            'remaining_volume' => $remainingVolume
                        ]
                    ]);
                    
                    // Use updateOrFail untuk memastikan update berhasil
                    $updateResult = $singleItem->updateOrFail([
                        'meter_kubik' => $newVolume,
                        'tonase' => $newTonase,
                    ]);
                    
                    \Log::info("Update Result", ['success' => $updateResult]);
                } else {
                    // Jika multiple items, update secara proporsional
                    $volumeRatio = $remainingVolume > 0 ? $remainingVolume / $currentVolume : 0;
                    $beratRatio = $remainingBeratTon > 0 ? $remainingBeratTon / $currentBeratTon : 0;
                    
                    \Log::info("Multiple Items Proportional Update", [
                        'items_count' => $itemCount,
                        'volume_ratio' => $volumeRatio,
                        'berat_ratio' => $beratRatio
                    ]);
                    
                    foreach ($originalTandaTerima->items as $item) {
                        $newVolume = round($item->meter_kubik * $volumeRatio, 3);
                        $newTonase = round($item->tonase * $beratRatio, 3);
                        
                        $item->update([
                            'meter_kubik' => $newVolume,
                            'tonase' => $newTonase,
                        ]);
                    }
                }
                
                // Update original tanda terima quantities
                $originalTandaTerima->update([
                    'kuantitas' => $remainingKuantitas,
                    'keterangan_barang' => ($originalTandaTerima->keterangan_barang ?? '') . ' [SEBAGIAN DIPINDAH KE: ' . $newNomorTandaTerima . ']',
                    'updated_by' => Auth::id(),
                ]);
                
                // Force refresh the relationship and verify the update
                $originalTandaTerima->unsetRelation('items');
                $originalTandaTerima->load('items');
                $updatedTotalVolume = $originalTandaTerima->items->sum('meter_kubik');
                $updatedTotalBerat = $originalTandaTerima->items->sum('tonase');
                
                \Log::info("After Update Verification", [
                    'original_id' => $originalId,
                    'updated_total_volume' => $updatedTotalVolume,
                    'expected_remaining_volume' => $remainingVolume,
                    'updated_total_berat' => $updatedTotalBerat,
                    'expected_remaining_berat' => $remainingBeratTon,
                    'volume_difference' => abs($updatedTotalVolume - $remainingVolume),
                    'is_volume_correct' => abs($updatedTotalVolume - $remainingVolume) < 0.001
                ]);
                
                // Additional check: Direct database query to verify
                $dbTotalVolume = DB::table('tanda_terima_lcl_items')
                    ->where('tanda_terima_lcl_id', $originalId)
                    ->sum('meter_kubik');
                
                \Log::info("Direct DB Verification", [
                    'original_id' => $originalId,
                    'db_total_volume' => $dbTotalVolume,
                    'eloquent_total_volume' => $updatedTotalVolume,
                    'volumes_match' => abs($dbTotalVolume - $updatedTotalVolume) < 0.001
                ]);
                
                $processedCount++;
            }
        });

        if ($processedCount == 0) {
            // Get first selected item to show current capacity
            $firstId = $ids[0] ?? null;
            if ($firstId) {
                $firstTandaTerima = TandaTerimaLcl::with('items')->find($firstId);
                if ($firstTandaTerima) {
                    $currentVolume = $firstTandaTerima->items->sum('meter_kubik');
                    $currentBeratTon = $firstTandaTerima->items->sum('tonase');
                    $message = "Tidak ada tanda terima yang dapat dipecah. Kapasitas tersedia pada tanda terima pertama: Volume {$currentVolume} m³, Berat {$currentBeratTon} ton. Pastikan volume dan berat yang diminta tidak melebihi kapasitas ini.";
                } else {
                    $message = 'Tidak ada tanda terima yang dapat dipecah. Pastikan volume dan berat yang diminta tidak melebihi kapasitas yang tersedia.';
                }
            } else {
                $message = 'Tidak ada tanda terima yang dapat dipecah. Pastikan volume dan berat yang diminta tidak melebihi kapasitas yang tersedia.';
            }
            
            return redirect()->back()->with('error', $message);
        }
        
        return redirect()->route('tanda-terima-tanpa-surat-jalan.index', ['tipe' => 'lcl'])
                        ->with('success', "Berhasil memecah {$processedCount} tanda terima. Kontainer baru telah dibuat dengan volume {$splitVolume} m³ dan berat {$splitBeratTon} ton.");
    }

    /**
     * Download specific image from a tanda terima LCL record
     */
    public function downloadImage(TandaTerimaLcl $tandaTerimaTanpaSuratJalan, $imageIndex)
    {
        $gambarArray = $tandaTerimaTanpaSuratJalan->gambar_surat_jalan;
        
        if (!is_array($gambarArray) || !isset($gambarArray[$imageIndex])) {
            return abort(404, 'Gambar tidak ditemukan');
        }
        
        $imagePath = $gambarArray[$imageIndex];
        
        if (!Storage::disk('public')->exists($imagePath)) {
            return abort(404, 'File gambar tidak ditemukan');
        }
        
        $fileName = basename($imagePath);
        return Storage::disk('public')->download($imagePath, $fileName);
    }

    /**
     * Create prospek entry from new LCL data
     */
    private function createProspekFromNewLcl($tandaTerima)
    {
        try {
            // Load related data
            $tandaTerima->load(['tujuanPengiriman']);
            
            // Prepare data for prospek
            $prospekData = [
                'tanggal' => $tandaTerima->tanggal_tanda_terima->format('Y-m-d'),
                'nama_supir' => $tandaTerima->supir ?? '',
                'barang' => $tandaTerima->nama_barang ?? '',
                'pt_pengirim' => $tandaTerima->nama_pengirim ?? '',
                'ukuran' => $tandaTerima->size_kontainer ? str_replace('ft', '', $tandaTerima->size_kontainer) : '20',
                'tipe' => 'LCL',
                'nomor_kontainer' => $tandaTerima->nomor_kontainer ?? '',
                'no_seal' => $tandaTerima->nomor_seal ?? '',
                'tujuan_pengiriman' => $tandaTerima->tujuanPengiriman->nama_tujuan ?? $tandaTerima->alamat_penerima ?? '',
                'nama_kapal' => '', // Will be filled later in prospek
                'keterangan' => 'Auto-created from Tanda Terima LCL: ' . $tandaTerima->nomor_tanda_terima,
                'status' => 'aktif',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ];

            // Create prospek entry
            $prospek = Prospek::create($prospekData);
            
            \Log::info('Auto-created prospek from LCL', [
                'lcl_id' => $tandaTerima->id,
                'prospek_id' => $prospek->id,
                'nomor_seal' => $tandaTerima->nomor_seal
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error auto-creating prospek from new LCL: ' . $e->getMessage(), [
                'lcl_id' => $tandaTerima->id
            ]);
        }
    }

    /**
     * Create prospek entry from LCL data
     */
    private function createProspekFromLcl($ids, &$prospekCreated, &$prospekMessage)
    {
        try {
            // Get the LCL data with all pivot relationships
            $tandaTerimas = TandaTerimaLcl::with([
                'tujuanPengiriman', 
                'items', 
                'pengirimPivot', 
                'penerimaPivot', 
                'kontainerPivot'
            ])->whereIn('id', $ids)->get();
            
            if ($tandaTerimas->isEmpty()) {
                $prospekMessage = "Tidak ada data LCL yang ditemukan.";
                return;
            }

            // Get container information from pivot table (should be same for all items)
            $firstTandaTerima = $tandaTerimas->first();
            $kontainerPivot = $firstTandaTerima->kontainerPivot()->first();
            
            if (!$kontainerPivot || !$kontainerPivot->nomor_kontainer) {
                $prospekMessage = "Nomor kontainer tidak ditemukan. Pastikan sudah assign kontainer terlebih dahulu.";
                return;
            }
            
            $nomorKontainer = $kontainerPivot->nomor_kontainer;
            
            // Extract size from catatan in kontainer pivot (format: "Size: 20ft" or "Tipe: HC, Size: 40ft")
            $sizeKontainer = '20'; // default
            if ($kontainerPivot->catatan) {
                if (preg_match('/Size:\s*(\d+)/', $kontainerPivot->catatan, $matches)) {
                    $sizeKontainer = $matches[1];
                }
            }
            
            // Collect all barang names from items pivot
            $allBarang = collect();
            foreach ($tandaTerimas as $tt) {
                $barangNames = $tt->items->pluck('nama_barang');
                $allBarang = $allBarang->merge($barangNames);
            }
            
            // Collect all pengirim names from pengirim pivot
            $allPengirim = collect();
            foreach ($tandaTerimas as $tt) {
                $pengirimNames = $tt->pengirimPivot->pluck('nama_pengirim');
                $allPengirim = $allPengirim->merge($pengirimNames);
            }
            
            // Collect all penerima for tujuan
            $allPenerima = collect();
            foreach ($tandaTerimas as $tt) {
                $penerimaNames = $tt->penerimaPivot->pluck('nama_penerima');
                $allPenerima = $allPenerima->merge($penerimaNames);
            }
            
            // Prepare data for prospek
            $prospekData = [
                'tanggal' => now()->format('Y-m-d'),
                'nama_supir' => $firstTandaTerima->supir ?? '',
                'barang' => $allBarang->unique()->implode(', '),
                'pt_pengirim' => $allPengirim->unique()->implode(', '),
                'ukuran' => $sizeKontainer,
                'tipe' => 'LCL',
                'nomor_kontainer' => $nomorKontainer,
                'no_seal' => '', // Will be filled by assignContainer if seal is provided
                'tujuan_pengiriman' => $firstTandaTerima->tujuanPengiriman->nama_tujuan ?? $allPenerima->first() ?? '',
                'nama_kapal' => '', // Will be filled later in prospek
                'keterangan' => 'Transfer dari Tanda Terima LCL - Total: ' . $tandaTerimas->count() . ' items',
                'status' => 'aktif',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ];

            // Create prospek entry
            $prospek = Prospek::create($prospekData);
            
            $prospekCreated = true;
            $prospekMessage = "Data kontainer berhasil ditambahkan ke menu prospek dengan ID #{$prospek->id}.";
            
        } catch (\Exception $e) {
            \Log::error('Error creating prospek from LCL: ' . $e->getMessage());
            $prospekMessage = "Terjadi error saat menambahkan ke prospek: " . $e->getMessage();
        }
    }
    
    /**
     * Show stuffing page for LCL - Display pivot table data grouped by container
     */
    public function stuffing(Request $request)
    {
        // Query pivot table with relationships
        $query = TandaTerimaLclKontainerPivot::with(['tandaTerima.items', 'assignedByUser']);
        
        // Filter pencarian
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhereHas('tandaTerima', function($sq) use ($searchTerm) {
                      $sq->where('nomor_tanda_terima', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('nama_penerima', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('nama_pengirim', 'LIKE', '%' . $searchTerm . '%');
                  });
            });
        }
        
        // Filter by container
        if ($request->filled('kontainer')) {
            $query->where('nomor_kontainer', $request->kontainer);
        }
        
        $pivotData = $query->orderBy('nomor_kontainer')->orderBy('assigned_at', 'desc')->paginate(20);
        
        // Group data by container AND seal status for display
        // This allows same container number to be used multiple times (different batches)
        $groupedQuery = TandaTerimaLclKontainerPivot::with(['tandaTerima.items', 'assignedByUser']);
        
        // Apply search filter to grouped query
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $groupedQuery->where(function($q) use ($searchTerm) {
                $q->where('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhereHas('tandaTerima', function($sq) use ($searchTerm) {
                      $sq->where('nomor_tanda_terima', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('nama_penerima', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('nama_pengirim', 'LIKE', '%' . $searchTerm . '%');
                  });
            });
        }
        
        // Apply container filter to grouped query
        if ($request->filled('kontainer')) {
            $groupedQuery->where('nomor_kontainer', $request->kontainer);
        }
        
        // Apply seal status filter before grouping
        if ($request->filled('seal_status')) {
            if ($request->seal_status === 'sealed') {
                $groupedQuery->whereNotNull('nomor_seal');
            } elseif ($request->seal_status === 'unsealed') {
                $groupedQuery->whereNull('nomor_seal');
            }
        }
        
        $groupedByContainer = $groupedQuery->get()
            ->groupBy(function($item) {
                // Group by container number and seal status
                // Sealed containers are grouped separately from unsealed ones with same number
                return $item->nomor_kontainer . '|' . ($item->nomor_seal ?? 'unsealed');
            })
            ->map(function($items) {
                return [
                    'nomor_kontainer' => $items->first()->nomor_kontainer,
                    'size_kontainer' => $items->first()->size_kontainer,
                    'tipe_kontainer' => $items->first()->tipe_kontainer,
                    'total_lcl' => $items->count(),
                    'total_volume' => $items->sum(function($item) {
                        return $item->tandaTerima ? $item->tandaTerima->items->sum('meter_kubik') : 0;
                    }),
                    'total_berat' => $items->sum(function($item) {
                        return $item->tandaTerima ? $item->tandaTerima->items->sum('tonase') : 0;
                    }),
                    'items' => $items, // Add the actual pivot items
                ];
            });
        
        // Get available containers for new stuffing (include all containers, sealed or not)
        // Containers can be reused - if sealed, new stuffing will create new batch
        $kontainers = Kontainer::where('status', '!=', 'inactive')->get();
        $stockKontainers = StockKontainer::active()->get();
        
        $availableKontainers = collect();
        
        // Merge all kontainers (don't exclude sealed ones - they can be reused)
        foreach ($kontainers as $k) {
            if ($k->nomor_kontainer) {
                $availableKontainers->push([
                    'nomor_kontainer' => $k->nomor_kontainer,
                    'ukuran' => $k->ukuran ?? $k->size ?? null,
                    'source' => 'kontainer'
                ]);
            }
        }
        
        foreach ($stockKontainers as $s) {
            if ($s->nomor_kontainer 
                && !$availableKontainers->contains('nomor_kontainer', $s->nomor_kontainer)) {
                $availableKontainers->push([
                    'nomor_kontainer' => $s->nomor_kontainer,
                    'ukuran' => $s->ukuran ?? null,
                    'source' => 'stock'
                ]);
            }
        }
        
        // Get LCL yang belum di-stuffing untuk add new functionality
        $unstuffedLcl = TandaTerimaLcl::with('items')
            ->doesntHave('kontainerPivot')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get unique containers for filter
        $uniqueContainers = TandaTerimaLclKontainerPivot::select('nomor_kontainer')
            ->distinct()
            ->orderBy('nomor_kontainer')
            ->pluck('nomor_kontainer');
        
        // Get statistics
        $stats = [
            'total_containers' => $groupedByContainer->count(),
            'total_lcl_stuffed' => TandaTerimaLclKontainerPivot::count(),
            'total_lcl_unstuffed' => $unstuffedLcl->count(),
        ];
        
        // Get master tujuan kirim for dropdown
        $masterTujuanKirim = MasterTujuanKirim::active()->orderBy('nama_tujuan')->get();
        
        return view('tanda-terima-lcl.stuffing', compact(
            'pivotData', 
            'groupedByContainer', 
            'availableKontainers', 
            'unstuffedLcl',
            'uniqueContainers',
            'stats',
            'masterTujuanKirim'
        ));
    }
    
    /**
     * Process stuffing - assign containers to LCL tanda terima
     */
    public function processStuffing(Request $request)
    {
        // Validasi input
        try {
            $request->validate([
                'tanda_terima_ids' => 'required|array|min:1',
                'tanda_terima_ids.*' => 'required|exists:tanda_terimas_lcl,id',
                'nomor_kontainer' => 'required|string|max:255',
                'size_kontainer' => 'nullable|string|max:50',
                'tipe_kontainer' => 'nullable|string|max:50',
            ], [
                'tanda_terima_ids.required' => 'Pilih minimal satu tanda terima LCL untuk di-stuffing',
                'tanda_terima_ids.array' => 'Data tanda terima tidak valid',
                'tanda_terima_ids.min' => 'Pilih minimal satu tanda terima LCL untuk di-stuffing',
                'tanda_terima_ids.*.required' => 'ID tanda terima tidak boleh kosong',
                'tanda_terima_ids.*.exists' => 'Terdapat tanda terima yang tidak ditemukan di database',
                'nomor_kontainer.required' => 'Nomor kontainer wajib diisi',
                'nomor_kontainer.max' => 'Nomor kontainer terlalu panjang (maksimal 255 karakter)',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                           ->withErrors($e->errors())
                           ->withInput()
                           ->with('error', 'Validasi gagal: ' . implode(', ', array_map(fn($errors) => implode(', ', $errors), $e->errors())));
        }
        
        // Validasi nomor kontainer tidak kosong
        if (empty(trim($request->nomor_kontainer))) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Nomor kontainer tidak boleh kosong. Silakan pilih atau input nomor kontainer.');
        }
        
        $tandaTerimaIds = $request->tanda_terima_ids;
        
        DB::beginTransaction();
        try {
            $stuffedCount = 0;
            $alreadyStuffed = [];
            $notFound = [];
            
            foreach ($tandaTerimaIds as $id) {
                $tandaTerima = TandaTerimaLcl::find($id);
                
                if (!$tandaTerima) {
                    $notFound[] = "ID-{$id}";
                    continue;
                }
                
                // Cek apakah LCL sudah di-stuffing sebelumnya
                $existingPivot = $tandaTerima->kontainerPivot()->first();
                if ($existingPivot) {
                    $alreadyStuffed[] = ($tandaTerima->nomor_tanda_terima ?? "TT-LCL-{$id}") . " (sudah di kontainer {$existingPivot->nomor_kontainer})";
                    continue;
                }
                
                // Create pivot entry
                $tandaTerima->kontainerPivot()->create([
                    'nomor_kontainer' => $request->nomor_kontainer,
                    'size_kontainer' => $request->size_kontainer,
                    'tipe_kontainer' => $request->tipe_kontainer,
                    'assigned_at' => now(),
                    'assigned_by' => Auth::id(),
                ]);
                
                $stuffedCount++;
            }
            
            DB::commit();
            
            // Build response message
            $messages = [];
            
            if ($stuffedCount > 0) {
                $messages[] = "✓ Berhasil stuffing {$stuffedCount} tanda terima LCL ke kontainer {$request->nomor_kontainer}";
            }
            
            if (count($alreadyStuffed) > 0) {
                $messages[] = "⚠ " . count($alreadyStuffed) . " tanda terima sudah di-stuffing sebelumnya: " . implode(', ', array_slice($alreadyStuffed, 0, 3)) . (count($alreadyStuffed) > 3 ? '...' : '');
            }
            
            if (count($notFound) > 0) {
                $messages[] = "⚠ " . count($notFound) . " tanda terima tidak ditemukan: " . implode(', ', $notFound);
            }
            
            if ($stuffedCount === 0) {
                return redirect()->route('tanda-terima-lcl.stuffing')
                               ->with('error', 'Tidak ada tanda terima yang berhasil di-stuffing. ' . implode(' ', $messages));
            }
            
            $messageType = (count($alreadyStuffed) > 0 || count($notFound) > 0) ? 'warning' : 'success';
            
            return redirect()->route('tanda-terima-lcl.stuffing')
                           ->with($messageType, implode(' | ', $messages));
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error processing stuffing: ' . $e->getMessage(), [
                'tanda_terima_ids' => $tandaTerimaIds,
                'nomor_kontainer' => $request->nomor_kontainer,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Provide more specific error messages
            $errorMessage = 'Gagal melakukan proses stuffing ke kontainer ' . $request->nomor_kontainer . '. ';
            
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $errorMessage .= 'Terdapat data duplikat. Beberapa LCL mungkin sudah di-stuffing ke kontainer ini.';
            } elseif (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                $errorMessage .= 'Terdapat masalah dengan relasi data. Pastikan semua data valid.';
            } elseif (strpos($e->getMessage(), 'Connection') !== false) {
                $errorMessage .= 'Koneksi database bermasalah. Silakan coba lagi.';
            } else {
                $errorMessage .= 'Detail error: ' . $e->getMessage();
            }
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', $errorMessage);
        }
    }

    /**
     * Seal kontainer - update nomor seal untuk semua pivot records dengan kontainer yang sama
     */
    public function sealKontainer(Request $request)
    {
        try {
            $request->validate([
                'nomor_kontainer' => 'required|string|max:255',
                'nomor_seal' => 'required|string|max:255',
                'tanggal_seal' => 'required|date',
                'tujuan' => 'required|string|max:255',
            ], [
                'nomor_kontainer.required' => 'Nomor kontainer wajib diisi',
                'nomor_seal.required' => 'Nomor seal wajib diisi',
                'tanggal_seal.required' => 'Tanggal seal wajib diisi',
                'tanggal_seal.date' => 'Format tanggal seal tidak valid',
                'tujuan.required' => 'Tujuan pengiriman wajib diisi',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                           ->withErrors($e->errors())
                           ->withInput()
                           ->with('error', 'Validasi gagal: ' . implode(', ', array_map(fn($errors) => implode(', ', $errors), $e->errors())));
        }

        DB::beginTransaction();
        try {
            // Cek apakah kontainer ini sudah di-seal sebelumnya
            $existingSeal = TandaTerimaLclKontainerPivot::where('nomor_kontainer', $request->nomor_kontainer)
                ->whereNotNull('nomor_seal')
                ->first();

            if ($existingSeal) {
                DB::rollBack();
                return redirect()->back()
                               ->with('error', "Kontainer {$request->nomor_kontainer} sudah di-seal sebelumnya dengan nomor seal: {$existingSeal->nomor_seal}");
            }

            // Update semua pivot records dengan kontainer yang sama
            $pivotRecords = TandaTerimaLclKontainerPivot::where('nomor_kontainer', $request->nomor_kontainer)
                ->with(['tandaTerima.items', 'tandaTerima.tujuanPengiriman'])
                ->get();

            if ($pivotRecords->isEmpty()) {
                DB::rollBack();
                return redirect()->back()
                               ->with('error', "Kontainer {$request->nomor_kontainer} tidak ditemukan atau belum ada LCL yang di-stuffing.");
            }

            // Update pivot records dengan nomor seal
            $updated = TandaTerimaLclKontainerPivot::where('nomor_kontainer', $request->nomor_kontainer)
                ->update([
                    'nomor_seal' => $request->nomor_seal,
                    'tanggal_seal' => $request->tanggal_seal,
                ]);

            // Ambil data pertama untuk informasi kontainer
            $firstPivot = $pivotRecords->first();
            
            // Hitung total volume dan berat
            $totalVolume = $pivotRecords->sum(function($pivot) {
                return $pivot->tandaTerima ? $pivot->tandaTerima->items->sum('meter_kubik') : 0;
            });
            
            $totalTon = $pivotRecords->sum(function($pivot) {
                return $pivot->tandaTerima ? $pivot->tandaTerima->items->sum('tonase') : 0;
            });

            // Kumpulkan informasi PT Pengirim dan barang
            $ptPengirimList = $pivotRecords->map(function($pivot) {
                return $pivot->tandaTerima ? $pivot->tandaTerima->nama_pengirim : null;
            })->filter()->unique()->implode(', ');

            // Limit to 180 characters to avoid database truncation error
            if (strlen($ptPengirimList) > 180) {
                $ptPengirimList = substr($ptPengirimList, 0, 177) . '...';
            }

            $barangList = $pivotRecords->map(function($pivot) {
                if (!$pivot->tandaTerima || !$pivot->tandaTerima->items) return null;
                return $pivot->tandaTerima->items->pluck('nama_barang')->filter()->unique()->implode(', ');
            })->filter()->unique()->implode(', ');

            // Limit barang to 180 characters to avoid database truncation error
            if (strlen($barangList) > 180) {
                $barangList = substr($barangList, 0, 177) . '...';
            }

            // Insert ke tabel prospek
            $prospek = Prospek::create([
                'tanggal' => $request->tanggal_seal,
                'nomor_kontainer' => $request->nomor_kontainer,
                'no_seal' => $request->nomor_seal,
                'ukuran' => $firstPivot->size_kontainer ? (strpos($firstPivot->size_kontainer, '20') !== false ? '20' : '40') : null,
                'tipe' => $firstPivot->tipe_kontainer,
                'pt_pengirim' => $ptPengirimList ?: null,
                'barang' => $barangList ?: 'LCL',
                'total_volume' => $totalVolume,
                'total_ton' => $totalTon,
                'kuantitas' => $pivotRecords->count(),
                'tujuan_pengiriman' => $request->tujuan,
                'status' => Prospek::STATUS_AKTIF,
                'keterangan' => "Kontainer LCL dengan {$pivotRecords->count()} tanda terima",
                'created_by' => Auth::id(),
            ]);

            // Update status kontainer menjadi selesai (jika ada di tabel kontainer)
            Kontainer::where('nomor_seri_gabungan', $request->nomor_kontainer)
                ->update([
                    'status' => 'selesai',
                    'updated_at' => now()
                ]);

            DB::commit();

            return redirect()->route('tanda-terima-lcl.stuffing')
                           ->with('success', "✓ Berhasil seal kontainer {$request->nomor_kontainer} dengan nomor seal: {$request->nomor_seal}. Total {$updated} LCL telah di-seal dan data telah dikirim ke prospek (ID: {$prospek->id}).");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error sealing container: ' . $e->getMessage(), [
                'nomor_kontainer' => $request->nomor_kontainer,
                'nomor_seal' => $request->nomor_seal,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal melakukan seal kontainer: ' . $e->getMessage());
        }
    }

    /**
     * Unseal (lepas seal) kontainer LCL
     */
    public function unsealKontainer(Request $request)
    {
        try {
            $request->validate([
                'nomor_kontainer' => 'required|string|max:255',
                'alasan_unseal' => 'required|string|min:5',
            ], [
                'nomor_kontainer.required' => 'Nomor kontainer wajib diisi',
                'alasan_unseal.required' => 'Alasan lepas seal wajib diisi',
                'alasan_unseal.min' => 'Alasan lepas seal minimal 5 karakter',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                           ->withErrors($e->errors())
                           ->withInput()
                           ->with('error', 'Validasi gagal: ' . implode(', ', array_map(fn($errors) => implode(', ', $errors), $e->errors())));
        }

        DB::beginTransaction();
        try {
            // Cek apakah kontainer ini sudah di-seal
            $existingSeal = TandaTerimaLclKontainerPivot::where('nomor_kontainer', $request->nomor_kontainer)
                ->whereNotNull('nomor_seal')
                ->first();

            if (!$existingSeal) {
                DB::rollBack();
                return redirect()->back()
                               ->with('error', "Kontainer {$request->nomor_kontainer} belum di-seal atau sudah dilepas sealnya.");
            }

            $oldSealNumber = $existingSeal->nomor_seal;
            $oldSealDate = $existingSeal->tanggal_seal;

            // Hapus seal dari pivot records
            $updated = TandaTerimaLclKontainerPivot::where('nomor_kontainer', $request->nomor_kontainer)
                ->update([
                    'nomor_seal' => null,
                    'tanggal_seal' => null,
                ]);

            // Update status kontainer kembali ke active (jika ada di tabel kontainer)
            Kontainer::where('nomor_seri_gabungan', $request->nomor_kontainer)
                ->update([
                    'status' => 'active',
                    'updated_at' => now()
                ]);

            // Hapus prospek terkait
            $deletedProspek = Prospek::where('nomor_kontainer', $request->nomor_kontainer)
                ->where('no_seal', $oldSealNumber)
                ->delete();

            // Log aktivitas
            \Log::info('Container unsealed', [
                'nomor_kontainer' => $request->nomor_kontainer,
                'old_seal' => $oldSealNumber,
                'old_seal_date' => $oldSealDate,
                'alasan' => $request->alasan_unseal,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'lcl_count' => $updated
            ]);

            DB::commit();

            return redirect()->route('tanda-terima-lcl.stuffing')
                           ->with('success', "✓ Berhasil melepas seal kontainer {$request->nomor_kontainer}. Nomor seal {$oldSealNumber} telah dihapus. Total {$updated} LCL telah dilepas sealnya.");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error unsealing container: ' . $e->getMessage(), [
                'nomor_kontainer' => $request->nomor_kontainer,
                'alasan' => $request->alasan_unseal,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal melepas seal kontainer: ' . $e->getMessage());
        }
    }
    
    /**
     * Get barang data from selected containers for split modal
     */
    public function getBarangFromContainers(Request $request)
    {
        try {
            $ids = $request->ids;
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada kontainer yang dipilih'
                ]);
            }
            
            // Get all TandaTerimaLcl records with their items
            $tandaTerimas = TandaTerimaLcl::whereIn('id', $ids)
                ->with('items')
                ->get();
            
            // Collect all unique barang from items
            $barangData = [];
            $barangNames = [];
            
            foreach ($tandaTerimas as $tandaTerima) {
                foreach ($tandaTerima->items as $item) {
                    $namaBarang = $item->nama_barang;
                    
                    // Skip if we already have this barang
                    if (in_array($namaBarang, $barangNames)) {
                        continue;
                    }
                    
                    $barangNames[] = $namaBarang;
                    
                    $barangData[] = [
                        'nama_barang' => $namaBarang,
                        'satuan' => $item->satuan,
                        'panjang' => $item->panjang,
                        'lebar' => $item->lebar,
                        'tinggi' => $item->tinggi,
                        'jumlah' => $item->jumlah,
                        'meter_kubik' => $item->meter_kubik,
                        'tonase' => $item->tonase
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'barang' => $barangData,
                'message' => 'Data barang berhasil dimuat'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting barang from containers: ' . $e->getMessage(), [
                'ids' => $request->ids ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Show detail of a specific container with all its LCL items
     */
    public function showContainer($nomor_kontainer)
    {
        try {
            // Get all pivot records for this container
            $pivots = TandaTerimaLclKontainerPivot::with(['tandaTerima.items', 'assignedByUser'])
                ->where('nomor_kontainer', $nomor_kontainer)
                ->orderBy('assigned_at', 'desc')
                ->get();
            
            if ($pivots->isEmpty()) {
                return redirect()->route('tanda-terima-lcl.stuffing')
                    ->with('error', 'Kontainer tidak ditemukan atau belum ada data stuffing.');
            }
            
            // Get first pivot for container info
            $firstPivot = $pivots->first();
            
            // Calculate totals
            $totalVolume = 0;
            $totalBerat = 0;
            
            foreach ($pivots as $pivot) {
                if ($pivot->tandaTerima && $pivot->tandaTerima->items) {
                    $totalVolume += $pivot->tandaTerima->items->sum('meter_kubik');
                    $totalBerat += $pivot->tandaTerima->items->sum('tonase');
                }
            }
            
            $containerData = [
                'nomor_kontainer' => $nomor_kontainer,
                'size_kontainer' => $firstPivot->size_kontainer,
                'tipe_kontainer' => $firstPivot->tipe_kontainer,
                'total_lcl' => $pivots->count(),
                'total_volume' => $totalVolume,
                'total_berat' => $totalBerat,
                'items' => $pivots
            ];
            
            return view('tanda-terima-lcl.show-container', compact('containerData'));
            
        } catch (\Exception $e) {
            \Log::error('Error showing container detail: ' . $e->getMessage(), [
                'nomor_kontainer' => $nomor_kontainer,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tanda-terima-lcl.stuffing')
                ->with('error', 'Terjadi error saat memuat detail kontainer: ' . $e->getMessage());
        }
    }
    
    /**
     * Get barang data from selected containers by nomor kontainer
     */
    public function getBarangFromContainersByNomor(Request $request)
    {
        try {
            $containers = $request->containers;
            
            if (empty($containers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada kontainer yang dipilih'
                ]);
            }
            
            \Log::info('Getting barang for containers:', ['containers' => $containers]);
            
            // Get pivot records for these containers
            $pivotRecords = TandaTerimaLclKontainerPivot::with(['tandaTerima.items'])
                ->whereIn('nomor_kontainer', $containers)
                ->get();
            
            if ($pivotRecords->isEmpty()) {
                \Log::warning('No pivot records found for containers:', ['containers' => $containers]);
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data kontainer ditemukan'
                ]);
            }
            
            \Log::info('Found pivot records:', ['count' => $pivotRecords->count()]);
            
            // Collect all barang from items (including duplicates with different dimensions)
            $barangData = [];
            $seenItems = []; // Track unique items to avoid duplicates
            
            foreach ($pivotRecords as $pivot) {
                if (!$pivot->tandaTerima) {
                    \Log::warning('Pivot without tanda_terima:', ['pivot_id' => $pivot->id]);
                    continue;
                }
                
                $tandaTerima = $pivot->tandaTerima;
                
                if ($tandaTerima->items && $tandaTerima->items->count() > 0) {
                    foreach ($tandaTerima->items as $item) {
                        // Create unique key based on item attributes
                        $itemKey = $item->id;
                        
                        if (!isset($seenItems[$itemKey])) {
                            $seenItems[$itemKey] = true;
                            
                            // Use nama_barang from item, fallback to tanda_terima if needed
                            $namaBarang = $item->nama_barang ?? $tandaTerima->nama_barang ?? 'N/A';
                            $satuan = $item->keterangan_barang ?? $tandaTerima->keterangan_barang ?? '';
                            
                            $barangData[] = [
                                'id' => $item->id,
                                'nama_barang' => $namaBarang,
                                'satuan' => $satuan,
                                'panjang' => $item->panjang,
                                'lebar' => $item->lebar,
                                'tinggi' => $item->tinggi,
                                'jumlah' => $tandaTerima->kuantitas ?? 1,
                                'meter_kubik' => $item->meter_kubik,
                                'tonase' => $item->tonase,
                                'display_label' => $namaBarang . 
                                                 ($tandaTerima->kuantitas ? ' (' . $tandaTerima->kuantitas . ' pcs)' : '') .
                                                 ($item->panjang && $item->lebar && $item->tinggi ? 
                                                     ' - ' . $item->panjang . 'x' . $item->lebar . 'x' . $item->tinggi . 'm' : '') .
                                                 ($item->meter_kubik ? ' - ' . number_format($item->meter_kubik, 3) . 'm³' : '')
                            ];
                        }
                    }
                }
            }
            
            \Log::info('Collected barang data:', ['count' => count($barangData)]);
            
            return response()->json([
                'success' => true,
                'barang' => $barangData,
                'message' => 'Data barang berhasil dimuat (' . count($barangData) . ' items)'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting barang from containers by nomor: ' . $e->getMessage(), [
                'containers' => $request->containers ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi error: ' . $e->getMessage()
            ], 500);
        }
    }
}
