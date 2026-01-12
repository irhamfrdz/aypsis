<?php

namespace App\Http\Controllers;

use App\Models\TandaTerima;
use App\Models\Prospek;
use App\Models\MasterKapal;
use App\Models\MasterPengirimPenerima;
use App\Models\SuratJalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TandaTerimaFilteredExport;

class TandaTerimaController extends Controller
{
    /**
     * Map an input tipe_kontainer string to the database enum values for tanda_terimas
     * Allowed enum values: fcl, lcl, cargo
     * Return mapped value string or null if not mappable
     */
    private function mapTipeKontainerValue(?string $value): ?string
    {
        if (!$value) return null;
        $v = strtolower(trim($value));

        $map = [
            // Keywords mapping to FCL
            'dry' => 'fcl',
            'dry container' => 'fcl',
            'high cube' => 'fcl',
            'hc' => 'fcl',
            'reefer' => 'fcl',
            'fcl' => 'fcl',

            // LCL
            'lcl' => 'lcl',
            'less than container load' => 'lcl',

            // Cargo / CARGO
            'cargo' => 'cargo',
            'cargo container' => 'cargo',
        ];

        // Try direct mapping
        if (isset($map[$v])) {
            return $map[$v];
        }

        // Try fuzzy matching with keywords
        foreach ($map as $k => $m) {
            if (strpos($v, $k) !== false) {
                return $m;
            }
        }

        return null;
    }
    /**
     * Show surat jalan selection page
     */
    public function selectSuratJalan(Request $request)
    {
        // Get search and filter parameters
        $search = $request->input('search', '');
        $status = $request->input('status', 'belum_ada_tanda_terima');

        // Status options
        $statusOptions = [
            'semua' => 'Semua Status',
            'belum_ada_tanda_terima' => 'Belum Ada Tanda Terima',
            'sudah_ada_tanda_terima' => 'Sudah Ada Tanda Terima'
        ];

        // Query surat jalan
        $query = SuratJalan::with(['order.pengirim']);

        // Exclude bongkaran
        $query->where(function($q) {
            $q->whereNull('kegiatan')
              ->orWhere('kegiatan', '')
              ->orWhere('kegiatan', 'NOT LIKE', '%bongkar%');
        });

        // Apply status filter
        if ($status === 'belum_ada_tanda_terima') {
            $query->whereDoesntHave('tandaTerima');
        } elseif ($status === 'sudah_ada_tanda_terima') {
            $query->whereHas('tandaTerima');
        }

        // Apply search filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhereHas('order', function($orderQuery) use ($search) {
                      $orderQuery->whereHas('pengirim', function($pengirimQuery) use ($search) {
                          $pengirimQuery->where('nama_pengirim', 'like', "%{$search}%");
                      });
                  });
            });
        }

        // Order by newest and paginate
        $perPage = (int) $request->input('per_page', 100);
        $suratJalans = $query->orderBy('created_at', 'desc')->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $suratJalans->items(),
                'meta' => [
                    'current_page' => $suratJalans->currentPage(),
                    'last_page' => $suratJalans->lastPage(),
                    'per_page' => $suratJalans->perPage(),
                    'total' => $suratJalans->total(),
                ],
            ]);
        }

        return view('tanda-terima.select-surat-jalan', compact('suratJalans', 'search', 'status', 'statusOptions'));
    }

    /**
     * Display a listing of tanda terima
     */
    public function index(Request $request)
    {
        // Get search and filter parameters
        $search = $request->input('search', '');
        $status = $request->input('status', '');
        $kegiatan = $request->input('kegiatan', '');
        $mode = $request->input('mode', '');
        $perPage = $request->input('rows_per_page', 25);

        // Get all kontainers for dropdown - combine from stock_kontainers and kontainers
        $stockKontainersFromStock = \App\Models\StockKontainer::select('nomor_seri_gabungan', 'ukuran', 'tipe_kontainer')
            ->whereNotNull('nomor_seri_gabungan')
            ->where('nomor_seri_gabungan', '!=', '')
            ->orderBy('nomor_seri_gabungan')
            ->get();
        
        $stockKontainersFromKontainers = \App\Models\Kontainer::select('nomor_seri_gabungan', 'ukuran', 'tipe_kontainer')
            ->whereNotNull('nomor_seri_gabungan')
            ->where('nomor_seri_gabungan', '!=', '')
            ->where('status', 'tersedia')
            ->orderBy('nomor_seri_gabungan')
            ->get();
        
        // Merge and remove duplicates
        $allKontainers = $stockKontainersFromStock->merge($stockKontainersFromKontainers)
            ->unique('nomor_seri_gabungan')
            ->sortBy('nomor_seri_gabungan')
            ->values();

        // Get distinct kegiatan for filter dropdown based on mode
        if ($mode === 'missing' || $mode === 'with_tanda_terima') {
            // Get distinct kegiatan from surat_jalans
            $kegiatanList = SuratJalan::select('kegiatan')
                ->whereNotNull('kegiatan')
                ->where('kegiatan', '!=', '')
                ->distinct()
                ->orderBy('kegiatan', 'asc')
                ->pluck('kegiatan');
        } else {
            // Get distinct kegiatan from tanda_terimas
            $kegiatanList = TandaTerima::select('kegiatan')
                ->whereNotNull('kegiatan')
                ->where('kegiatan', '!=', '')
                ->distinct()
                ->orderBy('kegiatan', 'asc')
                ->pluck('kegiatan');
        }

        // If mode is 'missing' then we should list Surat Jalan that don't have Tanda Terima
        if ($mode === 'missing') {
            $suratQuery = SuratJalan::with(['order.pengirim', 'uangJalan']);

            // Exclude bongkaran
            $suratQuery->where(function($q) {
                $q->whereNull('kegiatan')
                  ->orWhere('kegiatan', '')
                  ->orWhere('kegiatan', 'NOT LIKE', '%bongkar%');
            });

            // Apply search filter for surat jalan
            if (!empty($search)) {
                $suratQuery->where(function($q) use ($search) {
                    $q->where('no_surat_jalan', 'like', "%{$search}%")
                      ->orWhere('supir', 'like', "%{$search}%")
                      ->orWhere('no_plat', 'like', "%{$search}%")
                      ->orWhereHas('order', function($orderQuery) use ($search) {
                          $orderQuery->whereHas('pengirim', function($pengirimQuery) use ($search) {
                              $pengirimQuery->where('nama_pengirim', 'like', "%{$search}%");
                          });
                      });
                });
            }

            // Apply status filter to surat jalan if provided
            if (!empty($status)) {
                $suratQuery->where('status', $status);
            }

            // Apply kegiatan filter to surat jalan if provided
            if (!empty($kegiatan)) {
                $suratQuery->where('kegiatan', $kegiatan);
            }

            // Only include surat jalan that do not yet have a tanda terima
            $suratQuery->whereDoesntHave('tandaTerima');

            // Only include surat jalan that have completed pranota uang jalan payment
            $suratQuery->whereHas('uangJalans', function($uangJalanQuery) {
                $uangJalanQuery->whereHas('pranotaUangJalan', function($pranotaQuery) {
                    $pranotaQuery->whereHas('pembayaranPranotaUangJalans', function($pembayaranQuery) {
                        $pembayaranQuery->where('status_pembayaran', 'paid');
                    });
                });
            });

            $suratJalans = $suratQuery->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->except('page'));

            return view('tanda-terima.index', compact('suratJalans', 'search', 'status', 'kegiatan', 'mode', 'kegiatanList', 'allKontainers'));
        }
        
        // If mode is 'with_tanda_terima' then we should list Surat Jalan that have Tanda Terima
        if ($mode === 'with_tanda_terima') {
            // Build query to get uang jalan data
            $query = DB::table('surat_jalans as sj')
                ->join('tanda_terimas as tt', 'sj.id', '=', 'tt.surat_jalan_id')
                ->leftJoin('uang_jalans as uj', 'sj.id', '=', 'uj.surat_jalan_id')
                ->select(
                    'sj.id as surat_jalan_id',
                    'sj.no_surat_jalan',
                    'sj.tanggal_surat_jalan',
                    'sj.no_kontainer',
                    'sj.supir',
                    'sj.no_plat',
                    'sj.kegiatan',
                    'tt.id as tanda_terima_id',
                    'tt.created_at',
                    'uj.nomor_uang_jalan',
                    'uj.tanggal_uang_jalan'
                )
                ->where(function($q) {
                    $q->whereNull('sj.kegiatan')
                      ->orWhere('sj.kegiatan', '')
                      ->orWhere('sj.kegiatan', 'NOT LIKE', '%bongkar%');
                })
                ->groupBy('sj.id', 'sj.no_surat_jalan', 'sj.tanggal_surat_jalan', 'sj.no_kontainer', 'sj.supir', 'sj.no_plat', 'sj.kegiatan', 'tt.id', 'tt.created_at', 'uj.nomor_uang_jalan', 'uj.tanggal_uang_jalan');

            // Apply search filter
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('sj.no_surat_jalan', 'like', "%{$search}%")
                      ->orWhere('sj.supir', 'like', "%{$search}%")
                      ->orWhere('sj.no_kontainer', 'like', "%{$search}%");
                });
            }

            // Apply kegiatan filter
            if (!empty($kegiatan)) {
                $query->where('sj.kegiatan', $kegiatan);
            }

            $suratJalansWithTandaTerima = $query->orderBy('tt.created_at', 'desc')
                ->paginate($perPage)
                ->appends($request->except('page'));

            return view('tanda-terima.index', compact('suratJalansWithTandaTerima', 'search', 'status', 'kegiatan', 'mode', 'kegiatanList', 'allKontainers'));
        }
        // Query tanda terima with relations
        $query = TandaTerima::with(['suratJalan.order.pengirim', 'suratJalan.uangJalan']);

        // Exclude bongkaran - filter by kegiatan field
        $query->where(function($q) {
            $q->whereNull('kegiatan')
              ->orWhere('kegiatan', '')
              ->orWhere('kegiatan', 'NOT LIKE', '%bongkar%');
        });

        // Apply search filter for tanda terima
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhere('estimasi_nama_kapal', 'like', "%{$search}%")
                  ->orWhere('tujuan_pengiriman', 'like', "%{$search}%")
                  ->orWhere('jenis_barang', 'like', "%{$search}%")
                  ->orWhereHas('suratJalan.order', function($orderQuery) use ($search) {
                      $orderQuery->where('tujuan_ambil', 'like', "%{$search}%");
                  })
                  ->orWhereHas('suratJalan', function($suratJalanQuery) use ($search) {
                      $suratJalanQuery->where('no_surat_jalan', 'like', "%{$search}%")
                          ->orWhere('pengirim', 'like', "%{$search}%")
                          ->orWhere('tujuan_pengiriman', 'like', "%{$search}%");
                  });
            });
        }

        // Apply status filter
        if (!empty($status)) {
            $query->where('status', $status);
        }

        // Apply kegiatan filter
        if (!empty($kegiatan)) {
            $query->where('kegiatan', $kegiatan);
        }

        // Order by newest and paginate
        $tandaTerimas = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->except('page'));

        // If no results were found and a search term exists, try to fallback to 'missing' (surat jalan without tanda terima)
        if (!empty($search) && $tandaTerimas->total() === 0) {
            // Build missing surat jalan query similar to above
            $suratQuery = SuratJalan::with(['order.pengirim']);

            // Exclude bongkaran
            $suratQuery->where(function($q) {
                $q->whereNull('kegiatan')
                  ->orWhere('kegiatan', '')
                  ->orWhere('kegiatan', 'NOT LIKE', '%bongkar%');
            });

            // Apply search filter for surat jalan
            $suratQuery->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhereHas('order', function($orderQuery) use ($search) {
                      $orderQuery->whereHas('pengirim', function($pengirimQuery) use ($search) {
                          $pengirimQuery->where('nama_pengirim', 'like', "%{$search}%");
                      });
                  });
            });

            // Apply status filter if provided
            if (!empty($status)) {
                $suratQuery->where('status', $status);
            }

            // Apply kegiatan filter if provided
            if (!empty($kegiatan)) {
                $suratQuery->where('kegiatan', $kegiatan);
            }

            // Only include surat jalan without tanda terima
            $suratQuery->whereDoesntHave('tandaTerima');

            // Only include surat jalan that have completed pranota uang jalan payment
            $suratQuery->whereHas('uangJalans', function($uangJalanQuery) {
                $uangJalanQuery->whereHas('pranotaUangJalan', function($pranotaQuery) {
                    $pranotaQuery->whereHas('pembayaranPranotaUangJalans', function($pembayaranQuery) {
                        $pembayaranQuery->where('status_pembayaran', 'paid');
                    });
                });
            });

            $suratJalans = $suratQuery->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->except('page'));

            // Return the view with missing surat jalan results and a fallback flag
            $mode = 'missing';
            $fallback_missing = true;
            return view('tanda-terima.index', compact('suratJalans', 'search', 'status', 'kegiatan', 'mode', 'fallback_missing', 'kegiatanList', 'allKontainers'));
        }

        return view('tanda-terima.index', compact('tandaTerimas', 'search', 'status', 'kegiatan', 'mode', 'kegiatanList', 'allKontainers'));
    }

    /**
     * Show the form for creating a new tanda terima
     */
    public function create(Request $request)
    {
        $suratJalanId = $request->input('surat_jalan_id');
        
        if (!$suratJalanId) {
            return redirect()->route('tanda-terima.select-surat-jalan')
                ->with('error', 'Silakan pilih surat jalan terlebih dahulu');
        }

        $suratJalan = SuratJalan::with(['order.pengirim'])->findOrFail($suratJalanId);
        
        // Check if tanda terima already exists
        if ($suratJalan->tandaTerima) {
            return redirect()->route('tanda-terima.edit', $suratJalan->tandaTerima->id)
                ->with('info', 'Tanda terima untuk surat jalan ini sudah ada. Anda dapat mengeditnya.');
        }

        // Get master kapal for dropdown
        $masterKapals = MasterKapal::where('status', 'aktif')->orderBy('nama_kapal')->get();

        // Get all pengirims for dropdown
        $pengirims = \App\Models\Pengirim::orderBy('nama_pengirim')->get();

        // Get all terms for dropdown
        $terms = \App\Models\Term::where('status', 'active')->orderBy('nama_status')->get();

        // Get all jenis barangs for dropdown
        $jenisBarangs = \App\Models\JenisBarang::where('status', 'active')->orderBy('nama_barang')->get();

        // Get all master kegiatans for dropdown - only type kegiatan surat jalan
        $masterKegiatans = \App\Models\MasterKegiatan::where('status', 'aktif')
                                                      ->where('type', 'kegiatan surat jalan')
                                                      ->orderBy('nama_kegiatan')->get();

        // Get all karyawans for dropdown (supir) - only from divisi supir
        $karyawans = \App\Models\Karyawan::where('divisi', 'supir')->orderBy('nama_lengkap')->get();

        // Get all karyawans for dropdown (kenek/krani) - only from divisi krani
        $kranisKenek = \App\Models\Karyawan::where('divisi', 'krani')->orderBy('nama_lengkap')->get();

        // Get supir data for supir pengganti dropdown
        $karyawanSupirs = \App\Models\Karyawan::where('divisi', 'supir')->orderBy('nama_lengkap')->get();

        // Get all stock kontainers for dropdown - combine from stock_kontainers and kontainers (tersedia)
        $stockKontainersFromStock = \App\Models\StockKontainer::select('nomor_seri_gabungan', 'ukuran', 'tipe_kontainer')
            ->orderBy('nomor_seri_gabungan')
            ->get();
        
        $stockKontainersFromKontainers = \App\Models\Kontainer::select('nomor_seri_gabungan', 'ukuran', 'tipe_kontainer')
            ->where('status', 'tersedia')
            ->orderBy('nomor_seri_gabungan')
            ->get();
        
        // Merge both collections and remove duplicates based on nomor_seri_gabungan
        $stockKontainers = $stockKontainersFromStock->concat($stockKontainersFromKontainers)
            ->unique('nomor_seri_gabungan')
            ->sortBy('nomor_seri_gabungan')
            ->values();

        // Get all master tujuan kirims for dropdown
        $masterTujuanKirims = \App\Models\MasterTujuanKirim::where('status', 'active')->orderBy('nama_tujuan')->get();

        // Get all master pengirim/penerima for dropdown
        $masterPenerimaList = MasterPengirimPenerima::where('status', 'active')->orderBy('nama')->get();

        return view('tanda-terima.create', compact('suratJalan', 'masterKapals', 'pengirims', 'terms', 'jenisBarangs', 'masterKegiatans', 'karyawans', 'kranisKenek', 'karyawanSupirs', 'stockKontainers', 'masterTujuanKirims', 'masterPenerimaList'));
    }

    /**
     * Store a newly created tanda terima in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'surat_jalan_id' => 'required|exists:surat_jalans,id',
            // Field yang akan disinkronkan ke surat jalan
            'tanggal_surat_jalan' => 'nullable|date',
            'nomor_surat_jalan' => 'nullable|string|max:255',
            'rit' => 'nullable|string|max:255',
            'pengirim' => 'nullable|string|max:255',
            'term' => 'nullable|string|max:255',
            'aktifitas' => 'nullable|string|max:255',
            'jenis_barang' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:50',
            'jumlah_retur' => 'nullable|integer|min:0',
            'supir' => 'nullable|string|max:255',
            'supir_pengganti' => 'nullable|string|max:255',
            'kenek' => 'nullable|string|max:255',
            'kenek_pengganti' => 'nullable|string|max:255',
            'no_plat' => 'nullable|string|max:50',
            'kenek' => 'nullable|string|max:255',
            'krani' => 'nullable|string|max:255',
            'size' => 'nullable|array',
            'size.*' => 'nullable|string|max:50',
            'jumlah_kontainer' => 'nullable|integer|min:0',
            'karton' => 'nullable|string|max:50',
            'plastik' => 'nullable|string|max:50',
            'terpal' => 'nullable|string|max:50',
            // Field khusus tanda terima
            'estimasi_nama_kapal' => 'nullable|string|max:255',
            'nomor_ro' => 'nullable|string|max:255',
            'tanggal' => 'required|date',
            'tanggal_checkpoint_supir' => 'nullable|date',
            'tanggal_ambil_kontainer' => 'nullable|date',
            'tanggal_terima_pelabuhan' => 'nullable|date',
            'tanggal_garasi' => 'nullable|date',
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
            'tonase.*' => 'nullable|numeric|min:0',
            'tujuan_pengiriman' => 'nullable|string|max:255',
            'penerima' => 'nullable|string|max:255',
            'alamat_penerima' => 'nullable|string',
            'catatan' => 'nullable|string',
            'nomor_kontainer' => 'nullable|array',
            'nomor_kontainer.*' => 'nullable|string|max:255',
            'no_seal' => 'nullable|array',
            'no_seal.*' => 'nullable|string|max:255',
            // Validation for uploaded images
            'gambar_checkpoint' => 'nullable|array|max:5',
            'gambar_checkpoint.*' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10240', // 10MB per file
        ]);

        DB::beginTransaction();
        try {
            $suratJalan = SuratJalan::with(['order.pengirim'])->findOrFail($request->surat_jalan_id);

            // Use tipe_kontainer from existing surat jalan data only - no sync from form
            $rawTipe = $suratJalan->tipe_kontainer;
            if ($rawTipe) {
                $mapped = $this->mapTipeKontainerValue($rawTipe);
                if ($mapped === null) {
                    // Return a clear validation error instead of letting DB throw
                    DB::rollBack();
                    $message = "Tipe kontainer '{$rawTipe}' pada surat jalan tidak valid untuk Tanda Terima. Hubungi admin untuk perbaikan data surat jalan.";
                    Log::warning('Invalid tipe_kontainer for Tanda Terima', ['raw' => $rawTipe, 'surat_jalan_id' => $suratJalan->id]);
                    return redirect()->back()->withInput()->with('error', $message);
                }
                // normalize on $suratJalan for insertion and consistency
                $suratJalan->tipe_kontainer = $mapped;
            }

            // Create new tanda terima with data from surat jalan and form
            $tandaTerima = new TandaTerima();
            $tandaTerima->surat_jalan_id = $suratJalan->id;
            $tandaTerima->no_surat_jalan = $suratJalan->no_surat_jalan;
            $tandaTerima->tanggal_surat_jalan = $suratJalan->tanggal_surat_jalan;
            $tandaTerima->supir = $suratJalan->supir;
            $tandaTerima->kegiatan = $suratJalan->kegiatan;
            $tandaTerima->jenis_barang = $suratJalan->jenis_barang;
            $tandaTerima->tipe_kontainer = $suratJalan->tipe_kontainer;
            $tandaTerima->size = $suratJalan->size;
            $tandaTerima->jumlah_kontainer = $suratJalan->jumlah_kontainer;
            $tandaTerima->tujuan_pengiriman = $request->tujuan_pengiriman ?: $suratJalan->tujuan_pengiriman;
            $tandaTerima->pengirim = $suratJalan->order && $suratJalan->order->pengirim ? $suratJalan->order->pengirim->nama_pengirim : null;
            
            // Additional data from form
            $tandaTerima->estimasi_nama_kapal = $request->estimasi_nama_kapal;
            $tandaTerima->nomor_ro = $request->nomor_ro;
            $tandaTerima->tanggal = $request->tanggal;
            $tandaTerima->tanggal_checkpoint_supir = $request->tanggal_checkpoint_supir;
            $tandaTerima->supir_pengganti = $request->supir_pengganti;
            $tandaTerima->no_plat = $request->no_plat;
            $tandaTerima->tanggal_ambil_kontainer = $request->tanggal_ambil_kontainer;
            $tandaTerima->tanggal_terima_pelabuhan = $request->tanggal_terima_pelabuhan;
            $tandaTerima->tanggal_garasi = $request->tanggal_garasi;
            $tandaTerima->tujuan_pengiriman = $request->tujuan_pengiriman ?: $suratJalan->tujuan_pengiriman;
            $tandaTerima->penerima = $request->penerima;
            $tandaTerima->alamat_penerima = $request->alamat_penerima;
            $tandaTerima->catatan = $request->catatan;
            
            // Handle dimensi details (multiple dimensi entries)
            $dimensiDetails = [];
            if ($request->has('jumlah') && is_array($request->jumlah)) {
                $namaBarangArray = $request->nama_barang ?? [];
                $jumlahArray = $request->jumlah;
                $satuanArray = $request->satuan ?? [];
                $panjangArray = $request->panjang ?? [];
                $lebarArray = $request->lebar ?? [];
                $tinggiArray = $request->tinggi ?? [];
                $meterKubikArray = $request->meter_kubik ?? [];
                $tonaseArray = $request->tonase ?? [];
                
                foreach ($jumlahArray as $index => $jumlah) {
                    // Skip jika semua field kosong
                    if (empty($namaBarangArray[$index] ?? '') && empty($jumlah) && empty($satuanArray[$index] ?? '') && 
                        empty($panjangArray[$index] ?? '') && empty($lebarArray[$index] ?? '') && 
                        empty($tinggiArray[$index] ?? '') && empty($tonaseArray[$index] ?? '')) {
                        continue;
                    }
                    
                    $dimensiDetails[] = [
                        'nama_barang' => $namaBarangArray[$index] ?? null,
                        'jumlah' => $jumlah ? (int) $jumlah : null,
                        'satuan' => $satuanArray[$index] ?? null,
                        'panjang' => isset($panjangArray[$index]) && $panjangArray[$index] !== '' ? round((float) $panjangArray[$index], 3) : null,
                        'lebar' => isset($lebarArray[$index]) && $lebarArray[$index] !== '' ? round((float) $lebarArray[$index], 3) : null,
                        'tinggi' => isset($tinggiArray[$index]) && $tinggiArray[$index] !== '' ? round((float) $tinggiArray[$index], 3) : null,
                        'meter_kubik' => isset($meterKubikArray[$index]) && $meterKubikArray[$index] !== '' ? round((float) $meterKubikArray[$index], 3) : null,
                        'tonase' => isset($tonaseArray[$index]) && $tonaseArray[$index] !== '' ? round((float) $tonaseArray[$index], 3) : null,
                    ];
                }
            }
            
            // Simpan dimensi details jika ada
            if (!empty($dimensiDetails)) {
                $tandaTerima->dimensi_details = $dimensiDetails;
                
                // Simpan dimensi pertama ke field tunggal untuk backward compatibility
                $firstDimensi = $dimensiDetails[0];
                $tandaTerima->jumlah = $firstDimensi['jumlah'];
                $tandaTerima->satuan = $firstDimensi['satuan'];
                $tandaTerima->panjang = $firstDimensi['panjang'];
                $tandaTerima->lebar = $firstDimensi['lebar'];
                $tandaTerima->tinggi = $firstDimensi['tinggi'];
                $tandaTerima->meter_kubik = $firstDimensi['meter_kubik'];
                $tandaTerima->tonase = $firstDimensi['tonase'];
            } else {
                // Fallback ke single values jika tidak ada array
                $tandaTerima->jumlah = is_array($request->jumlah) ? null : $request->jumlah;
                $tandaTerima->satuan = is_array($request->satuan) ? null : $request->satuan;
                $tandaTerima->panjang = is_array($request->panjang) ? null : ($request->panjang ? round((float) $request->panjang, 3) : null);
                $tandaTerima->lebar = is_array($request->lebar) ? null : ($request->lebar ? round((float) $request->lebar, 3) : null);
                $tandaTerima->tinggi = is_array($request->tinggi) ? null : ($request->tinggi ? round((float) $request->tinggi, 3) : null);
                $tandaTerima->meter_kubik = is_array($request->meter_kubik) ? null : ($request->meter_kubik ? round((float) $request->meter_kubik, 3) : null);
                $tandaTerima->tonase = is_array($request->tonase) ? null : ($request->tonase ? round((float) $request->tonase, 3) : null);
            }
            
            // Handle multiple container numbers and details
            $kontainerDetails = [];
            if ($request->has('nomor_kontainer') && is_array($request->nomor_kontainer)) {
                $nomorKontainers = array_filter($request->nomor_kontainer, function($value) {
                    return !empty(trim($value));
                });
                
                if (!empty($nomorKontainers)) {
                    $tandaTerima->no_kontainer = implode(',', $nomorKontainers);
                    
                    // Build kontainer details - without tipe (keep separate from surat jalan)
                    $sizeArray = $request->size ?? [];
                    $noSealArray = $request->no_seal ?? [];
                    
                    foreach ($nomorKontainers as $index => $nomorKontainer) {
                        $kontainerDetails[] = [
                            'nomor_kontainer' => $nomorKontainer,
                            'size' => $sizeArray[$index] ?? null,
                            'no_seal' => $noSealArray[$index] ?? null,
                        ];
                    }
                    
                    $tandaTerima->kontainer_details = $kontainerDetails;
                } else {
                    $tandaTerima->no_kontainer = $suratJalan->no_kontainer;
                }
            } else {
                $tandaTerima->no_kontainer = $suratJalan->no_kontainer;
            }

            // Handle multiple seal numbers (untuk backward compatibility)
            if ($request->has('no_seal') && is_array($request->no_seal)) {
                $noSeals = array_filter($request->no_seal, function($value) {
                    return !empty(trim($value));
                });
                if (!empty($noSeals)) {
                    $tandaTerima->no_seal = implode(',', $noSeals);
                }
            }

            $tandaTerima->created_by = Auth::id();
            $tandaTerima->save();

            // Sync semua field yang diubah di form tanda terima ke surat jalan
            $suratJalanUpdate = [];
            
            // Nomor kontainer dan seal
            if (!empty($tandaTerima->no_kontainer)) {
                $suratJalanUpdate['no_kontainer'] = $tandaTerima->no_kontainer;
            }
            if (!empty($tandaTerima->no_seal)) {
                $suratJalanUpdate['no_seal'] = $tandaTerima->no_seal;
            }
            
            // Data dari form yang bisa diupdate
            if ($request->filled('tanggal_surat_jalan')) {
                $suratJalanUpdate['tanggal_surat_jalan'] = $request->tanggal_surat_jalan;
            }
            if ($request->filled('nomor_surat_jalan')) {
                $suratJalanUpdate['no_surat_jalan'] = $request->nomor_surat_jalan;
            }
            if ($request->filled('rit')) {
                $suratJalanUpdate['rit'] = $request->rit;
            }
            if ($request->filled('pengirim')) {
                $suratJalanUpdate['pengirim'] = $request->pengirim;
            }
            if ($request->filled('term')) {
                $suratJalanUpdate['term'] = $request->term;
            }
            if ($request->filled('aktifitas')) {
                $suratJalanUpdate['kegiatan'] = $request->aktifitas;
            }
            if ($request->filled('jenis_barang')) {
                $suratJalanUpdate['jenis_barang'] = $request->jenis_barang;
            }
            if ($request->filled('alamat')) {
                $suratJalanUpdate['alamat'] = $request->alamat;
            }
            if ($request->filled('telepon')) {
                $suratJalanUpdate['telepon'] = $request->telepon;
            }
            if ($request->filled('jumlah_retur')) {
                $suratJalanUpdate['jumlah_retur'] = $request->jumlah_retur;
            }
            
            // Data supir dan kendaraan
            if ($request->filled('supir')) {
                $suratJalanUpdate['supir'] = $request->supir;
            }
            if ($request->filled('supir_pengganti')) {
                $suratJalanUpdate['supir_pengganti'] = $request->supir_pengganti;
                
                // Update the supir field in surat jalan when supir pengganti is selected
                $karyawanSupir = \App\Models\Karyawan::where('nama_lengkap', $request->supir_pengganti)->first();
                if ($karyawanSupir) {
                    $suratJalanUpdate['supir'] = $karyawanSupir->nama_lengkap;
                }
            }
            if ($request->filled('no_plat')) {
                $suratJalanUpdate['no_plat'] = $request->no_plat;
            }
            if ($request->filled('kenek')) {
                $suratJalanUpdate['kenek'] = $request->kenek;
            }
            if ($request->filled('kenek_pengganti')) {
                $suratJalanUpdate['kenek_pengganti'] = $request->kenek_pengganti;
                
                // Update the kenek field in surat jalan when kenek pengganti is selected
                if (!empty($request->kenek_pengganti)) {
                    $karyawanKenek = \App\Models\Karyawan::where('nama_lengkap', $request->kenek_pengganti)->first();
                    if ($karyawanKenek) {
                        $suratJalanUpdate['kenek'] = $karyawanKenek->nama_lengkap;
                    }
                }
            }
            if ($request->filled('krani')) {
                $suratJalanUpdate['krani'] = $request->krani;
            }
            
            // Data kontainer - size and jumlah only, tipe_kontainer stays separate
            if ($request->filled('size')) {
                // Ambil size pertama jika ada (karena array)
                $size = is_array($request->size) ? $request->size[0] : $request->size;
                if (!empty($size)) {
                    $suratJalanUpdate['size'] = $size;
                }
            }
            if ($request->filled('jumlah_kontainer')) {
                $suratJalanUpdate['jumlah_kontainer'] = $request->jumlah_kontainer;
            }
            
            // Data packing/pengamanan
            if ($request->filled('karton')) {
                $suratJalanUpdate['karton'] = $request->karton;
            }
            if ($request->filled('plastik')) {
                $suratJalanUpdate['plastik'] = $request->plastik;
            }
            if ($request->filled('terpal')) {
                $suratJalanUpdate['terpal'] = $request->terpal;
            }
            
            // Tujuan pengiriman
            if ($request->filled('tujuan_pengiriman')) {
                $suratJalanUpdate['tujuan_pengiriman'] = $request->tujuan_pengiriman;
            }
            
            // Tanggal checkpoint supir
            if ($request->filled('tanggal_checkpoint_supir')) {
                $suratJalanUpdate['tanggal_checkpoint'] = $request->tanggal_checkpoint_supir;
            }
            
            // Handle gambar checkpoint upload
            $uploadedImages = [];
            if ($request->hasFile('gambar_checkpoint')) {
                $uploadedImages = $this->handleImageUpload($request->file('gambar_checkpoint'), 'TT_' . $tandaTerima->id);
                if (!empty($uploadedImages)) {
                    // Save images to tanda_terima as well
                    $tandaTerima->update(['gambar_checkpoint' => json_encode($uploadedImages)]);
                    
                    // Get existing images from surat jalan
                    $existingImages = [];
                    if (!empty($suratJalan->gambar_checkpoint)) {
                        if (is_string($suratJalan->gambar_checkpoint)) {
                            $decoded = json_decode($suratJalan->gambar_checkpoint, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $existingImages = array_filter($decoded);
                            } else {
                                $existingImages = [$suratJalan->gambar_checkpoint];
                            }
                        } elseif (is_array($suratJalan->gambar_checkpoint)) {
                            $existingImages = array_filter($suratJalan->gambar_checkpoint);
                        }
                    }
                    
                    // Merge existing and new images
                    $allImages = array_merge($existingImages, $uploadedImages);
                    
                    // Limit to 10 total images
                    if (count($allImages) > 10) {
                        $allImages = array_slice($allImages, 0, 10);
                    }
                    
                    $suratJalanUpdate['gambar_checkpoint'] = json_encode($allImages);
                    
                    Log::info('Gambar checkpoint uploaded', [
                        'tanda_terima_id' => $tandaTerima->id,
                        'surat_jalan_id' => $suratJalan->id,
                        'new_images_count' => count($uploadedImages),
                        'total_images_count' => count($allImages),
                        'uploaded_by' => Auth::user() ? Auth::user()->name : null,
                    ]);
                }
            }
            
            // Update surat jalan jika ada perubahan
            if (!empty($suratJalanUpdate)) {
                $suratJalan->update($suratJalanUpdate);
            }

            // Update status_surat_jalan menjadi selesai karena sudah ada tanda terima
            $suratJalan->update([
                'status_surat_jalan' => 'selesai',
                'status' => 'sudah_checkpoint'
            ]);

            // Update related Prospek data for newly created TandaTerima
            $updatedProspekCount = $this->updateRelatedProspekData($tandaTerima, $request);

            Log::info('Tanda terima created', [
                'tanda_terima_id' => $tandaTerima->id,
                'no_surat_jalan' => $tandaTerima->no_surat_jalan,
                'created_by' => Auth::user() ? Auth::user()->name : null,
                'prospeks_updated' => $updatedProspekCount,
            ]);

            DB::commit();

            $successMessage = 'Tanda terima berhasil dibuat dan data telah disinkronkan ke surat jalan';
            if ($updatedProspekCount > 0) {
                $successMessage .= " Berhasil mengupdate {$updatedProspekCount} prospek terkait dengan data volume, tonase, dan kuantitas terbaru.";
            }
            
            // Add message about uploaded images
            if (!empty($uploadedImages)) {
                $imageCount = count($uploadedImages);
                $successMessage .= " Berhasil mengupload {$imageCount} gambar checkpoint baru.";
            }

            return redirect()->route('tanda-terima.show', $tandaTerima->id)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating tanda terima: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat tanda terima: ' . $e->getMessage());
        }
    }

    /**
     * Create or find tanda terima from surat jalan (for backward compatibility)
     */
    public function createFromSuratJalan(Request $request, SuratJalan $suratJalan)
    {
        // Redirect to create page with surat jalan id
        return redirect()->route('tanda-terima.create', ['surat_jalan_id' => $suratJalan->id]);
    }

    /**
     * Show the form for editing the specified tanda terima
     */
    public function edit(TandaTerima $tandaTerima)
    {
        // Load relations
        $tandaTerima->load('suratJalan', 'prospeks.bls');

        // Check if tanda terima sudah masuk BL
        $sudahMasukBl = $tandaTerima->sudahMasukBl();

        // Get master kapal for dropdown
        $masterKapals = MasterKapal::where('status', 'aktif')->orderBy('nama_kapal')->get();

        // Get all pengirims for dropdown
        $pengirims = \App\Models\Pengirim::orderBy('nama_pengirim')->get();

        // Get stock kontainers for dropdown - combine from stock_kontainers and kontainers
        $stockKontainersFromStock = \App\Models\StockKontainer::select('nomor_seri_gabungan', 'ukuran', 'tipe_kontainer')
            ->orderBy('nomor_seri_gabungan')
            ->get();
        
        $stockKontainersFromKontainers = \App\Models\Kontainer::select('nomor_seri_gabungan', 'ukuran', 'tipe_kontainer')
            ->where('status', 'tersedia')
            ->orderBy('nomor_seri_gabungan')
            ->get();
        
        // Merge both collections and remove duplicates
        $stockKontainers = $stockKontainersFromStock->concat($stockKontainersFromKontainers)
            ->unique('nomor_seri_gabungan')
            ->sortBy('nomor_seri_gabungan')
            ->values();

        // Get supirs for dropdown - from karyawan with divisi supir
        $supirs = \App\Models\Karyawan::where('divisi', 'supir')
            ->orderBy('nama_lengkap')
            ->get();

        // Get keneks for dropdown - from karyawan with divisi kenek
        $keneks = \App\Models\Karyawan::where('divisi', 'kenek')
            ->orderBy('nama_lengkap')
            ->get();

        // Get kranis for dropdown - from karyawan with divisi krani
        $kranis = \App\Models\Karyawan::where('divisi', 'krani')
            ->orderBy('nama_lengkap')
            ->get();

        // Get master kegiatans
        $masterKegiatans = \App\Models\MasterKegiatan::orderBy('nama_kegiatan')->get();

        // Get all karyawans for dropdown (supir) - only from divisi supir
        $karyawans = \App\Models\Karyawan::where('divisi', 'supir')->orderBy('nama_lengkap')->get();

        // Get all master tujuan kirims for dropdown
        $masterTujuanKirims = \App\Models\MasterTujuanKirim::where('status', 'active')->orderBy('nama_tujuan')->get();
        $tujuanKirims = $masterTujuanKirims; // Alias for view compatibility

        // Get all jenis barangs for dropdown
        $jenisBarangs = \App\Models\JenisBarang::where('status', 'active')->orderBy('nama_barang')->get();

        // Get kranes for dropdown (krani divisi) - alias for kenek pengganti
        $kranes = \App\Models\Karyawan::where('divisi', 'krani')->orderBy('nama_lengkap')->get();

        // Get master penerima list for dropdown
        $masterPenerimaList = \App\Models\MasterPengirimPenerima::where('status', 'active')->orderBy('nama')->get();

        return view('tanda-terima.edit', compact('tandaTerima', 'masterKapals', 'pengirims', 'stockKontainers', 'supirs', 'keneks', 'kranis', 'masterKegiatans', 'karyawans', 'masterTujuanKirims', 'tujuanKirims', 'jenisBarangs', 'kranes', 'sudahMasukBl', 'masterPenerimaList'));
    }

    /**
     * Update the specified tanda terima in storage
     */
    public function update(Request $request, TandaTerima $tandaTerima)
    {
        // Check if tanda terima sudah masuk BL
        $sudahMasukBl = $tandaTerima->sudahMasukBl();

        // Jika sudah masuk BL, cek apakah ada perubahan pada field yang dilindungi
        if ($sudahMasukBl) {
            $protectedFields = [];
            
            // Check nomor kontainer
            if ($request->has('nomor_kontainer') && is_array($request->nomor_kontainer)) {
                $newNoKontainer = implode(',', array_filter($request->nomor_kontainer));
                if ($newNoKontainer !== $tandaTerima->no_kontainer) {
                    $protectedFields[] = 'Nomor Kontainer';
                }
            }
            
            // Check nomor seal
            if ($request->has('no_seal') && is_array($request->no_seal)) {
                $newNoSeal = implode(',', array_filter($request->no_seal));
                if ($newNoSeal !== $tandaTerima->no_seal) {
                    $protectedFields[] = 'Nomor Seal';
                }
            }
            
            // Check supir pengganti
            if ($request->has('supir_pengganti') && $request->supir_pengganti !== $tandaTerima->supir_pengganti) {
                $protectedFields[] = 'Supir Pengganti';
            }
            
            if (!empty($protectedFields)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tanda terima sudah masuk BL. Field berikut tidak dapat diubah: ' . implode(', ', $protectedFields));
            }
        }

        $request->validate([
            'estimasi_nama_kapal' => 'nullable|string|max:255',
            'tanggal_ambil_kontainer' => 'nullable|date',
            'tanggal_terima_pelabuhan' => 'nullable|date',
            'tanggal_garasi' => 'nullable|date',
            'tanggal_checkpoint_supir' => 'nullable|date',
            'jumlah' => 'nullable|integer|min:0',
            'satuan' => 'nullable|string|max:50',
            'panjang' => 'nullable|numeric|min:0',
            'lebar' => 'nullable|numeric|min:0',
            'tinggi' => 'nullable|numeric|min:0',
            'meter_kubik' => 'nullable|numeric|min:0',
            'tonase' => 'nullable|numeric|min:0',
            'tujuan_pengiriman' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'status' => 'nullable|in:draft,submitted,approved,completed,cancelled',
            'supir_pengganti' => 'nullable|string|max:255',
            'kenek_pengganti' => 'nullable|string|max:255',
            'nomor_kontainer' => 'nullable|array',
            'nomor_kontainer.*' => 'nullable|string|max:255',
            'no_seal' => 'nullable|array',
            'no_seal.*' => 'nullable|string|max:255',
            'jumlah_kontainer' => 'nullable|array',
            'jumlah_kontainer.*' => 'nullable|integer|min:0',
            'satuan_kontainer' => 'nullable|array',
            'satuan_kontainer.*' => 'nullable|string|max:50',
            'panjang_kontainer' => 'nullable|array',
            'panjang_kontainer.*' => 'nullable|numeric|min:0',
            'lebar_kontainer' => 'nullable|array',
            'lebar_kontainer.*' => 'nullable|numeric|min:0',
            'tinggi_kontainer' => 'nullable|array',
            'tinggi_kontainer.*' => 'nullable|numeric|min:0',
            'meter_kubik_kontainer' => 'nullable|array',
            'meter_kubik_kontainer.*' => 'nullable|numeric|min:0',
            'tonase_kontainer' => 'nullable|array',
            'tonase_kontainer.*' => 'nullable|numeric|min:0',
            'dimensi_items' => 'nullable|array',
            'dimensi_items.*.panjang' => 'nullable|numeric|min:0',
            'dimensi_items.*.lebar' => 'nullable|numeric|min:0',
            'dimensi_items.*.tinggi' => 'nullable|numeric|min:0',
            'dimensi_items.*.meter_kubik' => 'nullable|numeric|min:0',
            'dimensi_items.*.tonase' => 'nullable|numeric|min:0',
            // Support untuk array fields dari edit form
            'nama_barang' => 'nullable|array',
            'nama_barang.*' => 'nullable|string|max:255',
            'jumlah' => 'nullable',
            'satuan' => 'nullable',
            'panjang' => 'nullable',
            'lebar' => 'nullable',
            'tinggi' => 'nullable',
            'meter_kubik' => 'nullable',
            'tonase' => 'nullable',
            'gambar_checkpoint' => 'nullable|array|max:5',
            'gambar_checkpoint.*' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10240', // 10MB per file
        ]);

        DB::beginTransaction();
        try {
            // Handle dimensi details from arrays (nama_barang[], jumlah[], etc.)
            $dimensiDetails = [];
            if ($request->has('jumlah') && is_array($request->jumlah)) {
                $namaBarangArray = $request->nama_barang ?? [];
                $jumlahArray = $request->jumlah;
                $satuanArray = $request->satuan ?? [];
                $panjangArray = $request->panjang ?? [];
                $lebarArray = $request->lebar ?? [];
                $tinggiArray = $request->tinggi ?? [];
                $meterKubikArray = $request->meter_kubik ?? [];
                $tonaseArray = $request->tonase ?? [];
                
                foreach ($jumlahArray as $index => $jumlah) {
                    // Skip jika semua field kosong
                    if (empty($namaBarangArray[$index] ?? '') && empty($jumlah) && empty($satuanArray[$index] ?? '') && 
                        empty($panjangArray[$index] ?? '') && empty($lebarArray[$index] ?? '') && 
                        empty($tinggiArray[$index] ?? '') && empty($tonaseArray[$index] ?? '')) {
                        continue;
                    }
                    
                    $dimensiDetails[] = [
                        'nama_barang' => $namaBarangArray[$index] ?? null,
                        'jumlah' => $jumlah ? (int) $jumlah : null,
                        'satuan' => $satuanArray[$index] ?? null,
                        'panjang' => isset($panjangArray[$index]) && $panjangArray[$index] !== '' ? round((float) $panjangArray[$index], 3) : null,
                        'lebar' => isset($lebarArray[$index]) && $lebarArray[$index] !== '' ? round((float) $lebarArray[$index], 3) : null,
                        'tinggi' => isset($tinggiArray[$index]) && $tinggiArray[$index] !== '' ? round((float) $tinggiArray[$index], 3) : null,
                        'meter_kubik' => isset($meterKubikArray[$index]) && $meterKubikArray[$index] !== '' ? round((float) $meterKubikArray[$index], 3) : null,
                        'tonase' => isset($tonaseArray[$index]) && $tonaseArray[$index] !== '' ? round((float) $tonaseArray[$index], 3) : null,
                    ];
                }
            }
            
            $updateData = [
                'estimasi_nama_kapal' => $request->estimasi_nama_kapal,
                'tanggal_ambil_kontainer' => $request->tanggal_ambil_kontainer,
                'tanggal_terima_pelabuhan' => $request->tanggal_terima_pelabuhan,
                'tanggal_garasi' => $request->tanggal_garasi,
                'tanggal_checkpoint_supir' => $request->tanggal_checkpoint_supir,
                'jumlah' => is_array($request->jumlah) ? null : $request->jumlah,
                'satuan' => is_array($request->satuan) ? null : $request->satuan,
                // Format numeric fields to avoid excessive decimals
                'panjang' => $request->panjang ? round((float) $request->panjang, 3) : null,
                'lebar' => $request->lebar ? round((float) $request->lebar, 3) : null,
                'tinggi' => $request->tinggi ? round((float) $request->tinggi, 3) : null,
                'meter_kubik' => $request->meter_kubik ? round((float) $request->meter_kubik, 3) : null,
                'tonase' => $request->tonase ? round((float) $request->tonase, 3) : null,
                'tujuan_pengiriman' => $request->tujuan_pengiriman,
                'catatan' => $request->catatan,
                'supir_pengganti' => $request->supir_pengganti,
                'kenek_pengganti' => $request->kenek_pengganti,
                'updated_by' => Auth::id(),
            ];

            // Handle multiple container numbers
            if ($request->has('nomor_kontainer') && is_array($request->nomor_kontainer)) {
                $nomorKontainers = array_filter($request->nomor_kontainer, function($value) {
                    return !empty(trim($value));
                });
                if (!empty($nomorKontainers)) {
                    $updateData['no_kontainer'] = implode(',', $nomorKontainers);
                }
            }

            // Handle multiple seal numbers
            if ($request->has('no_seal') && is_array($request->no_seal)) {
                $noSeals = array_filter($request->no_seal, function($value) {
                    return !empty(trim($value));
                });
                if (!empty($noSeals)) {
                    $updateData['no_seal'] = implode(',', $noSeals);
                }
            }

            // Handle multiple jumlah per kontainer
            if ($request->has('jumlah_kontainer') && is_array($request->jumlah_kontainer)) {
                $jumlahKontainers = array_filter($request->jumlah_kontainer, function($value) {
                    return !empty(trim($value)) && is_numeric($value);
                });
                if (!empty($jumlahKontainers)) {
                    $updateData['jumlah'] = implode(',', $jumlahKontainers);
                }
            }

            // Handle multiple satuan per kontainer
            if ($request->has('satuan_kontainer') && is_array($request->satuan_kontainer)) {
                $satuanKontainers = array_filter($request->satuan_kontainer, function($value) {
                    return !empty(trim($value));
                });
                if (!empty($satuanKontainers)) {
                    $updateData['satuan'] = implode(',', $satuanKontainers);
                }
            }

            // Handle multiple panjang per kontainer
            if ($request->has('panjang_kontainer') && is_array($request->panjang_kontainer)) {
                $panjangKontainers = array_filter($request->panjang_kontainer, function($value) {
                    return !empty(trim($value)) && is_numeric($value);
                });
                if (!empty($panjangKontainers)) {
                    $updateData['panjang'] = implode(',', $panjangKontainers);
                }
            }

            // Handle multiple lebar per kontainer
            if ($request->has('lebar_kontainer') && is_array($request->lebar_kontainer)) {
                $lebarKontainers = array_filter($request->lebar_kontainer, function($value) {
                    return !empty(trim($value)) && is_numeric($value);
                });
                if (!empty($lebarKontainers)) {
                    $updateData['lebar'] = implode(',', $lebarKontainers);
                }
            }

            // Handle multiple tinggi per kontainer
            if ($request->has('tinggi_kontainer') && is_array($request->tinggi_kontainer)) {
                $tinggiKontainers = array_filter($request->tinggi_kontainer, function($value) {
                    return !empty(trim($value)) && is_numeric($value);
                });
                if (!empty($tinggiKontainers)) {
                    $updateData['tinggi'] = implode(',', $tinggiKontainers);
                }
            }

            // Handle multiple meter_kubik per kontainer
            if ($request->has('meter_kubik_kontainer') && is_array($request->meter_kubik_kontainer)) {
                $meterKubikKontainers = array_filter($request->meter_kubik_kontainer, function($value) {
                    return !empty(trim($value)) && is_numeric($value);
                });
                if (!empty($meterKubikKontainers)) {
                    $updateData['meter_kubik'] = implode(',', $meterKubikKontainers);
                }
            }

            // Handle multiple tonase per kontainer
            if ($request->has('tonase_kontainer') && is_array($request->tonase_kontainer)) {
                $tonaseKontainers = array_filter($request->tonase_kontainer, function($value) {
                    return !empty(trim($value)) && is_numeric($value);
                });
                if (!empty($tonaseKontainers)) {
                    $updateData['tonase'] = implode(',', $tonaseKontainers);
                }
            }

            // Only include status if the column exists and request has status
            if ($request->has('status') && Schema::hasColumn('tanda_terimas', 'status')) {
                $updateData['status'] = $request->status;
            }

            // Save dimensi_details if we have them from arrays
            if (!empty($dimensiDetails)) {
                $updateData['dimensi_details'] = $dimensiDetails;
                
                // Also save first dimensi to single fields for backward compatibility
                $firstDimensi = $dimensiDetails[0];
                $updateData['jumlah'] = $firstDimensi['jumlah'];
                $updateData['satuan'] = $firstDimensi['satuan'];
                $updateData['panjang'] = $firstDimensi['panjang'];
                $updateData['lebar'] = $firstDimensi['lebar'];
                $updateData['tinggi'] = $firstDimensi['tinggi'];
                $updateData['meter_kubik'] = $firstDimensi['meter_kubik'];
                $updateData['tonase'] = $firstDimensi['tonase'];
            }
            
            // If dimensi_items is present, format numeric values and store as JSON
            if ($request->has('dimensi_items') && is_array($request->dimensi_items)) {
                $formattedDimensiItems = [];
                foreach ($request->dimensi_items as $item) {
                    $formattedItem = [];
                    foreach ($item as $key => $value) {
                        if (in_array($key, ['panjang', 'lebar', 'tinggi', 'meter_kubik', 'tonase']) && is_numeric($value)) {
                            // Round to 3 decimal places to avoid excessive precision
                            $formattedItem[$key] = round((float) $value, 3);
                        } else {
                            $formattedItem[$key] = $value;
                        }
                    }
                    $formattedDimensiItems[] = $formattedItem;
                }
                $updateData['dimensi_items'] = json_encode($formattedDimensiItems);
            }

            // Handle image upload if present
            if ($request->hasFile('gambar_checkpoint')) {
                $uploadedImages = $this->handleImageUpload($request->file('gambar_checkpoint'), 'TT_' . $tandaTerima->id);
                
                // Update related Surat Jalan with checkpoint images if tanda terima is linked
                if ($tandaTerima->surat_jalan_id) {
                    $suratJalan = \App\Models\SuratJalan::find($tandaTerima->surat_jalan_id);
                    if ($suratJalan) {
                        // Merge existing images with new ones
                        $existingImages = [];
                        if (!empty($suratJalan->gambar_checkpoint)) {
                            if (is_string($suratJalan->gambar_checkpoint)) {
                                $decoded = json_decode($suratJalan->gambar_checkpoint, true);
                                if (is_array($decoded)) {
                                    $existingImages = array_filter($decoded);
                                } else {
                                    $existingImages = [$suratJalan->gambar_checkpoint];
                                }
                            } elseif (is_array($suratJalan->gambar_checkpoint)) {
                                $existingImages = array_filter($suratJalan->gambar_checkpoint);
                            }
                        }
                        
                        // Combine existing and new images
                        $allImages = array_merge($existingImages, $uploadedImages);
                        
                        // Limit to 5 images total
                        if (count($allImages) > 5) {
                            $allImages = array_slice($allImages, -5); // Keep the latest 5 images
                        }
                        
                        $suratJalanUpdate['gambar_checkpoint'] = json_encode($allImages);
                        $suratJalan->update($suratJalanUpdate);
                    }
                }
            }

            $tandaTerima->update($updateData);

            // Sync nomor kontainer dan seal kembali ke Surat Jalan terkait
            if ($tandaTerima->surat_jalan_id) {
                $suratJalan = \App\Models\SuratJalan::find($tandaTerima->surat_jalan_id);
                if ($suratJalan) {
                    $suratJalanUpdateData = [];
                    
                    // ALWAYS update nomor kontainer dan seal ke surat jalan (force sync)
                    if (isset($updateData['no_kontainer'])) {
                        $suratJalanUpdateData['no_kontainer'] = $updateData['no_kontainer'];
                    }
                    
                    if (isset($updateData['no_seal'])) {
                        $suratJalanUpdateData['no_seal'] = $updateData['no_seal'];
                    }
                    
                    // Update tanggal checkpoint jika ada perubahan
                    if (isset($updateData['tanggal_checkpoint_supir']) && $updateData['tanggal_checkpoint_supir'] != $suratJalan->tanggal_checkpoint) {
                        $suratJalanUpdateData['tanggal_checkpoint'] = $updateData['tanggal_checkpoint_supir'];
                    }
                    
                    // Update supir pengganti jika ada perubahan
                    if (isset($updateData['supir_pengganti']) && $updateData['supir_pengganti'] != $suratJalan->supir_pengganti) {
                        $suratJalanUpdateData['supir_pengganti'] = $updateData['supir_pengganti'];
                        
                        // Update the supir field in surat jalan when supir pengganti is changed
                        if (!empty($updateData['supir_pengganti'])) {
                            $karyawanSupir = \App\Models\Karyawan::where('nama_lengkap', $updateData['supir_pengganti'])->first();
                            if ($karyawanSupir) {
                                $suratJalanUpdateData['supir'] = $karyawanSupir->nama_lengkap;
                            }
                        }
                    }
                    
                    // Update kenek pengganti jika ada perubahan
                    if (isset($updateData['kenek_pengganti']) && $updateData['kenek_pengganti'] != $suratJalan->kenek_pengganti) {
                        $suratJalanUpdateData['kenek_pengganti'] = $updateData['kenek_pengganti'];
                        
                        // Update the kenek field in surat jalan when kenek pengganti is changed
                        if (!empty($updateData['kenek_pengganti'])) {
                            $karyawanKenek = \App\Models\Karyawan::where('nama_lengkap', $updateData['kenek_pengganti'])->first();
                            if ($karyawanKenek) {
                                $suratJalanUpdateData['kenek'] = $karyawanKenek->nama_lengkap;
                            }
                        }
                    }
                    
                    // Update krani pengganti jika ada perubahan
                    if (isset($updateData['krani_pengganti']) && $updateData['krani_pengganti'] != $suratJalan->krani_pengganti) {
                        $suratJalanUpdateData['krani_pengganti'] = $updateData['krani_pengganti'];
                        
                        // Update the krani field in surat jalan when krani pengganti is changed
                        if (!empty($updateData['krani_pengganti'])) {
                            $karyawanKrani = \App\Models\Karyawan::where('nama_lengkap', $updateData['krani_pengganti'])->first();
                            if ($karyawanKrani) {
                                $suratJalanUpdateData['krani'] = $karyawanKrani->nama_lengkap;
                            }
                        }
                    }
                    
                    // Update status menjadi sudah_checkpoint karena sudah ada tanda terima
                    $suratJalanUpdateData['status'] = 'sudah_checkpoint';
                    
                    // Lakukan update jika ada perubahan
                    if (!empty($suratJalanUpdateData)) {
                        $suratJalan->update($suratJalanUpdateData);
                        
                        Log::info('Surat Jalan synced from Tanda Terima', [
                            'surat_jalan_id' => $suratJalan->id,
                            'no_surat_jalan' => $suratJalan->no_surat_jalan,
                            'updated_fields' => array_keys($suratJalanUpdateData),
                            'no_kontainer' => $suratJalanUpdateData['no_kontainer'] ?? null,
                            'no_seal' => $suratJalanUpdateData['no_seal'] ?? null,
                            'tanggal_checkpoint' => $suratJalanUpdateData['tanggal_checkpoint'] ?? null,
                        ]);
                    }
                }
            }

            // Update related Prospek data and get count of updated prospeks
            $updatedProspekCount = $this->updateRelatedProspekData($tandaTerima, $request);

            Log::info('Tanda terima updated', [
                'tanda_terima_id' => $tandaTerima->id,
                'no_surat_jalan' => $tandaTerima->no_surat_jalan,
                'updated_by' => Auth::user()->name,
                'prospeks_updated' => $updatedProspekCount,
            ]);

            DB::commit();
            
            // Create success message with prospek update info
            $successMessage = 'Tanda terima berhasil diperbarui!';
            if ($updatedProspekCount > 0) {
                $successMessage .= " Berhasil mengupdate {$updatedProspekCount} prospek terkait dengan data volume, tonase, dan kuantitas terbaru.";
            }
            
            return redirect()->route('tanda-terima.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating tanda terima: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui tanda terima: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified tanda terima
     */
    public function show(TandaTerima $tandaTerima)
    {
        $tandaTerima->load(['suratJalan', 'creator', 'updater']);

        return view('tanda-terima.show', compact('tandaTerima'));
    }

    /**
     * Remove the specified tanda terima from storage
     */
    public function destroy(TandaTerima $tandaTerima)
    {
        DB::beginTransaction();
        try {
            $noSuratJalan = $tandaTerima->no_surat_jalan;
            
            // Hapus prospek terkait
            $deletedProspekCount = \App\Models\Prospek::where('tanda_terima_id', $tandaTerima->id)->delete();
            
            $tandaTerima->delete();

            Log::info('Tanda terima deleted', [
                'tanda_terima_id' => $tandaTerima->id,
                'no_surat_jalan' => $noSuratJalan,
                'deleted_prospek_count' => $deletedProspekCount,
                'deleted_by' => Auth::user()->name,
            ]);

            DB::commit();
            
            $successMessage = 'Tanda terima berhasil dihapus!';
            if ($deletedProspekCount > 0) {
                $successMessage .= " ({$deletedProspekCount} prospek terkait juga dihapus)";
            }
            
            return redirect()->route('tanda-terima.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting tanda terima: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus tanda terima: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete tanda terimas
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'tanda_terima_ids' => 'required|json',
        ]);

        DB::beginTransaction();
        try {
            $tandaTerimaIds = json_decode($request->tanda_terima_ids, true);
            
            if (empty($tandaTerimaIds) || !is_array($tandaTerimaIds)) {
                throw new \Exception('Invalid tanda terima IDs');
            }

            $tandaTerimas = TandaTerima::whereIn('id', $tandaTerimaIds)->get();
            $deletedCount = 0;
            $deletedProspekCount = 0;
            $noSuratJalans = [];

            foreach ($tandaTerimas as $tandaTerima) {
                $noSuratJalans[] = $tandaTerima->no_surat_jalan;
                
                // Hapus prospek terkait
                $deletedProspekCount += \App\Models\Prospek::where('tanda_terima_id', $tandaTerima->id)->delete();
                
                $tandaTerima->delete();
                $deletedCount++;
            }

            Log::info('Bulk delete tanda terimas', [
                'count' => $deletedCount,
                'deleted_prospek_count' => $deletedProspekCount,
                'no_surat_jalans' => implode(', ', $noSuratJalans),
                'deleted_by' => Auth::user()->name,
            ]);

            DB::commit();
            
            $successMessage = "Berhasil menghapus {$deletedCount} tanda terima!";
            if ($deletedProspekCount > 0) {
                $successMessage .= " ({$deletedProspekCount} prospek terkait juga dihapus)";
            }
            
            return redirect()->route('tanda-terima.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk deleting tanda terimas: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus tanda terima: ' . $e->getMessage());
        }
    }

    /**
     * Export selected tanda terimas to Excel
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'tanda_terima_ids' => 'required|json',
        ]);

        try {
            $tandaTerimaIds = json_decode($request->tanda_terima_ids, true);
            
            if (empty($tandaTerimaIds) || !is_array($tandaTerimaIds)) {
                throw new \Exception('Invalid tanda terima IDs');
            }

            $fileName = 'tanda_terima_export_' . date('Ymd_His') . '.xlsx';

            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\TandaTerimaExport($tandaTerimaIds),
                $fileName
            );

        } catch (\Exception $e) {
            Log::error('Error exporting tanda terima to Excel: ' . $e->getMessage());
            return back()->with('error', 'Gagal export tanda terima: ' . $e->getMessage());
        }
    }

    /**
     * Export tanda terima (or surat jalan missing) based on current filters (GET)
     */
    public function exportFiltered(Request $request)
    {
        try {
            $filters = $request->only(['search', 'mode', 'status', 'kegiatan']);
            $fileName = 'tanda_terima_export_' . date('Ymd_His') . '.xlsx';

            // If mode is missing, we will export surat jalans without tanda terima
            $export = new TandaTerimaFilteredExport($filters);
            return Excel::download($export, $fileName);
        } catch (\Exception $e) {
            Log::error('Error exporting filtered tanda terima: ' . $e->getMessage());
            return back()->with('error', 'Gagal export tanda terima: ' . $e->getMessage());
        }
    }

    /**
     * Add container to prospek
     */
    public function addToProspek(TandaTerima $tandaTerima)
    {
        try {
            // Tentukan ukuran kontainer yang valid (hanya 20 atau 40)
            $ukuran = null;
            if ($tandaTerima->size) {
                // Jika size adalah '20' atau '40', gunakan langsung
                if (in_array($tandaTerima->size, ['20', '40'])) {
                    $ukuran = $tandaTerima->size;
                }
            }

            // Buat data prospek dari tanda terima
            $prospekData = [
                'tanggal' => $tandaTerima->tanggal_surat_jalan,
                'nama_supir' => $tandaTerima->supir ?: 'Tidak ada supir',
                'barang' => $tandaTerima->jenis_barang ?: 'CARGO',
                'pt_pengirim' => $tandaTerima->pengirim ?: 'Tidak ada pengirim',
                'ukuran' => $ukuran, // Hanya '20', '40', atau null
                'tipe' => 'CARGO', // Set tipe sebagai CARGO untuk kontainer cargo
                'nomor_kontainer' => $tandaTerima->no_kontainer,
                'no_seal' => $tandaTerima->no_seal ?: 'Tidak ada seal',
                'tujuan_pengiriman' => $tandaTerima->tujuan_pengiriman ?: 'Tidak ada tujuan',
                'nama_kapal' => $tandaTerima->estimasi_nama_kapal ?: 'Tidak ada nama kapal',
                'keterangan' => "Data dari tanda terima: {$tandaTerima->no_surat_jalan}. Kegiatan: {$tandaTerima->kegiatan}",
                'status' => 'aktif',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            $createdProspek = Prospek::create($prospekData);

            return back()->with('success', "Kontainer CARGO dari surat jalan {$tandaTerima->no_surat_jalan} berhasil dimasukkan ke prospek (ID: {$createdProspek->id})!");

        } catch (\Exception $e) {
            Log::error('Error adding cargo to prospek: ' . $e->getMessage());
            Log::error('TandaTerima data: ' . json_encode($tandaTerima->toArray()));
            return back()->with('error', 'Gagal memasukkan kontainer ke prospek: ' . $e->getMessage());
        }
    }

    /**
     * Update related Prospek data when TandaTerima is updated
     */
    private function updateRelatedProspekData(TandaTerima $tandaTerima, Request $request)
    {
        try {
            // Calculate totals from dimensi items or fallback to single values
            $totalVolume = 0;
            $totalTonase = 0;
            $kuantitas = 0;

            // Priority 1: Calculate from dimensi_items if available
            if ($request->has('dimensi_items') && is_array($request->dimensi_items)) {
                foreach ($request->dimensi_items as $item) {
                    if (isset($item['meter_kubik']) && is_numeric($item['meter_kubik'])) {
                        // Round to 3 decimal places to avoid excessive precision
                        $volume = round((float) $item['meter_kubik'], 3);
                        $totalVolume += $volume;
                    }
                    if (isset($item['tonase']) && is_numeric($item['tonase'])) {
                        // Round to 3 decimal places to avoid excessive precision
                        $tonase = round((float) $item['tonase'], 3);
                        $totalTonase += $tonase;
                    }
                }
            }

            // Priority 2: Use arrays meter_kubik[] and tonase[] if provided
            if ($totalVolume == 0 && $request->has('meter_kubik') && is_array($request->meter_kubik)) {
                foreach ($request->meter_kubik as $mv) {
                    if (is_numeric($mv)) {
                        $totalVolume += round((float) $mv, 3);
                    }
                }
            }
            // Fallback to single meter_kubik scalar (if provided)
            if ($totalVolume == 0 && $request->filled('meter_kubik') && !is_array($request->meter_kubik)) {
                $totalVolume = round((float) $request->meter_kubik, 3);
            }

            if ($totalTonase == 0 && $request->has('tonase') && is_array($request->tonase)) {
                foreach ($request->tonase as $t) {
                    if (is_numeric($t)) {
                        $totalTonase += round((float) $t, 3);
                    }
                }
            }
            // Fallback to single tonase scalar (if provided)
            if ($totalTonase == 0 && $request->filled('tonase') && !is_array($request->tonase)) {
                $totalTonase = round((float) $request->tonase, 3);
            }

            // Calculate kuantitas from jumlah_kontainer or jumlah[] arrays, or single jumlah
            if ($request->has('jumlah_kontainer') && is_array($request->jumlah_kontainer)) {
                foreach ($request->jumlah_kontainer as $jumlah) {
                    if (is_numeric($jumlah)) {
                        $kuantitas += (int) $jumlah;
                    }
                }
            } elseif ($request->has('jumlah') && is_array($request->jumlah)) {
                foreach ($request->jumlah as $jumlah) {
                    if (is_numeric($jumlah)) {
                        $kuantitas += (int) $jumlah;
                    }
                }
            } elseif ($request->filled('jumlah')) {
                // Handle comma-separated values in jumlah field
                $jumlahArray = explode(',', $request->jumlah);
                foreach ($jumlahArray as $jumlah) {
                    if (is_numeric(trim($jumlah))) {
                        $kuantitas += (int) trim($jumlah);
                    }
                }
            }

            // Find related prospek records using multiple methods
            $prospeksToUpdate = collect();
            
            // Method 1: Find by surat_jalan_id (most reliable)
            if ($tandaTerima->surat_jalan_id) {
                $prospeksBySuratJalan = \App\Models\Prospek::where('surat_jalan_id', $tandaTerima->surat_jalan_id)->get();
                $prospeksToUpdate = $prospeksToUpdate->merge($prospeksBySuratJalan);
            }
            
            // Method 2: Find by no_surat_jalan if surat_jalan_id didn't yield results
            if ($prospeksToUpdate->isEmpty() && $tandaTerima->no_surat_jalan) {
                $prospeksByNoSuratJalan = \App\Models\Prospek::where('no_surat_jalan', $tandaTerima->no_surat_jalan)->get();
                $prospeksToUpdate = $prospeksToUpdate->merge($prospeksByNoSuratJalan);
            }
            
            // Method 3: Find by nomor_kontainer (fallback)
            if ($request->has('nomor_kontainer') && is_array($request->nomor_kontainer)) {
                $nomorKontainers = array_filter($request->nomor_kontainer, function($value) {
                    return !empty(trim($value));
                });
                
                if (!empty($nomorKontainers)) {
                    // Match nomor_kontainer that equals or contains the given container values (CSV matching)
                    $prospeksByKontainer = \App\Models\Prospek::where(function($q) use ($nomorKontainers) {
                        foreach ($nomorKontainers as $containerValue) {
                            $v = trim($containerValue);
                            // match exact equal or CSV contains
                            $q->orWhere('nomor_kontainer', $v)
                              ->orWhere('nomor_kontainer', 'like', '%'. $v .'%');
                        }
                    })->get();
                    $prospeksToUpdate = $prospeksToUpdate->merge($prospeksByKontainer);
                    Log::info('Prospek search by nomor_kontainer used; results: ' . $prospeksByKontainer->count(), ['search_kontainers' => $nomorKontainers]);
                }
            }

            // Remove duplicates based on ID
            $prospeksToUpdate = $prospeksToUpdate->unique('id');

            // Determine nomor_kontainer and no_seal values to set on Prospek (prefer request values)
            $noKontainerToSet = null;
            $noSealToSet = null;
            if ($request->has('nomor_kontainer') && is_array($request->nomor_kontainer)) {
                $nomorKontainers = array_filter($request->nomor_kontainer, function($value) {
                    return !empty(trim($value));
                });
                if (!empty($nomorKontainers)) {
                    $noKontainerToSet = implode(',', $nomorKontainers);
                }
            }
            if (empty($noKontainerToSet) && !empty($tandaTerima->no_kontainer)) {
                $noKontainerToSet = $tandaTerima->no_kontainer;
            }

            if ($request->has('no_seal') && is_array($request->no_seal)) {
                $noSeals = array_filter($request->no_seal, function($value) {
                    return !empty(trim($value));
                });
                if (!empty($noSeals)) {
                    $noSealToSet = implode(',', $noSeals);
                }
            }
            if (empty($noSealToSet) && !empty($tandaTerima->no_seal)) {
                $noSealToSet = $tandaTerima->no_seal;
            }

            // Update each related prospek
            $updatedCount = 0;
            foreach ($prospeksToUpdate as $prospek) {
                $updateFields = [
                    'tanda_terima_id' => $tandaTerima->id,
                    'updated_by' => Auth::id(),
                ];

                // Only update volume, tonase, kuantitas if we have calculated values
                if ($totalVolume > 0) {
                    $updateFields['total_volume'] = $totalVolume;
                }
                if ($totalTonase > 0) {
                    $updateFields['total_ton'] = $totalTonase;
                }
                if ($kuantitas > 0) {
                    $updateFields['kuantitas'] = $kuantitas;
                }

                // Update nomor_kontainer and no_seal if available
                if (!empty($noKontainerToSet)) {
                    $updateFields['nomor_kontainer'] = $noKontainerToSet;
                }
                if (!empty($noSealToSet)) {
                    $updateFields['no_seal'] = $noSealToSet;
                }

                // Update tujuan_pengiriman if available from request or tanda terima
                $tujuanPengiriman = $request->filled('tujuan_pengiriman') 
                    ? $request->tujuan_pengiriman 
                    : $tandaTerima->tujuan_pengiriman;
                
                if (!empty($tujuanPengiriman)) {
                    $updateFields['tujuan_pengiriman'] = $tujuanPengiriman;
                }

                $prospek->update($updateFields);
                $updatedCount++;

                Log::info('Prospek updated from TandaTerima', [
                    'prospek_id' => $prospek->id,
                    'tanda_terima_id' => $tandaTerima->id,
                    'nomor_kontainer' => $prospek->nomor_kontainer,
                    'nomor_kontainer_set' => $noKontainerToSet ?? null,
                    'tujuan_pengiriman' => $prospek->tujuan_pengiriman,
                    'surat_jalan_id' => $prospek->surat_jalan_id,
                    'no_surat_jalan' => $prospek->no_surat_jalan,
                    'total_volume' => $totalVolume,
                    'total_ton' => $totalTonase,
                    'kuantitas' => $kuantitas,
                    'update_method' => $prospek->surat_jalan_id == $tandaTerima->surat_jalan_id ? 'surat_jalan_id' : 
                                     ($prospek->no_surat_jalan == $tandaTerima->no_surat_jalan ? 'no_surat_jalan' : 'nomor_kontainer')
                ]);
            }

            Log::info('Updated related prospek data', [
                'tanda_terima_id' => $tandaTerima->id,
                'prospeks_found_total' => $prospeksToUpdate->count(),
                'prospeks_updated' => $updatedCount,
                'total_volume' => $totalVolume,
                'total_tonase' => $totalTonase,
                'kuantitas' => $kuantitas,
                'nomor_kontainer_set' => $noKontainerToSet ?? null,
                'no_seal_set' => $noSealToSet ?? null,
                'search_methods_used' => [
                    'surat_jalan_id' => $tandaTerima->surat_jalan_id ? true : false,
                    'no_surat_jalan' => $tandaTerima->no_surat_jalan ? true : false,
                    'nomor_kontainer' => $request->has('nomor_kontainer') ? true : false
                ]
            ]);

            return $updatedCount;

        } catch (\Exception $e) {
            Log::error('Error updating related prospek data: ' . $e->getMessage(), [
                'tanda_terima_id' => $tandaTerima->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw exception to avoid breaking the main update process
            return 0;
        }
    }

    /**
     * Handle image upload for checkpoint images
     * 
     * @param array $uploadedFiles
     * @param string|null $nomorTandaTerima
     * @return array Array of uploaded image paths
     */
    private function handleImageUpload($uploadedFiles, $nomorTandaTerima = null)
    {
        $imagePaths = [];
        
        try {
            // Ensure checkpoint directory exists
            if (!Storage::disk('public')->exists('checkpoint')) {
                Storage::disk('public')->makeDirectory('checkpoint');
            }
            
            foreach ($uploadedFiles as $index => $file) {
                if ($file->isValid()) {
                    // Generate filename based on tanda terima ID if available
                    $extension = $file->getClientOriginalExtension();
                    
                    if ($nomorTandaTerima) {
                        // Clean identifier for filename (remove special characters)
                        $cleanIdentifier = Str::slug($nomorTandaTerima, '_');
                        $filename = $cleanIdentifier . '_' . ($index + 1) . '.' . $extension;
                    } else {
                        // Fallback to original naming if no identifier
                        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $filename = 'checkpoint_' . time() . '_' . uniqid() . '_' . Str::slug($originalName) . '.' . $extension;
                    }
                    
                    // Store in public disk under checkpoint directory
                    $path = $file->storeAs('checkpoint', $filename, 'public');
                    
                    if ($path) {
                        $imagePaths[] = $path;
                        
                        Log::info('Image uploaded successfully', [
                            'original_name' => $file->getClientOriginalName(),
                            'stored_path' => $path,
                            'file_size' => $file->getSize(),
                            'uploaded_by' => Auth::user() ? Auth::user()->name : null,
                        ]);
                    }
                } else {
                    Log::warning('Invalid file uploaded', [
                        'file_name' => $file->getClientOriginalName(),
                        'error' => $file->getErrorMessage(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error uploading images: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        return $imagePaths;
    }
}

