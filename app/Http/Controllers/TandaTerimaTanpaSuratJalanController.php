<?php

namespace App\Http\Controllers;

use App\Models\TandaTerimaTanpaSuratJalan;
use App\Models\TandaTerimaDimensiItem;
use App\Models\TandaTerimaLcl;
use App\Models\Term;
use App\Models\Pengirim;
use App\Models\Karyawan;
use App\Models\MasterTujuanKirim;
use App\Models\MasterKapal;
use App\Models\Prospek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TandaTerimaTanpaSuratJalanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tanda-terima-tanpa-surat-jalan-view', ['only' => ['index', 'show']]);
        $this->middleware('permission:tanda-terima-tanpa-surat-jalan-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:tanda-terima-tanpa-surat-jalan-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:tanda-terima-tanpa-surat-jalan-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tipe = $request->get('tipe');
        
        // Jika tipe LCL dipilih, ambil data dari tabel tanda_terima_lcl
        if ($tipe == 'lcl') {
            $query = \App\Models\TandaTerimaLcl::query();
            
            // Search functionality untuk LCL
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('nomor_tanda_terima', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('nama_penerima', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('nama_pengirim', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('nama_barang', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('nomor_kontainer', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('nomor_seal', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('supir', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('no_plat', 'LIKE', '%' . $request->search . '%');
                });
            }

            // Date range filter untuk LCL
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('tanggal_tanda_terima', [$request->start_date, $request->end_date]);
            }

            $tandaTerimas = $query->with(['term', 'jenisBarang', 'tujuanPengiriman', 'createdBy'])
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(15);

            // Statistics untuk LCL
            $stats = [
                'total' => \App\Models\TandaTerimaLcl::count(),
                'draft' => 0,
                'terkirim' => 0,
                'selesai' => 0,
            ];
            
            // Set flag untuk view bahwa ini data LCL
            $isLclData = true;
        } else {
            // Data default dari tabel tanda_terima_tanpa_surat_jalan untuk FCL dan Cargo
            $query = TandaTerimaTanpaSuratJalan::query();

            // Filter berdasarkan tipe jika bukan LCL
            if ($request->filled('tipe') && in_array($tipe, ['fcl', 'cargo'])) {
                // Asumsi ada kolom tipe atau logika untuk membedakan FCL dan Cargo
                // Untuk sementara kita skip filter ini karena belum ada kolom tipe
            }

            // Search functionality
            if ($request->filled('search')) {
                $query->search($request->search);
            }

            // Date range filter
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->byDateRange($request->start_date, $request->end_date);
            }

            $tandaTerimas = $query->orderBy('created_at', 'desc')->paginate(15);

            // Statistics
            $stats = [
                'total' => TandaTerimaTanpaSuratJalan::count(),
                'draft' => 0,
                'terkirim' => 0,
                'selesai' => 0,
            ];
            
            $isLclData = false;
        }

        return view('tanda-terima-tanpa-surat-jalan.index', compact('tandaTerimas', 'stats', 'isLclData'));
    }

    /**
     * Show the form for selecting container type before create
     */
    public function pilihTipe()
    {
        return view('tanda-terima-tanpa-surat-jalan.pilih-tipe');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Validasi tipe kontainer harus ada
        $tipe = $request->get('tipe');
        if (!in_array($tipe, ['fcl', 'lcl', 'cargo'])) {
            return redirect()->route('tanda-terima-tanpa-surat-jalan.pilih-tipe')
                ->with('error', 'Silakan pilih tipe kontainer terlebih dahulu.');
        }

        $terms = Term::where('status', 'active')->get();
        $pengirims = Pengirim::where('status', 'active')->get();
        $supirs = Karyawan::whereRaw('UPPER(divisi) = ?', ['SUPIR'])->get();
        $kranis = Karyawan::whereRaw('UPPER(divisi) = ?', ['KRANI'])->get();
        $tujuan_kirims = MasterTujuanKirim::where('status', 'active')->orderBy('nama_tujuan')->get();
        $master_kapals = MasterKapal::where('status', 'aktif')->get();
        
        // Debug: pastikan data tujuan ada
        \Log::info('Tujuan Kirims Data:', ['count' => $tujuan_kirims->count(), 'data' => $tujuan_kirims->toArray()]);

        return view('tanda-terima-tanpa-surat-jalan.create', compact('terms', 'pengirims', 'supirs', 'kranis', 'tujuan_kirims', 'master_kapals', 'tipe'));
    }

    /**
     * Show the form for creating LCL specifically.
     */
    public function createLcl()
    {
        $terms = Term::where('status', 'active')->get();
        $pengirims = Pengirim::where('status', 'active')->get();
        $supirs = Karyawan::whereRaw('UPPER(divisi) = ?', ['SUPIR'])->get();
        $kranis = Karyawan::whereRaw('UPPER(divisi) = ?', ['KRANI'])->get();
        $tujuanKegiatanUtamas = MasterTujuanKirim::where('status', 'active')->get();
        
        // Get jenis barangs using raw SQL with proper column names
        $jenisBarangs = DB::table('jenis_barangs')
            ->select('id', 'nama_barang', 'kode', 'status')
            ->where('status', 'active')
            ->get();

        return view('tanda-terima-tanpa-surat-jalan.create-lcl', compact(
            'terms', 
            'pengirims', 
            'supirs', 
            'kranis', 
            'tujuanKegiatanUtamas', 
            'jenisBarangs'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_tanda_terima' => 'required|date',
            'nomor_surat_jalan_customer' => 'nullable|string|max:255',
            'no_surat_jalan_customer' => 'nullable|string|max:255',
            'nomor_tanda_terima' => 'nullable|string|max:255',
            'term_id' => 'nullable|exists:terms,id',
            'aktifitas' => 'nullable|string|max:255',
            'tipe_kontainer' => 'nullable|string|max:50',
            'tipe_kontainer_selected' => 'nullable|string|max:50',
            'no_kontainer' => 'nullable|string|max:255',
            'size_kontainer' => 'nullable|string|max:50',
            // Penerima dan Pengirim
            'penerima' => 'required|string|max:255',
            'pengirim' => 'required|string|max:255',
            'pic' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:50',
            'alamat_penerima' => 'nullable|string',
            'alamat_pengirim' => 'nullable|string',
            // Legacy fields for backward compatibility (if needed)
            'nama_penerima' => 'nullable|string|max:255',
            'nama_pengirim' => 'nullable|string|max:255',
            // Supir dan transportasi
            'supir' => 'required|string|max:255',
            'kenek' => 'nullable|string|max:255',
            'no_plat' => 'nullable|string|max:20',
            'tujuan_pengiriman' => 'required|string|max:255',
            'estimasi_naik_kapal' => 'nullable|string|max:255',
            'no_seal' => 'nullable|string|max:255',
            'tanggal_seal' => 'nullable|date',
            // Barang
            'nama_barang' => 'nullable|string|max:255',
            'kuantitas' => 'nullable|integer|min:1',
            'jenis_barang' => 'required|string|max:255',
            'jumlah_barang' => 'required|integer|min:1',
            'satuan_barang' => 'required|string|max:50',
            'keterangan_barang' => 'nullable|string',
            'berat' => 'nullable|numeric|min:0',
            'satuan_berat' => 'nullable|string|max:10',
            // Hidden fields for backward compatibility
            'panjang' => 'nullable|numeric|min:0',
            'lebar' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|numeric|min:0',
            'meter_kubik' => 'nullable|numeric|min:0',
            'tonase' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            // Dimensi items array
            'dimensi_items' => 'nullable|array',
            'dimensi_items.*.panjang' => 'nullable|numeric|min:0',
            'dimensi_items.*.lebar' => 'nullable|numeric|min:0',
            'dimensi_items.*.tinggi' => 'nullable|numeric|min:0',
            'dimensi_items.*.meter_kubik' => 'nullable|numeric|min:0',
            'dimensi_items.*.tonase' => 'nullable|numeric|min:0',
            // Auto-save to prospek
            'simpan_ke_prospek' => 'nullable|string|max:1',
        ]);

        try {
            DB::beginTransaction();

            // Generate tanda terima number
            $validated['no_tanda_terima'] = TandaTerimaTanpaSuratJalan::generateNoTandaTerima();
            $validated['created_by'] = Auth::user()->name;

            // Remove dimensi_items from main validation data
            $dimensiItems = $validated['dimensi_items'] ?? [];
            unset($validated['dimensi_items']);

            // Create main record
            $tandaTerima = TandaTerimaTanpaSuratJalan::create($validated);

            // Create dimensi items if provided
            if (!empty($dimensiItems)) {
                foreach ($dimensiItems as $index => $item) {
                    if (!empty($item['panjang']) || !empty($item['lebar']) || !empty($item['tinggi']) || !empty($item['tonase'])) {
                        $tandaTerima->dimensiItems()->create([
                            'panjang' => $item['panjang'] ?? null,
                            'lebar' => $item['lebar'] ?? null,
                            'tinggi' => $item['tinggi'] ?? null,
                            'meter_kubik' => $item['meter_kubik'] ?? null,
                            'tonase' => $item['tonase'] ?? null,
                            'item_order' => $index
                        ]);
                    }
                }
            }

            // Auto-create prospek for all tanda terima (sesuai permintaan user)
            if ($request->filled('simpan_ke_prospek') || true) { // Always create prospek
                // Extract size from size_kontainer (e.g., "20 ft" -> "20")
                $ukuran = null;
                if (!empty($validated['size_kontainer'])) {
                    $sizeStr = $validated['size_kontainer'];
                    if (strpos($sizeStr, '20') !== false) {
                        $ukuran = '20';
                    } elseif (strpos($sizeStr, '40') !== false) {
                        $ukuran = '40';
                    } elseif (strpos($sizeStr, '45') !== false) {
                        $ukuran = '45';
                    } elseif (strpos($sizeStr, '53') !== false) {
                        $ukuran = '53';
                    }
                }

                // Determine tipe based on tipe_kontainer
                $tipeProspek = 'FCL'; // Default
                if ($validated['tipe_kontainer'] === 'lcl') {
                    $tipeProspek = 'LCL';
                } elseif ($validated['tipe_kontainer'] === 'cargo') {
                    $tipeProspek = 'CARGO';
                }

                // Use correct field names based on validation rules
                $penerima = $validated['penerima'] ?? $validated['nama_penerima'] ?? 'Tidak diketahui';
                $pengirim = $validated['pengirim'] ?? $validated['nama_pengirim'] ?? 'Tidak diketahui';

                Prospek::create([
                    'tanggal' => $validated['tanggal_tanda_terima'],
                    'nama_supir' => $validated['supir'] ?: 'Supir Customer',
                    'barang' => $validated['nama_barang'] ?: $validated['jenis_barang'] ?: 'Barang',
                    'pt_pengirim' => $pengirim,
                    'ukuran' => $ukuran,
                    'tipe' => $tipeProspek,
                    'nomor_kontainer' => $validated['no_kontainer'] ?: ($tipeProspek === 'CARGO' ? 'CARGO' : null),
                    'no_seal' => $validated['no_seal'] ?: null,
                    'tujuan_pengiriman' => $validated['tujuan_pengiriman'],
                    'nama_kapal' => $validated['estimasi_naik_kapal'] ?: null,
                    'total_ton' => $validated['tonase'] ?: null,
                    'total_volume' => $validated['meter_kubik'] ?: null,
                    'kuantitas' => $validated['jumlah_barang'] ?: 1,
                    'keterangan' => 'Auto-generated from Tanda Terima Tanpa Surat Jalan: ' . $tandaTerima->no_tanda_terima . 
                                  ($validated['catatan'] ? ' | Catatan: ' . $validated['catatan'] : '') .
                                  ($penerima !== 'Tidak diketahui' ? ' | Penerima: ' . $penerima : ''),
                    'status' => 'aktif',
                    'created_by' => Auth::id()
                ]);

                $prospekCreated = true;
            }

            DB::commit();

            $message = 'Tanda terima berhasil dibuat.';
            if (isset($prospekCreated) && $prospekCreated) {
                $message .= ' Data juga telah tersimpan sebagai prospek.';
            }

            return redirect()->route('tanda-terima-tanpa-surat-jalan.index')
                           ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TandaTerimaTanpaSuratJalan $tandaTerimaTanpaSuratJalan)
    {
        return view('tanda-terima-tanpa-surat-jalan.show', compact('tandaTerimaTanpaSuratJalan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TandaTerimaTanpaSuratJalan $tandaTerimaTanpaSuratJalan)
    {
        $terms = Term::where('status', 'active')->get();
        $pengirims = Pengirim::where('status', 'active')->get();
        $supirs = Karyawan::whereRaw('UPPER(divisi) = ?', ['SUPIR'])->get();
        $kranis = Karyawan::whereRaw('UPPER(divisi) = ?', ['KRANI'])->get();
        $tujuan_kirims = MasterTujuanKirim::where('status', 'active')->get();
        $master_kapals = MasterKapal::where('status', 'aktif')->get();

        return view('tanda-terima-tanpa-surat-jalan.edit', compact('tandaTerimaTanpaSuratJalan', 'terms', 'pengirims', 'supirs', 'kranis', 'tujuan_kirims', 'master_kapals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TandaTerimaTanpaSuratJalan $tandaTerimaTanpaSuratJalan)
    {
        $validated = $request->validate([
            'tanggal_tanda_terima' => 'required|date',
            'nomor_surat_jalan_customer' => 'nullable|string|max:255',
            'nomor_tanda_terima' => 'nullable|string|max:255',
            'supir' => 'nullable|string|max:255',
            'kenek' => 'nullable|string|max:255',
            'term_id' => 'nullable|exists:terms,id',
            'aktifitas' => 'nullable|string|max:255',
            'tipe_kontainer' => 'nullable|string|max:50',
            'jenis_pengiriman' => 'nullable|string|max:255',
            'no_kontainer' => 'nullable|string|max:255',
            'size_kontainer' => 'nullable|string|max:50',
            'pengirim' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:50',
            'tujuan_pengiriman' => 'nullable|string|max:255',
            'estimasi_naik_kapal' => 'nullable|string|max:255',
            'no_seal' => 'nullable|string|max:255',
            'tanggal_seal' => 'nullable|date',
            'PIC' => 'nullable|string|max:255',
            'penerima' => 'required|string|max:255',
            'nama_barang' => 'nullable|string|max:255',
            'alamat_pengirim' => 'nullable|string',
            'alamat_penerima' => 'nullable|string',
            'jenis_barang' => 'required|string|max:255',
            'jumlah_barang' => 'required|integer|min:1',
            'satuan_barang' => 'required|string|max:50',
            'keterangan_barang' => 'nullable|string',
            'berat' => 'nullable|numeric|min:0',
            'satuan_berat' => 'nullable|string|max:10',
            'panjang' => 'nullable|numeric|min:0',
            'lebar' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|numeric|min:0',
            'meter_kubik' => 'nullable|numeric|min:0',
            'tonase' => 'nullable|numeric|min:0',
            'tujuan_pengambilan' => 'required|string|max:255',
            'no_plat' => 'nullable|string|max:20',
            'catatan' => 'nullable|string',
        ]);

        try {
            $validated['updated_by'] = Auth::user()->name;

            $tandaTerimaTanpaSuratJalan->update($validated);

            return redirect()->route('tanda-terima-tanpa-surat-jalan.index')
                           ->with('success', 'Tanda terima berhasil diupdate.');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TandaTerimaTanpaSuratJalan $tandaTerimaTanpaSuratJalan)
    {
        try {
            $tandaTerimaTanpaSuratJalan->delete();

            return redirect()->route('tanda-terima-tanpa-surat-jalan.index')
                           ->with('success', 'Tanda terima berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
