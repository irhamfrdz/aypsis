<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistTujuanKontainerSewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterPricelistTujuanKontainerSewaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('master-pricelist-tujuan-kontainer-sewa-view')) {
            abort(403, 'Unauthorized');
        }

        $query = MasterPricelistTujuanKontainerSewa::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('tujuan', 'like', "%{$search}%");
        }

        $pricelists = $query->latest()->paginate(15)->appends($request->query());

        return view('master-pricelist-tujuan-kontainer-sewa.index', compact('pricelists'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->can('master-pricelist-tujuan-kontainer-sewa-create')) {
            abort(403, 'Unauthorized');
        }

        return view('master-pricelist-tujuan-kontainer-sewa.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('master-pricelist-tujuan-kontainer-sewa-create')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'tujuan' => 'required|string|max:255',
            'ongkos_truk_20ft' => 'required|numeric|min:0',
            'ongkos_truk_40ft' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        MasterPricelistTujuanKontainerSewa::create($validated);

        return redirect()->route('master-pricelist-tujuan-kontainer-sewa.index')
            ->with('success', 'Pricelist tujuan berhasil ditambahkan');
    }

    public function edit(MasterPricelistTujuanKontainerSewa $masterPricelistTujuanKontainerSewa)
    {
        $user = Auth::user();
        if (!$user->can('master-pricelist-tujuan-kontainer-sewa-update')) {
            abort(403, 'Unauthorized');
        }

        return view('master-pricelist-tujuan-kontainer-sewa.edit', [
            'pricelist' => $masterPricelistTujuanKontainerSewa
        ]);
    }

    public function update(Request $request, MasterPricelistTujuanKontainerSewa $masterPricelistTujuanKontainerSewa)
    {
        $user = Auth::user();
        if (!$user->can('master-pricelist-tujuan-kontainer-sewa-update')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'tujuan' => 'required|string|max:255',
            'ongkos_truk_20ft' => 'required|numeric|min:0',
            'ongkos_truk_40ft' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $masterPricelistTujuanKontainerSewa->update($validated);

        return redirect()->route('master-pricelist-tujuan-kontainer-sewa.index')
            ->with('success', 'Pricelist tujuan berhasil diperbarui');
    }

    public function destroy(MasterPricelistTujuanKontainerSewa $masterPricelistTujuanKontainerSewa)
    {
        $user = Auth::user();
        if (!$user->can('master-pricelist-tujuan-kontainer-sewa-delete')) {
            abort(403, 'Unauthorized');
        }

        $masterPricelistTujuanKontainerSewa->delete();

        return redirect()->route('master-pricelist-tujuan-kontainer-sewa.index')
            ->with('success', 'Pricelist tujuan berhasil dihapus');
    }
}
