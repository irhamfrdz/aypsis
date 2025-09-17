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

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        $permission->load('users');
        return view('master-permission.show', compact('permission'));
    }

    /**
     * Sync permissions with database
     */
    public function sync(Request $request)
    {
        try {
            // This would typically sync permissions from config or other sources
            // For now, just return success
            return response()->json(['success' => true, 'message' => 'Permissions synced successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Bulk delete permissions
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'permission_ids' => 'required|array|min:1',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        try {
            $permissionIds = $request->permission_ids;
            $deletedCount = 0;

            foreach ($permissionIds as $permissionId) {
                $permission = Permission::find($permissionId);
                if ($permission && $permission->users()->count() === 0) {
                    $permission->delete();
                    $deletedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} permissions berhasil dihapus",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Assign permission to users
     */
    public function assignUsers(Request $request, Permission $permission)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        try {
            $permission->users()->syncWithoutDetaching($request->user_ids);

            return response()->json([
                'success' => true,
                'message' => 'Permission berhasil diassign ke users',
                'assigned_count' => count($request->user_ids)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get users with this permission
     */
    public function getUsers(Permission $permission)
    {
        $users = $permission->users()->select('id', 'username', 'email')->get();

        return response()->json([
            'success' => true,
            'users' => $users,
            'count' => $users->count()
        ]);
    }
}
