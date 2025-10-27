<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaLclItem;
use App\Models\Term;
use App\Models\JenisBarang;
use App\Models\TujuanKegiatanUtama;
use App\Models\Karyawan;

class TandaTerimaLclController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tandaTerimas = TandaTerimaLcl::with([
            'term', 
            'jenisBarang', 
            'tujuanPengiriman',
            'items',
            'createdBy'
        ])
        ->latest()
        ->paginate(20);
        
        return view('tanda-terima-lcl.index', compact('tandaTerimas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $terms = Term::all();
        $jenisBarangs = JenisBarang::all();
        $tujuanKegiatanUtamas = TujuanKegiatanUtama::all();
        // Ambil karyawan yang memiliki divisi 'supir'
        $supirs = Karyawan::where('divisi', 'supir')
            ->select('nama_lengkap as nama_supir', 'plat as no_plat')
            ->get();
        
        return view('tanda-terima-lcl.create', compact(
            'terms', 
            'jenisBarangs', 
            'tujuanKegiatanUtamas', 
            'supirs'
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
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'required|exists:jenis_barangs,id',
            'kuantitas' => 'required|integer|min:1',
            'supir' => 'required|string|max:255',
            'no_plat' => 'required|string|max:255',
            'tujuan_pengiriman' => 'required|exists:tujuan_kegiatan_utamas,id',
            'dimensi_items' => 'required|array|min:1',
            'dimensi_items.*.panjang' => 'nullable|numeric|min:0',
            'dimensi_items.*.lebar' => 'nullable|numeric|min:0',
            'dimensi_items.*.tinggi' => 'nullable|numeric|min:0',
            'dimensi_items.*.tonase' => 'nullable|numeric|min:0'
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
                'nama_barang' => $request->nama_barang,
                'jenis_barang_id' => $request->jenis_barang,
                'kuantitas' => $request->kuantitas,
                'keterangan_barang' => $request->keterangan_barang,
                'supir' => $request->supir,
                'no_plat' => $request->no_plat,
                'tujuan_pengiriman_id' => $request->tujuan_pengiriman,
                'tipe_kontainer' => 'lcl',
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            // Create dimension items
            foreach ($request->dimensi_items as $index => $item) {
                if (!empty($item['panjang']) || !empty($item['lebar']) || !empty($item['tinggi']) || !empty($item['tonase'])) {
                    TandaTerimaLclItem::create([
                        'tanda_terima_lcl_id' => $tandaTerima->id,
                        'item_number' => $index + 1,
                        'panjang' => $item['panjang'] ?? null,
                        'lebar' => $item['lebar'] ?? null,
                        'tinggi' => $item['tinggi'] ?? null,
                        'tonase' => $item['tonase'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('tanda-terima-lcl.index')
                        ->with('success', 'Tanda Terima LCL berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tandaTerima = TandaTerimaLcl::with([
            'term',
            'jenisBarang',
            'tujuanPengiriman', 
            'items',
            'createdBy',
            'updatedBy'
        ])->findOrFail($id);
        
        return view('tanda-terima-lcl.show', compact('tandaTerima'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tandaTerima = TandaTerimaLcl::with('items')->findOrFail($id);
        $terms = Term::all();
        $jenisBarangs = JenisBarang::all();
        $tujuanKegiatanUtamas = TujuanKegiatanUtama::all();
        $supirs = Supir::all();
        
        return view('tanda-terima-lcl.edit', compact(
            'tandaTerima',
            'terms',
            'jenisBarangs',
            'tujuanKegiatanUtamas',
            'supirs'
        ));
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
            'term_id' => 'required|exists:terms,id',
            'nama_penerima' => 'required|string|max:255',
            'alamat_penerima' => 'required|string',
            'nama_pengirim' => 'required|string|max:255',
            'alamat_pengirim' => 'required|string',
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'required|exists:jenis_barangs,id',
            'kuantitas' => 'required|integer|min:1',
            'supir' => 'required|string|max:255',
            'no_plat' => 'required|string|max:255',
            'tujuan_pengiriman' => 'required|exists:tujuan_kegiatan_utamas,id',
            'dimensi_items' => 'required|array|min:1',
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
                'jenis_barang_id' => $request->jenis_barang,
                'kuantitas' => $request->kuantitas,
                'keterangan_barang' => $request->keterangan_barang,
                'supir' => $request->supir,
                'no_plat' => $request->no_plat,
                'tujuan_pengiriman_id' => $request->tujuan_pengiriman,
                'updated_by' => Auth::id(),
            ]);

            // Delete existing items and create new ones
            $tandaTerima->items()->delete();
            
            foreach ($request->dimensi_items as $index => $item) {
                if (!empty($item['panjang']) || !empty($item['lebar']) || !empty($item['tinggi']) || !empty($item['tonase'])) {
                    TandaTerimaLclItem::create([
                        'tanda_terima_lcl_id' => $tandaTerima->id,
                        'item_number' => $index + 1,
                        'panjang' => $item['panjang'] ?? null,
                        'lebar' => $item['lebar'] ?? null,
                        'tinggi' => $item['tinggi'] ?? null,
                        'tonase' => $item['tonase'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('tanda-terima-lcl.index')
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
        
        return redirect()->route('tanda-terima-lcl.index')
                        ->with('success', 'Tanda Terima LCL berhasil dihapus.');
    }
}
