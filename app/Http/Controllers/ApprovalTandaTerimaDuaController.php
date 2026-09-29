<?php

namespace App\Http\Controllers;

use App\Models\Manifest;
use App\Models\ShipperConsignee;
use App\Models\TandaTerima;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaTanpaSuratJalan;
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

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'type' => 'nullable|in:fcl,lcl,ttsj',
            'search' => 'nullable|string|max:100',
            'status' => 'nullable|in:belum,sudah',
        ]);
        $type = $filters['type'] ?? 'fcl';
        $query = self::SOURCES[$type]::query()->with('shipperJb');

        if ($request->filled('search')) {
            $search = trim($filters['search']);
            $numberColumn = $this->numberColumn($type);
            $senderColumn = $type === 'lcl' ? 'nama_pengirim' : 'pengirim';
            $query->where(function ($query) use ($search, $numberColumn, $senderColumn) {
                $query->where($numberColumn, 'like', "%{$search}%")
                    ->orWhere($senderColumn, 'like', "%{$search}%");
                if ($numberColumn === 'no_tanda_terima') {
                    $query->orWhere('nomor_tanda_terima', 'like', "%{$search}%");
                }
            });
        }

        if ($request->input('status') === 'belum') {
            $query->whereNull('shipper_jb_id');
        } elseif ($request->input('status') === 'sudah') {
            $query->whereNotNull('shipper_jb_id');
        }

        return view('approval-tanda-terima-2.index', [
            'type' => $type,
            'items' => $query->latest('id')->paginate(20)->withQueryString(),
            'shippers' => ShipperConsignee::whereNotNull('shipper')->where('shipper', '!=', '')
                ->orderBy('shipper')->get(['id', 'shipper', 'consignee']),
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

    private function numberColumn(string $type): string
    {
        return match ($type) {
            'fcl' => 'no_surat_jalan',
            'lcl' => 'nomor_tanda_terima',
            'ttsj' => 'no_tanda_terima',
        };
    }
}
