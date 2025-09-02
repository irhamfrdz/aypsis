<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
    $permissions = Permission::latest()->paginate(20);

    // No route scanning here anymore. The view receives only the curated permission list.
    return view('master-permission.index', compact('permissions'));
    }

    public function create()
    {
    // allow prefilling the name from querystring (quick-create from detected features)
    $prefill = request()->query('name');
    return view('master-permission.create', ['prefill' => $prefill]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name|max:255|regex:/^[a-z\-]+$/',
            'description' => 'nullable|string|max:255',
        ], [
            'name.regex' => 'Nama izin hanya boleh berisi huruf kecil dan tanda hubung (-).'
        ]);

        Permission::create($validated);

        return redirect()->route('master.permission.index')
                         ->with('success', 'Izin berhasil ditambahkan.');
    }

    public function edit(Permission $permission)
    {
        return view('master-permission.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id . '|max:255|regex:/^[a-z\-]+$/',
            'description' => 'nullable|string|max:255',
        ], [
            'name.regex' => 'Nama izin hanya boleh berisi huruf kecil dan tanda hubung (-).'
        ]);

        $permission->update($validated);

        return redirect()->route('master.permission.index')
                         ->with('success', 'Izin berhasil diperbarui.');
    }

    public function destroy(Permission $permission)
    {
        // Cek apakah izin masih digunakan oleh user
        if ($permission->users()->exists()) {
            return back()->with('error', 'Izin tidak dapat dihapus karena masih digunakan oleh pengguna.');
        }

        $permission->delete();

        return redirect()->route('master.permission.index')
                         ->with('success', 'Izin berhasil dihapus.');
    }
}
