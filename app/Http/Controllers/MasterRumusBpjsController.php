<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\KaryawanTidakTetap;
use App\Models\MasterRumusBpjs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MasterRumusBpjsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:master-rumus-bpjs-view')->only(['index']);
        $this->middleware('permission:master-rumus-bpjs-create')->only(['store']);
        $this->middleware('permission:master-rumus-bpjs-update')->only(['update', 'discountEmployees', 'saveDiscountExclusions']);
        $this->middleware('permission:master-rumus-bpjs-delete')->only(['destroy']);
    }

    public function index()
    {
        $rumusJkn = MasterRumusBpjs::where('jenis', 'jkn')->get();
        $rumusJamsostek = MasterRumusBpjs::where('jenis', 'jamsostek')->get();

        return view('master.rumus-bpjs.index', compact('rumusJkn', 'rumusJamsostek'));
    }

    private function discountRecords(Request $request, $id)
    {
        $request->validate(['existing_ids' => ['nullable', 'string', 'regex:/^\d+(,\d+)*$/']]);
        $primary = MasterRumusBpjs::findOrFail($id);
        $ids = array_unique(explode(',', $request->input('existing_ids') ?: (string) $id));
        $records = MasterRumusBpjs::whereIn('id', $ids)->get();
        $key = fn ($record) => [$record->jenis, $record->jamsostekCategory(),
            strtoupper(trim($record->tipe ?? '')), strtoupper(trim($record->cabang_bpjs ?? ''))];
        if (! in_array((string) $id, $ids, true) || count($ids) !== $records->count()
            || $primary->jenis !== 'jamsostek' || $primary->jamsostekCategory() === 'ppu'
            || $records->contains(fn ($record) => $key($record) !== $key($primary))
            || (count($ids) > 1 && empty($primary->cabang_bpjs))) {
            throw ValidationException::withMessages(['existing_ids' => 'Group rumus diskon tidak valid. Muat ulang halaman.']);
        }

        return $records;
    }

    private function discountCandidates($records)
    {
        $compact = fn ($value) => preg_replace('/[\s_-]+/', '', strtoupper((string) $value));
        $normalize = fn ($value) => preg_replace('/\s+/', ' ', strtoupper(trim((string) $value)));
        $allRules = MasterRumusBpjs::where('jenis', 'jamsostek')->get();
        $targetIds = $records->pluck('id')->all();
        $employees = collect();
        foreach ([Karyawan::class => 'Tetap', KaryawanTidakTetap::class => 'Tidak Tetap'] as $class => $label) {
            $rows = $class::query()->get(['id', 'nik', 'nama_lengkap', 'group_bp_jamsostek', 'tipe_bp_jamsostek', 'cabang_bpjs']);
            foreach ($rows as $employee) {
                // Resolve the same formula as the pranota calculator before offering a checkbox.
                $group = $compact($employee->group_bp_jamsostek);
                $type = $normalize($employee->tipe_bp_jamsostek);
                $branch = $normalize($employee->cabang_bpjs);
                $candidates = $allRules->filter(fn ($record) => $compact($record->group_name) === $group);
                if ($type !== '') {
                    $typed = $candidates->filter(fn ($record) => $normalize($record->tipe) === $type);
                    $category = in_array($type, ['CREW', 'BPU-CREW'], true) ? 'bpu_crew' : (str_contains($group, 'PPU') ? 'ppu' : 'bpu_non_crew');
                    $candidates = $typed->isNotEmpty() ? $typed : $candidates->filter(fn ($record) => ! $record->tipe && $record->jamsostekCategory() === $category);
                } elseif ($candidates->map(fn ($record) => $normalize($record->tipe))->unique()->count() > 1) {
                    continue;
                }
                $exact = $branch !== '' ? $candidates->filter(fn ($record) => $normalize($record->cabang_bpjs) === $branch) : collect();
                $general = $candidates->filter(fn ($record) => ! $record->cabang_bpjs);
                $resolved = $exact->isNotEmpty() ? $exact : ($general->isNotEmpty() ? $general : ($branch === '' ? $candidates : collect()));
                if ($resolved->count() !== 1 || ! in_array($resolved->first()->id, $targetIds, true)) {
                    continue;
                }
                $matching = $resolved;
                $employees->push([
                    'unique_id' => class_basename($class).'_'.$employee->id,
                    'nik' => $employee->nik, 'nama_lengkap' => $employee->nama_lengkap, 'tipe_label' => $label,
                    'group' => $employee->group_bp_jamsostek, 'cabang' => $employee->cabang_bpjs,
                    'formula_ids' => $matching->pluck('id')->all(),
                ]);
            }
        }

        return $employees->sortBy('nama_lengkap')->values();
    }

    public function discountEmployees(Request $request, $id)
    {
        $records = $this->discountRecords($request, $id);

        return response()->json([
            'employees' => $this->discountCandidates($records),
            'selected' => $records->flatMap(fn ($record) => $record->diskon_pengecualian ?? [])->unique()->values(),
            'groups' => $records->pluck('group_name')->unique()->values(),
        ])->header('Cache-Control', 'no-store');
    }

    public function saveDiscountExclusions(Request $request, $id)
    {
        $request->validate([
            'diskon_pengecualian' => 'present|array|max:5000',
            'diskon_pengecualian.*' => ['required', 'string', 'distinct', 'regex:/^Karyawan(TidakTetap)?_[1-9][0-9]*$/'],
        ]);
        $records = $this->discountRecords($request, $id);
        $employees = $this->discountCandidates($records);
        $selected = $request->input('diskon_pengecualian');
        if (array_diff($selected, $employees->pluck('unique_id')->all())) {
            throw ValidationException::withMessages(['diskon_pengecualian' => 'Karyawan harus sesuai group, tipe, dan cabang rumus.']);
        }
        DB::transaction(function () use ($records, $employees, $selected) {
            foreach ($records as $record) {
                $eligible = $employees->filter(fn ($employee) => in_array($record->id, $employee['formula_ids'], true))->pluck('unique_id')->all();
                $record->update(['diskon_pengecualian' => array_values(array_intersect($selected, $eligible))]);
            }
        });

        return response()->json(['success' => true, 'count' => count($selected)]);
    }

    public function store(Request $request)
    {
        $tipeRules = [];
        foreach (array_keys((array) $request->input('jenis', [])) as $key) {
            $tipeRules["tipe.$key"] = 'required|string|max:255';
            $tipeRules["group_name.$key"] = 'required';
        }

        $request->validate([
            'jenis' => 'required|array',
            'jenis.*' => 'required|in:jkn,jamsostek',
            'group_name' => 'required|array',
            'tipe' => 'required|array',
            'tipe.*' => 'required|string|max:255',
            'cabang_bpjs' => 'nullable|array',
            'cabang_bpjs.*' => 'nullable|string|max:255',
            'tunjangan_persen' => 'nullable|array',
            'tunjangan_persen.*' => 'nullable|numeric|min:0',
            'hutang_persen' => 'nullable|array',
            'hutang_persen.*' => 'nullable|numeric|min:0',
            'hutang_tiers' => 'nullable|array',
            'hutang_tiers.*' => 'nullable|string',
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
            'jp_max_dpp' => 'nullable|array',
            'jp_max_dpp.*' => 'nullable|numeric|min:0',
            'jp_max_age' => 'nullable|array',
            'jp_max_age.*' => 'nullable|integer|min:0',
        ] + $tipeRules);

        foreach ($request->jenis as $key => $jenis) {
            $rawGroupNames = $request->group_name[$key] ?? [];
            if (is_string($rawGroupNames)) {
                $rawGroupNames = explode(',', $rawGroupNames);
            }
            $groupNames = array_values(array_filter(array_map('trim', (array) $rawGroupNames)));

            if (! empty($groupNames)) {
                // Decode hutang_tiers JSON dari hidden input
                $tiersRaw = $request->hutang_tiers[$key] ?? null;
                $tiers = null;
                if ($tiersRaw) {
                    $decoded = json_decode($tiersRaw, true);
                    // Filter tier kosong
                    if (is_array($decoded)) {
                        $tiers = array_values(array_filter($decoded, fn ($t) => ! empty($t['dpp']) || ! empty($t['potongan'])));
                        $tiers = count($tiers) ? $tiers : null;
                    }
                }

                foreach ($groupNames as $gName) {
                    MasterRumusBpjs::create([
                        'jenis' => $jenis,
                        'group_name' => $gName,
                        'tipe' => $request->tipe[$key],
                        'cabang_bpjs' => $request->cabang_bpjs[$key] ?? null,
                        'tunjangan_persen' => $request->tunjangan_persen[$key] ?? null,
                        'hutang_persen' => $request->hutang_persen[$key] ?? null,
                        'hutang_tiers' => $tiers,
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
                        'jp_max_dpp' => $request->jp_max_dpp[$key] ?? null,
                        'jp_max_age' => $request->jp_max_age[$key] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('master-rumus-bpjs.index')->with('success', 'Data rumus berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis' => 'sometimes|required|in:jkn,jamsostek',
            'group_name' => 'sometimes|required',
            'existing_ids' => 'nullable|string',
            'tipe' => 'required_with:group_name|string|max:255',
            'cabang_bpjs' => 'nullable|string|max:255',
            'tipe_rumus' => 'nullable|in:nominal,persentase',
            'nilai' => 'nullable|numeric|min:0',
            'tunjangan_persen' => 'nullable|numeric|min:0',
            'hutang_persen' => 'nullable|numeric|min:0',
            'hutang_tiers' => 'nullable|string',
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
            'jp_max_dpp' => 'nullable|numeric|min:0',
            'jp_max_age' => 'nullable|integer|min:0',
        ]);

        $rumus = MasterRumusBpjs::findOrFail($id);

        $dataToUpdate = [];
        if ($request->has('tipe')) {
            $dataToUpdate['tipe'] = $request->tipe;
        }
        if ($request->has('cabang_bpjs')) {
            $dataToUpdate['cabang_bpjs'] = $request->cabang_bpjs;
        }
        if ($request->has('jenis')) {
            $dataToUpdate['jenis'] = $request->jenis;
        }
        if ($request->has('tunjangan_persen')) {
            $dataToUpdate['tunjangan_persen'] = $request->tunjangan_persen;
        }
        if ($request->has('hutang_persen')) {
            $dataToUpdate['hutang_persen'] = $request->hutang_persen;
        }
        if ($request->has('biaya_persen')) {
            $dataToUpdate['biaya_persen'] = $request->biaya_persen;
        }
        if ($request->has('keterangan_custom')) {
            $dataToUpdate['keterangan_custom'] = $request->keterangan_custom;
        }
        if ($request->has('jht_biaya')) {
            $dataToUpdate['jht_biaya'] = $request->jht_biaya;
        }
        if ($request->has('jht_hutang')) {
            $dataToUpdate['jht_hutang'] = $request->jht_hutang;
        }
        if ($request->has('jkk_tunjangan')) {
            $dataToUpdate['jkk_tunjangan'] = $request->jkk_tunjangan;
        }
        if ($request->has('jkm_tunjangan')) {
            $dataToUpdate['jkm_tunjangan'] = $request->jkm_tunjangan;
        }
        if ($request->has('jp_biaya')) {
            $dataToUpdate['jp_biaya'] = $request->jp_biaya;
        }
        if ($request->has('jp_hutang')) {
            $dataToUpdate['jp_hutang'] = $request->jp_hutang;
        }
        if ($request->has('jp_max_dpp')) {
            $dataToUpdate['jp_max_dpp'] = $request->jp_max_dpp;
        }
        if ($request->has('jp_max_age')) {
            $dataToUpdate['jp_max_age'] = $request->jp_max_age;
        }
        if ($request->has('diskon_status')) {
            $dataToUpdate['diskon_status'] = $request->diskon_status;
            if ($request->diskon_status === 'tidak_ada') {
                $dataToUpdate['diskon_nilai'] = 0;
            }
        }
        if ($request->has('diskon_tipe')) {
            $dataToUpdate['diskon_tipe'] = $request->diskon_tipe;
        }
        if ($request->has('diskon_nilai')) {
            $dataToUpdate['diskon_nilai'] = $request->diskon_nilai;
        }

        // Handle hutang_tiers (JSON string dari form)
        if ($request->has('hutang_tiers')) {
            $tiersRaw = $request->hutang_tiers;
            $tiers = null;
            if ($tiersRaw) {
                $decoded = json_decode($tiersRaw, true);
                if (is_array($decoded)) {
                    $filtered = array_values(array_filter($decoded, fn ($t) => ! empty($t['dpp']) || ! empty($t['potongan'])));
                    $tiers = count($filtered) ? $filtered : null;
                }
            }
            $dataToUpdate['hutang_tiers'] = $tiers;
        }

        // Existing IDs from group
        $existingIds = [];
        if ($request->has('existing_ids') && ! empty($request->existing_ids)) {
            $existingIds = array_values(array_filter(explode(',', $request->existing_ids)));
        }
        if (empty($existingIds)) {
            $existingIds = [$rumus->id];
        }

        // Handle group_name (bisa string atau array multi-pilihan)
        if ($request->has('group_name')) {
            $rawGroupNames = $request->group_name;
            if (is_string($rawGroupNames)) {
                $rawGroupNames = explode(',', $rawGroupNames);
            }
            $groupNames = array_values(array_filter(array_map('trim', (array) $rawGroupNames)));

            if (! empty($groupNames)) {
                $existingRecords = MasterRumusBpjs::whereIn('id', $existingIds)->get()->keyBy('id');
                $existingIdList = $existingRecords->keys()->toArray();

                foreach ($groupNames as $i => $gName) {
                    $groupData = array_merge($dataToUpdate, ['group_name' => $gName]);
                    if (isset($existingIdList[$i])) {
                        $targetId = $existingIdList[$i];
                        $existingRecords[$targetId]->update($groupData);
                    } else {
                        $newRecordData = array_merge($rumus->toArray(), $groupData);
                        unset($newRecordData['id'], $newRecordData['created_at'], $newRecordData['updated_at']);
                        MasterRumusBpjs::create($newRecordData);
                    }
                }

                // If fewer group names than existing records, delete surplus
                if (count($existingIdList) > count($groupNames)) {
                    $surplusIds = array_slice($existingIdList, count($groupNames));
                    MasterRumusBpjs::whereIn('id', $surplusIds)->delete();
                }
            }
        } else {
            // Update all existing_ids (e.g. diskon AJAX)
            MasterRumusBpjs::whereIn('id', $existingIds)->update($dataToUpdate);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data rumus berhasil diperbarui.',
                'data' => $rumus->fresh(),
            ]);
        }

        return redirect()->route('master-rumus-bpjs.index')->with('success', 'Data rumus berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        if ($request->has('ids') && ! empty($request->ids)) {
            $ids = array_values(array_filter(explode(',', $request->ids)));
            MasterRumusBpjs::whereIn('id', $ids)->delete();
        } else {
            $rumus = MasterRumusBpjs::findOrFail($id);
            $rumus->delete();
        }

        return redirect()->route('master-rumus-bpjs.index')->with('success', 'Data rumus berhasil dihapus.');
    }
}
