<?php

namespace App\Http\Controllers;

use App\Models\MasterRumusBpjs;
use Illuminate\Http\Request;

class MasterRumusBpjsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:master-rumus-bpjs-view')->only(['index']);
        $this->middleware('permission:master-rumus-bpjs-create')->only(['store']);
        $this->middleware('permission:master-rumus-bpjs-update')->only(['update']);
        $this->middleware('permission:master-rumus-bpjs-delete')->only(['destroy']);
    }

    public function index()
    {
        $rumusJkn = MasterRumusBpjs::where('jenis', 'jkn')->get();
        $rumusJamsostek = MasterRumusBpjs::where('jenis', 'jamsostek')->get();
        return view('master.rumus-bpjs.index', compact('rumusJkn', 'rumusJamsostek'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:jkn,jamsostek',
            'group_name' => 'required|string|max:255',
            'tipe_rumus' => 'nullable|in:nominal,persentase',
            'nilai' => 'nullable|numeric|min:0',
            'tunjangan_persen' => 'nullable|numeric|min:0',
            'hutang_persen' => 'nullable|numeric|min:0',
            'biaya_persen' => 'nullable|numeric|min:0',
            'keterangan_custom' => 'nullable|string|max:255',
        ]);

        MasterRumusBpjs::create($request->all());

        return redirect()->route('master-rumus-bpjs.index')->with('success', 'Data rumus berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis' => 'required|in:jkn,jamsostek',
            'group_name' => 'required|string|max:255',
            'tipe_rumus' => 'nullable|in:nominal,persentase',
            'nilai' => 'nullable|numeric|min:0',
            'tunjangan_persen' => 'nullable|numeric|min:0',
            'hutang_persen' => 'nullable|numeric|min:0',
            'biaya_persen' => 'nullable|numeric|min:0',
            'keterangan_custom' => 'nullable|string|max:255',
        ]);

        $rumus = MasterRumusBpjs::findOrFail($id);
        $rumus->update($request->all());

        return redirect()->route('master-rumus-bpjs.index')->with('success', 'Data rumus berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $rumus = MasterRumusBpjs::findOrFail($id);
        $rumus->delete();

        return redirect()->route('master-rumus-bpjs.index')->with('success', 'Data rumus berhasil dihapus.');
    }
}
