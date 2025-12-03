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
use App\Models\Kontainer;
use App\Models\StockKontainer;
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

            $tandaTerimas = $query->with(['term', 'tujuanPengiriman', 'createdBy'])
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
        $supirs = Karyawan::whereRaw('UPPER(divisi) = ?', ['SUPIR'])
                          ->orderBy('nama_panggilan')
                          ->get(['id', 'nama_lengkap', 'nama_panggilan']);
        $kranis = Karyawan::whereRaw('UPPER(divisi) = ?', ['KRANI'])->get();
                $tujuan_kirims = MasterTujuanKirim::where('status', 'active')->orderBy('nama_tujuan')->get();
                // Load kegiatan list from master_kegiatans (use same type as surat jalan if available)
                $kegiatanSuratJalan = \App\Models\MasterKegiatan::where(function($q) {
                        $q->where('type', 'kegiatan tanda terima')
                            ->orWhere('type', 'kegiatan surat jalan');
                })->where('status', 'Aktif')
                    ->orderBy('nama_kegiatan')
                    ->get(['id', 'nama_kegiatan']);
        $master_kapals = MasterKapal::where('status', 'aktif')->get();
        
        // Debug: pastikan data tujuan ada
        \Log::info('Tujuan Kirims Data:', ['count' => $tujuan_kirims->count(), 'data' => $tujuan_kirims->toArray()]);

        // Fetch container options from Kontainer and StockKontainer, prefer Kontainer when duplicated
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

        return view('tanda-terima-tanpa-surat-jalan.create', compact('terms', 'pengirims', 'supirs', 'kranis', 'tujuan_kirims', 'master_kapals', 'tipe', 'containerOptions', 'kegiatanSuratJalan'));
    }

    /**
     * Show the form for creating LCL specifically.
     */
    public function createLcl()
    {
        $terms = Term::where('status', 'active')->get();
        $pengirims = Pengirim::where('status', 'active')->get();
        $supirs = Karyawan::whereRaw('UPPER(divisi) = ?', ['SUPIR'])
                          ->orderBy('nama_panggilan')
                          ->get(['id', 'nama_lengkap', 'nama_panggilan']);
        $kranis = Karyawan::whereRaw('UPPER(divisi) = ?', ['KRANI'])->get();
        $tujuanKegiatanUtamas = MasterTujuanKirim::where('status', 'active')->get();
        
        // Get jenis barangs using raw SQL with proper column names
        $jenisBarangs = DB::table('jenis_barangs')
            ->select('id', 'nama_barang', 'kode', 'status')
            ->where('status', 'active')
            ->get();

        // Container options for LCL
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
            'pengirims', 
            'supirs', 
            'kranis', 
            'tujuanKegiatanUtamas', 
            'jenisBarangs'
        , 'containerOptions'));
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
            // Barang - Array format dari form
            'nama_barang' => 'nullable|array',
            'nama_barang.*' => 'nullable|string|max:255',
            'jumlah' => 'nullable|array',
            'jumlah.*' => 'nullable|integer|min:1',
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
            // Hidden fields for backward compatibility (scalar values)
            'jenis_barang' => 'nullable|string|max:255',
            'aktifitas' => 'nullable|string|max:255',
            'jumlah_barang' => 'nullable|integer|min:1',
            'satuan_barang' => 'nullable|string|max:50',
            'keterangan_barang' => 'nullable|string',
            'berat' => 'nullable|numeric|min:0',
            'satuan_berat' => 'nullable|string|max:10',
            'catatan' => 'nullable|string',
            // Auto-save to prospek
            'simpan_ke_prospek' => 'nullable|string|max:1',
        ]);

        try {
            DB::beginTransaction();

            // Generate tanda terima number
            $validated['no_tanda_terima'] = TandaTerimaTanpaSuratJalan::generateNoTandaTerima();
            $validated['created_by'] = Auth::user()->name;

            // Extract array data for dimensi items
            $namaBarangArray = $validated['nama_barang'] ?? [];
            $jumlahArray = $validated['jumlah'] ?? [];
            $satuanArray = $validated['satuan'] ?? [];
            $panjangArray = $validated['panjang'] ?? [];
            $lebarArray = $validated['lebar'] ?? [];
            $tinggiArray = $validated['tinggi'] ?? [];
            $meterKubikArray = $validated['meter_kubik'] ?? [];
            $tonaseArray = $validated['tonase'] ?? [];

            // Remove array fields from main validation data
            unset($validated['nama_barang'], $validated['jumlah'], $validated['satuan']);
            unset($validated['panjang'], $validated['lebar'], $validated['tinggi']);
            unset($validated['meter_kubik'], $validated['tonase']);

            // Set backward compatibility fields from first item or defaults
            if (!empty($namaBarangArray)) {
                $validated['nama_barang'] = $namaBarangArray[0] ?? null;
            }
            if (!empty($jumlahArray)) {
                $validated['jumlah_barang'] = $jumlahArray[0] ?? 1;
            }
            if (!empty($satuanArray)) {
                $validated['satuan_barang'] = $satuanArray[0] ?? 'unit';
            }
            
            // Set total volume and weight from arrays
            if (!empty($meterKubikArray)) {
                $validated['meter_kubik'] = array_sum(array_filter($meterKubikArray, 'is_numeric'));
            }
            if (!empty($tonaseArray)) {
                $validated['tonase'] = array_sum(array_filter($tonaseArray, 'is_numeric'));
            }

            // Create main record
            $tandaTerima = TandaTerimaTanpaSuratJalan::create($validated);

            // Create dimensi items from array data
            $itemCount = max(
                count($namaBarangArray),
                count($panjangArray),
                count($lebarArray),
                count($tinggiArray),
                count($tonaseArray)
            );

            for ($i = 0; $i < $itemCount; $i++) {
                $namaBarang = $namaBarangArray[$i] ?? null;
                $jumlah = $jumlahArray[$i] ?? null;
                $satuan = $satuanArray[$i] ?? null;
                $panjang = $panjangArray[$i] ?? null;
                $lebar = $lebarArray[$i] ?? null;
                $tinggi = $tinggiArray[$i] ?? null;
                $meterKubik = $meterKubikArray[$i] ?? null;
                $tonase = $tonaseArray[$i] ?? null;

                // Only create if at least one field has value
                if ($namaBarang || $panjang || $lebar || $tinggi || $tonase) {
                    $tandaTerima->dimensiItems()->create([
                        'nama_barang' => $namaBarang,
                        'jumlah' => $jumlah,
                        'satuan' => $satuan,
                        'panjang' => $panjang,
                        'lebar' => $lebar,
                        'tinggi' => $tinggi,
                        'meter_kubik' => $meterKubik,
                        'tonase' => $tonase,
                        'item_order' => $i
                    ]);
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
            $tipeKontainer = $validated['tipe_kontainer_selected'] ?? $validated['tipe_kontainer'] ?? 'fcl';
            $tipeProspek = 'FCL'; // Default
            if ($tipeKontainer === 'lcl') {
                $tipeProspek = 'LCL';
            } elseif ($tipeKontainer === 'cargo') {
                $tipeProspek = 'CARGO';
            }
            
            // Use the selected tipe for the record
            if (isset($validated['tipe_kontainer_selected'])) {
                $validated['tipe_kontainer'] = $validated['tipe_kontainer_selected'];
            }                // Use correct field names based on validation rules
                $penerima = $validated['penerima'] ?? $validated['nama_penerima'] ?? 'Tidak diketahui';
                $pengirim = $validated['pengirim'] ?? $validated['nama_pengirim'] ?? 'Tidak diketahui';

                Prospek::create([
                    'tanggal' => $validated['tanggal_tanda_terima'],
                    'nama_supir' => $validated['supir'] ?: 'Supir Customer',
                    'barang' => $validated['nama_barang'] ?? $validated['jenis_barang'] ?? 'Barang',
                    'pt_pengirim' => $pengirim,
                    'ukuran' => $ukuran,
                    'tipe' => $tipeProspek,
                    'nomor_kontainer' => $validated['no_kontainer'] ?? ($tipeProspek === 'CARGO' ? 'CARGO' : null),
                    'no_seal' => $validated['no_seal'] ?? null,
                    'tujuan_pengiriman' => $validated['tujuan_pengiriman'],
                    'nama_kapal' => $validated['estimasi_naik_kapal'] ?? null,
                    'total_ton' => $validated['tonase'] ?? null,
                    'total_volume' => $validated['meter_kubik'] ?? null,
                    'kuantitas' => $validated['jumlah_barang'] ?? 1,
                    'keterangan' => 'Auto-generated from Tanda Terima Tanpa Surat Jalan: ' . $tandaTerima->no_tanda_terima . 
                                  (isset($validated['catatan']) && $validated['catatan'] ? ' | Catatan: ' . $validated['catatan'] : '') .
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
        // Load dimensi items relationship
        $tandaTerimaTanpaSuratJalan->load(['dimensiItems', 'term', 'creator', 'updater']);
        
        return view('tanda-terima-tanpa-surat-jalan.show', compact('tandaTerimaTanpaSuratJalan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TandaTerimaTanpaSuratJalan $tandaTerimaTanpaSuratJalan)
    {
        $terms = Term::where('status', 'active')->get();
        $pengirims = Pengirim::where('status', 'active')->get();
        $supirs = Karyawan::whereRaw('UPPER(divisi) = ?', ['SUPIR'])
                          ->orderBy('nama_panggilan')
                          ->get(['id', 'nama_lengkap', 'nama_panggilan']);
        $kranis = Karyawan::whereRaw('UPPER(divisi) = ?', ['KRANI'])->get();
        $tujuan_kirims = MasterTujuanKirim::where('status', 'active')->get();
        $master_kapals = MasterKapal::where('status', 'aktif')->get();

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

        return view('tanda-terima-tanpa-surat-jalan.edit', compact('tandaTerimaTanpaSuratJalan', 'terms', 'pengirims', 'supirs', 'kranis', 'tujuan_kirims', 'master_kapals', 'containerOptions'));
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
