<?php

namespace App\Http\Controllers;

use App\Models\ApprovalTandaTerimaDuaGoods;
use App\Models\Manifest;
use App\Models\ShipperConsignee;
use App\Models\TandaTerima;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaTanpaSuratJalan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

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
            $query->where(function ($query) use ($search, $numberColumn, $senderColumn, $containerColumn, $type) {
                $query->where($numberColumn, 'like', "%{$search}%")
                    ->orWhere($senderColumn, 'like', "%{$search}%")
                    ->orWhere($containerColumn, 'like', "%{$search}%");
                if ($numberColumn === 'no_tanda_terima') {
                    $query->orWhere('nomor_tanda_terima', 'like', "%{$search}%");
                }
                if ($type === 'lcl') {
                    $query->orWhereHas('kontainerPivot', fn ($pivot) => $pivot->where('nomor_kontainer', 'like', "%{$search}%"));
                }
            });
        }

        if ($request->input('status') === 'belum') {
            $query->whereNull('shipper_jb_id');
        } elseif ($request->input('status') === 'sudah') {
            $query->whereNotNull('shipper_jb_id');
        }

        if (! empty($filters['destination'])) {
            $destination = $filters['destination'];
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

        return view('approval-tanda-terima-2.index', [
            'type' => $type,
            'items' => $items,
            'goodsOverrides' => $goodsOverrides,
        ]);
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
        $validated = $request->validate([
            'goods' => 'required|array|min:1|max:50',
            'goods.*.nama_barang' => 'required|string|max:255',
            'goods.*.hs_code' => 'nullable|string|max:255',
            'goods.*.jumlah' => 'nullable|integer|min:0',
            'goods.*.satuan' => 'nullable|string|max:100',
            'goods.*.ukuran' => 'nullable|string|max:255',
            'goods.*.panjang' => 'nullable|numeric|min:0',
            'goods.*.lebar' => 'nullable|numeric|min:0',
            'goods.*.tinggi' => 'nullable|numeric|min:0',
            'goods.*.meter_kubik' => 'nullable|numeric|min:0',
            'goods.*.tonase' => 'nullable|numeric|min:0',
            'goods.*.keterangan_barang' => 'nullable|string|max:2000',
            'keterangan_barang' => 'nullable|string|max:2000',
        ]);

        $fields = ['nama_barang', 'hs_code', 'jumlah', 'satuan', 'ukuran', 'panjang', 'lebar', 'tinggi', 'meter_kubik', 'tonase', 'keterangan_barang'];
        $goods = collect($validated['goods'])->values()->map(fn ($good) => collect($fields)
            ->mapWithKeys(fn ($field) => [$field => $good[$field] ?? null])
            ->all())->all();

        ApprovalTandaTerimaDuaGoods::updateOrCreate(
            ['source_type' => $sourceType, 'source_id' => $id],
            [
                'goods' => $goods,
                'keterangan_barang' => $sourceType === 'ttsj' ? ($validated['keterangan_barang'] ?? null) : null,
                'updated_by' => auth()->id(),
            ]
        );

        return back()->with('success', 'Detail barang approval berhasil disimpan tanpa mengubah data tanda terima asli.');
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
