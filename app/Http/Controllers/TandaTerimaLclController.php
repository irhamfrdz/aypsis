<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaLclItem;
use App\Models\Term;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use App\Models\MasterTujuanKirim;
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
            'nomor_tanda_terima' => 'required|string|max:255|unique:tanda_terima_lcl',
            'tanggal_tanda_terima' => 'required|date',
            'term_id' => 'required|exists:terms,id',
            'nama_penerima' => 'required|string|max:255',
            'alamat_penerima' => 'required|string',
            'nama_pengirim' => 'required|string|max:255', 
            'alamat_pengirim' => 'required|string',
            'supir' => 'required|string|max:255',
            'no_plat' => 'required|string|max:255',
            'tujuan_pengiriman' => 'required|exists:master_tujuan_kirim,id',
            'nomor_kontainer' => 'nullable|string|max:255',
            'size_kontainer' => 'nullable|in:20ft,40ft,40hc,45ft',
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
                'nama_barang' => is_array($request->nama_barang) ? implode(', ', array_filter($request->nama_barang)) : '',
                'kuantitas' => is_array($request->jumlah) ? array_sum(array_filter($request->jumlah)) : 0,
                'keterangan_barang' => $request->keterangan_barang,
                'supir' => $request->supir,
                'no_plat' => $request->no_plat,
                'tujuan_pengiriman_id' => $request->tujuan_pengiriman,
                'tipe_kontainer' => 'lcl',
                'nomor_kontainer' => $request->nomor_kontainer,
                'size_kontainer' => $request->size_kontainer,
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

                    // Only create item if at least one value is provided
                    if ($panjang || $lebar || $tinggi || $volume || $tonase) {
                        TandaTerimaLclItem::create([
                            'tanda_terima_lcl_id' => $tandaTerima->id,
                            'item_number' => $i + 1,
                            'nama_barang' => isset($namaBarangArray[$i]) ? $namaBarangArray[$i] : null,
                            'jumlah' => isset($jumlahArray[$i]) ? intval($jumlahArray[$i]) : null,
                            'satuan' => isset($satuanArray[$i]) ? $satuanArray[$i] : null,
                            'panjang' => $panjang,
                            'lebar' => $lebar,
                            'tinggi' => $tinggi,
                            'meter_kubik' => $volume,
                            'tonase' => $tonase,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('tanda-terima-tanpa-surat-jalan.index', ['tipe' => 'lcl'])
                        ->with('success', 'Tanda Terima LCL berhasil dibuat.');
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

        return view('tanda-terima-tanpa-surat-jalan.edit-lcl', compact('tandaTerima', 'containerOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tandaTerima = TandaTerimaLcl::findOrFail($id);
        
        $request->validate([
            'nomor_tanda_terima' => 'required|string|max:255|unique:tanda_terima_lcl,nomor_tanda_terima,' . $id,
            'tanggal_tanda_terima' => 'required|date',
            'nama_penerima' => 'required|string|max:255',
            'nama_pengirim' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'supir' => 'required|string|max:255',
            'no_plat' => 'required|string|max:255',
            'tipe_kontainer' => 'required|in:cargo,lcl',
            'items' => 'array',
            'items.*.panjang' => 'nullable|numeric|min:0',
            'items.*.lebar' => 'nullable|numeric|min:0',
            'items.*.tinggi' => 'nullable|numeric|min:0',
            'items.*.meter_kubik' => 'nullable|numeric|min:0',
            'items.*.tonase' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $tandaTerima) {
            // Update main record
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
                'nama_barang' => $request->nama_barang,
                'kuantitas' => $request->kuantitas,
                'keterangan_barang' => $request->keterangan_barang,
                'tipe_kontainer' => $request->tipe_kontainer,
                'nomor_kontainer' => $request->tipe_kontainer === 'lcl' ? $request->nomor_kontainer : null,
                'size_kontainer' => $request->tipe_kontainer === 'lcl' ? $request->size_kontainer : null,
                'supir' => $request->supir,
                'no_plat' => $request->no_plat,
                'tujuan_pengiriman_id' => $request->master_tujuan_kirim_id,
                'updated_by' => Auth::id(),
            ]);

            // Update items if provided
            if ($request->has('items')) {
                // Handle existing items
                $existingIds = [];
                foreach ($request->items as $index => $item) {
                    if (!empty($item['panjang']) || !empty($item['lebar']) || !empty($item['tinggi']) || !empty($item['tonase'])) {
                        if (isset($item['id']) && $item['id']) {
                            // Update existing item
                            $existingItem = TandaTerimaLclItem::find($item['id']);
                            if ($existingItem) {
                                // Calculate volume if dimensions are provided
                                $volume = null;
                                if (!empty($item['panjang']) && !empty($item['lebar']) && !empty($item['tinggi'])) {
                                    $volume = $item['panjang'] * $item['lebar'] * $item['tinggi'];
                                }

                                $existingItem->update([
                                    'panjang' => $item['panjang'] ?? null,
                                    'lebar' => $item['lebar'] ?? null,
                                    'tinggi' => $item['tinggi'] ?? null,
                                    'meter_kubik' => $volume,
                                    'tonase' => $item['tonase'] ?? null,
                                ]);
                                $existingIds[] = $existingItem->id;
                            }
                        } else {
                            // Create new item
                            // Calculate volume if dimensions are provided
                            $volume = null;
                            if (!empty($item['panjang']) && !empty($item['lebar']) && !empty($item['tinggi'])) {
                                $volume = $item['panjang'] * $item['lebar'] * $item['tinggi'];
                            }

                            $newItem = TandaTerimaLclItem::create([
                                'tanda_terima_lcl_id' => $tandaTerima->id,
                                'item_number' => $index + 1,
                                'panjang' => $item['panjang'] ?? null,
                                'lebar' => $item['lebar'] ?? null,
                                'tinggi' => $item['tinggi'] ?? null,
                                'meter_kubik' => $volume,
                                'tonase' => $item['tonase'] ?? null,
                            ]);
                            $existingIds[] = $newItem->id;
                        }
                    }
                }
                
                // Delete items that are no longer present
                $tandaTerima->items()->whereNotIn('id', $existingIds)->delete();
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
                    $tandaTerima->tanggal_tanda_terima->format('d/m/Y'),
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
            'volume' => 'required|numeric|min:0.001',
            'berat' => 'required|numeric|min:0.001',
            'kuantitas' => 'nullable|integer|min:1',
            'keterangan' => 'required|string|max:1000'
        ]);

        $ids = json_decode($request->input('ids'), true);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada item yang dipilih.');
        }

        $splitVolume = $request->volume; // CBM (sudah dalam m³)
        $splitBeratTon = $request->berat; // Ton dari form (tidak perlu konversi lagi)
        $splitKuantitas = $request->kuantitas ?? 0;
        $processedCount = 0;
        
        DB::transaction(function () use ($ids, $request, $splitVolume, $splitBeratTon, $splitKuantitas, &$processedCount) {
            
            foreach ($ids as $originalId) {
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
                    'nama_barang' => $originalTandaTerima->nama_barang . ' (Pecahan)',
                    'kuantitas' => $splitKuantitas > 0 ? $splitKuantitas : $originalTandaTerima->kuantitas,
                    'keterangan_barang' => $request->keterangan,
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
                    'panjang' => null, // Will be calculated or set separately
                    'lebar' => null,
                    'tinggi' => null,
                    'meter_kubik' => $splitVolume,
                    'tonase' => $splitBeratTon, // Berat sudah dalam ton dari form
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
     * Create prospek entry from LCL data
     */
    private function createProspekFromLcl($ids, &$prospekCreated, &$prospekMessage)
    {
        try {
            // Get the LCL data
            $tandaTerimas = TandaTerimaLcl::with(['tujuanPengiriman'])->whereIn('id', $ids)->get();
            
            if ($tandaTerimas->isEmpty()) {
                $prospekMessage = "Tidak ada data LCL yang ditemukan.";
                return;
            }

            // Get container information (should be same for all items due to validation)
            $firstTandaTerima = $tandaTerimas->first();
            $nomorKontainer = $firstTandaTerima->nomor_kontainer;
            $nomorSeal = $firstTandaTerima->nomor_seal;
            
            // Prepare data for prospek
            $prospekData = [
                'tanggal' => now()->format('Y-m-d'),
                'nama_supir' => $firstTandaTerima->supir ?? '',
                'barang' => $tandaTerimas->pluck('nama_barang')->unique()->implode(', '),
                'pt_pengirim' => $tandaTerimas->pluck('nama_pengirim')->unique()->implode(', '),
                'ukuran' => $firstTandaTerima->size_kontainer ? str_replace('ft', '', $firstTandaTerima->size_kontainer) : '20',
                'tipe' => 'LCL',
                'nomor_kontainer' => $nomorKontainer,
                'no_seal' => $nomorSeal,
                'tujuan_pengiriman' => $tandaTerimas->first()->tujuanPengiriman->nama_tujuan ?? $tandaTerimas->first()->alamat_penerima ?? '',
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
}
