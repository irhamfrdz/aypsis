<?php

namespace App\Http\Controllers;

use App\Models\KodeNomor;
use App\Models\TipeAkun;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KodeNomorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = KodeNomor::query();

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where('kode', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_akun', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_akun', 'like', '%' . $request->search . '%')
                  ->orWhere('catatan', 'like', '%' . $request->search . '%');
        }

        $kodeNomors = $query->orderBy('kode')->paginate(15);

        return view('master.kode-nomor.index', compact('kodeNomors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tipeAkuns = TipeAkun::orderBy('tipe_akun')->get();
        return view('master.kode-nomor.create', compact('tipeAkuns'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:kode_nomor,kode',
            'nomor_akun' => 'nullable|string|max:50',
            'nama_akun' => 'nullable|string|max:255',
            'tipe_akun' => 'nullable|string|max:100',
            'saldo' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string|max:1000'
        ]);

        KodeNomor::create($request->all());

        return redirect()->route('master.kode-nomor.index')
                        ->with('success', 'Kode Nomor berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(KodeNomor $kodeNomor)
    {
        return view('master.kode-nomor.show', compact('kodeNomor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KodeNomor $kodeNomor)
    {
        $tipeAkuns = TipeAkun::orderBy('tipe_akun')->get();
        return view('master.kode-nomor.edit', compact('kodeNomor', 'tipeAkuns'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KodeNomor $kodeNomor)
    {
        $request->validate([
            'kode' => ['required', 'string', 'max:50', Rule::unique('kode_nomor')->ignore($kodeNomor->id)],
            'nomor_akun' => 'nullable|string|max:50',
            'nama_akun' => 'nullable|string|max:255',
            'tipe_akun' => 'nullable|string|max:100',
            'saldo' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string|max:1000'
        ]);

        $kodeNomor->update($request->all());

        return redirect()->route('master.kode-nomor.index')
                        ->with('success', 'Kode Nomor berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KodeNomor $kodeNomor)
    {
        $kodeNomor->delete();

        return redirect()->route('master.kode-nomor.index')
                        ->with('success', 'Kode Nomor berhasil dihapus.');
    }
}
