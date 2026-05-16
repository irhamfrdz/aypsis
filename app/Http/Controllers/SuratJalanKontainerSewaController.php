<?php

namespace App\Http\Controllers;

use App\Models\SuratJalanKontainerSewa;
use App\Models\SuratJalanKontainerSewaItem;
use App\Models\Kontainer;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SuratJalanKontainerSewaController extends Controller
{
    /**
     * Display a listing of surat jalan kontainer sewa.
     */
    public function index(Request $request)
    {
        $query = SuratJalanKontainerSewa::with('items')->withCount('items');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhereHas('items', function ($q2) use ($search) {
                      $q2->where('nomor_kontainer', 'like', "%{$search}%");
                  });
            });
        }

        // Filter tipe
        if ($request->filled('tipe') && $request->tipe !== 'all') {
            $query->where('tipe', $request->tipe);
        }

        // Filter status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter vendor
        if ($request->filled('vendor')) {
            $query->where('vendor', $request->vendor);
        }

        // Filter date range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $suratJalans = $query->orderByDesc('tanggal')
                             ->orderByDesc('id')
                             ->paginate(20)
                             ->withQueryString();

        // Get vendor options for filter
        $vendors = Kontainer::distinct()
            ->whereNotNull('vendor')
            ->where('vendor', '!=', '')
            ->pluck('vendor')
            ->sort()
            ->values();

        return view('surat-jalan-kontainer-sewa.index', compact('suratJalans', 'vendors'));
    }

    /**
     * Show the form for creating a new surat jalan.
     */
    public function create(Request $request)
    {
        $tipe = $request->get('tipe', 'pengambilan');

        // Get available kontainers
        $kontainers = Kontainer::whereNotNull('vendor')
            ->where('vendor', '!=', '')
            ->orderBy('vendor')
            ->orderBy('nomor_seri_gabungan')
            ->get();

        // Get vendor list
        $vendors = Kontainer::distinct()
            ->whereNotNull('vendor')
            ->where('vendor', '!=', '')
            ->pluck('vendor')
            ->sort()
            ->values();

        // Get supir list from karyawan table
        $supirs = Karyawan::where('divisi', 'supir')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'plat']);

        // Default empty for manual entry
        $nomorSuratJalan = '';

        return view('surat-jalan-kontainer-sewa.create', compact('tipe', 'kontainers', 'vendors', 'nomorSuratJalan', 'supirs'));
    }

    /**
     * Store a newly created surat jalan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_surat_jalan' => 'required|string|max:100|unique:surat_jalan_kontainer_sewas,nomor_surat_jalan',
            'tipe' => 'required|in:pengambilan,pengembalian',
            'tanggal' => 'required|date',
            'vendor' => 'nullable|string|max:255',
            'supir' => 'nullable|string|max:255',
            'no_plat' => 'nullable|string|max:50',
            'lokasi_pengambilan' => 'nullable|string|max:255',
            'lokasi_pengembalian' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
            'kontainer_ids' => 'required|array|min:1',
            'kontainer_ids.*' => 'required|string',
            'kondisi' => 'nullable|array',
            'kondisi.*' => 'nullable|in:baik,rusak_ringan,rusak_berat',
            'catatan_kondisi' => 'nullable|array',
            'catatan_kondisi.*' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $nomorSuratJalan = $request->nomor_surat_jalan;

            $suratJalan = SuratJalanKontainerSewa::create([
                'nomor_surat_jalan' => $nomorSuratJalan,
                'tipe' => $request->tipe,
                'tanggal' => $request->tanggal,
                'vendor' => $request->vendor,
                'supir' => $request->supir,
                'no_plat' => $request->no_plat,
                'antar_lokasi' => $request->boolean('antar_lokasi'),
                'lokasi_pengambilan' => $request->lokasi_pengambilan,
                'lokasi_pengembalian' => $request->lokasi_pengembalian,
                'keterangan' => $request->keterangan,
                'status' => 'aktif',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Create items
            foreach ($request->kontainer_ids as $index => $nomorKontainer) {
                $nomorKontainer = trim($nomorKontainer);
                if (empty($nomorKontainer)) continue;

                // Lookup kontainer info
                $kontainer = Kontainer::where('nomor_seri_gabungan', $nomorKontainer)->first();

                SuratJalanKontainerSewaItem::create([
                    'surat_jalan_kontainer_sewa_id' => $suratJalan->id,
                    'nomor_kontainer' => $nomorKontainer,
                    'ukuran' => $kontainer->ukuran ?? null,
                    'tipe_kontainer' => $kontainer->tipe_kontainer ?? null,
                    'vendor' => $kontainer->vendor ?? $request->vendor,
                    'kondisi' => $request->kondisi[$index] ?? 'baik',
                    'catatan_kondisi' => $request->catatan_kondisi[$index] ?? null,
                ]);
            }

            DB::commit();

            Log::info('Surat Jalan Kontainer Sewa created', [
                'id' => $suratJalan->id,
                'nomor' => $nomorSuratJalan,
                'tipe' => $request->tipe,
                'jumlah_kontainer' => count($request->kontainer_ids),
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('surat-jalan-kontainer-sewa.show', $suratJalan->id)
                             ->with('success', "Surat Jalan {$nomorSuratJalan} berhasil dibuat.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating SJ Kontainer Sewa: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal membuat surat jalan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified surat jalan.
     */
    public function show($id)
    {
        $suratJalan = SuratJalanKontainerSewa::with('items', 'createdByUser')->findOrFail($id);
        return view('surat-jalan-kontainer-sewa.show', compact('suratJalan'));
    }

    /**
     * Print the specified surat jalan.
     */
    public function print($id)
    {
        $suratJalan = SuratJalanKontainerSewa::with('items')->findOrFail($id);
        return view('surat-jalan-kontainer-sewa.print', compact('suratJalan'));
    }

    /**
     * Update status of surat jalan.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:draft,aktif,selesai,batal',
        ]);

        $suratJalan = SuratJalanKontainerSewa::findOrFail($id);
        $suratJalan->update([
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Status berhasil diperbarui menjadi ' . ucfirst($request->status));
    }

    /**
     * Remove the specified surat jalan.
     */
    public function destroy($id)
    {
        $suratJalan = SuratJalanKontainerSewa::findOrFail($id);

        if ($suratJalan->status === 'selesai') {
            return back()->with('error', 'Surat jalan yang sudah selesai tidak dapat dihapus.');
        }

        $suratJalan->delete();
        return redirect()->route('surat-jalan-kontainer-sewa.index')
                         ->with('success', 'Surat jalan berhasil dihapus.');
    }

    /**
     * Get kontainer data via AJAX (for create form).
     */
    public function getKontainerByVendor(Request $request)
    {
        $vendor = $request->get('vendor');

        $query = Kontainer::whereNotNull('nomor_seri_gabungan')
            ->where('nomor_seri_gabungan', '!=', '');

        if ($vendor) {
            $query->where('vendor', $vendor);
        }

        $kontainers = $query->orderBy('nomor_seri_gabungan')
            ->get(['id', 'nomor_seri_gabungan', 'ukuran', 'tipe_kontainer', 'vendor', 'status']);

        return response()->json(['success' => true, 'kontainers' => $kontainers]);
    }
}
