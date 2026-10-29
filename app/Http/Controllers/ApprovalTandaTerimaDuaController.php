<?php

namespace App\Http\Controllers;

use App\Models\ApprovalTandaTerimaDuaGoods;
use App\Models\Manifest;
use App\Models\ShipperConsignee;
use App\Models\TandaTerima;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaTanpaSuratJalan;
use App\Services\ApprovalTandaTerimaDuaExcelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ApprovalTandaTerimaDuaController extends Controller
{
    private const SOURCES = [
        'fcl' => TandaTerima::class,
        'lcl' => TandaTerimaLcl::class,
        'ttsj' => TandaTerimaTanpaSuratJalan::class,
    ];

    public function searchShippers(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('q', ''));

        $query = ShipperConsignee::query()
            ->whereNotNull('shipper')
            ->where('shipper', '!=', '');

        $items = $request->has('id')
            ? $query->whereKey($request->integer('id'))->get()
            : $query->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('shipper', 'like', '%'.$search.'%')
                    ->orWhere('consignee', 'like', '%'.$search.'%');
            }))
                ->orderBy('shipper')
                ->limit(50)
                ->get();

        return response()->json($items->map(fn (ShipperConsignee $item) => [
            ...$item->only([
                'shipper', 'alamat_shipper', 'npwp_shipper',
                'consignee', 'alamat_consignee', 'npwp_consignee',
                'notify_party_consignee', 'alamat_notify_party_consignee', 'npwp_notify_party_consignee',
                'delivery_address_contact_person', 'document_ppftz_03', 'condition', 'status',
            ]),
            'real_id' => $item->id,
            'text' => $item->shipper,
            'display_text' => $item->shipper.($item->consignee ? ' - '.$item->consignee : ''),
        ])->values())->header('Cache-Control', 'no-store');
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'type' => 'nullable|in:fcl,lcl,ttsj',
            'search' => 'nullable|string|max:100',
            'status' => 'nullable|in:belum,sudah',
            'destination' => 'nullable|in:jakarta,batam,tanjung-pinang',
        ]);
        $type = $filters['type'] ?? 'fcl';
        $destination = $filters['destination'] ?? 'batam';
        $query = self::SOURCES[$type]::query()->with('shipperJb');
        if ($type === 'lcl') {
            $query->with(['kontainerPivot', 'items']);
        } elseif ($type === 'ttsj') {
            $query->with('dimensiItems');
        }

        // A receipt that already has a manifest no longer needs approval here.
        if ($type === 'fcl') {
            $query->whereDoesntHave('prospeks.manifests');
        }

        $dateColumn = $type === 'fcl' ? 'tanggal_surat_jalan' : 'tanggal_tanda_terima';
        $query->where(function ($query) use ($dateColumn) {
            $query->whereNull($dateColumn)
                ->orWhereYear($dateColumn, '!=', 2025);
        });

        $sourceTable = $query->getModel()->getTable();
        $numberColumns = $type === 'ttsj'
            ? ['no_tanda_terima', 'nomor_tanda_terima']
            : [$this->numberColumn($type)];
        $query->whereNotExists(function ($manifestQuery) use ($sourceTable, $numberColumns) {
            $manifestQuery->selectRaw('1')->from('manifests')
                ->where(function ($numbers) use ($sourceTable, $numberColumns) {
                    foreach ($numberColumns as $column) {
                        $numbers->orWhere(function ($match) use ($sourceTable, $column) {
                            $match->whereNotNull("{$sourceTable}.{$column}")
                                ->where("{$sourceTable}.{$column}", '!=', '')
                                ->whereColumn('manifests.nomor_tanda_terima', "{$sourceTable}.{$column}");
                        });
                    }
                });
        });

        if ($request->filled('search')) {
            $search = trim($filters['search']);
            $numberColumn = $this->numberColumn($type);
            $senderColumn = $type === 'lcl' ? 'nama_pengirim' : 'pengirim';
            $containerColumn = $type === 'lcl' ? 'nomor_kontainer' : 'no_kontainer';
            $query->where(function ($query) use ($search, $numberColumn, $senderColumn, $containerColumn, $type, $sourceTable) {
                $query->where($numberColumn, 'like', "%{$search}%")
                    ->orWhere($senderColumn, 'like', "%{$search}%")
                    ->orWhere($containerColumn, 'like', "%{$search}%");
                $query->orWhereExists(function ($goodsQuery) use ($type, $sourceTable, $search) {
                    $goodsQuery->selectRaw('1')
                        ->from('approval_tanda_terima_2_goods')
                        ->where('source_type', $type)
                        ->whereColumn('source_id', "{$sourceTable}.id")
                        ->where('goods', 'like', "%{$search}%");
                });
                if ($numberColumn === 'no_tanda_terima') {
                    $query->orWhere('nomor_tanda_terima', 'like', "%{$search}%");
                }
                if ($type === 'fcl') {
                    $query->orWhere('jenis_barang', 'like', "%{$search}%")
                        ->orWhere('nama_barang', 'like', "%{$search}%")
                        ->orWhere('dimensi_items', 'like', "%{$search}%")
                        ->orWhere('dimensi_details', 'like', "%{$search}%");
                } elseif ($type === 'lcl') {
                    $query->orWhereHas('kontainerPivot', fn ($pivot) => $pivot->where('nomor_kontainer', 'like', "%{$search}%"));
                    $query->orWhereHas('items', fn ($items) => $items->where('nama_barang', 'like', "%{$search}%"));
                } else {
                    $query->orWhere('nama_barang', 'like', "%{$search}%")
                        ->orWhere('jenis_barang', 'like', "%{$search}%")
                        ->orWhereHas('dimensiItems', fn ($items) => $items->where('nama_barang', 'like', "%{$search}%"));
                }
            });
        }

        if ($request->input('status') === 'belum') {
            $query->whereNull('shipper_jb_id');
        } elseif ($request->input('status') === 'sudah') {
            $query->whereNotNull('shipper_jb_id');
        }

        if ($destination !== '') {
            $destinationFilter = function ($query) use ($destination, $type) {
                $column = $type === 'lcl' ? 'nama_tujuan' : 'tujuan_pengiriman';
                if ($destination === 'tanjung-pinang') {
                    $query->where(function ($query) use ($column) {
                        $query->where($column, 'like', '%Tanjung Pinang%')
                            ->orWhere($column, 'like', '%Tanjungpinang%');
                    });
                } else {
                    $query->where($column, 'like', '%'.ucfirst($destination).'%');
                }
            };

            if ($type === 'lcl') {
                $query->whereHas('tujuanPengiriman', $destinationFilter);
            } else {
                $destinationFilter($query);
            }
        }

        $items = $query->latest('id')->paginate(20)->withQueryString();
        $goodsOverrides = ApprovalTandaTerimaDuaGoods::query()
            ->where('source_type', $type)
            ->whereIn('source_id', $items->pluck('id'))
            ->get()
            ->keyBy('source_id');
        $incompleteMarks = DB::table('approval_tanda_terima_2_incomplete_marks')
            ->where('source_type', $type)
            ->whereIn('source_id', $items->pluck('id'))
            ->pluck('source_id')
            ->flip();
        $exportedMarks = DB::table('approval_tanda_terima_2_exports')
            ->where('source_type', $type)
            ->whereIn('source_id', $items->pluck('id'))
            ->pluck('source_id')
            ->flip();

        return view('approval-tanda-terima-2.index', [
            'type' => $type,
            'destination' => $destination,
            'items' => $items,
            'goodsOverrides' => $goodsOverrides,
            'incompleteMarks' => $incompleteMarks,
            'exportedMarks' => $exportedMarks,
        ]);
    }

    public function export(string $sourceType, int $id, ApprovalTandaTerimaDuaExcelService $excel): BinaryFileResponse
    {
        abort_unless(array_key_exists($sourceType, self::SOURCES), 404);
        $item = self::SOURCES[$sourceType]::with('shipperJb')->findOrFail($id);
        if ($sourceType === 'lcl') {
            $item->load('items', 'kontainerPivot');
        }
        if ($sourceType === 'ttsj') {
            $item->load('dimensiItems');
        }
        $override = ApprovalTandaTerimaDuaGoods::where('source_type', $sourceType)->where('source_id', $id)->first();
        $path = $excel->create($sourceType, $item, $override);

        $now = now();
        try {
            DB::table('approval_tanda_terima_2_exports')->upsert([[
                'source_type' => $sourceType,
                'source_id' => $id,
                'exported_by' => auth()->id(),
                'exported_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]], ['source_type', 'source_id'], ['exported_by', 'exported_at', 'updated_at']);
        } catch (\Throwable $exception) {
            @unlink($path);
            throw $exception;
        }

        $number = $sourceType === 'fcl' ? $item->no_surat_jalan
            : ($sourceType === 'lcl' ? $item->nomor_tanda_terima : ($item->no_tanda_terima ?: $item->nomor_tanda_terima));
        $filename = 'Manifest_'.preg_replace('/[^A-Za-z0-9_-]+/', '_', $number ?: $sourceType.'_'.$id).'.xlsx';

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function exportBulk(Request $request, string $sourceType, ApprovalTandaTerimaDuaExcelService $excel): BinaryFileResponse
    {
        abort_unless(array_key_exists($sourceType, self::SOURCES), 404);
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:300'],
            'ids.*' => ['required', 'integer', 'min:1', 'distinct'],
        ]);
        $ids = array_map('intval', $validated['ids']);
        $query = self::SOURCES[$sourceType]::with('shipperJb')->whereKey($ids);
        if ($sourceType === 'lcl') {
            $query->with('items', 'kontainerPivot');
        } elseif ($sourceType === 'ttsj') {
            $query->with('dimensiItems');
        }
        $receipts = $query->get()->keyBy('id');
        abort_if($receipts->count() !== count($ids), 404);
        $overrides = ApprovalTandaTerimaDuaGoods::where('source_type', $sourceType)
            ->whereIn('source_id', $ids)->get()->keyBy('source_id');
        $entries = array_map(fn ($id) => [
            'receipt' => $receipts->get($id),
            'override' => $overrides->get($id),
        ], $ids);
        $path = $excel->createMany($sourceType, $entries);

        $now = now();
        try {
            DB::table('approval_tanda_terima_2_exports')->upsert(array_map(fn ($id) => [
                'source_type' => $sourceType,
                'source_id' => $id,
                'exported_by' => auth()->id(),
                'exported_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ], $ids), ['source_type', 'source_id'], ['exported_by', 'exported_at', 'updated_at']);
        } catch (\Throwable $exception) {
            @unlink($path);
            throw $exception;
        }

        return response()->download($path, 'Manifest_Bulk_'.now()->format('Ymd_His').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function clearExport(string $sourceType, int $id): RedirectResponse
    {
        abort_unless(array_key_exists($sourceType, self::SOURCES), 404);
        self::SOURCES[$sourceType]::findOrFail($id);
        DB::table('approval_tanda_terima_2_exports')
            ->where('source_type', $sourceType)
            ->where('source_id', $id)
            ->delete();

        return back()->with('success', 'Tanda sudah diexport berhasil dilepas.');
    }

    public function clearExportBulk(Request $request, string $sourceType): RedirectResponse
    {
        abort_unless(array_key_exists($sourceType, self::SOURCES), 404);
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:300'],
            'ids.*' => ['required', 'integer', 'min:1', 'distinct'],
        ]);
        $ids = array_map('intval', $validated['ids']);
        abort_if(self::SOURCES[$sourceType]::whereKey($ids)->count() !== count($ids), 404);
        DB::table('approval_tanda_terima_2_exports')
            ->where('source_type', $sourceType)
            ->whereIn('source_id', $ids)
            ->delete();

        return back()->with('success', 'Tanda sudah diexport pada pilihan berhasil dilepas.');
    }

    public function setIncomplete(Request $request, string $sourceType, int $id): RedirectResponse
    {
        abort_unless(array_key_exists($sourceType, self::SOURCES), 404);
        self::SOURCES[$sourceType]::findOrFail($id);
        $validated = $request->validate(['incomplete' => 'required|boolean']);

        $key = ['source_type' => $sourceType, 'source_id' => $id];
        if ($validated['incomplete']) {
            DB::table('approval_tanda_terima_2_incomplete_marks')->insertOrIgnore($key + [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('approval_tanda_terima_2_incomplete_marks')->where($key)->delete();
        }

        return back()->with('success', $validated['incomplete']
            ? 'Tanda terima ditandai belum lengkap.'
            : 'Tanda belum lengkap berhasil dihapus.');
    }

    public function update(Request $request, string $sourceType, int $id): RedirectResponse
    {
        abort_unless(array_key_exists($sourceType, self::SOURCES), 404);
        $item = self::SOURCES[$sourceType]::findOrFail($id);
        $validated = $request->validate([
            'shipper_jb_id' => 'required|integer|exists:shipper_consignees,id',
        ]);
        $shipper = ShipperConsignee::findOrFail($validated['shipper_jb_id']);
        abort_unless(filled($shipper->shipper), 422, 'Nama shipper belum diisi pada master.');

        DB::transaction(function () use ($item, $sourceType, $shipper) {
            $item->update(['shipper_jb_id' => $shipper->id]);

            $number = $item->{$this->numberColumn($sourceType)};
            if ($sourceType === 'ttsj' && blank($number)) {
                $number = $item->nomor_tanda_terima;
            }

            $manifests = collect();
            if ($sourceType === 'fcl' || filled($number)) {
                $manifests = Manifest::where('no_voyage', 'like', '%JB%')
                    ->where(function ($query) use ($sourceType, $item, $number) {
                        if ($sourceType === 'fcl') {
                            $query->whereIn('prospek_id', $item->prospeks()->select('id'));
                        }
                        if (filled($number)) {
                            $query->orWhere('nomor_tanda_terima', $number);
                        }
                    })->get();
            }

            foreach ($manifests as $manifest) {
                $manifest->applyShipperJb($shipper);
                $manifest->updated_by = auth()->id();
                $manifest->save();
            }
        });

        return back()->with('success', 'Shipper JB tanda terima dan manifest terkait berhasil disimpan.');
    }

    public function updateGoods(Request $request, string $sourceType, int $id): RedirectResponse
    {
        abort_unless(array_key_exists($sourceType, self::SOURCES), 404);
        self::SOURCES[$sourceType]::findOrFail($id);
        $manifestInput = $request->input('manifest_goods');
        if (is_array($manifestInput)) {
            $request->merge(['manifest_goods' => collect($manifestInput)
                ->reject(fn ($good) => is_array($good) && collect($good)->every(fn ($value) => blank($value)))
                ->all()]);
        }
        $validated = $request->validate([
            'goods' => 'required|array|size:1',
            'goods.*.nama_barang' => 'required|string|max:255',
            'goods.*.jumlah' => 'nullable|integer|min:0',
            'goods.*.satuan' => 'nullable|string|max:100',
            'goods.*.ukuran' => 'nullable|string|max:255',
            'goods.*.keterangan_barang' => 'nullable|string|max:2000',
            'manifest_goods' => 'nullable|array|max:1',
            'manifest_goods.*.nama_barang' => 'required|string|max:255',
            'manifest_goods.*.hs_code' => 'nullable|string|max:255',
            'manifest_goods.*.jumlah' => 'nullable|integer|min:0',
            'manifest_goods.*.satuan' => 'nullable|string|max:100',
            'manifest_goods.*.ukuran' => 'nullable|string|max:255',
            'manifest_goods.*.meter_kubik' => ['nullable', 'string', 'max:30', 'regex:/^(?:\d{1,3}(?:\.\d{3})+(?:,\d+)?|\d+(?:[.,]\d+)?)$/'],
            'manifest_goods.*.tonase' => ['nullable', 'string', 'max:30', 'regex:/^(?:\d{1,3}(?:\.\d{3})+(?:,\d+)?|\d+(?:[.,]\d+)?)$/'],
            'manifest_goods.*.keterangan_barang' => 'nullable|string|max:2000',
            'keterangan_barang' => 'nullable|string|max:2000',
        ]);

        $goodsFields = ['nama_barang', 'jumlah', 'satuan', 'ukuran', 'keterangan_barang'];
        $manifestFields = ['nama_barang', 'hs_code', 'jumlah', 'satuan', 'ukuran', 'meter_kubik', 'tonase', 'keterangan_barang'];
        $goods = collect($validated['goods'])->values()->map(fn ($good) => collect($goodsFields)
            ->mapWithKeys(fn ($field) => [$field => $good[$field] ?? null])
            ->all())->all();
        $manifestGoods = collect($validated['manifest_goods'] ?? [])->values()->map(function ($good) use ($manifestFields) {
            $item = collect($manifestFields)->mapWithKeys(fn ($field) => [$field => $good[$field] ?? null])->all();
            if (filled($item['tonase'])) {
                $item['tonase'] = $this->formatManifestNumber($item['tonase']);
            }
            if (filled($item['meter_kubik'])) {
                $item['meter_kubik'] = $this->formatManifestNumber($item['meter_kubik']);
            }

            return $item;
        })->all();

        ApprovalTandaTerimaDuaGoods::updateOrCreate(
            ['source_type' => $sourceType, 'source_id' => $id],
            [
                'goods' => $goods,
                'manifest_goods' => $manifestGoods,
                'keterangan_barang' => $sourceType === 'ttsj' ? ($validated['keterangan_barang'] ?? null) : null,
                'updated_by' => auth()->id(),
            ]
        );

        return back()->with('success', 'Detail barang approval berhasil disimpan tanpa mengubah data tanda terima asli.');
    }

    private function formatManifestNumber(string $value): string
    {
        $value = trim($value);
        if (str_contains($value, ',')) {
            [$whole, $fraction] = explode(',', $value, 2);
        } elseif (preg_match('/^\d+\.\d{1,2}$/', $value)) {
            [$whole, $fraction] = explode('.', $value, 2);
        } else {
            $whole = $value;
            $fraction = null;
        }

        $whole = ltrim(str_replace('.', '', $whole), '0');
        $grouped = preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $whole === '' ? '0' : $whole);

        return $grouped.($fraction === null ? '' : ','.$fraction);
    }

    public function destroy(string $sourceType, int $id): RedirectResponse
    {
        abort_unless(array_key_exists($sourceType, self::SOURCES), 404);
        $item = self::SOURCES[$sourceType]::findOrFail($id);
        $shipperId = $item->shipper_jb_id;

        if (! $shipperId) {
            return back()->with('success', 'Tanda terima ini belum memiliki shipper JB.');
        }

        DB::transaction(function () use ($item, $sourceType, $shipperId) {
            $item->update(['shipper_jb_id' => null]);

            $number = $item->{$this->numberColumn($sourceType)};
            if ($sourceType === 'ttsj' && blank($number)) {
                $number = $item->nomor_tanda_terima;
            }

            if ($sourceType !== 'fcl' && blank($number)) {
                return;
            }

            Manifest::where('no_voyage', 'like', '%JB%')
                ->where('shipper_jb_id', $shipperId)
                ->where(function ($query) use ($sourceType, $item, $number) {
                    if ($sourceType === 'fcl') {
                        $query->whereIn('prospek_id', $item->prospeks()->select('id'));
                    }
                    if (filled($number)) {
                        $query->orWhere('nomor_tanda_terima', $number);
                    }
                })
                ->update([
                    'shipper_jb_id' => null,
                    'updated_by' => auth()->id(),
                ]);
        });

        return back()->with('success', 'Shipper JB berhasil dilepas dari tanda terima dan manifest terkait.');
    }

    private function numberColumn(string $type): string
    {
        return match ($type) {
            'fcl' => 'no_surat_jalan',
            'lcl' => 'nomor_tanda_terima',
            'ttsj' => 'no_tanda_terima',
        };
    }
}
