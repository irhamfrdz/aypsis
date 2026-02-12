<?php

namespace App\Http\Controllers;

use App\Models\MasterPricelistKanisirBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterPricelistKanisirBanController extends Controller
{
    public function index()
    {
        $pricelists = MasterPricelistKanisirBan::latest()->paginate(15);
        return view('master.pricelist-kanisir-ban.index', compact('pricelists'));
    }

    public function create()
    {
        return view('master.pricelist-kanisir-ban.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor' => 'required|string|max:255',
            'harga_1000_kawat' => 'required|numeric|min:0',
            'harga_1000_benang' => 'required|numeric|min:0',
            'harga_900_kawat' => 'required|numeric|min:0',
            'harga_900_benang' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        MasterPricelistKanisirBan::create(array_merge($request->all(), [
            'created_by' => Auth::id()
        ]));

        return redirect()->route('master.pricelist-kanisir-ban.index')->with('success', 'Pricelist Kanisir Ban berhasil ditambahkan');
    }

    public function edit($id)
    {
        $pricelist = MasterPricelistKanisirBan::findOrFail($id);
        return view('master.pricelist-kanisir-ban.edit', compact('pricelist'));
    }

    public function update(Request $request, $id)
    {
        $pricelist = MasterPricelistKanisirBan::findOrFail($id);

        $request->validate([
            'vendor' => 'required|string|max:255',
            'harga_1000_kawat' => 'required|numeric|min:0',
            'harga_1000_benang' => 'required|numeric|min:0',
            'harga_900_kawat' => 'required|numeric|min:0',
            'harga_900_benang' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $pricelist->update(array_merge($request->all(), [
            'updated_by' => Auth::id()
        ]));

        return redirect()->route('master.pricelist-kanisir-ban.index')->with('success', 'Pricelist Kanisir Ban berhasil diperbarui');
    }

    public function destroy($id)
    {
        $pricelist = MasterPricelistKanisirBan::findOrFail($id);
        $pricelist->delete();

        return redirect()->route('master.pricelist-kanisir-ban.index')->with('success', 'Pricelist Kanisir Ban berhasil dihapus');
    }
}
