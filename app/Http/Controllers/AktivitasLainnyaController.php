<?php

namespace App\Http\Controllers;

use App\Models\AktivitasLainnya;
use App\Models\VendorBengkel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AktivitasLainnyaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AktivitasLainnya::with(['createdBy', 'approvedBy', 'vendor', 'pembayaran']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('tanggal_aktivitas', [$request->date_from, $request->date_to]);
        } elseif ($request->filled('date_from')) {
            $query->where('tanggal_aktivitas', '>=', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $query->where('tanggal_aktivitas', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nomor_aktivitas', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi_aktivitas', 'like', '%' . $request->search . '%');
            });
        }

        $aktivitas = $query->latest()->paginate(20);

        return view('aktivitas-lainnya.index', compact('aktivitas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendors = VendorBengkel::where('status', 'active')->get();
        $statusOptions = AktivitasLainnya::getStatusOptions();
        $kategoriOptions = AktivitasLainnya::getKategoriOptions();

        return view('aktivitas-lainnya.create', compact('vendors', 'statusOptions', 'kategoriOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_aktivitas' => 'required|date',
            'deskripsi_aktivitas' => 'required|string|max:1000',
            'kategori' => 'required|in:operasional,maintenance,administrasi,transport,lainnya',
            'vendor_id' => 'nullable|exists:vendor_bengkel,id',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $aktivitas = new AktivitasLainnya();
            $aktivitas->nomor_aktivitas = AktivitasLainnya::generateNomorAktivitas();
            $aktivitas->tanggal_aktivitas = $request->tanggal_aktivitas;
            $aktivitas->deskripsi_aktivitas = $request->deskripsi_aktivitas;
            $aktivitas->kategori = $request->kategori;
            $aktivitas->vendor_id = $request->vendor_id;
            $aktivitas->nominal = $request->nominal;
            $aktivitas->status = 'draft';
            $aktivitas->keterangan = $request->keterangan;
            $aktivitas->created_by = Auth::id();
            $aktivitas->save();

            DB::commit();

            return redirect()->route('aktivitas-lainnya.index')
                ->with('success', 'Aktivitas lainnya berhasil dibuat dengan nomor: ' . $aktivitas->nomor_aktivitas);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create aktivitas lainnya', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Gagal membuat aktivitas lainnya: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AktivitasLainnya $aktivitasLainnya)
    {
        $aktivitasLainnya->load(['creator', 'approver', 'vendor', 'pembayaran']);
        return view('aktivitas-lainnya.show', compact('aktivitasLainnya'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AktivitasLainnya $aktivitasLainnya)
    {
        // Hanya bisa edit jika status draft atau rejected
        if (!in_array($aktivitasLainnya->status, ['draft', 'rejected'])) {
            return redirect()->route('aktivitas-lainnya.index')
                ->with('error', 'Aktivitas dengan status ' . $aktivitasLainnya->status . ' tidak dapat diedit');
        }

        $vendors = VendorBengkel::where('status', 'active')->get();
        $statusOptions = AktivitasLainnya::getStatusOptions();
        $kategoriOptions = AktivitasLainnya::getKategoriOptions();

        return view('aktivitas-lainnya.edit', compact('aktivitasLainnya', 'vendors', 'statusOptions', 'kategoriOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AktivitasLainnya $aktivitasLainnya)
    {
        // Hanya bisa update jika status draft atau rejected
        if (!in_array($aktivitasLainnya->status, ['draft', 'rejected'])) {
            return redirect()->route('aktivitas-lainnya.index')
                ->with('error', 'Aktivitas dengan status ' . $aktivitasLainnya->status . ' tidak dapat diupdate');
        }

        $request->validate([
            'tanggal_aktivitas' => 'required|date',
            'deskripsi_aktivitas' => 'required|string|max:1000',
            'kategori' => 'required|in:operasional,maintenance,administrasi,transport,lainnya',
            'vendor_id' => 'nullable|exists:vendor_bengkel,id',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $aktivitasLainnya->tanggal_aktivitas = $request->tanggal_aktivitas;
            $aktivitasLainnya->deskripsi_aktivitas = $request->deskripsi_aktivitas;
            $aktivitasLainnya->kategori = $request->kategori;
            $aktivitasLainnya->vendor_id = $request->vendor_id;
            $aktivitasLainnya->nominal = $request->nominal;
            $aktivitasLainnya->keterangan = $request->keterangan;
            $aktivitasLainnya->save();

            DB::commit();

            return redirect()->route('aktivitas-lainnya.index')
                ->with('success', 'Aktivitas lainnya berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update aktivitas lainnya', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Gagal mengupdate aktivitas lainnya: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AktivitasLainnya $aktivitasLainnya)
    {
        // Hanya bisa delete jika status draft
        if ($aktivitasLainnya->status !== 'draft') {
            return redirect()->route('aktivitas-lainnya.index')
                ->with('error', 'Hanya aktivitas dengan status draft yang dapat dihapus');
        }

        DB::beginTransaction();
        try {
            $aktivitasLainnya->delete();
            DB::commit();

            return redirect()->route('aktivitas-lainnya.index')
                ->with('success', 'Aktivitas lainnya berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete aktivitas lainnya', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Gagal menghapus aktivitas lainnya: ' . $e->getMessage());
        }
    }

    /**
     * Submit aktivitas for approval
     */
    public function submitForApproval(AktivitasLainnya $aktivitasLainnya)
    {
        if ($aktivitasLainnya->status !== 'draft') {
            return redirect()->route('aktivitas-lainnya.index')
                ->with('error', 'Hanya aktivitas dengan status draft yang dapat disubmit untuk approval');
        }

        DB::beginTransaction();
        try {
            $aktivitasLainnya->status = 'pending';
            $aktivitasLainnya->save();

            DB::commit();

            return redirect()->route('aktivitas-lainnya.index')
                ->with('success', 'Aktivitas berhasil disubmit untuk approval');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit aktivitas for approval', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Gagal submit aktivitas untuk approval: ' . $e->getMessage());
        }
    }

    /**
     * Approve aktivitas
     */
    public function approve(AktivitasLainnya $aktivitasLainnya)
    {
        if ($aktivitasLainnya->status !== 'pending') {
            return redirect()->route('aktivitas-lainnya.index')
                ->with('error', 'Hanya aktivitas dengan status pending yang dapat diapprove');
        }

        DB::beginTransaction();
        try {
            $aktivitasLainnya->status = 'approved';
            $aktivitasLainnya->approved_by = Auth::id();
            $aktivitasLainnya->approved_at = now();
            $aktivitasLainnya->save();

            DB::commit();

            return redirect()->route('aktivitas-lainnya.index')
                ->with('success', 'Aktivitas berhasil diapprove');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve aktivitas', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Gagal approve aktivitas: ' . $e->getMessage());
        }
    }

    /**
     * Reject aktivitas
     */
    public function reject(Request $request, AktivitasLainnya $aktivitasLainnya)
    {
        if ($aktivitasLainnya->status !== 'pending') {
            return redirect()->route('aktivitas-lainnya.index')
                ->with('error', 'Hanya aktivitas dengan status pending yang dapat direject');
        }

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $aktivitasLainnya->status = 'rejected';
            $aktivitasLainnya->keterangan = ($aktivitasLainnya->keterangan ? $aktivitasLainnya->keterangan . "\n\n" : '') .
                                          'REJECTED: ' . $request->reason;
            $aktivitasLainnya->approved_by = Auth::id();
            $aktivitasLainnya->approved_at = now();
            $aktivitasLainnya->save();

            DB::commit();

            return redirect()->route('aktivitas-lainnya.index')
                ->with('success', 'Aktivitas berhasil direject');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject aktivitas', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Gagal reject aktivitas: ' . $e->getMessage());
        }
    }
}
