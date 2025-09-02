<?php

namespace App\Http\Controllers;

use App\Models\TagihanKontainerSewa;
use Illuminate\Http\Request;

class TagihanKontainerSewaController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $tagihan = TagihanKontainerSewa::search($q)->orderBy('tanggal_harga_awal', 'desc')->paginate(15);
        return view('tagihan-kontainer-sewa.index', ['tagihanKontainerSewa' => $tagihan]);
    }

    public function create()
    {
        return view('tagihan-kontainer-sewa.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor' => 'required|string|max:255',
            'nomor_kontainer' => 'nullable|string|max:2000',
            'group' => 'nullable|string|max:255',
            'tanggal_harga_awal' => 'nullable|date',
            'tanggal_harga_akhir' => 'nullable|date',
            'periode' => 'nullable|string|max:50',
            'masa' => 'nullable|string|max:255',
            'dpp' => 'nullable|numeric',
            'dpp_nilai_lain' => 'nullable|numeric',
            'ppn' => 'nullable|numeric',
            'pph' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric',
        ]);

        $model = TagihanKontainerSewa::create($data);
        return redirect()->route('tagihan-kontainer-sewa.index')->with('success', 'Tagihan created');
    }

    public function show($id)
    {
        $tagihan = TagihanKontainerSewa::findOrFail($id);
        return view('tagihan-kontainer-sewa.show', compact('tagihan'));
    }

    public function edit($id)
    {
        $tagihan = TagihanKontainerSewa::findOrFail($id);
        return view('tagihan-kontainer-sewa.edit', compact('tagihan'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'vendor' => 'required|string|max:255',
            'nomor_kontainer' => 'nullable|string|max:2000',
            'group' => 'nullable|string|max:255',
            'tanggal_harga_awal' => 'nullable|date',
            'tanggal_harga_akhir' => 'nullable|date',
            'periode' => 'nullable|string|max:50',
            'masa' => 'nullable|string|max:255',
            'dpp' => 'nullable|numeric',
            'dpp_nilai_lain' => 'nullable|numeric',
            'ppn' => 'nullable|numeric',
            'pph' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric',
        ]);
        $tagihan = TagihanKontainerSewa::findOrFail($id);
        $tagihan->update($data);
        return redirect()->route('tagihan-kontainer-sewa.index')->with('success', 'Tagihan updated');
    }

    public function destroy($id)
    {
        $tagihan = TagihanKontainerSewa::findOrFail($id);
        $tagihan->delete();
        return redirect()->route('tagihan-kontainer-sewa.index')->with('success', 'Tagihan deleted');
    }

    // Lightweight AJAX search used by the view for container number heuristics
    public function searchByKontainer(Request $request)
    {
        $q = $request->query('q');
        if (!$q) return response()->json(['data' => []]);
        $items = TagihanKontainerSewa::where('nomor_kontainer', 'like', '%' . $q . '%')
            ->selectRaw('vendor, DATE(tanggal_harga_awal) as tanggal, group_code')
            ->groupBy('vendor', 'tanggal')
            ->limit(20)
            ->get()
            ->map(function ($r) {
                return ['vendor' => $r->vendor, 'tanggal' => (string)$r->tanggal, 'group' => $r->group_code];
            });
        return response()->json(['data' => $items]);
    }
}
