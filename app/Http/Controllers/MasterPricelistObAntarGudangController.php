<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistObAntarGudang;
use App\Models\Gudang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class MasterPricelistObAntarGudangController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterPricelistObAntarGudang::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('size_kontainer', 'like', "%{$search}%")
                    ->orWhere('status_kontainer', 'like', "%{$search}%")
                    ->orWhere('status_service', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('gudangTujuan', fn ($gudangQuery) => $gudangQuery->where('nama_gudang', 'like', "%{$search}%"));
            });
        }

        foreach (['size_kontainer', 'status_kontainer', 'status_service'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('gudang_tujuan_id')) {
            if ($request->input('gudang_tujuan_id') === '0') {
                $query->whereNull('gudang_tujuan_id');
            } else {
                $query->where('gudang_tujuan_id', $request->input('gudang_tujuan_id'));
            }
        }

        $pricelists = $query->with('gudangTujuan')->orderBy('size_kontainer')
            ->orderBy('gudang_tujuan_id')
            ->orderBy('status_kontainer')
            ->orderBy('status_service')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        $gudangs = Gudang::orderBy('nama_gudang')->get(['id', 'nama_gudang']);

        return view('master.pricelist-ob-antar-gudang.index', compact('pricelists', 'gudangs'));
    }

    public function create()
    {
        return view('master.pricelist-ob-antar-gudang.create', $this->options());
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        if ($this->combinationExists($validated)) {
            return back()->with('error', 'Kombinasi size, status kontainer, dan status service sudah ada.')->withInput();
        }

        MasterPricelistObAntarGudang::create($validated);

        return redirect()->route('master.pricelist-ob-antar-gudang.index')
            ->with('success', 'Pricelist OB Antar Gudang berhasil ditambahkan.');
    }

    public function edit(MasterPricelistObAntarGudang $pricelistObAntarGudang)
    {
        return view('master.pricelist-ob-antar-gudang.edit', array_merge(
            ['pricelist' => $pricelistObAntarGudang],
            $this->options()
        ));
    }

    public function update(Request $request, MasterPricelistObAntarGudang $pricelistObAntarGudang)
    {
        $validated = $this->validateData($request);

        if ($this->combinationExists($validated, $pricelistObAntarGudang->id)) {
            return back()->with('error', 'Kombinasi size, status kontainer, dan status service sudah ada.')->withInput();
        }

        $pricelistObAntarGudang->update($validated);

        return redirect()->route('master.pricelist-ob-antar-gudang.index')
            ->with('success', 'Pricelist OB Antar Gudang berhasil diperbarui.');
    }

    public function destroy(MasterPricelistObAntarGudang $pricelistObAntarGudang)
    {
        $pricelistObAntarGudang->delete();

        return redirect()->route('master.pricelist-ob-antar-gudang.index')
            ->with('success', 'Pricelist OB Antar Gudang berhasil dihapus.');
    }

    private function options(): array
    {
        return [
            'gudangs' => Gudang::orderBy('nama_gudang')->get(['id', 'nama_gudang', 'lokasi']),
            'sizeOptions' => MasterPricelistObAntarGudang::getSizeKontainerOptions(),
            'statusOptions' => MasterPricelistObAntarGudang::getStatusKontainerOptions(),
            'statusServiceOptions' => MasterPricelistObAntarGudang::getStatusServiceOptions(),
        ];
    }

    private function validateData(Request $request): array
    {
        $validated = Validator::make($request->all(), [
            'size_kontainer' => 'required|in:20ft,40ft',
            'status_kontainer' => [
                'nullable',
                'in:full,empty',
                Rule::requiredIf(function () use ($request) {
                    if ($request->input('status_service') === 'service') {
                        return false;
                    }

                    $namaGudangTujuan = Gudang::whereKey($request->input('gudang_tujuan_id'))->value('nama_gudang');

                    return ! str_contains(mb_strtolower($namaGudangTujuan ?? ''), 'temas jkt');
                }),
            ],
            'status_service' => 'required|in:service,non_service',
            'gudang_tujuan_id' => 'nullable|exists:gudangs,id',
            'biaya' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ])->validate();

        $namaGudangTujuan = Gudang::whereKey($validated['gudang_tujuan_id'] ?? null)->value('nama_gudang');
        if ($validated['status_service'] === 'service' || str_contains(mb_strtolower($namaGudangTujuan ?? ''), 'temas jkt')) {
            $validated['status_kontainer'] = null;
        }

        return $validated;
    }

    private function combinationExists(array $data, ?int $exceptId = null): bool
    {
        return MasterPricelistObAntarGudang::where('size_kontainer', $data['size_kontainer'])
            ->when($data['status_kontainer'] === null,
                fn ($query) => $query->whereNull('status_kontainer'),
                fn ($query) => $query->where('status_kontainer', $data['status_kontainer']))
            ->where('status_service', $data['status_service'])
            ->when(empty($data['gudang_tujuan_id']),
                fn ($query) => $query->whereNull('gudang_tujuan_id'),
                fn ($query) => $query->where('gudang_tujuan_id', $data['gudang_tujuan_id']))
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->exists();
    }
}
