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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProspekExport;

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

            $query = Prospek::with(['createdBy', 'updatedBy', 'bls', 'suratJalan'])->orderBy('created_at', 'desc');

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
                                    ->orWhere('no_surat_jalan', 'like', '%' . $search . '%')
                      ->orWhere('barang', 'like', '%' . $search . '%')
                      ->orWhere('pt_pengirim', 'like', '%' . $search . '%')
                      ->orWhere('nomor_kontainer', 'like', '%' . $search . '%')
                      ->orWhere('no_seal', 'like', '%' . $search . '%')
                      ->orWhere('tujuan_pengiriman', 'like', '%' . $search . '%')
                      ->orWhere('nama_kapal', 'like', '%' . $search . '%');
                });
            }

            // Allow configurable rows per page, default to 15. Validate allowed values to prevent abuse.
            $allowedPerPage = [10, 25, 50, 100];
            $perPage = (int) $request->get('per_page', 10);
            if (!in_array($perPage, $allowedPerPage)) {
                $perPage = 10;
            }

                        // Keep a copy of the filtered query so totals use the same filters
                        $filteredQuery = clone $query;

                        $prospeks = $query->paginate($perPage)->appends($request->query());

                        // Statistik untuk summary cards - use the same filtered query
                        $totalBelumMuat = (clone $filteredQuery)->where(function ($q) {
                                $q->whereNull('status')
                                    ->orWhere('status', '')
                                    ->orWhere('status', 'aktif');
                        })->count();

                        $totalSudahMuat = (clone $filteredQuery)->where('status', 'sudah_muat')->count();
                        $totalBatal = (clone $filteredQuery)->where('status', 'batal')->count();

            return view('prospek.index', compact('prospeks', 'totalBelumMuat', 'totalSudahMuat', 'totalBatal'));

        } catch (\Exception $e) {
            return back()->with('error', 'Error loading data prospek: ' . $e->getMessage());
        }
    }

    /**
     * Export listing to Excel using current filters
     */
    public function exportExcel(Request $request)
    {
        try {
            $filters = $request->query();
            $fileName = 'prospek_export_' . date('Ymd_His') . '.xlsx';
            $export = new ProspekExport($filters, []);

            return Excel::download($export, $fileName);
        } catch (\Exception $e) {
            \Log::error('Error exporting prospek: ' . $e->getMessage());
            return back()->with('error', 'Gagal export prospek: ' . $e->getMessage());
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

            // Get prospek aktif dan batal yang sesuai dengan tujuan ini
            $keywords = $tujuan->keywords;
            $prospeksAktif = Prospek::whereIn('status', ['aktif', 'batal'])
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
        // Debug - buat file debug sederhana
        $debugFile = storage_path('logs/debug_submit.txt');
        file_put_contents($debugFile, 'Submit attempt at: ' . now() . "\n", FILE_APPEND);
        file_put_contents($debugFile, 'Request data: ' . json_encode($request->all()) . "\n", FILE_APPEND);
        
        try {
            $user = Auth::user();
            
            // Log incoming request data
            \Log::info('Execute Naik Kapal - Request Data:', $request->all());
            
            if (!$this->hasProspekPermission($user, 'prospek-edit')) {
                abort(403, "Tidak memiliki akses untuk mengubah status prospek");
            }

            // Validate input
            $validatedData = $request->validate([
                'tujuan_id' => 'required|string|in:jakarta,batam,pinang,surabaya,medan',
                'tanggal' => 'required|date',
                'kapal_id' => 'required|exists:master_kapals,id',
                'no_voyage' => 'required|string|max:50',
                'prospek_ids' => 'required|array|min:1',
                'prospek_ids.*' => 'exists:prospek,id',
                'pelabuhan_asal' => 'required|string|max:100'
            ]);
            
            $debugFile = storage_path('logs/debug_submit.txt');
            file_put_contents($debugFile, 'Validation passed' . "\n", FILE_APPEND);
            \Log::info('Validation passed successfully');

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
            \Log::info('Selected Prospek IDs:', $prospekIds);
            
            $prospeks = Prospek::whereIn('id', $prospekIds)
                ->where('status', 'aktif')
                ->get();

            \Log::info('Found ' . $prospeks->count() . ' active prospeks');

            if ($prospeks->isEmpty()) {
                \Log::warning('No active prospeks found for selected IDs');
                return redirect()->back()
                    ->with('error', 'Tidak ada prospek aktif yang dipilih')
                    ->withInput();
            }

            $updatedCount = 0;
            $updatedKontainers = [];

            // Update semua prospek yang dipilih dan simpan ke tabel naik_kapal
            foreach ($prospeks as $prospek) {
                \Log::info('Processing prospek ID: ' . $prospek->id);
                
                // Jangan ubah status prospek, biarkan tetap aktif
                // Hanya update informasi tambahan terkait kapal tanpa mengubah status
                $updateData = [
                    'tanggal_muat' => $request->tanggal,
                    'nama_kapal' => $masterKapal->nama_kapal,
                    'kapal_id' => $masterKapal->id,
                    'no_voyage' => $request->no_voyage,
                    'pelabuhan_asal' => $request->pelabuhan_asal,
                    'updated_by' => $user->id
                ];
                
                $prospek->update($updateData);
                
                \Log::info('Updated prospek info (status tetap) for ID: ' . $prospek->id);

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
                    \Log::info('Creating NaikKapal record with data:', $naikKapalData);
                    $naikKapal = NaikKapal::create($naikKapalData);
                    \Log::info('Successfully created NaikKapal record with ID: ' . $naikKapal->id);
                    
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
            
            \Log::info('Successfully processed ' . $updatedCount . ' prospeks');
            
            return redirect()->route('prospek.index')
                ->with('success', "Berhasil memproses {$updatedCount} prospek ({$kontainerList}) untuk naik kapal {$masterKapal->nama_kapal} ke {$tujuanData['nama']}. Data telah disimpan ke tabel naik kapal dan BL.");
        
        } catch (\Exception $e) {
            $debugFile = storage_path('logs/debug_submit.txt');
            file_put_contents($debugFile, 'ERROR: ' . $e->getMessage() . "\n", FILE_APPEND);
            file_put_contents($debugFile, 'Stack trace: ' . $e->getTraceAsString() . "\n", FILE_APPEND);
            \Log::error('Error in executeNaikKapal: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
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

    /**
     * Update seal for prospek (inline edit)
     */
    public function updateSeal(Request $request, Prospek $prospek)
    {
        try {
            $user = Auth::user();
            if (!$this->hasProspekPermission($user, 'prospek-edit')) {
                return response()->json(['error' => 'Tidak memiliki akses untuk mengubah data prospek'], 403);
            }

            $request->validate([
                'no_seal' => 'required|string|max:255'
            ]);

            $oldSeal = $prospek->no_seal;
            
            $prospek->update([
                'no_seal' => $request->no_seal,
                'updated_by' => Auth::id()
            ]);

            // Log the update
            \Log::info('Seal updated for prospek (inline edit)', [
                'prospek_id' => $prospek->id,
                'old_seal' => $oldSeal,
                'new_seal' => $request->no_seal,
                'updated_by' => Auth::user()->name,
                'supir' => $prospek->nama_supir
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nomor seal berhasil diperbarui',
                'data' => [
                    'id' => $prospek->id,
                    'no_seal' => $prospek->no_seal
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating seal (inline edit)', [
                'prospek_id' => $prospek->id ?? null,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update status of a prospek via AJAX
     */
    public function updateStatus(Request $request, Prospek $prospek)
    {
        try {
            $user = Auth::user();
            if (!$this->hasProspekPermission($user, 'prospek-edit')) {
                return response()->json(['error' => 'Tidak memiliki akses untuk mengubah status prospek'], 403);
            }

            $request->validate([
                'status' => 'required|string|in:aktif,sudah_muat,batal'
            ]);

            $oldStatus = $prospek->status;
            
            $prospek->update([
                'status' => $request->status,
                'updated_by' => Auth::id()
            ]);

            // Log the update
            \Log::info('Status updated for prospek', [
                'prospek_id' => $prospek->id,
                'no_surat_jalan' => $prospek->no_surat_jalan,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'updated_by' => Auth::user()->name,
                'supir' => $prospek->nama_supir
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'data' => [
                    'id' => $prospek->id,
                    'status' => $prospek->status,
                    'old_status' => $oldStatus
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating status', [
                'prospek_id' => $prospek->id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Terjadi kesalahan saat memperbarui status'], 500);
        }
    }

    /**
     * Remove the specified prospek from storage.
     */
    public function destroy(Prospek $prospek)
    {
        try {
            $user = Auth::user();
            
            // Check permission
            if (!$this->hasProspekPermission($user, 'prospek-delete')) {
                return response()->json([
                    'error' => 'Anda tidak memiliki izin untuk menghapus prospek'
                ], 403);
            }

            // Store data for audit log before deletion
            $prospekData = [
                'id' => $prospek->id,
                'no_surat_jalan' => $prospek->no_surat_jalan,
                'tanggal' => $prospek->tanggal,
                'nama_supir' => $prospek->nama_supir,
                'barang' => $prospek->barang,
                'pt_pengirim' => $prospek->pt_pengirim,
                'tipe' => $prospek->tipe,
                'ukuran' => $prospek->ukuran,
                'nomor_kontainer' => $prospek->nomor_kontainer,
                'no_seal' => $prospek->no_seal,
                'tujuan_pengiriman' => $prospek->tujuan_pengiriman,
                'status' => $prospek->status,
            ];

            // Delete the prospek
            $prospek->delete();

            // Log the deletion
            \Log::info('Prospek deleted', [
                'prospek_data' => $prospekData,
                'deleted_by' => $user->username,
                'deleted_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Prospek berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting prospek', [
                'prospek_id' => $prospek->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat menghapus prospek: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync prospek data from surat jalan
     */
    public function syncFromSuratJalan($id)
    {
        try {
            $user = Auth::user();
            if (!$this->hasProspekPermission($user, 'prospek-edit')) {
                return response()->json([
                    'error' => 'Tidak memiliki akses untuk mengubah prospek'
                ], 403);
            }

            $prospek = Prospek::with('suratJalan')->findOrFail($id);
            
            if (!$prospek->suratJalan) {
                return response()->json([
                    'error' => 'Prospek tidak memiliki surat jalan terkait'
                ], 400);
            }

            $suratJalan = $prospek->suratJalan;
            
            // Update data prospek dari surat jalan
            $prospek->nomor_kontainer = $suratJalan->no_kontainer;
            $prospek->nama_supir = $suratJalan->supir;
            $prospek->barang = $suratJalan->jenis_barang;
            $prospek->pt_pengirim = $suratJalan->pengirim;
            $prospek->tujuan_pengiriman = $suratJalan->tujuan_pengiriman;
            $prospek->updated_by = $user->id;
            $prospek->save();

            return response()->json([
                'success' => true,
                'message' => 'Data prospek berhasil disinkronkan dari surat jalan',
                'data' => [
                    'nomor_kontainer' => $prospek->nomor_kontainer,
                    'nama_supir' => $prospek->nama_supir,
                    'barang' => $prospek->barang,
                    'pt_pengirim' => $prospek->pt_pengirim,
                    'tujuan_pengiriman' => $prospek->tujuan_pengiriman
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error syncing prospek from surat jalan', [
                'prospek_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat sinkronisasi: ' . $e->getMessage()
            ], 500);
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
