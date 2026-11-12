<?php

namespace App\Http\Controllers;

use App\Models\ManifestSugeng;
use App\Services\ManifestSugengExcelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
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
        ]);

        $items = ManifestSugeng::query()
            ->with('shipperJb')
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('source_type', $type))
            ->when($filters['nama_kapal'] ?? null, fn ($query, $ship) => $query->where('nama_kapal', $ship))
            ->when($filters['no_voyage'] ?? null, fn ($query, $voyage) => $query->where('no_voyage', $voyage))
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
            ->orderByRaw("CASE WHEN source_type = 'lcl' OR UPPER(COALESCE(tipe_kontainer, '')) LIKE '%LCL%' THEN 0 ELSE 1 END")
            ->orderBy('nomor_kontainer')
            ->orderBy('id')
            ->paginate(25)
            ->withQueryString();

        $ships = ManifestSugeng::query()->whereNotNull('nama_kapal')->distinct()->orderBy('nama_kapal')->pluck('nama_kapal');
        $voyages = ManifestSugeng::query()->whereNotNull('no_voyage')
            ->when($filters['nama_kapal'] ?? null, fn ($query, $ship) => $query->where('nama_kapal', $ship))
            ->distinct()->orderBy('no_voyage')->pluck('no_voyage');

        return view('manifest-sugeng.index', compact('items', 'ships', 'voyages'));
    }

    public function show(ManifestSugeng $manifestSugeng): View
    {
        $manifestSugeng->load(['shipperJb', 'sender']);

        return view('manifest-sugeng.show', compact('manifestSugeng'));
    }

    public function destroy(ManifestSugeng $manifestSugeng): RedirectResponse
    {
        $manifestSugeng->delete();

        return redirect()->route('manifest-sugeng.index')
            ->with('success', 'Data Manifest Sugeng berhasil dihapus.');
    }

    public function update(Request $request, ManifestSugeng $manifestSugeng): RedirectResponse
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
        $manifestSugeng->update($data);

        return back()->with('success', 'Manifest Sugeng berhasil diperbarui.');
    }

    public function updateNomorBl(Request $request, ManifestSugeng $manifestSugeng): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'nomor_bl' => 'nullable|string|max:255',
        ]);

        $manifestSugeng->update(['nomor_bl' => trim($data['nomor_bl'] ?? '') ?: null]);

        if ($request->expectsJson()) {
            return response()->json(['nomor_bl' => $manifestSugeng->nomor_bl]);
        }

        return back()->with('success', 'Nomor BL Manifest Sugeng berhasil disimpan.');
    }

    public function updateCell(Request $request, ManifestSugeng $manifestSugeng): JsonResponse
    {
        $columns = array_merge(range('B', 'Z'), ['AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG']);
        $data = $request->validate([
            'kind' => ['required', Rule::in(['container', 'detail'])],
            'line' => ['required', 'integer', 'min:0', 'max:1000'],
            'column' => ['required', Rule::in($columns)],
            'value' => ['nullable', 'string', 'max:10000'],
        ]);
        $value = $data['value'] ?? '';

        DB::transaction(function () use ($manifestSugeng, $data, $value): void {
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
            }
        });

        return response()->json(['value' => $value]);
    }

    public function export(Request $request, ManifestSugengExcelService $excel): BinaryFileResponse
    {
        $filters = $request->validate([
            'nama_kapal' => 'required|string|max:255',
            'no_voyage' => 'required|string|max:255',
        ]);
        $items = ManifestSugeng::query()->with('shipperJb')
            ->where('nama_kapal', $filters['nama_kapal'])
            ->where('no_voyage', $filters['no_voyage'])
            ->orderByRaw("CASE WHEN source_type = 'lcl' OR UPPER(COALESCE(tipe_kontainer, '')) LIKE '%LCL%' THEN 0 ELSE 1 END")
            ->orderBy('nomor_kontainer')->orderBy('id')->get();
        abort_if($items->isEmpty(), 404);
        $path = $excel->create($items);
        $filename = 'Manifest_Sugeng_'.preg_replace('/[^A-Za-z0-9_-]+/', '_', $filters['nama_kapal'].'_'.$filters['no_voyage']).'.xlsx';

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
