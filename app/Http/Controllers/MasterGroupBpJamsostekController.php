<?php

namespace App\Http\Controllers;

use App\Models\MasterGroupBpJamsostek;
use Illuminate\Http\Request;

class MasterGroupBpJamsostekController extends Controller
{
    public function index()
    {
        $groups = MasterGroupBpJamsostek::orderBy('nama_group')->get();
        return view('master-group-bp-jamsostek.index', compact('groups'));
    }

    public function create()
    {
        // Not used (using modal)
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_group' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,tidak aktif',
        ]);

        MasterGroupBpJamsostek::create($request->all());

        return redirect()->route('master-group-bp-jamsostek.index')
            ->with('success', 'Group BP Jamsostek berhasil ditambahkan.');
    }

    public function show(MasterGroupBpJamsostek $master_group_bp_jamsostek)
    {
        // Not used
    }

    public function edit(MasterGroupBpJamsostek $master_group_bp_jamsostek)
    {
        // Not used (using modal)
    }

    public function update(Request $request, MasterGroupBpJamsostek $master_group_bp_jamsostek)
    {
        $request->validate([
            'nama_group' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,tidak aktif',
        ]);

        $master_group_bp_jamsostek->update($request->all());

        return redirect()->route('master-group-bp-jamsostek.index')
            ->with('success', 'Group BP Jamsostek berhasil diperbarui.');
    }

    public function destroy(MasterGroupBpJamsostek $master_group_bp_jamsostek)
    {
        $master_group_bp_jamsostek->delete();

        return redirect()->route('master-group-bp-jamsostek.index')
            ->with('success', 'Group BP Jamsostek berhasil dihapus.');
    }
}
