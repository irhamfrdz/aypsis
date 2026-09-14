<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistObAntarGudang;
use Illuminate\Http\Request;
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
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        foreach (['size_kontainer', 'status_kontainer', 'status_service'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        $pricelists = $query->orderBy('size_kontainer')
            ->orderBy('status_kontainer')
            ->orderBy('status_service')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return view('master.pricelist-ob-antar-gudang.index', compact('pricelists'));
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
            'sizeOptions' => MasterPricelistObAntarGudang::getSizeKontainerOptions(),
            'statusOptions' => MasterPricelistObAntarGudang::getStatusKontainerOptions(),
            'statusServiceOptions' => MasterPricelistObAntarGudang::getStatusServiceOptions(),
        ];
    }

    private function validateData(Request $request): array
    {
        return Validator::make($request->all(), [
            'size_kontainer' => 'required|in:20ft,40ft',
            'status_kontainer' => 'required|in:full,empty',
            'status_service' => 'required|in:service,non_service',
            'biaya' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ])->validate();
    }

    private function combinationExists(array $data, ?int $exceptId = null): bool
    {
        return MasterPricelistObAntarGudang::where('size_kontainer', $data['size_kontainer'])
            ->where('status_kontainer', $data['status_kontainer'])
            ->where('status_service', $data['status_service'])
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->exists();
    }
}
