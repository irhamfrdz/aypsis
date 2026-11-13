<?php

namespace App\Http\Controllers;

use App\Models\ApprovalTandaTerimaDuaFclShipper;
use App\Models\ApprovalTandaTerimaDuaGoods;
use App\Models\ApprovalTandaTerimaDuaShipperOverride;
use App\Models\Manifest;
use App\Models\ShipperConsignee;
use App\Models\TandaTerima;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaTanpaSuratJalan;
use App\Services\ApprovalTandaTerimaDuaExcelService;
use App\Services\ManifestApprovalGoodsSync;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
        if ($request->boolean('complete_pair')) {
            $query->whereNotNull('consignee')->where('consignee', '!=', '');
        }

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
            'search' => 'nullable|string',
            'status' => 'nullable|in:belum,sudah,belum_lengkap',
            'destination' => 'nullable|in:jakarta,batam,tanjung-pinang',
            'manifest_status' => 'nullable|in:belum,sudah,semua',
            'voyage' => 'nullable|string|max:255',
        ]);
        $type = $filters['type'] ?? 'fcl';
        $destination = $filters['destination'] ?? 'batam';
        $manifestStatus = $filters['manifest_status'] ?? 'belum';
        $query = self::SOURCES[$type]::query();

        // Old receipts that should not enter the Approval Tanda Terima 2 queue.
        if ($type === 'fcl' && $manifestStatus === 'belum') {
            $query->whereNotIn('no_surat_jalan', ['JB0033973', 'JB0033887']);
        }

        $dateColumn = $type === 'fcl' ? 'tanggal_surat_jalan' : 'tanggal_tanda_terima';
        $query->where(function ($query) use ($dateColumn) {
            $query->whereNull($dateColumn)
                ->orWhere($dateColumn, '<', '2025-01-01')
                ->orWhere($dateColumn, '>=', '2026-01-01');
        });

        $sourceTable = $query->getModel()->getTable();
        $numberColumns = match ($type) {
            'fcl' => ['no_surat_jalan', 'nomor_tanda_terima'],
            'ttsj' => ['no_tanda_terima', 'nomor_tanda_terima'],
            default => [$this->numberColumn($type)],
        };
        $manifestByNumber = function ($manifestQuery) use ($sourceTable, $numberColumns) {
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
        };
        $manifestBySingleNumber = function ($manifestQuery, string $column) use ($sourceTable) {
            $manifestQuery->selectRaw('1')->from('manifests')
                ->whereNotNull("{$sourceTable}.{$column}")
                ->where("{$sourceTable}.{$column}", '!=', '')
                ->whereColumn('manifests.nomor_tanda_terima', "{$sourceTable}.{$column}");
        };
        $voyageSearch = trim((string) ($filters['voyage'] ?? ''));

        if ($manifestStatus === 'belum') {
            if ($type === 'fcl') {
                $query->whereDoesntHave('prospeks.manifests');
            }
            foreach ($numberColumns as $column) {
                $query->whereNotExists(fn ($manifestQuery) => $manifestBySingleNumber($manifestQuery, $column));
            }
        } elseif ($manifestStatus === 'sudah') {
            if ($voyageSearch !== '') {
                $query->where(function ($query) use ($type, $numberColumns, $manifestBySingleNumber, $voyageSearch) {
                    foreach ($numberColumns as $column) {
                        $query->orWhereExists(function ($manifestQuery) use ($manifestBySingleNumber, $column, $voyageSearch) {
                            $manifestBySingleNumber($manifestQuery, $column);
                            $manifestQuery->where('manifests.no_voyage', $voyageSearch);
                        });
                    }

                    if ($type === 'fcl') {
                        $query->orWhereHas('prospeks.manifests', fn ($manifestQuery) =>
                            $manifestQuery->where('no_voyage', $voyageSearch));
                    }
                });
            } else {
                $query->where(function ($query) use ($type, $numberColumns, $manifestBySingleNumber) {
                    foreach ($numberColumns as $column) {
                        $query->orWhereExists(fn ($manifestQuery) => $manifestBySingleNumber($manifestQuery, $column));
                    }
                    if ($type === 'fcl') {
                        $query->orWhereHas('prospeks.manifests');
                    }
                });
            }
        }

        if ($request->filled('search')) {
            $searches = array_values(array_filter(array_map('trim', preg_split('/[,\r\n]+/', $filters['search']))));
            $numberColumn = $this->numberColumn($type);
            $senderColumn = $type === 'lcl' ? 'nama_pengirim' : 'pengirim';
            $containerColumn = $type === 'lcl' ? 'nomor_kontainer' : 'no_kontainer';
            $matchContainer = function ($containerQuery, string $search) use ($type, $containerColumn, $manifestByNumber, $manifestStatus, $voyageSearch) {
                $containerQuery->where(function ($matches) use ($type, $containerColumn, $manifestByNumber, $manifestStatus, $voyageSearch, $search) {
                    $matches->where($containerColumn, 'like', "%{$search}%");
                    if ($type === 'lcl') {
                        $matches->orWhereHas('kontainerPivot', fn ($pivot) => $pivot->where('nomor_kontainer', 'like', "%{$search}%"));
                        $matches->orWhereExists(function ($manifestQuery) use ($manifestByNumber, $manifestStatus, $voyageSearch, $search) {
                            $manifestByNumber($manifestQuery);
                            $manifestQuery->where('manifests.nomor_kontainer', 'like', "%{$search}%");
                            if ($manifestStatus === 'sudah' && $voyageSearch !== '') {
                                $manifestQuery->where('manifests.no_voyage', $voyageSearch);
                            }
                        });
                    }
                });
            };
            if ($searches !== []) {
                $containerMatches = [];
                if (count($searches) === 1) {
                    $search = $searches[0];
                    $containerMatches[$search] = (clone $query)->where(fn ($containerQuery) => $matchContainer($containerQuery, $search))->exists();
                }

                $query->where(function ($query) use ($searches, $containerMatches, $numberColumn, $senderColumn, $containerColumn, $type, $sourceTable, $matchContainer) {
                    foreach ($searches as $search) {
                        $query->orWhere(function ($query) use ($search, $containerMatches, $numberColumn, $senderColumn, $containerColumn, $type, $sourceTable, $matchContainer) {
                            if ($containerMatches[$search] ?? false) {
                                $matchContainer($query, $search);

                                return;
                            }

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
                                $query->orWhere('nomor_tanda_terima', 'like', "%{$search}%")
                                    ->orWhere('jenis_barang', 'like', "%{$search}%")
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
                });
            }
        }

        if ($request->input('status') === 'belum') {
            $query->whereNull('shipper_jb_id');
        } elseif ($request->input('status') === 'sudah') {
            $query->whereNotNull('shipper_jb_id');
        } elseif ($request->input('status') === 'belum_lengkap') {
            $query->whereExists(function ($markQuery) use ($type, $sourceTable) {
                $markQuery->selectRaw('1')
                    ->from('approval_tanda_terima_2_incomplete_marks')
                    ->where('source_type', $type)
                    ->whereColumn('source_id', "{$sourceTable}.id");
            });
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

        $containerQuantityTotal = null;
        $containerSearch = trim((string) ($filters['search'] ?? ''));
        if ($containerSearch !== '' && ! preg_match('/[,\r\n]/', $containerSearch)) {
            $containerColumn = $type === 'lcl' ? 'nomor_kontainer' : 'no_kontainer';
            $containerQuery = (clone $query)->where(function ($query) use ($type, $containerColumn, $containerSearch) {
                $query->where($containerColumn, 'like', "%{$containerSearch}%");
                if ($type === 'lcl') {
                    $query->orWhereHas('kontainerPivot', fn ($pivot) => $pivot->where('nomor_kontainer', 'like', "%{$containerSearch}%"));
                }
            });

            if ($containerQuery->exists()) {
                $containerQuantityTotal = ApprovalTandaTerimaDuaGoods::query()
                    ->where('source_type', $type)
                    ->whereIn('source_id', $containerQuery->select("{$sourceTable}.id"))
                    ->cursor()
                    ->sum(fn ($goods) => (int) data_get($goods->manifest_goods, '0.jumlah', 0));
            }
        }

        $pageIds = $query
            ->leftJoin('approval_tanda_terima_2_orders as approval_order', function ($join) use ($type, $sourceTable) {
                $join->on('approval_order.source_id', '=', $sourceTable.'.id')
                    ->where('approval_order.source_type', '=', $type);
            })
            ->select($sourceTable.'.id')
            ->orderByRaw("COALESCE(approval_order.position, {$sourceTable}.id) DESC")
            ->orderBy($sourceTable.'.id', 'desc')
            ->simplePaginate(20)->withQueryString();
        $ids = $pageIds->getCollection()->pluck('id');
        $details = self::SOURCES[$type]::query()->with(match ($type) {
            'fcl' => ['shipperJb', 'fclShipperDetails.shipperJb'],
            'lcl' => ['shipperJb', 'kontainerPivot', 'items'],
            'ttsj' => ['shipperJb', 'dimensiItems'],
        })->whereKey($ids)->get()->keyBy('id');
        $items = $pageIds->setCollection($ids->map(fn ($id) => $details->get($id)));
        $goodsOverrides = ApprovalTandaTerimaDuaGoods::query()
            ->where('source_type', $type)
            ->whereIn('source_id', $items->pluck('id'))
            ->get()
            ->keyBy('source_id');
        $shipperOverrides = ApprovalTandaTerimaDuaShipperOverride::query()
            ->where('source_type', $type)
            ->whereIn('source_id', $items->pluck('id'))
            ->get()
            ->keyBy(fn ($override) => $override->source_id.'|'.$override->context);
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
            'kapals' => \App\Models\Manifest::query()->whereNotNull('nama_kapal')->distinct()->pluck('nama_kapal')
                ->merge(\App\Models\NaikKapal::query()->whereNotNull('nama_kapal')->distinct()->pluck('nama_kapal'))
                ->filter()->unique()->sort()->values(),
            'type' => $type,
            'destination' => $destination,
            'manifestStatus' => $manifestStatus,
            'items' => $items,
            'containerQuantityTotal' => $containerQuantityTotal,
            'goodsOverrides' => $goodsOverrides,
            'shipperOverrides' => $shipperOverrides,
            'incompleteMarks' => $incompleteMarks,
            'exportedMarks' => $exportedMarks,
        ]);
    }

    public function swapOrder(Request $request, string $sourceType): JsonResponse
    {
        abort_unless(array_key_exists($sourceType, self::SOURCES), 404);
        $validated = $request->validate([
            'dragged_id' => ['required', 'integer', 'min:1', 'different:target_id'],
            'target_id' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($sourceType, $validated): void {
            $ids = [$validated['dragged_id'], $validated['target_id']];
            $receipts = self::SOURCES[$sourceType]::query()->whereKey($ids)
                ->orderBy('id')->lockForUpdate()->get(['id']);
            abort_unless($receipts->count() === 2, 404);

            $positions = DB::table('approval_tanda_terima_2_orders')
                ->where('source_type', $sourceType)->whereIn('source_id', $ids)
                ->pluck('position', 'source_id');
            $draggedPosition = $positions->get($ids[0], $ids[0]);
            $targetPosition = $positions->get($ids[1], $ids[1]);
            $now = now();
            foreach ([[$ids[0], $targetPosition], [$ids[1], $draggedPosition]] as [$id, $position]) {
                DB::table('approval_tanda_terima_2_orders')->updateOrInsert(
                    ['source_type' => $sourceType, 'source_id' => $id],
                    ['position' => $position, 'created_at' => $now, 'updated_at' => $now]
                );
            }
        });

        return response()->json(['message' => 'Urutan tanda terima berhasil disimpan.']);
    }

    public function storeTtsj(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'no_tanda_terima' => ['required', 'string'],
            'shipper_jb_id' => ['required', 'integer', 'exists:shipper_consignees,id'],
            'tanggal_tanda_terima' => [
                'required',
                'date',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (is_string($value) && str_starts_with($value, '2025-')) {
                        $fail('Tanda terima tahun 2025 tidak ditampilkan pada halaman approval.');
                    }
                },
            ],
            'tujuan_pengiriman' => ['required', 'string'],
            'tipe_kontainer' => ['required', 'in:fcl,cargo'],
            'manifest_goods' => ['required', 'array', 'size:1'],
            'manifest_goods.0.nama_barang' => ['required', 'string'],
            'manifest_goods.0.hs_codes' => ['nullable', 'string'],
            'manifest_goods.0.jumlah' => ['required', 'integer', 'min:1'],
            'manifest_goods.0.satuan' => ['required', 'string'],
            'manifest_goods.0.ukuran' => ['nullable', 'string'],
            'manifest_goods.0.tonase' => ['nullable', 'string', 'max:30', 'regex:/^(?:\d{1,3}(?:\.\d{3})+(?:,\d+)?|\d+(?:[.,]\d+)?)$/'],
            'manifest_goods.0.meter_kubik' => ['nullable', 'string', 'max:30', 'regex:/^(?:\d{1,3}(?:\.\d{3})+(?:,\d+)?|\d+(?:[.,]\d+)?)$/'],
            'goods' => ['required', 'array', 'size:1'],
            'goods.0.nama_barang' => ['required', 'string'],
            'goods.0.jumlah' => ['required', 'integer', 'min:1'],
            'goods.0.satuan' => ['required', 'string'],
            'goods.0.ukuran' => ['nullable', 'string'],
            'no_kontainer' => ['required_if:tipe_kontainer,fcl', 'nullable', 'string'],
            'size_kontainer' => ['nullable', 'string'],
            'no_seal' => ['nullable', 'string'],
            'keterangan_barang' => ['nullable', 'string'],
        ]);

        [$shipper, $shipperFields] = $this->validateShipperSelection($request);
        $shipperName = array_key_exists('shipper', $shipperFields) ? $shipperFields['shipper'] : $shipper->shipper;
        $consigneeName = array_key_exists('consignee', $shipperFields) ? $shipperFields['consignee'] : $shipper->consignee;
        if (! filled($shipperName) || ! filled($consigneeName)) {
            throw ValidationException::withMessages([
                'shipper_jb_id' => 'Pilih data master yang memiliki shipper dan consignee.',
            ]);
        }

        $number = trim($data['no_tanda_terima']);
        $alreadyExists = TandaTerimaTanpaSuratJalan::query()
            ->where('no_tanda_terima', $number)
            ->orWhere('nomor_tanda_terima', $number)
            ->exists();
        if ($alreadyExists || Manifest::query()->where('nomor_tanda_terima', $number)->exists()) {
            return back()->withInput()->withErrors([
                'no_tanda_terima' => 'Nomor tanda terima sudah terdaftar. Cari nomor tersebut sebelum menambah data baru.',
            ]);
        }

        $manifest = $data['manifest_goods'][0];
        $manifest['hs_codes'] = collect(preg_split('/\R/u', $manifest['hs_codes'] ?? ''))
            ->map(fn ($code) => trim($code))->filter()->values()->all();
        $manifest['hs_code'] = $manifest['hs_codes'][0] ?? null;
        foreach (['tonase', 'meter_kubik'] as $field) {
            if (filled($manifest[$field] ?? null)) {
                $manifest[$field] = $this->formatManifestNumber($manifest[$field]);
            }
        }
        $perincian = $data['goods'][0];
        if ($data['tipe_kontainer'] === 'cargo') {
            $data['no_kontainer'] = 'CARGO';
            $data['size_kontainer'] = null;
            $data['no_seal'] = null;
        }
        unset($data['manifest_goods'], $data['goods']);

        DB::transaction(function () use ($data, $number, $shipper, $shipperFields, $manifest, $perincian, $request) {
            $receipt = TandaTerimaTanpaSuratJalan::create([
                ...$data,
                'shipper_jb_id' => $shipper->id,
                'pengirim' => $shipperFields['shipper'] ?? $shipper->shipper,
                'penerima' => $shipperFields['consignee'] ?? $shipper->consignee,
                'alamat_pengirim' => $shipperFields['alamat_shipper'] ?? $shipper->alamat_shipper,
                'alamat_penerima' => $shipperFields['alamat_consignee'] ?? $shipper->alamat_consignee,
                'notify_party' => $shipperFields['notify_party_consignee'] ?? $shipper->notify_party_consignee,
                'alamat_notify_party' => $shipperFields['alamat_notify_party_consignee'] ?? $shipper->alamat_notify_party_consignee,
                'no_tanda_terima' => $number,
                'nomor_tanda_terima' => $number,
                'nama_barang' => $perincian['nama_barang'],
                'jenis_barang' => $perincian['nama_barang'],
                'jumlah_barang' => $perincian['jumlah'],
                'satuan_barang' => $perincian['satuan'],
                'created_by' => $request->user()?->name,
            ]);
            $this->saveShipperApprovalOverride('ttsj', $receipt->id, 'main', $shipper, $shipperFields);
            ApprovalTandaTerimaDuaGoods::create([
                'source_type' => 'ttsj',
                'source_id' => $receipt->id,
                'manifest_goods' => [$manifest],
                'goods' => [$perincian],
                'keterangan_barang' => $data['keterangan_barang'] ?? null,
                'updated_by' => $request->user()?->id,
            ]);
        });

        return redirect()->route('approval-tanda-terima-2.index', [
            'type' => 'ttsj',
            'destination' => '',
            'search' => strlen($number) <= 1500 ? $number : '',
        ])->with('success', 'Tanda terima tanpa surat jalan berhasil ditambahkan.');
    }

    public function export(string $sourceType, int $id, ApprovalTandaTerimaDuaExcelService $excel): BinaryFileResponse
    {
        abort_unless(array_key_exists($sourceType, self::SOURCES), 404);
        $item = self::SOURCES[$sourceType]::with('shipperJb')->findOrFail($id);
        if ($sourceType === 'fcl') {
            $item->load('fclShipperDetails.shipperJb');
            $this->ensureFclDetailsComplete($item);
        }
        if ($sourceType === 'lcl') {
            $item->load('items', 'kontainerPivot');
        }
        if ($sourceType === 'ttsj') {
            $item->load('dimensiItems');
        }
        $override = ApprovalTandaTerimaDuaGoods::where('source_type', $sourceType)->where('source_id', $id)->first();
        $shipperOverride = ApprovalTandaTerimaDuaShipperOverride::where('source_type', $sourceType)
            ->where('source_id', $id)->where('context', 'main')->first()?->fields;
        $path = $excel->create($sourceType, $item, $override, $shipperOverride);

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
        $sourceTable = $query->getModel()->getTable();
        if ($sourceType === 'lcl') {
            $query->with('items', 'kontainerPivot');
        } elseif ($sourceType === 'fcl') {
            $query->with('fclShipperDetails.shipperJb');
        } elseif ($sourceType === 'ttsj') {
            $query->with('dimensiItems');
        }
        $receipts = $query
            ->leftJoin('approval_tanda_terima_2_orders as approval_order', function ($join) use ($sourceType, $sourceTable) {
                $join->on('approval_order.source_id', '=', $sourceTable.'.id')
                    ->where('approval_order.source_type', '=', $sourceType);
            })
            ->select($sourceTable.'.*')
            ->orderByRaw("COALESCE(approval_order.position, {$sourceTable}.id) DESC")
            ->orderBy($sourceTable.'.id', 'desc')
            ->get()
            ->keyBy('id');
        abort_if($receipts->count() !== count($ids), 404);
        if ($sourceType === 'fcl') {
            $receipts->each(fn ($receipt) => $this->ensureFclDetailsComplete($receipt));
        }
        $overrides = ApprovalTandaTerimaDuaGoods::where('source_type', $sourceType)
            ->whereIn('source_id', $ids)->get()->keyBy('source_id');
        $shipperOverrides = ApprovalTandaTerimaDuaShipperOverride::where('source_type', $sourceType)
            ->whereIn('source_id', $ids)->get()->groupBy('source_id');
        $entries = $receipts->map(fn ($receipt) => [
            'receipt' => $receipt,
            'override' => $overrides->get($receipt->id),
            'shipper_override' => $shipperOverrides->get($receipt->id, collect())->firstWhere('context', 'main')?->fields,
            'detail_shipper_overrides' => $shipperOverrides->get($receipt->id, collect())
                ->filter(fn ($row) => str_starts_with($row->context, 'detail:'))
                ->keyBy('context')->map(fn ($row) => $row->fields)->all(),
        ])->values()->all();
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

    public function sendToManifestSugeng(Request $request, string $sourceType): RedirectResponse
    {
        abort_unless(array_key_exists($sourceType, self::SOURCES), 404);
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:300'],
            'ids.*' => ['required', 'integer', 'min:1', 'distinct'],
        ]);
        $ids = array_map('intval', $validated['ids']);
        $receipts = self::SOURCES[$sourceType]::query()
            ->when($sourceType === 'lcl', fn ($query) => $query->with(['tujuanPengiriman', 'kontainerPivot']))
            ->whereKey($ids)->get()->keyBy('id');
        abort_if($receipts->count() !== count($ids), 404);
        $goods = ApprovalTandaTerimaDuaGoods::query()
            ->where('source_type', $sourceType)->whereIn('source_id', $ids)
            ->get()->keyBy('source_id');
        $now = now();

        DB::transaction(function () use ($sourceType, $ids, $receipts, $goods, $now, $validated): void {
            foreach ($ids as $id) {
                $receipt = $receipts->get($id);
                DB::table('manifest_sugengs')->updateOrInsert(
                    ['source_type' => $sourceType, 'source_id' => $id],
                    function (bool $exists) use ($sourceType, $receipt, $goods, $id, $now, $validated) {
                        return [
                            ...($exists ? [] : ['nama_kapal' => null, 'no_voyage' => null]),
                            'no_seal' => $sourceType === 'lcl' ? null : $receipt->no_seal,
                            'tipe_kontainer' => $sourceType === 'lcl' ? 'LCL' : $receipt->tipe_kontainer,
                            'size_kontainer' => $sourceType === 'fcl' ? $receipt->size : ($sourceType === 'lcl' ? $receipt->ukuran : ($receipt->size_kontainer ?: $receipt->ukuran)),
                            'perincian_barang' => json_encode($goods->get($id)?->goods ?? [], JSON_UNESCAPED_UNICODE),
                            'nomor_tanda_terima' => $sourceType === 'fcl'
                                ? ($receipt->nomor_tanda_terima ?: $receipt->no_surat_jalan)
                                : ($sourceType === 'lcl' ? $receipt->nomor_tanda_terima : ($receipt->no_tanda_terima ?: $receipt->nomor_tanda_terima)),
                            'tanggal_tanda_terima' => $sourceType === 'fcl' ? $receipt->tanggal_surat_jalan : $receipt->tanggal_tanda_terima,
                            'nomor_kontainer' => $sourceType === 'lcl' ? $receipt->nomor_kontainer : $receipt->no_kontainer,
                            'pengirim' => $sourceType === 'lcl' ? $receipt->nama_pengirim : $receipt->pengirim,
                            'tujuan_pengiriman' => $sourceType === 'lcl' ? $receipt->tujuanPengiriman?->nama_tujuan : $receipt->tujuan_pengiriman,
                            'shipper_jb_id' => $receipt->shipper_jb_id,
                            'manifest_goods' => json_encode($goods->get($id)?->manifest_goods ?? [], JSON_UNESCAPED_UNICODE),
                            'dikirim_oleh' => auth()->id(),
                            'dikirim_pada' => $now,
                            ...($exists ? [] : ['created_at' => $now]),
                            'updated_at' => $now,
                        ];
                    }
                );
            }
        });

        return redirect()->route('manifest-sugeng.index')
            ->with('success', count($ids).' tanda terima berhasil dikirim ke Manifest Sugeng.');
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
        $request->merge(['nomor_tanda_terima' => trim((string) $request->input('nomor_tanda_terima'))]);
        $numberColumn = $sourceType === 'ttsj' ? 'no_tanda_terima' : 'nomor_tanda_terima';
        $numberData = $request->validate([
            'nomor_tanda_terima' => array_filter([
                'required', 'string', $sourceType === 'ttsj' ? null : 'max:255',
                Rule::unique($item->getTable(), $numberColumn)->ignore($item->id),
            ]),
        ]);
        $receiptNumber = trim($numberData['nomor_tanda_terima']);
        if ($sourceType === 'ttsj' && TandaTerimaTanpaSuratJalan::query()
            ->whereKeyNot($item->id)->where('nomor_tanda_terima', $receiptNumber)->exists()) {
            throw ValidationException::withMessages(['nomor_tanda_terima' => 'Nomor tanda terima sudah digunakan.']);
        }
        [$shipper, $fields] = $this->validateShipperSelection($request);
        $withGoods = $request->boolean('save_goods');
        $goods = $withGoods ? $this->validateApprovalGoods($request) : null;
        if ($sourceType === 'fcl') {
            abort_if($item->fclShipperDetails()->where('shipper_jb_id', $shipper->id)->exists(), 422, 'Shipper ini sudah menjadi shipper tambahan.');
        }

        DB::transaction(function () use ($item, $sourceType, $shipper, $fields, $goods, $id, $receiptNumber) {
            $this->saveShipperApprovalOverride($sourceType, $item->id, 'main', $shipper, $fields);
            $number = $item->{$this->numberColumn($sourceType)};
            if ($sourceType === 'ttsj' && blank($number)) {
                $number = $item->nomor_tanda_terima;
            }
            $item->update([
                'shipper_jb_id' => $shipper->id,
                'nomor_tanda_terima' => $receiptNumber,
                ...($sourceType === 'ttsj' ? ['no_tanda_terima' => $receiptNumber] : []),
            ]);

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
            if ($goods !== null) {
                $this->saveApprovalGoods($sourceType, $id, $goods);
            }
        });

        return back()->with('success', $withGoods
            ? 'Shipper dan detail barang approval berhasil disimpan.'
            : 'Shipper JB tanda terima dan manifest terkait berhasil disimpan.');
    }

    private function validateShipperSelection(Request $request): array
    {
        $validated = $request->validate([
            'shipper_jb_id' => 'required|integer|exists:shipper_consignees,id',
            'shipper' => 'sometimes|required|string',
            'alamat_shipper' => 'nullable|string',
            'npwp_shipper' => 'nullable|string',
            'consignee' => 'nullable|string',
            'alamat_consignee' => 'nullable|string',
            'npwp_consignee' => 'nullable|string',
            'notify_party_consignee' => 'nullable|string',
            'alamat_notify_party_consignee' => 'nullable|string',
            'npwp_notify_party_consignee' => 'nullable|string',
            'delivery_address_contact_person' => 'nullable|string',
            'document_ppftz_03' => 'nullable|string',
            'condition' => 'nullable|string',
            'status' => 'sometimes|required|boolean',
        ]);
        $shipper = ShipperConsignee::findOrFail($validated['shipper_jb_id']);
        $fields = collect($validated)->except('shipper_jb_id')->all();
        foreach ($fields as $field => $value) {
            if ($field !== 'status' && $value === '') {
                $fields[$field] = null;
            }
        }
        abort_unless(filled($fields['shipper'] ?? $shipper->shipper), 422, 'Nama shipper harus diisi.');

        return [$shipper, $fields];
    }

    private function saveShipperApprovalOverride(string $sourceType, int $sourceId, string $context, ShipperConsignee $shipper, array $fields): void
    {
        ApprovalTandaTerimaDuaShipperOverride::updateOrCreate(
            ['source_type' => $sourceType, 'source_id' => $sourceId, 'context' => $context],
            ['shipper_jb_id' => $shipper->id, 'fields' => $fields, 'updated_by' => auth()->id()]
        );
    }

    public function addFclShipper(Request $request, int $id): RedirectResponse
    {
        $item = TandaTerima::findOrFail($id);
        abort_unless($item->shipper_jb_id, 422, 'Pilih shipper utama terlebih dahulu.');
        [$shipper, $fields] = $this->validateShipperSelection($request);
        abort_if((int) $item->shipper_jb_id === (int) $shipper->id, 422, 'Shipper ini sudah menjadi shipper utama.');
        abort_if($item->fclShipperDetails()->where('shipper_jb_id', $shipper->id)->exists(), 422, 'Shipper ini sudah menjadi shipper tambahan.');

        DB::transaction(function () use ($item, $shipper, $fields) {
            $detail = ApprovalTandaTerimaDuaFclShipper::create([
                'tanda_terima_id' => $item->id,
                'shipper_jb_id' => $shipper->id,
                'updated_by' => auth()->id(),
            ]);
            $this->saveShipperApprovalOverride('fcl', $item->id, 'detail:'.$detail->id, $shipper, $fields);
        });

        return back()->with('success', 'Shipper tambahan ditambahkan. Lengkapi rincian barangnya sebelum ekspor.');
    }

    public function updateFclShipper(Request $request, int $id, int $detailId): RedirectResponse
    {
        $item = TandaTerima::findOrFail($id);
        $detail = $item->fclShipperDetails()->findOrFail($detailId);
        [$shipper, $fields] = $this->validateShipperSelection($request);
        abort_if((int) $item->shipper_jb_id === (int) $shipper->id, 422, 'Shipper ini sudah menjadi shipper utama.');
        abort_if($item->fclShipperDetails()->whereKeyNot($detail->id)->where('shipper_jb_id', $shipper->id)->exists(), 422, 'Shipper ini sudah menjadi shipper tambahan.');

        DB::transaction(function () use ($detail, $shipper, $fields, $id) {
            $this->saveShipperApprovalOverride('fcl', $id, 'detail:'.$detail->id, $shipper, $fields);
            $detail->update(['shipper_jb_id' => $shipper->id, 'updated_by' => auth()->id()]);
        });

        return back()->with('success', 'Data shipper tambahan berhasil disimpan.');
    }

    public function updateFclShipperGoods(Request $request, int $id, int $detailId): RedirectResponse
    {
        $detail = ApprovalTandaTerimaDuaFclShipper::where('tanda_terima_id', $id)->findOrFail($detailId);
        $validated = $request->validate([
            'manifest_goods.nama_barang' => 'required|string',
            'manifest_goods.hs_codes' => 'nullable|string|max:4000',
            'manifest_goods.jumlah' => 'nullable|integer|min:0',
            'manifest_goods.satuan' => 'nullable|string|max:100',
            'manifest_goods.ukuran' => 'nullable|string|max:255',
            'manifest_goods.tonase' => ['nullable', 'string', 'max:30', 'regex:/^(?:\d{1,3}(?:\.\d{3})+(?:,\d+)?|\d+(?:[.,]\d+)?)$/'],
            'manifest_goods.meter_kubik' => ['nullable', 'string', 'max:30', 'regex:/^(?:\d{1,3}(?:\.\d{3})+(?:,\d+)?|\d+(?:[.,]\d+)?)$/'],
            'manifest_goods.keterangan_barang' => 'nullable|string|max:2000',
            'goods.nama_barang' => 'required|string',
            'goods.jumlah' => 'nullable|integer|min:0',
            'goods.satuan' => 'nullable|string|max:100',
            'goods.ukuran' => 'nullable|string|max:255',
            'goods.keterangan_barang' => 'nullable|string|max:2000',
        ]);
        $manifest = $validated['manifest_goods'];
        $manifest['hs_codes'] = collect(preg_split('/\R/u', $manifest['hs_codes'] ?? ''))
            ->map(fn ($code) => trim($code))->filter()->values()->all();
        $manifest['hs_code'] = $manifest['hs_codes'][0] ?? null;
        foreach (['tonase', 'meter_kubik'] as $field) {
            if (filled($manifest[$field] ?? null)) {
                $manifest[$field] = $this->formatManifestNumber($manifest[$field]);
            }
        }
        $detail->update([
            'manifest_goods' => $manifest,
            'goods' => $validated['goods'],
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Rincian barang shipper berhasil disimpan.');
    }

    public function removeFclShipper(int $id, int $detailId): RedirectResponse
    {
        DB::transaction(function () use ($id, $detailId) {
            ApprovalTandaTerimaDuaFclShipper::where('tanda_terima_id', $id)->findOrFail($detailId)->delete();
            ApprovalTandaTerimaDuaShipperOverride::where('source_type', 'fcl')
                ->where('source_id', $id)->where('context', 'detail:'.$detailId)->delete();
        });

        return back()->with('success', 'Shipper tambahan dihapus.');
    }

    private function ensureFclDetailsComplete(TandaTerima $item): void
    {
        foreach ($item->fclShipperDetails as $detail) {
            abort_unless(
                filled(data_get($detail->manifest_goods, 'nama_barang')) && filled(data_get($detail->goods, 'nama_barang')),
                422,
                'Lengkapi barang manifest dan perincian untuk semua shipper tambahan pada tanda terima '.$item->no_surat_jalan.'.'
            );
        }
    }

    public function updateGoods(Request $request, string $sourceType, int $id): RedirectResponse
    {
        abort_unless(array_key_exists($sourceType, self::SOURCES), 404);
        self::SOURCES[$sourceType]::findOrFail($id);
        DB::transaction(function () use ($sourceType, $id, $request) {
            $this->saveApprovalGoods($sourceType, $id, $this->validateApprovalGoods($request));
            app(ManifestApprovalGoodsSync::class)->syncSource($sourceType, $id);
        });

        return back()->with('success', 'Detail barang approval berhasil disimpan dan Manifest terkait diperbarui.');
    }

    private function validateApprovalGoods(Request $request): array
    {
        $manifestInput = $request->input('manifest_goods');
        if (is_array($manifestInput)) {
            $request->merge(['manifest_goods' => collect($manifestInput)
                ->reject(fn ($good) => is_array($good) && collect($good)->every(fn ($value) => blank($value)))
                ->all()]);
        }
        $validated = $request->validate([
            'goods' => 'required|array|size:1',
            'goods.*.nama_barang' => 'required|string',
            'goods.*.jumlah' => 'nullable|integer|min:0',
            'goods.*.satuan' => 'nullable|string',
            'goods.*.ukuran' => 'nullable|string',
            'goods.*.keterangan_barang' => 'nullable|string',
            'manifest_goods' => 'nullable|array|max:1',
            'manifest_goods.*.nama_barang' => 'required|string',
            'manifest_goods.*.hs_code' => 'nullable|string',
            'manifest_goods.*.hs_codes' => 'nullable|string',
            'manifest_goods.*.jumlah' => 'nullable|integer|min:0',
            'manifest_goods.*.satuan' => 'nullable|string',
            'manifest_goods.*.ukuran' => 'nullable|string',
            'manifest_goods.*.meter_kubik' => ['nullable', 'string', 'max:30', 'regex:/^(?:\d{1,3}(?:\.\d{3})+(?:,\d+)?|\d+(?:[.,]\d+)?)$/'],
            'manifest_goods.*.tonase' => ['nullable', 'string', 'max:30', 'regex:/^(?:\d{1,3}(?:\.\d{3})+(?:,\d+)?|\d+(?:[.,]\d+)?)$/'],
            'manifest_goods.*.keterangan_barang' => 'nullable|string',
            'keterangan_barang' => 'nullable|string',
        ]);

        return $validated;
    }

    private function saveApprovalGoods(string $sourceType, int $id, array $validated): void
    {
        $goodsFields = ['nama_barang', 'jumlah', 'satuan', 'ukuran', 'keterangan_barang'];
        $manifestFields = ['nama_barang', 'jumlah', 'satuan', 'ukuran', 'meter_kubik', 'tonase', 'keterangan_barang'];
        $goods = collect($validated['goods'])->values()->map(fn ($good) => collect($goodsFields)
            ->mapWithKeys(fn ($field) => [$field => $good[$field] ?? null])
            ->all())->all();
        $manifestGoods = collect($validated['manifest_goods'] ?? [])->values()->map(function ($good) use ($manifestFields) {
            $item = collect($manifestFields)->mapWithKeys(fn ($field) => [$field => $good[$field] ?? null])->all();
            $hsCodesInput = $good['hs_codes'] ?? ($good['hs_code'] ?? '');
            $item['hs_codes'] = collect(preg_split('/\R/u', $hsCodesInput))
                ->map(fn ($code) => trim($code))
                ->filter(fn ($code) => $code !== '')
                ->values()
                ->all();
            $item['hs_code'] = $item['hs_codes'][0] ?? null;
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
        if ($sourceType === 'fcl') {
            abort_if($item->fclShipperDetails()->exists(), 422, 'Hapus shipper tambahan sebelum melepas shipper utama.');
        }
        $shipperId = $item->shipper_jb_id;

        if (! $shipperId) {
            return back()->with('success', 'Tanda terima ini belum memiliki shipper JB.');
        }

        DB::transaction(function () use ($item, $sourceType, $shipperId) {
            $item->update(['shipper_jb_id' => null]);
            ApprovalTandaTerimaDuaShipperOverride::where('source_type', $sourceType)
                ->where('source_id', $item->id)->where('context', 'main')->delete();

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
