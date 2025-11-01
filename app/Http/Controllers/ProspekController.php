<?php

namespace App\Http\Controllers;

use App\Models\Prospek;
use App\Models\User;
use App\Models\MasterKapal;
use App\Models\NaikKapal;
use App\Models\Bl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ProspekController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$this->hasProspekPermission($user, 'prospek-view')) {
                abort(403, "Tidak memiliki akses ke halaman prospek");
            }

            $query = Prospek::with(['createdBy', 'updatedBy'])->orderBy('created_at', 'desc');

            // Filter berdasarkan status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter berdasarkan tipe
            if ($request->filled('tipe')) {
                $query->where('tipe', $request->tipe);
            }

            // Filter berdasarkan tujuan
            if ($request->filled('tujuan')) {
                $query->where('tujuan_pengiriman', 'like', '%' . $request->tujuan . '%');
            }

            // Filter berdasarkan ukuran
            if ($request->filled('ukuran')) {
                $query->where('ukuran', $request->ukuran);
            }

            // Filter berdasarkan tanggal
            if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
                $query->whereBetween('tanggal', [$request->tanggal_dari, $request->tanggal_sampai]);
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama_supir', 'like', '%' . $search . '%')
                      ->orWhere('barang', 'like', '%' . $search . '%')
                      ->orWhere('pt_pengirim', 'like', '%' . $search . '%')
                      ->orWhere('nomor_kontainer', 'like', '%' . $search . '%')
                      ->orWhere('no_seal', 'like', '%' . $search . '%')
                      ->orWhere('tujuan_pengiriman', 'like', '%' . $search . '%')
                      ->orWhere('nama_kapal', 'like', '%' . $search . '%');
                });
            }

            $prospeks = $query->paginate(15)->appends($request->query());

            // Statistik untuk summary cards
            $totalBelumMuat = Prospek::where(function ($q) {
                $q->whereNull('status')
                  ->orWhere('status', '')
                  ->orWhere('status', 'aktif');
            })->count();
            
            $totalSudahMuat = Prospek::where('status', 'sudah_muat')->count();
            $totalBatal = Prospek::where('status', 'batal')->count();

            return view('prospek.index', compact('prospeks', 'totalBelumMuat', 'totalSudahMuat', 'totalBatal'));

        } catch (\Exception $e) {
            return back()->with('error', 'Error loading data prospek: ' . $e->getMessage());
        }
    }

    // Create and Store methods removed - Prospek is read-only

    /**
     * Display the specified resource.
     */
    public function show(Prospek $prospek)
    {
        $user = Auth::user();
        if (!$this->hasProspekPermission($user, 'prospek-view')) {
            abort(403, "Tidak memiliki akses untuk melihat detail prospek");
        }

        $prospek->load(['createdBy', 'updatedBy']);

        return view('prospek.show', compact('prospek'));
    }

    // Edit, Update, and Destroy methods removed - Prospek is read-only

    /**
     * Check if user has specific prospek permission
     */
    /**
     * Show form to select destination for prospek
     */
    public function pilihTujuan()
    {
        try {
            $user = Auth::user();
            if (!$this->hasProspekPermission($user, 'prospek-edit')) {
                abort(403, "Tidak memiliki akses untuk mengubah status prospek");
            }

            // Ambil prospek aktif yang tersedia
            $prospeksAktif = Prospek::where('status', 'aktif')
                ->orderBy('created_at', 'desc')
                ->get();

            // Ambil daftar tujuan yang tersedia
            $tujuans = collect([
                (object) [
                    'id' => 'jakarta',
                    'nama' => 'Jakarta',
                    'kode' => 'JKT',
                    'deskripsi' => 'Pelabuhan Tanjung Priok, Jakarta'
                ],
                (object) [
                    'id' => 'batam',
                    'nama' => 'Batam',
                    'kode' => 'BTM',
                    'deskripsi' => 'Pelabuhan Sekupang, Batam'
                ],
                (object) [
                    'id' => 'pinang',
                    'nama' => 'Pinang',
                    'kode' => 'PNG',
                    'deskripsi' => 'Pulau Pinang, Malaysia'
                ],
                (object) [
                    'id' => 'surabaya',
                    'nama' => 'Surabaya',
                    'kode' => 'SBY',
                    'deskripsi' => 'Pelabuhan Tanjung Perak, Surabaya'
                ],
                (object) [
                    'id' => 'medan',
                    'nama' => 'Medan',
                    'kode' => 'MDN',
                    'deskripsi' => 'Pelabuhan Belawan, Medan'
                ]
            ]);

            return view('prospek.pilih-tujuan', compact('prospeksAktif', 'tujuans'));
        } catch (\Exception $e) {
            return redirect()->route('prospek.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show proses naik kapal page
     */
    public function prosesNaikKapal(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$this->hasProspekPermission($user, 'prospek-edit')) {
                abort(403, "Tidak memiliki akses untuk mengubah status prospek");
            }

            // Validate tujuan_id
            $request->validate([
                'tujuan_id' => 'required|string|in:jakarta,batam,pinang,surabaya,medan'
            ]);

            $tujuanId = $request->tujuan_id;
            
            // Mapping tujuan
            $tujuanMapping = [
                'jakarta' => [
                    'nama' => 'Jakarta',
                    'kode' => 'JKT',
                    'deskripsi' => 'Pelabuhan Tanjung Priok, Jakarta',
                    'keywords' => ['jakarta', 'tanjung priok', 'jkt']
                ],
                'batam' => [
                    'nama' => 'Batam',
                    'kode' => 'BTM', 
                    'deskripsi' => 'Pelabuhan Sekupang, Batam',
                    'keywords' => ['batam', 'sekupang', 'btm']
                ],
                'pinang' => [
                    'nama' => 'Pinang',
                    'kode' => 'PNG',
                    'deskripsi' => 'Pulau Pinang, Malaysia',
                    'keywords' => ['pinang', 'penang', 'malaysia', 'png']
                ],
                'surabaya' => [
                    'nama' => 'Surabaya',
                    'kode' => 'SBY',
                    'deskripsi' => 'Pelabuhan Tanjung Perak, Surabaya',
                    'keywords' => ['surabaya', 'tanjung perak', 'sby']
                ],
                'medan' => [
                    'nama' => 'Medan',
                    'kode' => 'MDN',
                    'deskripsi' => 'Pelabuhan Belawan, Medan',
                    'keywords' => ['medan', 'belawan', 'mdn']
                ]
            ];

            $tujuan = (object) $tujuanMapping[$tujuanId];

            // Get prospek aktif yang sesuai dengan tujuan ini
            $keywords = $tujuan->keywords;
            $prospeksAktif = Prospek::where('status', 'aktif')
                ->get()
                ->filter(function($prospek) use ($keywords) {
                    $tujuanPengiriman = strtolower($prospek->tujuan_pengiriman ?? '');
                    foreach ($keywords as $keyword) {
                        if (stripos($tujuanPengiriman, strtolower($keyword)) !== false) {
                            return true;
                        }
                    }
                    return false;
                });

            // Get data kapal dan pelabuhan untuk dropdown
            $masterKapals = \App\Models\MasterKapal::where('status', 'aktif')
                ->orderBy('nama_kapal', 'asc')
                ->get();

            $masterTujuanKirims = \App\Models\MasterTujuanKirim::all(); // Debug: get all data first
            \Log::info('Master Tujuan Kirim data:', $masterTujuanKirims->toArray());

            return view('prospek.proses-naik-kapal', compact('tujuan', 'prospeksAktif', 'tujuanId', 'masterKapals', 'masterTujuanKirims'));
        } catch (\Exception $e) {
            return redirect()->route('prospek.pilih-tujuan')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Execute naik kapal process
     */
    public function executeNaikKapal(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$this->hasProspekPermission($user, 'prospek-edit')) {
                abort(403, "Tidak memiliki akses untuk mengubah status prospek");
            }

            // Validate input
            $request->validate([
                'tujuan_id' => 'required|string|in:jakarta,batam,pinang,surabaya,medan',
                'tanggal' => 'required|date',
                'kapal_id' => 'required|exists:master_kapals,id',
                'no_voyage' => 'required|string|max:50',
                'prospek_ids' => 'required|array|min:1',
                'prospek_ids.*' => 'exists:prospek,id',
                'pelabuhan_asal' => 'required|string|max:100'
            ]);

            $tujuanId = $request->tujuan_id;
            
            // Mapping tujuan dan keywords
            $tujuanMapping = [
                'jakarta' => ['nama' => 'Jakarta', 'keywords' => ['jakarta', 'tanjung priok', 'jkt']],
                'batam' => ['nama' => 'Batam', 'keywords' => ['batam', 'sekupang', 'btm']],
                'pinang' => ['nama' => 'Pinang', 'keywords' => ['pinang', 'penang', 'malaysia', 'png']],
                'surabaya' => ['nama' => 'Surabaya', 'keywords' => ['surabaya', 'tanjung perak', 'sby']],
                'medan' => ['nama' => 'Medan', 'keywords' => ['medan', 'belawan', 'mdn']]
            ];

            $tujuanData = $tujuanMapping[$tujuanId];
            $keywords = $tujuanData['keywords'];

            // Get data kapal yang dipilih
            $masterKapal = \App\Models\MasterKapal::find($request->kapal_id);
            if (!$masterKapal) {
                return redirect()->back()
                    ->with('error', 'Kapal tidak ditemukan')
                    ->withInput();
            }

            // Get prospek yang dipilih berdasarkan prospek_ids
            $prospekIds = $request->prospek_ids;
            $prospeks = Prospek::whereIn('id', $prospekIds)
                ->where('status', 'aktif')
                ->get();

            if ($prospeks->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'Tidak ada prospek aktif yang dipilih')
                    ->withInput();
            }

            $updatedCount = 0;
            $updatedKontainers = [];

            // Update semua prospek yang dipilih dan simpan ke tabel naik_kapal
            foreach ($prospeks as $prospek) {
                // Update status prospek
                $prospek->update([
                    'status' => 'sudah_muat',
                    'tanggal_muat' => $request->tanggal,
                    'nama_kapal' => $masterKapal->nama_kapal,
                    'kapal_id' => $masterKapal->id,
                    'no_voyage' => $request->no_voyage,
                    'pelabuhan_asal' => $request->pelabuhan_asal,
                    'updated_by' => $user->id
                ]);

                // Simpan data ke tabel naik_kapal
                $naikKapalData = [
                    'prospek_id' => $prospek->id,
                    'nomor_kontainer' => $prospek->nomor_kontainer ?: 'CARGO-' . $prospek->id, // Handle null
                    'jenis_barang' => $prospek->barang,
                    'tipe_kontainer' => $prospek->tipe,
                    'ukuran_kontainer' => $prospek->ukuran ? $prospek->ukuran . ' Feet' : null,
                    'nama_kapal' => $masterKapal->nama_kapal,
                    'no_voyage' => $request->no_voyage,
                    'pelabuhan_asal' => $request->pelabuhan_asal,
                    'pelabuhan_tujuan' => $tujuanData['nama'],
                    'tanggal_muat' => $request->tanggal,
                    'total_volume' => $prospek->volume_m3,
                    'total_tonase' => $prospek->tonase,
                    'kuantitas' => $prospek->kuantitas,
                    'created_by' => $user->id,
                    'updated_by' => $user->id
                ];
                
                try {
                    // Simpan ke naik_kapal
                    $naikKapal = NaikKapal::create($naikKapalData);
                    
                    // Simpan data ke tabel bls juga
                    $blData = [
                        'prospek_id' => $prospek->id,
                        'nomor_kontainer' => $naikKapalData['nomor_kontainer'],
                        'no_seal' => $prospek->no_seal,
                        'tipe_kontainer' => $prospek->tipe,
                        'no_voyage' => $request->no_voyage,
                        'nama_kapal' => $masterKapal->nama_kapal,
                        'nama_barang' => $prospek->barang,
                        'tonnage' => $prospek->tonase,
                        'volume' => $naikKapalData['total_volume'],
                        'term' => $prospek->tandaTerima ? $prospek->tandaTerima->term : null,
                        'kuantitas' => $prospek->kuantitas,
                    ];
                    
                    try {
                        $bl = Bl::create($blData);
                    } catch (\Exception $blError) {
                        \Log::error('Failed to create BL record', [
                            'prospek_id' => $prospek->id,
                            'error' => $blError->getMessage(),
                            'data' => $blData
                        ]);
                        // Lanjutkan meski BL gagal dibuat
                    }
                    
                    $updatedCount++;
                    $updatedKontainers[] = $naikKapalData['nomor_kontainer'];
                    
                } catch (\Exception $createError) {
                    \Log::error('Failed to create NaikKapal record', [
                        'prospek_id' => $prospek->id,
                        'error' => $createError->getMessage()
                    ]);
                    // Lanjutkan ke prospek berikutnya meski ada error
                }
            }

            $kontainerList = implode(', ', $updatedKontainers);
            
            return redirect()->route('prospek.index')
                ->with('success', "Berhasil memproses {$updatedCount} prospek ({$kontainerList}) untuk naik kapal {$masterKapal->nama_kapal} ke {$tujuanData['nama']}. Data telah disimpan ke tabel naik kapal dan BL.");
        
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get voyage numbers by kapal_id
     */
    public function getVoyageByKapal(Request $request)
    {
        try {
            $kapalId = $request->kapal_id;
            
            if (!$kapalId) {
                return response()->json(['error' => 'Kapal ID required'], 400);
            }

            // Get kapal name
            $masterKapal = \App\Models\MasterKapal::find($kapalId);
            if (!$masterKapal) {
                return response()->json(['error' => 'Kapal not found'], 404);
            }

            // Get voyage numbers from pergerakan_kapal for this kapal
            $voyages = \App\Models\PergerakanKapal::where('nama_kapal', $masterKapal->nama_kapal)
                ->whereNotNull('voyage')
                ->orderBy('created_at', 'desc')
                ->pluck('voyage')
                ->unique()
                ->values();

            return response()->json([
                'success' => true,
                'voyages' => $voyages
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function hasProspekPermission($user, $permission)
    {
        // Admin and user_admin always have access
        if (in_array($user->role, ["admin", "user_admin"])) {
            return true;
        }

        try {
            $hasPermission = DB::table("user_permissions")
                ->join("permissions", "user_permissions.permission_id", "=", "permissions.id")
                ->where("user_permissions.user_id", $user->id)
                ->where("permissions.name", $permission)
                ->exists();
            
            // Debug logging - remove this later
            if (!$hasPermission) {
                \Log::warning("User {$user->username} (ID: {$user->id}) missing permission: {$permission}");
            }
            
            return $hasPermission;
        } catch (\Exception $e) {
            \Log::error("Permission check failed for user {$user->id}: " . $e->getMessage());
            return false;
        }
    }
}
