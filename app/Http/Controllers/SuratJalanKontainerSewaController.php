<?php

namespace App\Http\Controllers;

use App\Models\SuratJalanKontainerSewa;
use App\Models\SuratJalanKontainerSewaItem;
use App\Models\MasterPricelistTujuanKontainerSewa;
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
        $query = SuratJalanKontainerSewa::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%")
                  ->orWhere('no_plat', 'like', "%{$search}%")
                  ->orWhere('nomor_kontainer', 'like', "%{$search}%");
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

        // Get tujuan list
        $tujuans = MasterPricelistTujuanKontainerSewa::where('status', 'aktif')
            ->orderBy('tujuan')
            ->get();

        // Default empty for manual entry
        $nomorSuratJalan = '';

        return view('surat-jalan-kontainer-sewa.create', compact('tipe', 'kontainers', 'vendors', 'nomorSuratJalan', 'supirs', 'tujuans'));
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
            'nomor_kontainer' => 'required|string|max:100',
            'kondisi' => 'nullable|in:baik,rusak_ringan,rusak_berat',
            'catatan_kondisi' => 'nullable|string|max:500',
            'menggunakan_rit' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $nomorSuratJalan = $request->nomor_surat_jalan;
            
            // Lookup kontainer info
            $kontainer = Kontainer::where('nomor_seri_gabungan', $request->nomor_kontainer)->first();

            $suratJalan = SuratJalanKontainerSewa::create([
                'nomor_surat_jalan' => $nomorSuratJalan,
                'tipe' => $request->tipe,
                'tanggal' => $request->tanggal,
                'vendor' => $request->vendor,
                'supir' => $request->supir,
                'no_plat' => $request->no_plat,
                'antar_lokasi' => $request->boolean('antar_lokasi'),
                'nominal_uang_jalan' => $request->nominal_uang_jalan ?? 0,
                'nomor_kontainer' => $request->nomor_kontainer,
                'ukuran' => $kontainer->ukuran ?? null,
                'tipe_kontainer' => $kontainer->tipe_kontainer ?? null,
                'vendor_item' => $kontainer->vendor ?? null,
                'menggunakan_rit' => $request->boolean('menggunakan_rit'),
                'kondisi' => $request->kondisi,
                'catatan_kondisi' => $request->catatan_kondisi,
                'lokasi_pengambilan' => $request->lokasi_pengambilan,
                'lokasi_pengembalian' => $request->lokasi_pengembalian,
                'tujuan' => $request->tujuan,
                'keterangan' => $request->keterangan,
                'status' => 'aktif',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            Log::info('Surat Jalan Kontainer Sewa created', [
                'id' => $suratJalan->id,
                'nomor' => $nomorSuratJalan,
                'tipe' => $request->tipe,
                'kontainer' => $request->nomor_kontainer,
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
     * Show the form for editing the specified surat jalan.
     */
    public function edit($id)
    {
        $suratJalan = SuratJalanKontainerSewa::findOrFail($id);
        $tipe = $suratJalan->tipe;

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

        // Get tujuan list
        $tujuans = MasterPricelistTujuanKontainerSewa::where('status', 'aktif')
            ->orderBy('tujuan')
            ->get();

        return view('surat-jalan-kontainer-sewa.edit', compact('suratJalan', 'tipe', 'kontainers', 'vendors', 'supirs', 'tujuans'));
    }

    /**
     * Update the specified surat jalan in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tipe' => 'required|in:pengambilan,pengembalian',
            'tanggal' => 'required|date',
            'vendor' => 'required|string',
            'supir' => 'required|string',
            'no_plat' => 'nullable|string',
            'nomor_kontainer' => 'required|string',
            'nominal_uang_jalan' => 'nullable|numeric|min:0',
            'menggunakan_rit' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $suratJalan = SuratJalanKontainerSewa::findOrFail($id);
            $kontainer = Kontainer::where('nomor_seri_gabungan', $request->nomor_kontainer)->first();

            $suratJalan->update([
                'tipe' => $request->tipe,
                'tanggal' => $request->tanggal,
                'vendor' => $request->vendor,
                'supir' => $request->supir,
                'no_plat' => $request->no_plat,
                'antar_lokasi' => $request->has('antar_lokasi') ? 1 : 0,
                'nomor_kontainer' => $request->nomor_kontainer,
                'ukuran' => $kontainer->ukuran ?? $suratJalan->ukuran,
                'tipe_kontainer' => $kontainer->tipe_kontainer ?? $suratJalan->tipe_kontainer,
                'vendor_item' => $kontainer->vendor ?? $suratJalan->vendor_item,
                'menggunakan_rit' => $request->boolean('menggunakan_rit'),
                'kondisi' => $request->kondisi,
                'catatan_kondisi' => $request->catatan_kondisi,
                'lokasi_pengambilan' => $request->lokasi_pengambilan,
                'lokasi_pengembalian' => $request->lokasi_pengembalian,
                'tujuan' => $request->tujuan,
                'nominal_uang_jalan' => $request->nominal_uang_jalan,
                'keterangan' => $request->keterangan,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('surat-jalan-kontainer-sewa.show', $suratJalan->id)
                             ->with('success', "Surat Jalan {$suratJalan->nomor_surat_jalan} berhasil diperbarui.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating SJ Kontainer Sewa: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui surat jalan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified surat jalan.
     */
    public function show($id)
    {
        $suratJalan = SuratJalanKontainerSewa::with('createdByUser')->findOrFail($id);
        return view('surat-jalan-kontainer-sewa.show', compact('suratJalan'));
    }

    /**
     * Print the specified surat jalan.
     */
    public function print($id)
    {
        $suratJalan = SuratJalanKontainerSewa::findOrFail($id);
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
