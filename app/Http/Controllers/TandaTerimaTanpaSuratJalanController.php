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
use App\Models\MasterPengirimPenerima;
use App\Models\Prospek;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $masterPengirimPenerima = MasterPengirimPenerima::where('status', 'active')->orderBy('nama')->get();
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

        return view('tanda-terima-tanpa-surat-jalan.create', compact('terms', 'pengirims', 'masterPengirimPenerima', 'supirs', 'kranis', 'tujuan_kirims', 'master_kapals', 'tipe', 'containerOptions', 'kegiatanSuratJalan'));
    }

    /**
     * Show the form for creating LCL specifically.
     */
    public function createLcl()
    {
        $terms = Term::where('status', 'active')->get();
        $pengirims = Pengirim::where('status', 'active')->get();
        $masterPengirimPenerima = MasterPengirimPenerima::where('status', 'active')->orderBy('nama')->get();
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
            'masterPengirimPenerima',
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
        // DISABLED: dimensi_items[][] is no longer sent from form
        // Form now sends panjang[], lebar[], tinggi[], meter_kubik[], tonase[] arrays directly
        // This code is kept for backward compatibility with old forms only
        /*
        if ($request->has('dimensi_items') && is_array($request->input('dimensi_items'))) {
            $nested = $request->input('dimensi_items');
            $flattened = [
                'nama_barang' => [],
                'jumlah' => [],
                'satuan' => [],
                'panjang' => [],
                'lebar' => [],
                'tinggi' => [],
                'meter_kubik' => [],
                'tonase' => []
            ];
            foreach ($nested as $idx => $n) {
                $flattened['nama_barang'][] = $n['nama_barang'] ?? null;
                $flattened['jumlah'][] = $n['jumlah'] ?? null;
                $flattened['satuan'][] = $n['satuan'] ?? null;
                $flattened['panjang'][] = $n['panjang'] ?? null;
                $flattened['lebar'][] = $n['lebar'] ?? null;
                $flattened['tinggi'][] = $n['tinggi'] ?? null;
                $flattened['meter_kubik'][] = $n['meter_kubik'] ?? null;
                $flattened['tonase'][] = $n['tonase'] ?? null;
            }
            foreach ($flattened as $k => $vals) {
                $existing = (array) $request->input($k, []);
                $merged = array_values(array_filter(array_merge($existing, (array) $vals), function ($v) {
                    return $v !== null && $v !== '';
                }));
                if (!empty($merged)) {
                    $request->merge([$k => $merged]);
                }
            }
        }
        */

        // If legacy scalar values exist (hidden single values) convert them into arrays
        // REMOVED - This logic is incorrect and causes issues
        // The form always sends arrays (panjang[], lebar[], etc.) so no conversion needed
        /*
        foreach (['panjang', 'lebar', 'tinggi', 'meter_kubik', 'tonase', 'nama_barang', 'jumlah', 'satuan'] as $k) {
            $val = $request->input($k);
            if (!is_null($val) && !is_array($val)) {
                // Only convert scalar to array when arrays not present already
                if (empty($request->input($k)) || !is_array($request->input($k))) {
                    $request->merge([$k => [$val]]);
                }
            }
        }
        */

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
            // Validation for uploaded images
            'gambar_tanda_terima' => 'nullable|array|max:5',
            'gambar_tanda_terima.*' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10240', // 10MB per file
        ]);

        try {
            DB::beginTransaction();

            // Debug: Log request data untuk troubleshooting
            \Log::info('=== TANDA TERIMA STORE - RAW REQUEST ===');
            \Log::info('panjang[]:', ['data' => $request->input('panjang'), 'count' => count($request->input('panjang', []))]);
            \Log::info('lebar[]:', ['data' => $request->input('lebar'), 'count' => count($request->input('lebar', []))]);
            \Log::info('tinggi[]:', ['data' => $request->input('tinggi'), 'count' => count($request->input('tinggi', []))]);
            \Log::info('meter_kubik[]:', ['data' => $request->input('meter_kubik'), 'count' => count($request->input('meter_kubik', []))]);
            \Log::info('tonase[]:', ['data' => $request->input('tonase'), 'count' => count($request->input('tonase', []))]);
            \Log::info('nama_barang[]:', ['data' => $request->input('nama_barang'), 'count' => count($request->input('nama_barang', []))]);
            \Log::info('dimensi_items (should be empty):', ['data' => $request->input('dimensi_items')]);

            // Generate tanda terima number
            $validated['no_tanda_terima'] = TandaTerimaTanpaSuratJalan::generateNoTandaTerima();
            $validated['created_by'] = Auth::user()->name;

            // (Legacy flattened dimensi items merged before validation)

            // Extract array data for dimensi items and sanitize values
            // Keep indices aligned - don't filter out items, just convert types
            $maxCount = max(
                count($validated['nama_barang'] ?? []),
                count($validated['jumlah'] ?? []),
                count($validated['panjang'] ?? []),
                count($validated['lebar'] ?? []),
                count($validated['tinggi'] ?? []),
                count($validated['meter_kubik'] ?? []),
                count($validated['tonase'] ?? [])
            );
            
            $namaBarangArray = [];
            $jumlahArray = [];
            $satuanArray = [];
            $panjangArray = [];
            $lebarArray = [];
            $tinggiArray = [];
            $meterKubikArray = [];
            $tonaseArray = [];
            
            for ($idx = 0; $idx < $maxCount; $idx++) {
                $namaBarangArray[$idx] = isset($validated['nama_barang'][$idx]) ? trim((string)$validated['nama_barang'][$idx]) : null;
                $jumlahArray[$idx] = isset($validated['jumlah'][$idx]) && is_numeric($validated['jumlah'][$idx]) ? (int)$validated['jumlah'][$idx] : null;
                $satuanArray[$idx] = isset($validated['satuan'][$idx]) ? trim((string)$validated['satuan'][$idx]) : null;
                $panjangArray[$idx] = isset($validated['panjang'][$idx]) && is_numeric($validated['panjang'][$idx]) ? (float)$validated['panjang'][$idx] : null;
                $lebarArray[$idx] = isset($validated['lebar'][$idx]) && is_numeric($validated['lebar'][$idx]) ? (float)$validated['lebar'][$idx] : null;
                $tinggiArray[$idx] = isset($validated['tinggi'][$idx]) && is_numeric($validated['tinggi'][$idx]) ? (float)$validated['tinggi'][$idx] : null;
                $meterKubikArray[$idx] = isset($validated['meter_kubik'][$idx]) && is_numeric($validated['meter_kubik'][$idx]) ? (float)$validated['meter_kubik'][$idx] : null;
                $tonaseArray[$idx] = isset($validated['tonase'][$idx]) && is_numeric($validated['tonase'][$idx]) ? (float)$validated['tonase'][$idx] : null;
            }

            // Debug: Log extracted arrays (sanitized)
            \Log::info('Extracted Arrays Debug:', [
                'namaBarangArray' => $namaBarangArray,
                'panjangArray' => $panjangArray,
                'lebarArray' => $lebarArray,
                'tinggiArray' => $tinggiArray,
                'meterKubikArray' => $meterKubikArray,
                'tonaseArray' => $tonaseArray,
            ]);

            // Debug: lengths
            \Log::info('Dimensi arrays count', [
                'nama_count' => count($namaBarangArray),
                'panjang_count' => count($panjangArray),
                'lebar_count' => count($lebarArray),
                'tinggi_count' => count($tinggiArray),
                'meter_kubik_count' => count($meterKubikArray),
                'tonase_count' => count($tonaseArray),
            ]);

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

            // Handle gambar upload
            $uploadedImages = [];
            if ($request->hasFile('gambar_tanda_terima')) {
                $uploadedImages = $this->handleImageUpload($request->file('gambar_tanda_terima'));
                if (!empty($uploadedImages)) {
                    $validated['gambar_tanda_terima'] = json_encode($uploadedImages);
                    
                    \Log::info('Images uploaded for tanda terima tanpa surat jalan', [
                        'images_count' => count($uploadedImages),
                        'uploaded_by' => Auth::user() ? Auth::user()->name : null,
                    ]);
                }
            }

            // Create main record
            $tandaTerima = TandaTerimaTanpaSuratJalan::create($validated);
            \Log::info('Tanda Terima created', ['id' => $tandaTerima->id, 'no_tanda_terima' => $tandaTerima->no_tanda_terima]);

            // Create dimensi items from array data
            $itemCount = $maxCount;

            for ($i = 0; $i < $itemCount; $i++) {
                $namaBarang = $namaBarangArray[$i] ?? null;
                $jumlah = $jumlahArray[$i] ?? null;
                $satuan = $satuanArray[$i] ?? null;
                $panjang = $panjangArray[$i] ?? null;
                $lebar = $lebarArray[$i] ?? null;
                $tinggi = $tinggiArray[$i] ?? null;
                $meterKubik = $meterKubikArray[$i] ?? null;
                $tonase = $tonaseArray[$i] ?? null;

                // Only create if at least one field has meaningful data (not all nulls)
                if (!is_null($namaBarang) || !is_null($panjang) || !is_null($lebar) || !is_null($tinggi) || !is_null($meterKubik) || !is_null($tonase)) {
                    \Log::info('Creating dimensi item', [
                        'index' => $i,
                        'namaBarang' => $namaBarang,
                        'jumlah' => $jumlah,
                        'satuan' => $satuan,
                        'panjang' => $panjang,
                        'lebar' => $lebar,
                        'tinggi' => $tinggi,
                        'meterKubik' => $meterKubik,
                        'tonase' => $tonase,
                    ]);
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

            \Log::info('Dimensi items count after creation', ['count' => $tandaTerima->dimensiItems()->count()]);

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

        // Eager load dimensiItems for edit view
        $tandaTerimaTanpaSuratJalan->load('dimensiItems');
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
            // Accept either scalar or array inputs for LCL rows during edit
            'nama_barang' => 'nullable',
            'nama_barang.*' => 'nullable|string|max:255',
            'alamat_pengirim' => 'nullable|string',
            'alamat_penerima' => 'nullable|string',
            'jenis_barang' => 'required|string|max:255',
            'jumlah_barang' => 'required|integer|min:1',
            'satuan_barang' => 'required|string|max:50',
            'keterangan_barang' => 'nullable|string',
            'berat' => 'nullable|numeric|min:0',
            'satuan_berat' => 'nullable|string|max:10',
            'panjang' => 'nullable',
            'panjang.*' => 'nullable|numeric|min:0',
            'lebar' => 'nullable',
            'lebar.*' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable',
            'tinggi.*' => 'nullable|numeric|min:0',
            'meter_kubik' => 'nullable',
            'meter_kubik.*' => 'nullable|numeric|min:0',
            'tonase' => 'nullable',
            'tonase.*' => 'nullable|numeric|min:0',
            'tujuan_pengambilan' => 'required|string|max:255',
            'no_plat' => 'nullable|string|max:20',
            'catatan' => 'nullable|string',
            // Image handling for update
            'gambar_tanda_terima' => 'nullable|array|max:5',
            'gambar_tanda_terima.*' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10240', // 10MB per file
            'hapus_gambar' => 'nullable|array',
            'hapus_gambar.*' => 'nullable|string',
        ]);

        try {
            $validated['updated_by'] = Auth::user()->name;

            // Handle image deletions (existing images removed by user)
            $existingImages = $tandaTerimaTanpaSuratJalan->gambar_tanda_terima ?: [];
            if (is_string($existingImages)) {
                $decoded = json_decode($existingImages, true);
                $existingImages = is_array($decoded) ? $decoded : [];
            }

            $hapusGambar = $request->input('hapus_gambar', []);
            if (is_array($hapusGambar) && count($hapusGambar)) {
                foreach ($hapusGambar as $pathToDelete) {
                    // Normalize storage path (strip possible asset prefix)
                    $normalizedPath = ltrim(preg_replace('#^https?://[^/]+/storage/#', '', $pathToDelete), '/');
                    // Remove from existingImages
                    $existingImages = array_values(array_filter($existingImages, function($v) use ($normalizedPath) {
                        return $v !== $normalizedPath && ltrim($v, '/') !== ltrim($normalizedPath, '/');
                    }));
                    // Remove from storage if present
                    if (Storage::disk('public')->exists($normalizedPath)) {
                        Storage::disk('public')->delete($normalizedPath);
                    }
                }
            }

            // Handle gambar uploads
            $newUploads = [];
            if ($request->hasFile('gambar_tanda_terima')) {
                $newUploads = $this->handleImageUpload($request->file('gambar_tanda_terima'));
                if (!empty($newUploads)) {
                    
                    
                    Log::info('New image uploads during update', ['count' => count($newUploads), 'uploaded_by' => Auth::user() ? Auth::user()->name : null]);
                }
            }

            // Merge existing and new uploads
            $mergedImages = array_values(array_filter(array_merge($existingImages, $newUploads)));
            // Enforce max 5 images
            if (count($mergedImages) > 5) {
                $mergedImages = array_slice($mergedImages, 0, 5);
            }
            if (!empty($mergedImages)) {
                $validated['gambar_tanda_terima'] = json_encode($mergedImages);
            } else {
                $validated['gambar_tanda_terima'] = null;
            }

            // Handle LCL-style dimensi arrays when present in the request
            // Convert scalar inputs to arrays if necessary (fallback)
            $namaArray = $request->input('nama_barang');
            $jumlahArray = $request->input('jumlah');
            $satuanArray = $request->input('satuan');
            $panjangArray = $request->input('panjang');
            $lebarArray = $request->input('lebar');
            $tinggiArray = $request->input('tinggi');
            $meterArray = $request->input('meter_kubik');
            $tonaseArray = $request->input('tonase');

            // If arrays are provided, sanitize and persist dimensi items
            if (is_array($namaArray) || is_array($panjangArray) || is_array($meterArray)) {
                $maxCount = max(
                    count(is_array($namaArray) ? $namaArray : []),
                    count(is_array($jumlahArray) ? $jumlahArray : []),
                    count(is_array($panjangArray) ? $panjangArray : []),
                    count(is_array($lebarArray) ? $lebarArray : []),
                    count(is_array($tinggiArray) ? $tinggiArray : []),
                    count(is_array($meterArray) ? $meterArray : []),
                    count(is_array($tonaseArray) ? $tonaseArray : [])
                );

                $namaBarangArray = [];
                $jumlahClean = [];
                $satuanClean = [];
                $panjangClean = [];
                $lebarClean = [];
                $tinggiClean = [];
                $meterClean = [];
                $tonaseClean = [];

                for ($i = 0; $i < $maxCount; $i++) {
                    $namaBarangArray[$i] = isset($namaArray[$i]) ? trim((string)$namaArray[$i]) : null;
                    $jumlahClean[$i] = isset($jumlahArray[$i]) && is_numeric($jumlahArray[$i]) ? (int)$jumlahArray[$i] : null;
                    $satuanClean[$i] = isset($satuanArray[$i]) ? trim((string)$satuanArray[$i]) : null;
                    $panjangClean[$i] = isset($panjangArray[$i]) && is_numeric($panjangArray[$i]) ? (float)$panjangArray[$i] : null;
                    $lebarClean[$i] = isset($lebarArray[$i]) && is_numeric($lebarArray[$i]) ? (float)$lebarArray[$i] : null;
                    $tinggiClean[$i] = isset($tinggiArray[$i]) && is_numeric($tinggiArray[$i]) ? (float)$tinggiArray[$i] : null;
                    $meterClean[$i] = isset($meterArray[$i]) && is_numeric($meterArray[$i]) ? (float)$meterArray[$i] : null;
                    $tonaseClean[$i] = isset($tonaseArray[$i]) && is_numeric($tonaseArray[$i]) ? (float)$tonaseArray[$i] : null;
                }

                // Delete existing items and recreate from arrays
                try {
                    $tandaTerimaTanpaSuratJalan->dimensiItems()->delete();
                } catch (\Exception $e) {
                    // ignore delete errors
                }

                for ($i = 0; $i < $maxCount; $i++) {
                    $n = $namaBarangArray[$i] ?? null;
                    $j = $jumlahClean[$i] ?? null;
                    $s = $satuanClean[$i] ?? null;
                    $p = $panjangClean[$i] ?? null;
                    $l = $lebarClean[$i] ?? null;
                    $t = $tinggiClean[$i] ?? null;
                    $m = $meterClean[$i] ?? null;
                    $o = $tonaseClean[$i] ?? null;

                    if (!is_null($n) || !is_null($p) || !is_null($l) || !is_null($t) || !is_null($m) || !is_null($o)) {
                        $tandaTerimaTanpaSuratJalan->dimensiItems()->create([
                            'nama_barang' => $n,
                            'jumlah' => $j,
                            'satuan' => $s,
                            'panjang' => $p,
                            'lebar' => $l,
                            'tinggi' => $t,
                            'meter_kubik' => $m,
                            'tonase' => $o,
                            'item_order' => $i,
                        ]);
                    }
                }

                // Update scalar fallback values
                if (!empty($namaBarangArray)) {
                    $validated['nama_barang'] = $namaBarangArray[0] ?? $validated['nama_barang'] ?? null;
                }
                if (!empty($jumlahClean)) {
                    $validated['jumlah_barang'] = array_sum(array_filter($jumlahClean, 'is_numeric')) ?: ($validated['jumlah_barang'] ?? 1);
                }
                if (!empty($satuanClean)) {
                    $validated['satuan_barang'] = $satuanClean[0] ?? ($validated['satuan_barang'] ?? 'unit');
                }
                if (!empty($meterClean)) {
                    $validated['meter_kubik'] = array_sum(array_filter($meterClean, 'is_numeric'));
                }
                if (!empty($tonaseClean)) {
                    $validated['tonase'] = array_sum(array_filter($tonaseClean, 'is_numeric'));
                }
            }

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

    /**
     * Handle image upload for tanda terima images
     * 
     * @param array $uploadedFiles
     * @return array Array of uploaded image paths
     */
    private function handleImageUpload($uploadedFiles)
    {
        $imagePaths = [];
        
        try {
            foreach ($uploadedFiles as $file) {
                if ($file->isValid()) {
                    // Generate unique filename with timestamp
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $filename = 'tanda_terima_' . time() . '_' . uniqid() . '_' . Str::slug($originalName) . '.' . $extension;
                    
                    // Store in public disk under tanda-terima directory
                    $path = $file->storeAs('tanda-terima', $filename, 'public');
                    
                    if ($path) {
                        $imagePaths[] = $path;
                        
                        Log::info('Tanda terima image uploaded successfully', [
                            'original_name' => $file->getClientOriginalName(),
                            'stored_path' => $path,
                            'file_size' => $file->getSize(),
                            'uploaded_by' => Auth::user() ? Auth::user()->name : null,
                        ]);
                    }
                } else {
                    Log::warning('Invalid file uploaded for tanda terima', [
                        'file_name' => $file->getClientOriginalName(),
                        'error' => $file->getErrorMessage(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error uploading tanda terima images: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        return $imagePaths;
    }
}
