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
        $users = User::with('permissions:id,name')->select('id', 'username')->get();

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
            'username'=>'required|string|max:255|unique:users',
            'password'=>'required|string|min:8|confirmed',
            'karyawan_id' => 'nullable|exists:karyawans,id',
            'simple_permissions' => 'nullable|array', // Legacy support
            'permissions' => 'nullable|array', // New matrix permissions
        ]);

        $user = User::create([
            'username' => $request->username,
            'password'=> Hash::make($request->password),
            'karyawan_id' => $request->karyawan_id,
        ]);

        // Handle permissions - prioritize new matrix format
        $permissionIds = [];
        if ($request->has('permissions') && !empty($request->permissions)) {
            $permissionIds = $this->convertMatrixPermissionsToIds($request->permissions);
        } elseif ($request->has('simple_permissions') && !empty($request->simple_permissions)) {
            // Fallback to simple permissions
            $permissionIds = $this->convertSimplePermissionsToIds($request->simple_permissions);
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
        $users = User::with('permissions:id,name')->select('id', 'username')->where('id', '!=', $user->id)->get();

        // Convert user permissions to simple permission names for the view
        $userSimplePermissions = $user->permissions->pluck('name')->toArray();

        // Convert user permissions to matrix format for the new permission matrix system
        $userMatrixPermissions = $this->convertPermissionsToMatrix($userSimplePermissions);

        return view('master-user.edit', compact('user', 'permissions', 'userPermissions', 'userSimplePermissions', 'userMatrixPermissions', 'karyawans', 'users'));
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
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'karyawan_id' => 'nullable|exists:karyawans,id',
            'simple_permissions' => 'nullable|array', // Legacy support
            'permissions' => 'nullable|array', // New matrix permissions
        ]);

        $user->username = $request->username;
        $user->karyawan_id = $request->karyawan_id;

        if($request->password){
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Handle permissions - prioritize new matrix format
        $permissionIds = [];
        if ($request->has('permissions') && !empty($request->permissions)) {
            $permissionIds = $this->convertMatrixPermissionsToIds($request->permissions);
        } elseif ($request->has('simple_permissions') && !empty($request->simple_permissions)) {
            // Fallback to simple permissions
            $permissionIds = $this->convertSimplePermissionsToIds($request->simple_permissions);
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
     * Convert permission names to matrix format for the view
     *
     * @param array $permissionNames
     * @return array
     */
    private function convertPermissionsToMatrix(array $permissionNames): array
    {
        $matrixPermissions = [];

        foreach ($permissionNames as $permissionName) {
            // Skip if not a string
            if (!is_string($permissionName)) {
                continue;
            }

            // Priority order: dot notation first, then dash notation, then simple

            // Pattern 1: module.submodule.action (e.g., master.karyawan.index) - HIGHEST PRIORITY
            if (strpos($permissionName, '.') !== false) {
                $parts = explode('.', $permissionName);
                if (count($parts) >= 3 && $parts[0] === 'master') {
                    // For master.karyawan.index format
                    $module = $parts[0] . '-' . $parts[1]; // master-karyawan
                    $action = $parts[2]; // index

                    // Initialize module array if not exists
                    if (!isset($matrixPermissions[$module])) {
                        $matrixPermissions[$module] = [];
                    }

                    // Map database actions to matrix actions
                    $actionMap = [
                        'index' => 'view',
                        'create' => 'create',
                        'store' => 'create',
                        'show' => 'view',
                        'edit' => 'update',
                        'update' => 'update',
                        'destroy' => 'delete',
                        'print' => 'print',
                        'export' => 'export',
                        'import' => 'import',
                        'approve' => 'approve',
                        'template' => 'template',
                        'single' => 'print'
                    ];

                    $mappedAction = isset($actionMap[$action]) ? $actionMap[$action] : $action;
                    $matrixPermissions[$module][$mappedAction] = true;
                    continue; // Skip other patterns
                } elseif (count($parts) >= 2) {
                    // Handle other dot notation patterns
                    $module = $parts[0];
                    $action = $parts[1];

                    // Special handling for specific modules
                    if ($module === 'pembayaran-pranota-tagihan-kontainer') {
                        $module = 'pembayaran-pranota-tagihan-kontainer';
                    } elseif ($module === 'admin') {
                        // Handle admin permissions
                        if ($action === 'debug') {
                            $action = 'debug';
                        } elseif ($action === 'features') {
                            $action = 'features';
                        } elseif (strpos($action, 'user-approval') !== false) {
                            $action = str_replace('user-approval.', 'user-approval-', $action);
                        }
                    } elseif ($module === 'profile') {
                        // Handle profile permissions
                        if ($action === 'show') {
                            $action = 'view';
                        } elseif ($action === 'edit' || strpos($action, 'update') !== false) {
                            $action = 'update';
                        } elseif ($action === 'destroy') {
                            $action = 'delete';
                        }
                    } elseif ($module === 'supir') {
                        // Handle supir permissions
                        if ($action === 'dashboard') {
                            $action = 'dashboard';
                        } elseif ($action === 'checkpoint') {
                            $action = 'checkpoint';
                        }
                    } elseif ($module === 'approval') {
                        // Handle approval permissions
                        if ($action === 'dashboard') {
                            $action = 'dashboard';
                        } elseif ($action === 'mass_process') {
                            $action = 'mass_process';
                        } elseif ($action === 'create' || $action === 'store') {
                            $action = 'create';
                        } elseif ($action === 'riwayat') {
                            $action = 'riwayat';
                        }
                    } elseif ($module === 'tagihan-kontainer-sewa') {
                        // Handle tagihan-kontainer-sewa permissions
                        if ($action === 'group' && isset($parts[2])) {
                            $action = 'group_' . $parts[2];
                        }
                    }

                    // Initialize module array if not exists
                    if (!isset($matrixPermissions[$module])) {
                        $matrixPermissions[$module] = [];
                    }

                    // Map database actions to matrix actions
                    $actionMap = [
                        'index' => 'view',
                        'create' => 'create',
                        'store' => 'create',
                        'show' => 'view',
                        'edit' => 'update',
                        'update' => 'update',
                        'destroy' => 'delete',
                        'print' => 'print',
                        'export' => 'export',
                        'import' => 'import',
                        'approve' => 'approve',
                        'history' => 'history'
                    ];

                    $mappedAction = isset($actionMap[$action]) ? $actionMap[$action] : $action;
                    $matrixPermissions[$module][$mappedAction] = true;
                    continue; // Skip other patterns
                }
            }

            // Pattern 2: Master permissions (e.g., master-pranota-tagihan-kontainer) - CHECK BEFORE DASH PATTERN
            if (strpos($permissionName, 'master-') === 0) {
                $module = $permissionName;

                // Initialize module array if not exists
                if (!isset($matrixPermissions[$module])) {
                    $matrixPermissions[$module] = [];
                }

                // Set access permission to true for master permissions
                $matrixPermissions[$module]['access'] = true;
                continue; // Skip other patterns
            }

            // Special case: user-approval should be treated as single module
            if ($permissionName === 'user-approval') {
                $module = 'user-approval';

                // Initialize module array if not exists
                if (!isset($matrixPermissions[$module])) {
                    $matrixPermissions[$module] = [];
                }

                $matrixPermissions[$module]['view'] = true;
                continue; // Skip other patterns
            }

            // Pattern 3: module-action (e.g., dashboard-view, master-karyawan-view)
            if (strpos($permissionName, '-') !== false) {
                $parts = explode('-', $permissionName, 2);
                if (count($parts) == 2) {
                    $module = $parts[0];
                    $action = $parts[1];

                    // Special handling for master-* permissions
                    if ($module === 'master' && strpos($action, '-') !== false) {
                        // For master-karyawan-view, split further
                        $subParts = explode('-', $action, 2);
                        if (count($subParts) == 2) {
                            $module = $module . '-' . $subParts[0]; // master-karyawan
                            $action = $subParts[1]; // view
                        }
                    }

                    // Special handling for tagihan-kontainer-* permissions
                    if ($module === 'tagihan' && strpos($action, 'kontainer-') === 0) {
                        // For tagihan-kontainer-view, split further
                        $action = str_replace('kontainer-', '', $action); // Remove 'kontainer-' prefix
                        $module = 'tagihan-kontainer'; // Set module to tagihan-kontainer
                    }

                    // Special handling for pranota-supir-* permissions
                    if ($module === 'pranota' && strpos($action, 'supir-') === 0) {
                        $action = str_replace('supir-', '', $action);
                        $module = 'pranota-supir';
                    }

                    // Special handling for pembayaran-pranota-supir-* permissions
                    if ($module === 'pembayaran' && strpos($action, 'pranota-supir-') === 0) {
                        $action = str_replace('pranota-supir-', '', $action);
                        $module = 'pembayaran-pranota-supir';
                    }

                    // Special handling for perbaikan-kontainer-* permissions
                    if ($module === 'perbaikan' && strpos($action, 'kontainer-') === 0) {
                        $action = str_replace('kontainer-', '', $action);
                        $module = 'perbaikan-kontainer';
                    }

                    // Skip approve action for tagihan-kontainer since it's not implemented yet
                    if ($module === 'tagihan-kontainer' && $action === 'approve') {
                        continue; // Skip processing this permission
                    }

                    // Special handling for permohonan-* permissions
                    if ($module === 'permohonan' && in_array($action, ['create', 'view', 'edit', 'delete'])) {
                        $module = 'permohonan';
                        // Action remains the same
                    }

                    // Special handling for profile-* permissions
                    if ($module === 'profile' && in_array($action, ['show', 'edit', 'update', 'destroy'])) {
                        $module = 'profile';
                        // Action remains the same
                    }

                    // Special handling for supir-* permissions
                    if ($module === 'supir' && in_array($action, ['dashboard', 'checkpoint'])) {
                        $module = 'supir';
                        // Action remains the same
                    }

                    // Special handling for approval-* permissions
                    if ($module === 'approval' && in_array($action, ['dashboard', 'mass_process', 'create', 'riwayat'])) {
                        $module = 'approval';
                        // Action remains the same
                    }

                    // Skip approve action for tagihan-kontainer since it's not implemented yet
                    if ($module === 'tagihan-kontainer' && $action === 'approve') {
                        continue; // Skip processing this permission
                    }

                    // Initialize module array if not exists
                    if (!isset($matrixPermissions[$module])) {
                        $matrixPermissions[$module] = [];
                    }

                    // Map database actions to matrix actions
                    $actionMap = [
                        'view' => 'view',
                        'create' => 'create',
                        'update' => 'update',
                        'edit' => 'update',
                        'delete' => 'delete',
                        'destroy' => 'delete',
                        'print' => 'print',
                        'export' => 'export',
                        'import' => 'import',
                        'approve' => 'approve'
                    ];

                    $mappedAction = isset($actionMap[$action]) ? $actionMap[$action] : $action;
                    $matrixPermissions[$module][$mappedAction] = true;
                    continue; // Skip other patterns
                }
            }

            // Pattern 4: Simple module names (e.g., master-karyawan) - ONLY if no separators found
            if (strpos($permissionName, '-') === false && strpos($permissionName, '.') === false) {
                $module = $permissionName;

                // Initialize module array if not exists
                if (!isset($matrixPermissions[$module])) {
                    $matrixPermissions[$module] = [];
                }

                // Set view permission to true for simple permissions
                $matrixPermissions[$module]['view'] = true;
            }

            // Pattern 5: Admin permissions (e.g., admin.debug.perms, admin.features)
            if (strpos($permissionName, 'admin.') === 0) {
                $parts = explode('.', $permissionName);
                if (count($parts) >= 2) {
                    $module = 'admin';
                    $action = $parts[1];

                    // Initialize module array if not exists
                    if (!isset($matrixPermissions[$module])) {
                        $matrixPermissions[$module] = [];
                    }

                    // Map admin actions
                    if ($action === 'debug') {
                        $matrixPermissions[$module]['debug'] = true;
                    } elseif ($action === 'features') {
                        $matrixPermissions[$module]['features'] = true;
                    }
                }

                // Always include module-level permission for view/access actions
                if (in_array($action, ['view', 'access'])) {
                    // Exact module name (dash form)
                    $modulePerm = Permission::where('name', $module)->first();
                    if ($modulePerm) {
                        $permissionIds[] = $modulePerm->id;
                    }

                    // Dot-form variant of module (e.g., master.karyawan)
                    $dotModule = str_replace('-', '.', $module);
                    $modulePermDot = Permission::where('name', $dotModule)->first();
                    if ($modulePermDot) {
                        $permissionIds[] = $modulePermDot->id;
                    }
                }
                continue; // Skip other patterns
            }

            // Pattern 6: User approval permissions
            if ($permissionName === 'user-approval') {
                $module = 'user-approval';

                // Initialize module array if not exists
                if (!isset($matrixPermissions[$module])) {
                    $matrixPermissions[$module] = [];
                }

                $matrixPermissions[$module]['view'] = true;
                continue; // Skip other patterns
            }

            // Pattern 7: Storage permissions
            if ($permissionName === 'storage.local') {
                $module = 'storage';

                // Initialize module array if not exists
                if (!isset($matrixPermissions[$module])) {
                    $matrixPermissions[$module] = [];
                }

                $matrixPermissions[$module]['local'] = true;
                continue; // Skip other patterns
            }

            // Pattern 8: Login/Logout permissions
            if (in_array($permissionName, ['login', 'logout'])) {
                $module = 'auth';

                // Initialize module array if not exists
                if (!isset($matrixPermissions[$module])) {
                    $matrixPermissions[$module] = [];
                }

                $matrixPermissions[$module][$permissionName] = true;
                continue; // Skip other patterns
            }
        }

        return $matrixPermissions;
    }

    /**
     * Convert matrix permissions to permission IDs (Accurate style)
     *
     * @param array $matrixPermissions
     * @return array
     */
    private function convertMatrixPermissionsToIds(array $matrixPermissions): array
    {
        $permissionIds = [];

        foreach ($matrixPermissions as $module => $actions) {
            // Skip if no actions are selected for this module
            if (!is_array($actions)) continue;

            foreach ($actions as $action => $value) {
                // Only process checked permissions (value = true or 1)
                if ($value == '1' || $value === true) {

                    // If the matrix action is 'access' or 'main', prefer a single module-level permission
                    // (dash form like 'master-karyawan' or dot form like 'master.karyawan')
                    if ($action === 'access' || $action === 'main') {
                        $modulePerm = Permission::where('name', $module)->first();
                        if ($modulePerm) {
                            $permissionIds[] = $modulePerm->id;
                            continue; // done with this action
                        }

                        $dotModule = str_replace('-', '.', $module);
                        $modulePermDot = Permission::where('name', $dotModule)->first();
                        if ($modulePermDot) {
                            $permissionIds[] = $modulePermDot->id;
                            continue; // done with this action
                        }
                    }
                    // Map matrix actions to database permission actions
                    $actionMap = [
                        'view' => ['index', 'show', 'view'],
                        'create' => ['create', 'store'],
                        'update' => ['edit', 'update'], // edit comes first for database lookup
                        'delete' => ['destroy', 'delete'],
                        'print' => ['print'],
                        'export' => ['export'],
                        'import' => ['import'],
                        'approve' => ['approve'],
                        'access' => ['access'],
                        'main' => ['main'] // Add main action mapping
                    ];

                    $possibleActions = isset($actionMap[$action]) ? $actionMap[$action] : [$action];

                    // Try multiple naming patterns to find the permission
                    $found = false;

                    // Special handling for different module patterns
                    if (strpos($module, 'master-') === 0) {
                        // Convert master-karyawan to master.karyawan format
                        $moduleParts = explode('-', $module);
                        if (count($moduleParts) >= 2) {
                            $baseModule = $moduleParts[0]; // master
                            $subModule = $moduleParts[1]; // karyawan

                            // Try master.submodule.action pattern - PRIORITY for master modules
                            foreach ($possibleActions as $dbAction) {
                                $permissionName = $baseModule . '.' . $subModule . '.' . $dbAction;
                                $permission = Permission::where('name', $permissionName)->first();

                                if ($permission) {
                                    $permissionIds[] = $permission->id;
                                    $found = true;
                                    break; // Stop after finding the first matching permission for this action
                                }
                                // Also try dash variant if present (module-action)
                                $permissionDash = Permission::where('name', $baseModule . '-' . $subModule . '-' . $dbAction)->first();
                                if ($permissionDash) {
                                    $permissionIds[] = $permissionDash->id;
                                    $found = true;
                                    break; // Stop after finding the first matching permission
                                }
                            }

                            // Special handling for pricelist (different pattern)
                            if (!$found && $subModule === 'pricelist' && isset($moduleParts[2])) {
                                $remainingParts = array_slice($moduleParts, 2);
                                $fullSubModule = implode('-', $remainingParts); // sewa-kontainer

                                foreach ($possibleActions as $dbAction) {
                                    $permissionName = $baseModule . '.' . $subModule . '-' . $fullSubModule . '.' . $dbAction;
                                    $permission = Permission::where('name', $permissionName)->first();

                                    if ($permission) {
                                        $permissionIds[] = $permission->id;
                                        $found = true;
                                        break;
                                    }
                                }
                            }

                            // If not found, try master.submodule pattern for simple permissions
                            if (!$found && in_array($action, ['view', 'access'])) {
                                $permissionName = $baseModule . '.' . $subModule;
                                $permission = Permission::where('name', $permissionName)->first();

                                if ($permission) {
                                    $permissionIds[] = $permission->id;
                                    $found = true;
                                    continue;
                                }
                            }
                            // If we collected any master module permissions, continue to next action
                            // but avoid skipping module-level inclusion below (do not use continue here).
                            // (no-op)
                        }
                    }

                    // Special handling for admin modules
                    if ($module === 'admin') {
                        foreach ($possibleActions as $dbAction) {
                            if ($dbAction === 'debug') {
                                $permissionName = 'admin.debug.perms';
                            } elseif ($dbAction === 'features') {
                                $permissionName = 'admin.features';
                            } else {
                                continue;
                            }

                            $permission = Permission::where('name', $permissionName)->first();
                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                                break;
                            }
                        }
                    }

                    // Special handling for user-approval
                    if ($module === 'user-approval') {
                        if ($action === 'view') {
                            $permission = Permission::where('name', 'user-approval')->first();
                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                            }
                        }
                    }

                    // Special handling for pranota-supir and pembayaran-pranota-supir
                    if (in_array($module, ['pranota-supir', 'pembayaran-pranota-supir'])) {
                        foreach ($possibleActions as $dbAction) {
                            // Only process if the action from form matches the current dbAction
                            if ($action === $dbAction) {
                                // Try dash notation first (pranota-supir-view)
                                $permissionName1 = $module . '-' . $dbAction;
                                $permission1 = Permission::where('name', $permissionName1)->first();

                                if ($permission1) {
                                    $permissionIds[] = $permission1->id;
                                    $found = true;
                                    break;
                                }

                                // Try dot notation (pranota-supir.view)
                                $permissionName2 = $module . '.' . $dbAction;
                                $permission2 = Permission::where('name', $permissionName2)->first();

                                if ($permission2) {
                                    $permissionIds[] = $permission2->id;
                                    $found = true;
                                    break;
                                }
                            }
                        }
                    }

                    // Special handling for pembayaran-pranota-tagihan-kontainer
                    if ($module === 'pembayaran-pranota-tagihan-kontainer') {
                        foreach ($possibleActions as $dbAction) {
                            $permissionName = 'pembayaran-pranota-tagihan-kontainer.' . $dbAction;
                            $permission = Permission::where('name', $permissionName)->first();

                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                                // NOTE: Removed automatic addition of 'store' permission when 'create' is found
                                // to prevent unwanted duplication
                            }
                        }
                    }

                    // Special handling for perbaikan-kontainer module
                    if ($module === 'perbaikan-kontainer') {
                        // For perbaikan-kontainer, map matrix actions directly to permission names
                        $directActionMap = [
                            'view' => 'view',
                            'create' => 'create',
                            'update' => 'update',
                            'delete' => 'delete'
                        ];

                        if (isset($directActionMap[$action])) {
                            $permissionName = 'perbaikan-kontainer.' . $directActionMap[$action];
                            $permission = Permission::where('name', $permissionName)->first();

                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                            }
                        }
                    }

                    // Special handling for permohonan module
                    if ($module === 'permohonan') {
                        foreach ($possibleActions as $dbAction) {
                            // Prefer dash notation first (permohonan-create)
                            $permissionName2 = $module . '-' . $dbAction;
                            $permission2 = Permission::where('name', $permissionName2)->first();

                            if ($permission2) {
                                $permissionIds[] = $permission2->id;
                                $found = true;
                                break;
                            }

                            // Fallback to dot notation (permohonan.create)
                            $permissionName1 = $module . '.' . $dbAction;
                            $permission1 = Permission::where('name', $permissionName1)->first();

                            if ($permission1) {
                                $permissionIds[] = $permission1->id;
                                $found = true;
                                break;
                            }
                        }

                        // Also add the simple 'permohonan' permission if any action is checked
                        if (!$found) {
                            $simplePermission = Permission::where('name', $module)->first();
                            if ($simplePermission) {
                                $permissionIds[] = $simplePermission->id;
                                $found = true;
                            }
                        }
                    }

                    // Special handling for profile module
                    if ($module === 'profile') {
                        foreach ($possibleActions as $dbAction) {
                            if ($dbAction === 'view') {
                                $permission = Permission::where('name', 'profile.show')->first();
                            } elseif ($dbAction === 'update') {
                                $permission = Permission::where('name', 'profile.edit')->first();
                                if (!$permission) {
                                    $permission = Permission::where('name', 'profile.update.account')->first();
                                }
                            } elseif ($dbAction === 'delete') {
                                $permission = Permission::where('name', 'profile.destroy')->first();
                            } else {
                                continue;
                            }

                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                                break;
                            }
                        }
                    }

                    // Special handling for supir module
                    if ($module === 'supir') {
                        foreach ($possibleActions as $dbAction) {
                            if ($dbAction === 'dashboard') {
                                $permission = Permission::where('name', 'supir.dashboard')->first();
                            } elseif ($dbAction === 'checkpoint') {
                                $permission = Permission::where('name', 'supir.checkpoint.create')->first();
                                if ($permission) {
                                    $permissionIds[] = $permission->id;
                                }
                                $permission = Permission::where('name', 'supir.checkpoint.store')->first();
                            } else {
                                continue;
                            }

                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                                break;
                            }
                        }
                    }

                    // Special handling for approval module
                    if ($module === 'approval') {
                        foreach ($possibleActions as $dbAction) {
                            if ($dbAction === 'dashboard') {
                                $permission = Permission::where('name', 'approval.dashboard')->first();
                            } elseif ($dbAction === 'mass_process') {
                                $permission = Permission::where('name', 'approval.mass_process')->first();
                            } elseif ($dbAction === 'create') {
                                $permission = Permission::where('name', 'approval.create')->first();
                                if (!$permission) {
                                    $permission = Permission::where('name', 'approval.store')->first();
                                }
                            } elseif ($dbAction === 'riwayat') {
                                $permission = Permission::where('name', 'approval.riwayat')->first();
                            } else {
                                continue;
                            }

                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                                break;
                            }
                        }
                    }

                    // Special handling for storage module
                    if ($module === 'storage') {
                        if ($action === 'local') {
                            $permission = Permission::where('name', 'storage.local')->first();
                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                            }
                        }
                    }

                    // Special handling for auth module
                    if ($module === 'auth') {
                        if ($action === 'login') {
                            $permission = Permission::where('name', 'login')->first();
                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                            }
                        }
                        if ($action === 'logout') {
                            $permission = Permission::where('name', 'logout')->first();
                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                            }
                        }
                    }

                    // Special handling for tagihan-kontainer module
                    if ($module === 'tagihan-kontainer') {
                        foreach ($possibleActions as $dbAction) {
                            // Skip approve action since it's not implemented yet
                            if ($dbAction === 'approve') {
                                continue;
                            }

                            // Try dash notation first (tagihan-kontainer-view)
                            $permissionName1 = $module . '-' . $dbAction;
                            $permission1 = Permission::where('name', $permissionName1)->first();

                            if ($permission1) {
                                $permissionIds[] = $permission1->id;
                                $found = true;
                                break;
                            }

                            // Fallback to dot notation (tagihan-kontainer.view)
                            $permissionName2 = $module . '.' . $dbAction;
                            $permission2 = Permission::where('name', $permissionName2)->first();

                            if ($permission2) {
                                $permissionIds[] = $permission2->id;
                                $found = true;
                                break;
                            }
                        }
                    }

                    // Special handling for tagihan-kontainer-sewa module
                    if ($module === 'tagihan-kontainer-sewa') {
                        foreach ($possibleActions as $dbAction) {
                            // Handle group actions
                            if ($dbAction === 'group_show') {
                                $permission = Permission::where('name', 'tagihan-kontainer-sewa.group.show')->first();
                            } elseif ($dbAction === 'group_adjust_price') {
                                $permission = Permission::where('name', 'tagihan-kontainer-sewa.group.adjust_price')->first();
                            } else {
                                $permission = Permission::where('name', 'tagihan-kontainer-sewa.' . $dbAction)->first();
                            }

                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                                // Don't break here, continue to find all matching permissions
                            }
                        }
                    }

                    // If not master module or not found above, try other patterns
                    if (!$found) {

                        // Pattern 1: module-action (dash notation) - try this first as many permissions use dash
                        foreach ($possibleActions as $dbAction) {
                            $permissionName2 = $module . '-' . $dbAction;
                            $permission2 = Permission::where('name', $permissionName2)->first();

                            if ($permission2) {
                                $permissionIds[] = $permission2->id;
                                $found = true;
                                break;
                            }
                        }

                        // Pattern 2: module.action (dot notation)
                        if (!$found) {
                            foreach ($possibleActions as $dbAction) {
                                $permissionName1 = $module . '.' . $dbAction;
                                $permission1 = Permission::where('name', $permissionName1)->first();

                                if ($permission1) {
                                    $permissionIds[] = $permission1->id;
                                    $found = true;
                                    break;
                                }
                            }
                        }

                        // Pattern 3: action-module (alternative format)
                        if (!$found) {
                            foreach ($possibleActions as $dbAction) {
                                $permissionName3 = $dbAction . '-' . $module;
                                $permission3 = Permission::where('name', $permissionName3)->first();

                                if ($permission3) {
                                    $permissionIds[] = $permission3->id;
                                    $found = true;
                                    break;
                                }
                            }
                        }

                        // Pattern 4: module only (for simple permissions)
                        if (!$found && ($action === 'view' || $action === 'access')) {
                            $permission4 = Permission::where('name', $module)->first();
                            if ($permission4) {
                                $permissionIds[] = $permission4->id;
                                $found = true;
                            }
                        }

                        // Pattern 5: Try common variations as fallback
                        if (!$found) {
                            // Try common variations with dash-first preference
                            $commonVariations = [
                                $module . '-' . $action,
                                $module . '.' . $action,
                                $action . '-' . $module,
                                $action . '.' . $module,
                                $module,
                                $action
                            ];

                            foreach ($commonVariations as $variation) {
                                $permission = Permission::where('name', $variation)->first();
                                if ($permission) {
                                    $permissionIds[] = $permission->id;
                                    $found = true;
                                    break;
                                }
                            }
                        }

                        // If we found at least one matching permission, also try to collect any other
                        // DB rows that match common naming variants for the same module/action.
                        // NOTE: This section has been simplified to avoid unwanted permission duplication
                        if ($found) {
                            // Only add the exact permission we found, no variants
                            // This prevents the system from adding extra permissions that weren't explicitly checked
                        }
                    }
                }
            }
        }

        // Special handling: Add simple permissions for modules that need them
        if (isset($matrixPermissions['permohonan']) && is_array($matrixPermissions['permohonan'])) {
            // Check if any permohonan action is checked
            $hasPermohonanAction = false;
            foreach ($matrixPermissions['permohonan'] as $action => $value) {
                if ($value == '1' || $value === true) {
                    $hasPermohonanAction = true;
                    break;
                }
            }

            // If any permohonan action is checked, also add the simple 'permohonan' permission
            if ($hasPermohonanAction) {
                $simplePermission = Permission::where('name', 'permohonan')->first();
                if ($simplePermission && !in_array($simplePermission->id, $permissionIds)) {
                    $permissionIds[] = $simplePermission->id;
                }
            }
        }

        return array_unique($permissionIds); // Remove duplicates
    }

    /**
     * Convert simple permission names to permission IDs
     *
     * @param array $simplePermissions
     * @return array
     */
    private function convertSimplePermissionsToIds(array $simplePermissions): array
    {
        $permissionIds = [];

        foreach ($simplePermissions as $permissionName) {
            // Find permission by name
            $permission = Permission::where('name', $permissionName)->first();

            if ($permission) {
                $permissionIds[] = $permission->id;
            }
        }

        return array_unique($permissionIds); // Remove duplicates
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

        // Convert permissions to matrix format for the new system
        $matrixPermissions = $this->convertPermissionsToMatrix($permissions);

        return response()->json([
            'success' => true,
            'permissions' => $matrixPermissions, // Return matrix format
            'simple_permissions' => $permissions, // Keep legacy format for backward compatibility
            'user' => [
                'id' => $user->id,
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
        $users = User::with('permissions')->select('id', 'username')->get();
        $permissions = Permission::select('id', 'name', 'description')->get();

        return view('master-user.bulk-manage', compact('users', 'permissions'));
    }

    /**
     * TEMPORARY PUBLIC METHOD FOR DEBUGGING
     * Convert matrix permission format to permission IDs
     */
    public function testConvertMatrixPermissionsToIds(array $matrixPermissions): array
    {
        return $this->convertMatrixPermissionsToIds($matrixPermissions);
    }
}
