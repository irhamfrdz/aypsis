<?php

namespace App\Http\Controllers;

use App\Models\ProspekBatam;
use App\Models\User;
use App\Models\MasterKapal;
use App\Models\NaikKapal;
use App\Models\Bl;
use App\Exports\ProspekBatamExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProspekBatamController extends Controller
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
            if (!$this->hasProspekPermission($user, 'prospek-batam-view')) {
                abort(403, "Tidak memiliki akses ke halaman prospek batam");
            }

            $query = ProspekBatam::with(['creator', 'updater', 'bls', 'suratJalan'])->orderBy('created_at', 'desc');

            // Filter berdasarkan status
            if ($request->filled('status')) {
                if ($request->status == 'sudah_muat_no_voyage') {
                    $query->where('status', 'sudah_muat')
                          ->where(function($q) {
                              $q->whereNull('no_voyage')->orWhere('no_voyage', '');
                          });
                } else {
                    $query->where('status', $request->status);
                }
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
                      ->orWhere('nama_kapal', 'like', '%' . $search . '%')
                      ->orWhere('no_voyage', 'like', '%' . $search . '%');
                });
            }

            // Filter Duplicate No. Surat Jalan
            if ($request->has('show_duplicates') && $request->show_duplicates == '1') {
                $duplicateNos = ProspekBatam::select('no_surat_jalan')
                    ->whereNotNull('no_surat_jalan')
                    ->where('no_surat_jalan', '!=', '')
                    ->groupBy('no_surat_jalan')
                    ->havingRaw('COUNT(no_surat_jalan) > 1')
                    ->pluck('no_surat_jalan');
                
                $query->whereIn('no_surat_jalan', $duplicateNos);
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

            // Mobile detection using regex (fallback since package install failed)
            $userAgent = $request->header('User-Agent');
            $isMobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent);

            // Check if mobile (but allow override via query param for testing)
            if ($isMobile || $request->has('mobile_view')) {
                return view('prospek-batam.index_mobile', compact('prospeks', 'totalBelumMuat', 'totalSudahMuat', 'totalBatal'));
            }

            return view('prospek-batam.index', compact('prospeks', 'totalBelumMuat', 'totalSudahMuat', 'totalBatal'));

        } catch (\Exception $e) {
            Log::error('Error loading data prospek batam: ' . $e->getMessage());
            return back()->with('error', 'Error loading data prospek batam: ' . $e->getMessage());
        }
    }

    /**
     * Export listing to Excel using current filters
     */
    public function exportExcel(Request $request)
    {
        $filters = $request->only(['status', 'tipe', 'ukuran', 'tujuan', 'search', 'tanggal_dari', 'tanggal_sampai']);
        $prospekIds = $request->input('prospek_ids', []);

        if (is_string($prospekIds)) {
            $prospekIds = explode(',', $prospekIds);
        }

        return Excel::download(new ProspekBatamExport($filters, $prospekIds), 'prospek_batam_' . now()->format('YmdHis') . '.xlsx');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $prospek = ProspekBatam::findOrFail($id);
        $user = Auth::user();
        if (!$this->hasProspekPermission($user, 'prospek-batam-view')) {
            abort(403, "Tidak memiliki akses untuk melihat detail prospek batam");
        }

        $prospek->load(['creator', 'updater']);

        return view('prospek-batam.show', compact('prospek'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $prospek = ProspekBatam::findOrFail($id);
        $user = Auth::user();
        if (!$this->hasProspekPermission($user, 'prospek-batam-edit')) {
            abort(403, "Tidak memiliki akses untuk mengedit prospek batam");
        }

        $prospek->load(['suratJalan', 'tandaTerima', 'creator', 'updater']);
        
        // Get master data for dropdowns
        $kapals = MasterKapal::orderBy('nama_kapal')->get();

        return view('prospek-batam.edit', compact('prospek', 'kapals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $prospek = ProspekBatam::findOrFail($id);
        $user = Auth::user();
        if (!$this->hasProspekPermission($user, 'prospek-batam-edit')) {
            abort(403, "Tidak memiliki akses untuk mengedit prospek batam");
        }

        $validated = $request->validate([
            'tanggal' => 'nullable|date',
            'nama_supir' => 'nullable|string|max:255',
            'supir_ob' => 'nullable|string|max:255',
            'barang' => 'nullable|string|max:255',
            'pt_pengirim' => 'nullable|string|max:255',
            'ukuran' => 'nullable|string|max:50',
            'tipe' => 'nullable|string|max:50',
            'nomor_kontainer' => 'nullable|string|max:255',
            'no_seal' => 'nullable|string|max:255',
            'tujuan_pengiriman' => 'nullable|string|max:255',
            'total_ton' => 'nullable|numeric',
            'kuantitas' => 'nullable|integer',
            'total_volume' => 'nullable|numeric',
            'kapal_id' => 'nullable|exists:master_kapals,id',
            'nama_kapal' => 'nullable|string|max:255',
            'no_voyage' => 'nullable|string|max:255',
            'pelabuhan_asal' => 'nullable|string|max:255',
            'tanggal_muat' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'status' => 'nullable|string|max:50',
        ]);

        // Update kapal name if kapal_id is selected
        if ($request->filled('kapal_id')) {
            $kapal = MasterKapal::find($request->kapal_id);
            if ($kapal) {
                $validated['nama_kapal'] = $kapal->nama_kapal;
            }
        }

        $validated['updated_by'] = $user->id;
        
        $prospek->update($validated);

        return redirect()->route('prospek-batam.index')
            ->with('success', 'Prospek Batam berhasil diperbarui');
    }

    /**
     * Update seal for prospek (inline edit)
     */
    public function updateSeal(Request $request, $id)
    {
        $prospek = ProspekBatam::findOrFail($id);
        try {
            $user = Auth::user();
            if (!$this->hasProspekPermission($user, 'prospek-batam-edit')) {
                return response()->json(['error' => 'Tidak memiliki akses untuk mengubah data prospek batam'], 403);
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
            Log::info('Seal updated for prospek batam (inline edit)', [
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
            Log::error('Error updating seal (inline edit) batam', [
                'prospek_id' => $prospek->id ?? null,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update status of a prospek via AJAX
     */
    public function updateStatus(Request $request, $id)
    {
        $prospek = ProspekBatam::findOrFail($id);
        try {
            $user = Auth::user();
            if (!$this->hasProspekPermission($user, 'prospek-batam-edit')) {
                return response()->json(['error' => 'Tidak memiliki akses untuk mengubah status prospek batam'], 403);
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
            Log::info('Status updated for prospek batam', [
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
            Log::error('Error updating status batam', [
                'prospek_id' => $prospek->id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Terjadi kesalahan saat memperbarui status'], 500);
        }
    }

    /**
     * Remove the specified prospek from storage.
     */
    public function destroy($id)
    {
        $prospek = ProspekBatam::findOrFail($id);
        try {
            $user = Auth::user();
            
            // Check permission
            if (!$this->hasProspekPermission($user, 'prospek-batam-delete')) {
                return response()->json([
                    'error' => 'Anda tidak memiliki izin untuk menghapus prospek batam'
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
            Log::info('Prospek batam deleted', [
                'prospek_data' => $prospekData,
                'deleted_by' => $user->username,
                'deleted_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Prospek batam berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting prospek batam', [
                'prospek_id' => $prospek->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat menghapus prospek batam: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user has specific prospek permission
     */
    private function hasProspekPermission($user, $permission) {
        if (!$user) return false;
        if ($user->isAdmin()) return true;
        return $user->can($permission);
    }
}
