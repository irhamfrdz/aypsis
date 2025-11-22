<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ApprovalSuratJalanController extends Controller
{
    /**
     * Display a listing of approval surat jalan.
     */
    public function index(Request $request)
    {
        $query = SuratJalan::with(['order', 'tujuanPengambilanRelation', 'tujuanPengirimanRelation']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat_jalan', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('jenis_barang', 'like', "%{$search}%")
                  ->orWhere('no_kontainer', 'like', "%{$search}%")
                  ->orWhere('supir', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_surat_jalan', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_surat_jalan', '<=', $request->end_date);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Pagination
        $perPage = $request->get('per_page', 50);
        $suratJalans = $query->paginate($perPage)->withQueryString();

        return view('approval-surat-jalan.index', compact('suratJalans'));
    }

    /**
     * Show the form for creating a new approval.
     */
    public function create()
    {
        // Get surat jalan yang belum ada term-nya
        $suratJalans = SuratJalan::with(['order'])
            ->whereDoesntHave('term')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('approval-surat-jalan.create', compact('suratJalans'));
    }

    /**
     * Store a newly created approval (add term to surat jalan).
     */
    public function store(Request $request)
    {
        $request->validate([
            'surat_jalan_id' => 'required|exists:surat_jalan,id',
            'term_name' => 'required|string|max:255',
            'term_days' => 'required|integer|min:0',
            'description' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Create term
            $term = Term::create([
                'name' => $request->term_name,
                'days' => $request->term_days,
                'description' => $request->description,
                'created_by' => Auth::id()
            ]);

            // Update surat jalan with term
            $suratJalan = SuratJalan::findOrFail($request->surat_jalan_id);
            $suratJalan->term_id = $term->id;
            $suratJalan->save();

            DB::commit();

            return redirect()->route('approval-surat-jalan.index')
                           ->with('success', 'Term berhasil ditambahkan ke Surat Jalan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Gagal menambahkan term: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Display the specified approval.
     */
    public function show($id)
    {
        $suratJalan = SuratJalan::with(['order', 'term', 'tujuanPengambilanRelation', 'tujuanPengirimanRelation'])
                               ->findOrFail($id);

        return view('approval-surat-jalan.show', compact('suratJalan'));
    }

    /**
     * Show the form for editing the specified approval.
     */
    public function edit($id)
    {
        $suratJalan = SuratJalan::with(['order', 'term'])->findOrFail($id);
        $terms = Term::orderBy('kode')->get();

        return view('approval-surat-jalan.edit', compact('suratJalan', 'terms'));
    }

    /**
     * Update the specified approval.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'term_id' => 'required|exists:terms,id'
        ]);

        try {
            $suratJalan = SuratJalan::findOrFail($id);
            $suratJalan->term_id = $request->term_id;
            $suratJalan->save();

            return redirect()->route('approval-surat-jalan.index')
                           ->with('success', 'Term Surat Jalan berhasil diupdate');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal mengupdate term: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Remove the specified approval.
     */
    public function destroy($id)
    {
        try {
            $suratJalan = SuratJalan::findOrFail($id);
            $suratJalan->term_id = null;
            $suratJalan->save();

            return redirect()->route('approval-surat-jalan.index')
                           ->with('success', 'Term berhasil dihapus dari Surat Jalan');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menghapus term: ' . $e->getMessage());
        }
    }

    /**
     * Approve the surat jalan.
     */
    public function approve($id)
    {
        try {
            $suratJalan = SuratJalan::findOrFail($id);
            $suratJalan->status = 'approved';
            $suratJalan->approved_by = Auth::id();
            $suratJalan->approved_at = Carbon::now();
            $suratJalan->save();

            return redirect()->route('approval-surat-jalan.index')
                           ->with('success', 'Surat Jalan berhasil disetujui');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menyetujui Surat Jalan: ' . $e->getMessage());
        }
    }

    /**
     * Reject the surat jalan.
     */
    public function reject($id)
    {
        try {
            $suratJalan = SuratJalan::findOrFail($id);
            $suratJalan->status = 'rejected';
            $suratJalan->rejected_by = Auth::id();
            $suratJalan->rejected_at = Carbon::now();
            $suratJalan->save();

            return redirect()->route('approval-surat-jalan.index')
                           ->with('success', 'Surat Jalan berhasil ditolak');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menolak Surat Jalan: ' . $e->getMessage());
        }
    }
}
