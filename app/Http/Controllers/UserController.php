<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Karyawan;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;

use function PHPUnit\Framework\returnSelf;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        $users = User::with('karyawan')->get();
        return view('master-user.index', compact('users'));
    }

    /**
     * Menampilkan formulir untuk membuat pengguna baru.
     *
     * @return \Illuminate\View\View
     */

    public function create()
    {
        // Mengambil semua izin yang tersedia dari tabel permission
        $permissions = Permission::select('id', 'name', 'description')->get();
        $karyawans = Karyawan::select('id', 'nama_lengkap')->get();

        // Mengambil semua users dengan permissions untuk fitur copy
        $users = User::with('permissions:id,name')->select('id', 'name', 'username')->get();

        return view('master-user.create', compact('permissions', 'karyawans', 'users'));
    }

    /**
     * Menyimpan pengguna baru ke dalam database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'username'=>'required|string|max:255|unique:users',
            'password'=>'required|string|min:8|confirmed',
            'karyawan_id' => 'nullable|exists:karyawans,id',
            'simple_permissions' => 'nullable|array',
            'permissions' => 'nullable|array', // Fallback untuk permission lama
            'permissions.*' => 'exists:permissions,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password'=> Hash::make($request->password),
            'karyawan_id' => $request->karyawan_id,
        ]);

        // Handle simple permissions
        $permissionIds = [];
        if ($request->has('simple_permissions') && !empty($request->simple_permissions)) {
            $permissionIds = $this->convertSimplePermissionsToIds($request->simple_permissions);
        } elseif ($request->has('permissions')) {
            // Fallback untuk permission lama
            $permissionIds = $request->input('permissions', []);
        }

        // Melampirkan izin yang dipilih ke pengguna
        $user->permissions()->sync($permissionIds);

        return redirect()->route('master.user.index')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        // Mengambil semua izin yang tersedia dan izin yang dimiliki pengguna
        $permissions = Permission::select('id', 'name', 'description')->get();
        $userPermissions = $user->permissions->pluck('id')->toArray();
        $karyawans = Karyawan::select('id', 'nama_lengkap')->get();

        // Mengambil semua users dengan permissions untuk fitur copy (kecuali user yang sedang diedit)
        $users = User::with('permissions:id,name')->select('id', 'name', 'username')->where('id', '!=', $user->id)->get();

        // Convert user permissions to simple permission names for the view
        $userSimplePermissions = $user->permissions->pluck('name')->toArray();

        return view('master-user.edit', compact('user', 'permissions', 'userPermissions', 'userSimplePermissions', 'karyawans', 'users'));

    }

    /**
     * Memperbarui data pengguna yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'karyawan_id' => 'nullable|exists:karyawans,id',
            'simple_permissions' => 'nullable|array',
            'permissions' => 'nullable|array', // Fallback untuk permission lama
            'permissions.*' => 'exists:permissions,id',
        ]);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->karyawan_id = $request->karyawan_id;

        if($request->password){
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Handle simple permissions
        $permissionIds = [];
        if ($request->has('simple_permissions') && !empty($request->simple_permissions)) {
            $permissionIds = $this->convertSimplePermissionsToIds($request->simple_permissions);
        } elseif ($request->has('permissions')) {
            // Fallback untuk permission lama
            $permissionIds = $request->input('permissions', []);
        }

        // Memperbarui izin pengguna
        $user->permissions()->sync($permissionIds);

        return redirect()->route('master.user.index')->with('success','Pengguna berhasil diperbarui!');
    }

    /**
     * Menghapus pengguna dari database.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy(User $user)
    {
        //Menghapus relasi izin sebelum menghapus pengguna
        $user->permissions()->detach();
        $user->delete();
        return redirect()->route('master.user.index')->with('success','Pengguna berhsail dihapus!');
    }

    /**
     * Assign permissions from a template to a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignTemplate(Request $request, User $user)
    {
        $request->validate([
            'template' => 'required|string',
        ]);

        $templates = config('permission_templates', []);
        $templateKey = $request->template;

        if (!isset($templates[$templateKey])) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        $templatePermissions = $templates[$templateKey]['permissions'];

        // Get permission IDs from names
        $permissionIds = Permission::whereIn('name', $templatePermissions)->pluck('id')->toArray();

        // Sync permissions
        $user->permissions()->sync($permissionIds);

        return response()->json([
            'success' => true,
            'message' => 'Template permissions assigned successfully',
            'assigned_count' => count($permissionIds)
        ]);
    }

    /**
     * Bulk assign permissions to multiple users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAssignPermissions(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
            'action' => 'required|in:add,remove,replace',
        ]);

        $userIds = $request->user_ids;
        $permissionIds = $request->permission_ids;
        $action = $request->action;

        $users = User::whereIn('id', $userIds)->get();
        $affectedCount = 0;

        foreach ($users as $user) {
            if ($action === 'add') {
                $user->permissions()->syncWithoutDetaching($permissionIds);
            } elseif ($action === 'remove') {
                $user->permissions()->detach($permissionIds);
            } elseif ($action === 'replace') {
                $user->permissions()->sync($permissionIds);
            }
            $affectedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Permissions {$action}ed for {$affectedCount} users",
            'affected_users' => $affectedCount
        ]);
    }

    /**
     * Get user's current permissions for AJAX requests.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserPermissions(User $user)
    {
        $permissions = $user->permissions()->select('id', 'name', 'description')->get();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
            ],
            'permissions' => $permissions,
            'count' => $permissions->count()
        ]);
    }

    /**
     * Convert simple permissions to permission IDs
     *
     * @param array $simplePermissions
     * @return array
     */
    private function convertSimplePermissionsToIds(array $simplePermissions): array
    {
        $permissionIds = [];

        foreach ($simplePermissions as $simplePerm) {
            // Find permission by name
            $permission = Permission::where('name', $simplePerm)->first();
            if ($permission) {
                $permissionIds[] = $permission->id;
            }
        }

        return $permissionIds;
    }

        /**
     * Get user's permissions for copying (without middleware for AJAX use).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Get user's permissions for copying (without middleware for AJAX use).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserPermissionsForCopy(User $user)
    {
        $permissions = $user->permissions->pluck('name')->toArray();

        return response()->json([
            'success' => true,
            'permissions' => $permissions,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username
            ],
            'count' => count($permissions)
        ]);
    }

    /**
     * Show bulk permission management page.
     *
     * @return \Illuminate\View\View
     */
    public function bulkManage()
    {
        $users = User::with('permissions')->select('id', 'name', 'username')->get();
        $permissions = Permission::select('id', 'name', 'description')->get();

        return view('master-user.bulk-manage', compact('users', 'permissions'));
    }
}
