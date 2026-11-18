<?php

namespace App\Http\Controllers;

use App\Models\ManifestSugeng;
use App\Models\Manifest;
use App\Models\NaikKapal;
use App\Services\ManifestSugengExcelService;
use App\Services\ManifestSugengSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ManifestSugengController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'type' => 'nullable|in:fcl,lcl,ttsj',
            'search' => 'nullable|string|max:255',
            'nama_kapal' => 'nullable|string|max:255',
            'no_voyage' => 'nullable|string|max:255',
            'assignment' => 'nullable|in:unassigned',
        ]);

        $showUnassigned = ($filters['assignment'] ?? null) === 'unassigned';
        $hasViewChoice = $showUnassigned || (filled($filters['nama_kapal'] ?? null) && filled($filters['no_voyage'] ?? null));

        $items = ManifestSugeng::query()
            ->with('shipperJb')
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('source_type', $type))
            ->when($filters['nama_kapal'] ?? null, fn ($query, $ship) => $query->where('nama_kapal', $ship))
            ->when($filters['no_voyage'] ?? null, fn ($query, $voyage) => $query->where('no_voyage', $voyage))
            ->when($showUnassigned, fn ($query) => $query
                ->where(fn ($query) => $query->whereNull('nama_kapal')->orWhere('nama_kapal', ''))
                ->where(fn ($query) => $query->whereNull('no_voyage')->orWhere('no_voyage', '')))
            ->when(! $hasViewChoice, fn ($query) => $query->whereRaw('1 = 0'))
            ->when(trim($filters['search'] ?? '') !== '', function ($query) use ($filters) {
                $search = trim($filters['search']);
                $query->where(fn ($query) => $query
                    ->where('nomor_bl', 'like', '%'.$search.'%')
                    ->orWhere('nomor_tanda_terima', 'like', '%'.$search.'%')
                    ->orWhere('nomor_kontainer', 'like', '%'.$search.'%')
                    ->orWhere('pengirim', 'like', '%'.$search.'%'));
            })
            ->orderBy('nama_kapal')
            ->orderBy('no_voyage')
            ->orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(25)
            ->withQueryString();

        $ships = Manifest::query()->whereNotNull('nama_kapal')->where('nama_kapal', '!=', '')->distinct()->pluck('nama_kapal')
            ->merge(NaikKapal::query()->whereNotNull('nama_kapal')->where('nama_kapal', '!=', '')->distinct()->pluck('nama_kapal'))
            ->merge(ManifestSugeng::query()->whereNotNull('nama_kapal')->where('nama_kapal', '!=', '')->distinct()->pluck('nama_kapal'))
            ->unique()->sort()->values();
        $voyages = ManifestSugeng::query()->whereNotNull('no_voyage')
            ->when($filters['nama_kapal'] ?? null, fn ($query, $ship) => $query->where('nama_kapal', $ship))
            ->distinct()->orderBy('no_voyage')->pluck('no_voyage');
        $viewShips = ManifestSugeng::query()->whereNotNull('nama_kapal')->where('nama_kapal', '!=', '')
            ->whereNotNull('no_voyage')->where('no_voyage', '!=', '')
            ->distinct()->orderBy('nama_kapal')->pluck('nama_kapal');

        return view('manifest-sugeng.index', compact('items', 'ships', 'voyages', 'viewShips', 'hasViewChoice', 'showUnassigned'));
    }

    public function voyages(string $namaKapal): JsonResponse
    {
        return response()->json(['voyages' => ManifestSugeng::query()
            ->where('nama_kapal', $namaKapal)
            ->whereNotNull('no_voyage')->where('no_voyage', '!=', '')
            ->distinct()->orderBy('no_voyage')->pluck('no_voyage')]);
    }

    public function show(ManifestSugeng $manifestSugeng): View
    {
        $manifestSugeng->load(['shipperJb', 'sender']);

        return view('manifest-sugeng.show', compact('manifestSugeng'));
    }

    public function assignVoyage(Request $request, ManifestSugengSyncService $sync): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:manifest_sugengs,id'],
            'nama_kapal' => ['required', 'string', 'max:255'],
            'no_voyage' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data, $sync): void {
            $items = ManifestSugeng::query()->whereIn('id', $data['ids'])->lockForUpdate()->get()->keyBy('id');
            $position = (int) (ManifestSugeng::query()
                ->where('nama_kapal', $data['nama_kapal'])
                ->where('no_voyage', $data['no_voyage'])
                ->whereNotIn('id', $data['ids'])
                ->max('sort_order') ?? 0);
            foreach ($data['ids'] as $id) {
                $item = $items->get($id);
                if (! $item) continue;
                $sync->syncHeaderFields($item, [
                    'nama_kapal' => $data['nama_kapal'],
                    'no_voyage' => $data['no_voyage'],
                ]);
                $item->update([
                    'nama_kapal' => $data['nama_kapal'],
                    'no_voyage' => $data['no_voyage'],
                    'sort_order' => ++$position,
                ]);
            }
        });

        return redirect()->route('manifest-sugeng.index', $request->query())
            ->with('success', count($data['ids']).' data manifest berhasil ditugaskan ke '.$data['nama_kapal'].' / '.$data['no_voyage'].'.');
    }

    public function destroy(ManifestSugeng $manifestSugeng): RedirectResponse
    {
        $manifestSugeng->delete();

        return redirect()->route('manifest-sugeng.index')
            ->with('success', 'Data Manifest Sugeng berhasil dihapus.');
    }

    public function update(Request $request, ManifestSugeng $manifestSugeng, ManifestSugengSyncService $sync): RedirectResponse
    {
        $data = $request->validate([
            'nama_kapal' => 'required|string|max:255',
            'no_voyage' => 'required|string|max:255',
            'nomor_bl' => 'nullable|string|max:255',
            'nomor_kontainer' => 'nullable|string|max:255',
            'no_seal' => 'nullable|string|max:255',
            'tipe_kontainer' => 'nullable|string|max:255',
            'size_kontainer' => 'nullable|string|max:255',
        ]);
        DB::transaction(function () use ($manifestSugeng, $data, $sync): void {
            $sync->syncHeaderFields($manifestSugeng, $data);
            $sheetValues = $manifestSugeng->sheet_values ?? [];
            foreach (['nomor_bl' => 'B', 'nomor_kontainer' => 'D', 'no_seal' => 'E'] as $field => $column) {
                $sheetValues['details'][0][$column] = $data[$field] ?? '';
                if ($manifestSugeng->source_type === 'lcl' && in_array($column, ['D', 'E'], true)) {
                    $sheetValues['container'][$column] = $data[$field] ?? '';
                }
            }
            $manifestSugeng->fill($data);
            $manifestSugeng->sheet_values = $sheetValues;
            $manifestSugeng->save();
        });

        return back()->with('success', 'Manifest Sugeng berhasil diperbarui.');
    }

    public function updateNomorBl(Request $request, ManifestSugeng $manifestSugeng, ManifestSugengSyncService $sync): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'nomor_bl' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($manifestSugeng, $data, $sync): void {
            $value = trim($data['nomor_bl'] ?? '');
            $sync->syncCell($manifestSugeng, 'detail', 0, 'B', $value);
            $sheetValues = $manifestSugeng->sheet_values ?? [];
            $sheetValues['details'][0]['B'] = $value;
            $manifestSugeng->nomor_bl = $value ?: null;
            $manifestSugeng->sheet_values = $sheetValues;
            $manifestSugeng->save();
        });

        if ($request->expectsJson()) {
            return response()->json(['nomor_bl' => $manifestSugeng->nomor_bl]);
        }

        return back()->with('success', 'Nomor BL Manifest Sugeng berhasil disimpan.');
    }

    public function updateCell(Request $request, ManifestSugeng $manifestSugeng, ManifestSugengSyncService $sync): JsonResponse
    {
        $columns = array_merge(range('B', 'Z'), ['AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG']);
        $data = $request->validate([
            'kind' => ['required', Rule::in(['container', 'detail'])],
            'line' => ['required', 'integer', 'min:0', 'max:1000'],
            'column' => ['required', Rule::in($columns)],
            'value' => ['nullable', 'string', 'max:10000'],
        ]);
        $value = $data['value'] ?? '';

        DB::transaction(function () use ($manifestSugeng, $data, $value, $sync): void {
            $query = ManifestSugeng::query()->whereKey($manifestSugeng->id);
            if ($data['kind'] === 'container') {
                abort_if(blank($manifestSugeng->nomor_kontainer), 422);
                $query = ManifestSugeng::query()
                    ->where('nama_kapal', $manifestSugeng->nama_kapal)
                    ->where('no_voyage', $manifestSugeng->no_voyage)
                    ->where('nomor_kontainer', $manifestSugeng->nomor_kontainer)
                    ->where(function ($query): void {
                        $query->where('source_type', 'lcl')->orWhere('tipe_kontainer', 'like', '%LCL%');
                    });
            }
            foreach ($query->lockForUpdate()->get() as $item) {
                $sheetValues = $item->sheet_values ?? [];
                if ($data['kind'] === 'container') {
                    $sheetValues['container'][$data['column']] = $value;
                } else {
                    $sheetValues['details'][$data['line']][$data['column']] = $value;
                    if ($data['line'] === 0 && $data['column'] === 'B') {
                        $item->nomor_bl = trim($value) ?: null;
                    }
                }
                $item->sheet_values = $sheetValues;
                $item->save();
                $sync->syncCell($item, $data['kind'], $data['line'], $data['column'], $value);
            }
        });

        return response()->json(['value' => $value]);
    }

    public function markRows(Request $request): JsonResponse
    {
        $data = $request->validate([
            'rows' => ['required', 'array', 'min:1', 'max:500'],
            'rows.*.id' => ['required', 'integer', 'exists:manifest_sugengs,id'],
            'rows.*.line' => ['required', 'integer', 'min:0', 'max:1000'],
            'mark' => ['present', Rule::in(['', 'ungu', 'biru', 'merah'])],
        ]);

        DB::transaction(function () use ($data): void {
            foreach ($data['rows'] as $row) {
                $item = ManifestSugeng::query()->lockForUpdate()->findOrFail($row['id']);
                $values = $item->sheet_values ?? [];
                if ($data['mark'] === '') {
                    unset($values['row_marks'][$row['line']]);
                } else {
                    $values['row_marks'][$row['line']] = $data['mark'];
                }
                $item->sheet_values = $values;
                $item->save();
            }
        });

        return response()->json(['mark' => $data['mark']]);
    }

    public function moveRows(Request $request): JsonResponse
    {
        $data = $request->validate([
            'source_ids' => ['required', 'array', 'min:1', 'max:500'],
            'source_ids.*' => ['required', 'integer', 'distinct', 'exists:manifest_sugengs,id'],
            'target_id' => ['required', 'integer', 'exists:manifest_sugengs,id'],
            'position' => ['required', Rule::in(['before', 'after'])],
        ]);

        DB::transaction(function () use ($data): void {
            $sourceIds = array_map('intval', $data['source_ids']);
            $source = ManifestSugeng::query()->findOrFail($sourceIds[0]);
            $items = ManifestSugeng::query()
                ->where('nama_kapal', $source->nama_kapal)
                ->where('no_voyage', $source->no_voyage)
                ->orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
                ->orderBy('sort_order')->orderBy('id')
                ->lockForUpdate()->get();

            $itemIds = $items->pluck('id')->all();
            $sourceIds = array_values(array_unique($sourceIds));
            $targetId = (int) $data['target_id'];
            abort_if(
                ! in_array($targetId, $itemIds, true)
                    || array_diff($sourceIds, $itemIds)
                    || in_array($targetId, $sourceIds, true),
                422,
                'Baris hanya dapat dipindahkan dalam kapal dan voyage yang sama.'
            );

            $moving = $items->filter(fn ($item) => in_array($item->id, $sourceIds, true))->values()->all();
            abort_if(count($moving) !== count($sourceIds), 422, 'Sebagian baris yang dipilih tidak ditemukan.');
            $ordered = $items->reject(fn ($item) => in_array($item->id, $sourceIds, true))->values()->all();
            $targetIndex = collect($ordered)->search(fn ($item) => $item->id === $targetId);
            abort_if($targetIndex === false, 422, 'Baris tujuan tidak ditemukan.');
            array_splice($ordered, $targetIndex + ($data['position'] === 'after' ? 1 : 0), 0, $moving);
            foreach ($ordered as $position => $item) {
                if ($item->sort_order !== $position + 1) {
                    $item->sort_order = $position + 1;
                    $item->save();
                }
            }
        });

        return response()->json(['success' => true]);
    }

    public function removeRows(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:500'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:manifest_sugengs,id'],
        ]);

        $removed = ManifestSugeng::query()->whereIn('id', $data['ids'])->delete();

        return response()->json(['removed' => $removed]);
    }

    public function export(Request $request, ManifestSugengExcelService $excel): BinaryFileResponse
    {
        $filters = $request->validate([
            'nama_kapal' => 'nullable|string|max:255',
            'no_voyage' => 'nullable|string|max:255',
            'assignment' => 'nullable|in:unassigned',
            'type' => 'nullable|in:fcl,lcl,ttsj',
            'search' => 'nullable|string|max:255',
        ]);
        $showUnassigned = ($filters['assignment'] ?? null) === 'unassigned';
        abort_unless($showUnassigned || (filled($filters['nama_kapal'] ?? null) && filled($filters['no_voyage'] ?? null)), 404);

        $items = ManifestSugeng::query()->with('shipperJb')
            ->when($filters['nama_kapal'] ?? null, fn ($query, $ship) => $query->where('nama_kapal', $ship))
            ->when($filters['no_voyage'] ?? null, fn ($query, $voyage) => $query->where('no_voyage', $voyage))
            ->when($showUnassigned, fn ($query) => $query
                ->where(fn ($query) => $query->whereNull('nama_kapal')->orWhere('nama_kapal', ''))
                ->where(fn ($query) => $query->whereNull('no_voyage')->orWhere('no_voyage', '')))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('source_type', $type))
            ->when(trim($filters['search'] ?? '') !== '', function ($query) use ($filters) {
                $search = trim($filters['search']);
                $query->where(fn ($query) => $query
                    ->where('nomor_bl', 'like', '%'.$search.'%')
                    ->orWhere('nomor_tanda_terima', 'like', '%'.$search.'%')
                    ->orWhere('nomor_kontainer', 'like', '%'.$search.'%')
                    ->orWhere('pengirim', 'like', '%'.$search.'%'));
            })
            ->orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('sort_order')->orderBy('id')->get();
        abort_if($items->isEmpty(), 404);
        $path = $excel->create($items);
        $selection = $showUnassigned ? 'Tanpa_Kapal_Voyage' : $filters['nama_kapal'].'_'.$filters['no_voyage'];
        $filename = 'Manifest_Sugeng_'.preg_replace('/[^A-Za-z0-9_-]+/', '_', $selection).'.xlsx';

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
