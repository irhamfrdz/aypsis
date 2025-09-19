<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\KodeNomor;
use App\Exports\MasterCoaTemplateExport;
use App\Imports\MasterCoaImport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterCoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coas = Coa::orderBy('nomor_akun')->paginate(15);

        return view('master-coa.index', compact('coas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tipeAkuns = \App\Models\TipeAkun::orderBy('tipe_akun')->get();
        $kodeNomors = KodeNomor::orderBy('kode')->get();
        return view('master-coa.create', compact('tipeAkuns', 'kodeNomors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_akun' => 'required|string|max:20|unique:akun_coa,nomor_akun',
            'kode_nomor' => 'nullable|string|max:50',
            'nama_akun' => 'required|string|max:255',
            'tipe_akun' => 'required|string|max:50',
            'saldo' => 'nullable|numeric|min:0',
        ]);

        Coa::create([
            'nomor_akun' => $request->nomor_akun,
            'kode_nomor' => $request->kode_nomor,
            'nama_akun' => $request->nama_akun,
            'tipe_akun' => $request->tipe_akun,
            'saldo' => $request->saldo ?? 0,
        ]);

        return redirect()->route('master-coa-index')
            ->with('success', 'COA berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coa $coa)
    {
        return view('master-coa.show', compact('coa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coa $coa)
    {
        $tipeAkuns = \App\Models\TipeAkun::orderBy('tipe_akun')->get();
        $kodeNomors = KodeNomor::orderBy('kode')->get();
        return view('master-coa.edit', compact('coa', 'tipeAkuns', 'kodeNomors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coa $coa)
    {
        $request->validate([
            'nomor_akun' => ['required', 'string', 'max:20', Rule::unique('akun_coa')->ignore($coa->id)],
            'kode_nomor' => 'nullable|string|max:50',
            'nama_akun' => 'required|string|max:255',
            'tipe_akun' => 'required|string|max:50',
            'saldo' => 'nullable|numeric|min:0',
        ]);

        $coa->update([
            'nomor_akun' => $request->nomor_akun,
            'kode_nomor' => $request->kode_nomor,
            'nama_akun' => $request->nama_akun,
            'tipe_akun' => $request->tipe_akun,
            'saldo' => $request->saldo ?? 0,
        ]);

        return redirect()->route('master-coa-index')
            ->with('success', 'COA berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coa $coa)
    {
        $coa->delete();

        return redirect()->route('master-coa-index')
            ->with('success', 'COA berhasil dihapus.');
    }

    /**
     * Download template Excel untuk import COA
     */
    public function downloadTemplate()
    {
        $export = new MasterCoaTemplateExport();
        return $export->download();
    }

    /**
     * Import data COA dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:2048'
        ]);

        try {
            $import = new MasterCoaImport();
            $result = $import->import($request->file('file'));

            if ($result['success_count'] > 0) {
                $message = "Berhasil mengimport {$result['success_count']} data COA";
                if (!empty($result['errors'])) {
                    $message .= ". Namun ada " . count($result['errors']) . " error: " . implode('; ', $result['errors']);
                }
                return redirect()->route('master-coa-index')->with('success', $message);
            } else {
                return redirect()->route('master-coa-index')->with('error', 'Gagal mengimport data: ' . implode('; ', $result['errors']));
            }
        } catch (\Exception $e) {
            return redirect()->route('master-coa-index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
