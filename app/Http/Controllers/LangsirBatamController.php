<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\LangsirBatam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LangsirBatamController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:langsir-batam-view')->only(['index', 'show']);
        $this->middleware('permission:langsir-batam-create')->only(['create', 'store']);
        $this->middleware('permission:langsir-batam-update')->only(['edit', 'update']);
        $this->middleware('permission:langsir-batam-delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $tanggal_dari = $request->get('tanggal_dari', '');
        $tanggal_sampai = $request->get('tanggal_sampai', '');

        $query = LangsirBatam::with('user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                    ->orWhere('no_kontainer', 'like', "%{$search}%")
                    ->orWhere('supir', 'like', "%{$search}%");
            });
        }

        if ($tanggal_dari) {
            $query->whereDate('tanggal', '>=', $tanggal_dari);
        }
        if ($tanggal_sampai) {
            $query->whereDate('tanggal', '<=', $tanggal_sampai);
        }

        $langsirs = $query->orderBy('tanggal', 'desc')->paginate(15);

        return view('langsir-batam.index', compact('langsirs', 'search', 'tanggal_dari', 'tanggal_sampai'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $no_transaksi = LangsirBatam::generateNoTransaksi();
        $supirs = Karyawan::where('pekerjaan', 'LIKE', '%supir%')
            ->orWhere('divisi', 'LIKE', '%supir%')
            ->orderBy('nama_panggilan', 'asc')
            ->get();

        $kontainers = \App\Models\Kontainer::select('nomor_seri_gabungan as no_kontainer', 'ukuran as size')->get();
        $stock_kontainers = \App\Models\StockKontainer::select('nomor_seri_gabungan as no_kontainer', 'ukuran as size')->get();
        $all_kontainers = $kontainers->concat($stock_kontainers)->unique('no_kontainer')->sortBy('no_kontainer');

        return view('langsir-batam.create', compact('no_transaksi', 'supirs', 'all_kontainers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_transaksi' => 'required|unique:langsir_batams,no_transaksi',
            'tanggal' => 'required|date',
            'no_kontainer' => 'required|string',
            'size' => 'required|string',
            'no_seal' => 'nullable|string',
            'dari' => 'required|string',
            'ke' => 'required|string',
            'no_plat' => 'nullable|string',
            'supir' => 'nullable|string',
            'biaya' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $validated['input_by'] = Auth::id();

        LangsirBatam::create($validated);

        return redirect()->route('langsir-batam.index')->with('success', 'Data Langsir Batam berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $langsir = LangsirBatam::with('user')->findOrFail($id);

        return view('langsir-batam.show', compact('langsir'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $langsir = LangsirBatam::findOrFail($id);
        $supirs = Karyawan::where('pekerjaan', 'LIKE', '%supir%')
            ->orWhere('divisi', 'LIKE', '%supir%')
            ->orderBy('nama_panggilan', 'asc')
            ->get();

        $kontainers = \App\Models\Kontainer::select('nomor_seri_gabungan as no_kontainer', 'ukuran as size')->get();
        $stock_kontainers = \App\Models\StockKontainer::select('nomor_seri_gabungan as no_kontainer', 'ukuran as size')->get();
        $all_kontainers = $kontainers->concat($stock_kontainers)->unique('no_kontainer')->sortBy('no_kontainer');

        return view('langsir-batam.edit', compact('langsir', 'supirs', 'all_kontainers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $langsir = LangsirBatam::findOrFail($id);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'no_kontainer' => 'required|string',
            'size' => 'required|string',
            'no_seal' => 'nullable|string',
            'dari' => 'required|string',
            'ke' => 'required|string',
            'no_plat' => 'nullable|string',
            'supir' => 'nullable|string',
            'biaya' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $langsir->update($validated);

        return redirect()->route('langsir-batam.index')->with('success', 'Data Langsir Batam berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $langsir = LangsirBatam::findOrFail($id);
        $langsir->delete();

        return redirect()->route('langsir-batam.index')->with('success', 'Data Langsir Batam berhasil dihapus.');
    }
}
