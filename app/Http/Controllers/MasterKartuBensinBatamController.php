<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\MasterKartuBensinBatam;
use App\Models\Mobil;
use Illuminate\Http\Request;

class MasterKartuBensinBatamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterKartuBensinBatam::with(['mobil', 'karyawan', 'createdBy', 'updatedBy']);

        // Search filter
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_kartu', 'like', "%{$search}%")
                    ->orWhere('nama_kartu', 'like', "%{$search}%")
                    ->orWhere('provider', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('mobil', function ($mq) use ($search) {
                        $mq->where('nomor_polisi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('karyawan', function ($kq) use ($search) {
                        $kq->where('nama_lengkap', 'like', "%{$search}%")
                            ->orWhere('nama_panggilan', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Statistics for summary cards
        $statsQuery = clone $query;
        $totalCards = MasterKartuBensinBatam::count();
        $activeCards = MasterKartuBensinBatam::where('status', 'aktif')->count();
        $inactiveCards = MasterKartuBensinBatam::where('status', 'tidak_aktif')->count();

        $items = $query->paginate(20)->appends($request->all());

        return view('master-kartu-bensin-batam.index', compact('items', 'totalCards', 'activeCards', 'inactiveCards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        $karyawans = Karyawan::orderBy('nama_lengkap')->get();

        return view('master-kartu-bensin-batam.create', compact('mobils', 'karyawans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_kartu' => 'required|string|max:255|unique:master_kartu_bensin_batams,nomor_kartu',
            'nama_kartu' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'mobil_id' => 'nullable|exists:mobils,id',
            'karyawan_id' => 'nullable|exists:karyawans,id',
            'status' => 'required|in:aktif,tidak_aktif',
            'saldo' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $validated['saldo'] = $validated['saldo'] ?? 0;
        $validated['created_by'] = auth()->id();

        MasterKartuBensinBatam::create($validated);

        return redirect()->route('master-kartu-bensin-batam.index')
            ->with('success', 'Data kartu bensin Batam berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = MasterKartuBensinBatam::findOrFail($id);
        $mobils = Mobil::orderBy('nomor_polisi')->get();
        $karyawans = Karyawan::orderBy('nama_lengkap')->get();

        return view('master-kartu-bensin-batam.edit', compact('item', 'mobils', 'karyawans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = MasterKartuBensinBatam::findOrFail($id);

        $validated = $request->validate([
            'nomor_kartu' => 'required|string|max:255|unique:master_kartu_bensin_batams,nomor_kartu,'.$item->id,
            'nama_kartu' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'mobil_id' => 'nullable|exists:mobils,id',
            'karyawan_id' => 'nullable|exists:karyawans,id',
            'status' => 'required|in:aktif,tidak_aktif',
            'saldo' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $validated['saldo'] = $validated['saldo'] ?? 0;
        $validated['updated_by'] = auth()->id();

        $item->update($validated);

        return redirect()->route('master-kartu-bensin-batam.index')
            ->with('success', 'Data kartu bensin Batam berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = MasterKartuBensinBatam::findOrFail($id);
        $item->delete();

        return redirect()->route('master-kartu-bensin-batam.index')
            ->with('success', 'Data kartu bensin Batam berhasil dihapus.');
    }
}
