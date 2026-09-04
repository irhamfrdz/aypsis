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
            'jenis' => 'required|array',
            'jenis.*' => 'required|in:jkn,jamsostek',
            'group_name' => 'required|array',
            'group_name.*' => 'required|string|max:255',
            'tunjangan_persen' => 'nullable|array',
            'tunjangan_persen.*' => 'nullable|numeric|min:0',
            'hutang_persen' => 'nullable|array',
            'hutang_persen.*' => 'nullable|numeric|min:0',
            'biaya_persen' => 'nullable|array',
            'biaya_persen.*' => 'nullable|numeric|min:0',
            'keterangan_custom' => 'nullable|array',
            'keterangan_custom.*' => 'nullable|string|max:255',
            'jht_biaya' => 'nullable|array',
            'jht_biaya.*' => 'nullable|numeric|min:0',
            'jht_hutang' => 'nullable|array',
            'jht_hutang.*' => 'nullable|numeric|min:0',
            'jkk_tunjangan' => 'nullable|array',
            'jkk_tunjangan.*' => 'nullable|numeric|min:0',
            'jkm_tunjangan' => 'nullable|array',
            'jkm_tunjangan.*' => 'nullable|numeric|min:0',
            'jp_biaya' => 'nullable|array',
            'jp_biaya.*' => 'nullable|numeric|min:0',
            'jp_hutang' => 'nullable|array',
            'jp_hutang.*' => 'nullable|numeric|min:0',
        ]);

        foreach ($request->jenis as $key => $jenis) {
            if (!empty($request->group_name[$key])) {
                MasterRumusBpjs::create([
                    'jenis' => $jenis,
                    'group_name' => $request->group_name[$key],
                    'tunjangan_persen' => $request->tunjangan_persen[$key] ?? null,
                    'hutang_persen' => $request->hutang_persen[$key] ?? null,
                    'biaya_persen' => $request->biaya_persen[$key] ?? null,
                    'keterangan_custom' => $request->keterangan_custom[$key] ?? null,
                    'jht_biaya' => $request->jht_biaya[$key] ?? null,
                    'jht_hutang' => $request->jht_hutang[$key] ?? null,
                    'jkk_tunjangan' => $request->jkk_tunjangan[$key] ?? null,
                    'jkm_tunjangan' => $request->jkm_tunjangan[$key] ?? null,
                    'jp_biaya' => $request->jp_biaya[$key] ?? null,
                    'jp_hutang' => $request->jp_hutang[$key] ?? null,
                ]);
            }
        }

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
            'jht_biaya' => 'nullable|numeric|min:0',
            'jht_hutang' => 'nullable|numeric|min:0',
            'jkk_tunjangan' => 'nullable|numeric|min:0',
            'jkm_tunjangan' => 'nullable|numeric|min:0',
            'jp_biaya' => 'nullable|numeric|min:0',
            'jp_hutang' => 'nullable|numeric|min:0',
        ]);

        $rumus = MasterRumusBpjs::findOrFail($id);
        $rumus->update([
            'group_name' => $request->group_name,
            'tunjangan_persen' => $request->tunjangan_persen,
            'hutang_persen' => $request->hutang_persen,
            'biaya_persen' => $request->biaya_persen,
            'keterangan_custom' => $request->keterangan_custom,
            'jht_biaya' => $request->jht_biaya,
            'jht_hutang' => $request->jht_hutang,
            'jkk_tunjangan' => $request->jkk_tunjangan,
            'jkm_tunjangan' => $request->jkm_tunjangan,
            'jp_biaya' => $request->jp_biaya,
            'jp_hutang' => $request->jp_hutang,
        ]);

        return redirect()->route('master-rumus-bpjs.index')->with('success', 'Data rumus berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $rumus = MasterRumusBpjs::findOrFail($id);
        $rumus->delete();

        return redirect()->route('master-rumus-bpjs.index')->with('success', 'Data rumus berhasil dihapus.');
    }
}
