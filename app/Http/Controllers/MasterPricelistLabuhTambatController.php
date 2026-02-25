<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterPricelistLabuhTambatController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!($user instanceof \App\Models\User) || !$user->can('master-pricelist-labuh-tambat-view')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat master pricelist labuh tambat.');
        }

        $query = \App\Models\MasterPricelistLabuhTambat::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_agen', 'like', '%' . $search . '%')
                  ->orWhere('nama_kapal', 'like', '%' . $search . '%')
                  ->orWhere('lokasi', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('lokasi')) {
            $query->where('lokasi', $request->lokasi);
        }

        $perPage = $request->get('per_page', 10);
        $pricelists = $query->orderBy('nama_agen', 'asc')->paginate($perPage)->withQueryString();

        return view('master-pricelist-labuh-tambat.index', compact('pricelists'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || !$user->can('master-pricelist-labuh-tambat-create')) {
            abort(403, 'Anda tidak memiliki akses untuk membuat master pricelist labuh tambat.');
        }

        $kapals = \App\Models\MasterKapal::aktif()->orderBy('nama_kapal')->get();
        return view('master-pricelist-labuh-tambat.create', compact('kapals'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || !$user->can('master-pricelist-labuh-tambat-create')) {
            abort(403, 'Anda tidak memiliki akses untuk membuat master pricelist labuh tambat.');
        }

        $validated = $request->validate([
            'nama_agen' => 'required|string|max:255',
            'nama_kapal' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'lokasi' => 'required|string|in:Jakarta,Batam,Pinang',
            'keterangan' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        \App\Models\MasterPricelistLabuhTambat::create($validated);

        return redirect()->route('master.master-pricelist-labuh-tambat.index')
            ->with('success', 'Pricelist labuh tambat berhasil ditambahkan.');
    }

    public function edit($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || !$user->can('master-pricelist-labuh-tambat-update')) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah master pricelist labuh tambat.');
        }

        $pricelist = \App\Models\MasterPricelistLabuhTambat::findOrFail($id);
        $kapals = \App\Models\MasterKapal::aktif()->orderBy('nama_kapal')->get();
        return view('master-pricelist-labuh-tambat.edit', compact('pricelist', 'kapals'));
    }

    public function update(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || !$user->can('master-pricelist-labuh-tambat-update')) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah master pricelist labuh tambat.');
        }

        $pricelist = \App\Models\MasterPricelistLabuhTambat::findOrFail($id);

        $validated = $request->validate([
            'nama_agen' => 'required|string|max:255',
            'nama_kapal' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'lokasi' => 'required|string|in:Jakarta,Batam,Pinang',
            'keterangan' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $pricelist->update($validated);

        return redirect()->route('master.master-pricelist-labuh-tambat.index')
            ->with('success', 'Pricelist labuh tambat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || !$user->can('master-pricelist-labuh-tambat-delete')) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus master pricelist labuh tambat.');
        }

        $pricelist = \App\Models\MasterPricelistLabuhTambat::findOrFail($id);
        $pricelist->delete();

        return redirect()->route('master.master-pricelist-labuh-tambat.index')
            ->with('success', 'Pricelist labuh tambat berhasil dihapus.');
    }
}
