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
            'cabang_bpjs' => 'nullable|array',
            'cabang_bpjs.*' => 'nullable|string|max:255',
            'tunjangan_persen' => 'nullable|array',
            'tunjangan_persen.*' => 'nullable|numeric|min:0',
            'hutang_persen' => 'nullable|array',
            'hutang_persen.*' => 'nullable|numeric|min:0',
            'biaya_persen' => 'nullable|array',
            'biaya_persen.*' => 'nullable|numeric|min:0',
            'keterangan_custom' => 'nullable|array',
            'keterangan_custom.*' => 'nullable|string|max:255',
            'diskon_status' => 'nullable|array',
            'diskon_status.*' => 'nullable|string|in:tidak_ada,ada',
            'diskon_tipe' => 'nullable|array',
            'diskon_tipe.*' => 'nullable|string|in:persen,nominal',
            'diskon_nilai' => 'nullable|array',
            'diskon_nilai.*' => 'nullable|numeric|min:0',
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
                    'cabang_bpjs' => $request->cabang_bpjs[$key] ?? null,
                    'tunjangan_persen' => $request->tunjangan_persen[$key] ?? null,
                    'hutang_persen' => $request->hutang_persen[$key] ?? null,
                    'biaya_persen' => $request->biaya_persen[$key] ?? null,
                    'keterangan_custom' => $request->keterangan_custom[$key] ?? null,
                    'diskon_status' => $request->diskon_status[$key] ?? 'tidak_ada',
                    'diskon_tipe' => $request->diskon_tipe[$key] ?? 'persen',
                    'diskon_nilai' => ($request->diskon_status[$key] ?? 'tidak_ada') === 'ada' ? ($request->diskon_nilai[$key] ?? 0) : 0,
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
            'jenis' => 'sometimes|required|in:jkn,jamsostek',
            'group_name' => 'sometimes|required|string|max:255',
            'cabang_bpjs' => 'nullable|string|max:255',
            'tipe_rumus' => 'nullable|in:nominal,persentase',
            'nilai' => 'nullable|numeric|min:0',
            'tunjangan_persen' => 'nullable|numeric|min:0',
            'hutang_persen' => 'nullable|numeric|min:0',
            'biaya_persen' => 'nullable|numeric|min:0',
            'keterangan_custom' => 'nullable|string|max:255',
            'diskon_status' => 'nullable|string|in:tidak_ada,ada',
            'diskon_tipe' => 'nullable|string|in:persen,nominal',
            'diskon_nilai' => 'nullable|numeric|min:0',
            'jht_biaya' => 'nullable|numeric|min:0',
            'jht_hutang' => 'nullable|numeric|min:0',
            'jkk_tunjangan' => 'nullable|numeric|min:0',
            'jkm_tunjangan' => 'nullable|numeric|min:0',
            'jp_biaya' => 'nullable|numeric|min:0',
            'jp_hutang' => 'nullable|numeric|min:0',
        ]);

        $rumus = MasterRumusBpjs::findOrFail($id);

        $dataToUpdate = [];
        if ($request->has('group_name')) $dataToUpdate['group_name'] = $request->group_name;
        if ($request->has('cabang_bpjs')) $dataToUpdate['cabang_bpjs'] = $request->cabang_bpjs;
        if ($request->has('jenis')) $dataToUpdate['jenis'] = $request->jenis;
        if ($request->has('tunjangan_persen')) $dataToUpdate['tunjangan_persen'] = $request->tunjangan_persen;
        if ($request->has('hutang_persen')) $dataToUpdate['hutang_persen'] = $request->hutang_persen;
        if ($request->has('biaya_persen')) $dataToUpdate['biaya_persen'] = $request->biaya_persen;
        if ($request->has('keterangan_custom')) $dataToUpdate['keterangan_custom'] = $request->keterangan_custom;
        if ($request->has('jht_biaya')) $dataToUpdate['jht_biaya'] = $request->jht_biaya;
        if ($request->has('jht_hutang')) $dataToUpdate['jht_hutang'] = $request->jht_hutang;
        if ($request->has('jkk_tunjangan')) $dataToUpdate['jkk_tunjangan'] = $request->jkk_tunjangan;
        if ($request->has('jkm_tunjangan')) $dataToUpdate['jkm_tunjangan'] = $request->jkm_tunjangan;
        if ($request->has('jp_biaya')) $dataToUpdate['jp_biaya'] = $request->jp_biaya;
        if ($request->has('jp_hutang')) $dataToUpdate['jp_hutang'] = $request->jp_hutang;
        if ($request->has('diskon_status')) {
            $dataToUpdate['diskon_status'] = $request->diskon_status;
            if ($request->diskon_status === 'tidak_ada') {
                $dataToUpdate['diskon_nilai'] = 0;
            }
        }
        if ($request->has('diskon_tipe')) $dataToUpdate['diskon_tipe'] = $request->diskon_tipe;
        if ($request->has('diskon_nilai')) $dataToUpdate['diskon_nilai'] = $request->diskon_nilai;

        $rumus->update($dataToUpdate);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data rumus berhasil diperbarui.',
                'data' => $rumus,
            ]);
        }

        return redirect()->route('master-rumus-bpjs.index')->with('success', 'Data rumus berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $rumus = MasterRumusBpjs::findOrFail($id);
        $rumus->delete();

        return redirect()->route('master-rumus-bpjs.index')->with('success', 'Data rumus berhasil dihapus.');
    }
}
