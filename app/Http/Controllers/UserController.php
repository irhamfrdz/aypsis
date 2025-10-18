<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Permission;
use App\Helpers\PermissionMatrixHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna.
     *
     * @return \Illuminate\View\View
     */

    public function index(Request $request)
    {
        $query = User::with('karyawan');

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('username', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhereHas('karyawan', function ($karyawanQuery) use ($searchTerm) {
                      $karyawanQuery->where('nama_lengkap', 'LIKE', '%' . $searchTerm . '%')
                                   ->orWhere('nik', 'LIKE', '%' . $searchTerm . '%')
                                   ->orWhere('divisi', 'LIKE', '%' . $searchTerm . '%')
                                   ->orWhere('pekerjaan', 'LIKE', '%' . $searchTerm . '%');
                  });
            });
        }

        $users = $query->paginate(15); // 15 users per page for better performance

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
            try {
                // Add debug logging for operational modules
                Log::info('Processing permission matrix for user: ' . $user->username, [
                    'permissions_input' => $request->permissions
                ]);

                $permissionIds = $this->convertMatrixPermissionsToIds($request->permissions);

                Log::info('Converted permission IDs for user: ' . $user->username, [
                    'permission_ids' => $permissionIds,
                    'count' => count($permissionIds)
                ]);

            } catch (\Exception $e) {
                Log::error('Error converting matrix permissions for user: ' . $user->username, [
                    'error' => $e->getMessage(),
                    'input' => $request->permissions
                ]);
                return redirect()->back()->with('error', 'Error processing permissions: ' . $e->getMessage())->withInput();
            }
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

                // Handle 4-part permissions: master.karyawan.import.store
                if (count($parts) == 4 && $parts[0] === 'master') {
                    $module = $parts[0] . '-' . $parts[1]; // master-karyawan
                    $action = $parts[2]; // import
                    $subaction = $parts[3]; // store

                    // Initialize module array if not exists
                    if (!isset($matrixPermissions[$module])) {
                        $matrixPermissions[$module] = [];
                    }

                    // Map combined actions to matrix actions
                    if ($action === 'import' && $subaction === 'store') {
                        $matrixPermissions[$module]['import'] = true;
                    } elseif ($action === 'print' && $subaction === 'single') {
                        $matrixPermissions[$module]['print'] = true;
                    } else {
                        // Generic handling for other 4-part permissions
                        $matrixPermissions[$module][$action] = true;
                    }

                    continue; // Skip other patterns
                }

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
                    // Special handling for master-vendor-bengkel permissions
                    if ($parts[0] === 'master-vendor-bengkel') {
                        $module = 'master-vendor-bengkel';
                        $action = $parts[1]; // view, create, update, delete

                        // Initialize module array if not exists
                        if (!isset($matrixPermissions[$module])) {
                            $matrixPermissions[$module] = [];
                        }

                        // Map database actions to matrix actions
                        $actionMap = [
                            'view' => 'view',
                            'create' => 'create',
                            'update' => 'update',
                            'delete' => 'delete'
                        ];

                        $mappedAction = isset($actionMap[$action]) ? $actionMap[$action] : $action;
                        $matrixPermissions[$module][$mappedAction] = true;
                        continue; // Skip other patterns
                    }

                    // Handle other dot notation patterns
                    $module = $parts[0];
                    $action = $parts[1];

                    // Special handling for specific modules
                    if ($module === 'pembayaran-pranota-tagihan-kontainer') {
                        $module = 'pembayaran-pranota-tagihan-kontainer';
                    } elseif ($module === 'pranota-tagihan-kontainer') {
                        $module = 'pranota-tagihan-kontainer';
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
                    } elseif ($module === 'perbaikan-kontainer') {
                        // Handle perbaikan-kontainer permissions
                        // Map actions directly (view, create, update, delete)
                        // Action remains as is
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

            // Special handling for approval-tugas permissions (dot notation)
            if (strpos($permissionName, 'approval-tugas-') === 0 && strpos($permissionName, '.') !== false) {
                $parts = explode('.', $permissionName);
                if (count($parts) >= 2) {
                    $module = $parts[0]; // approval-tugas-1 or approval-tugas-2
                    $action = $parts[1]; // view or approve

                    // Initialize module array if not exists
                    if (!isset($matrixPermissions[$module])) {
                        $matrixPermissions[$module] = [];
                    }

                    // Map database actions to matrix actions
                    $actionMap = [
                        'view' => 'view',
                        'approve' => 'approve'
                    ];

                    $mappedAction = isset($actionMap[$action]) ? $actionMap[$action] : $action;
                    $matrixPermissions[$module][$mappedAction] = true;
                    continue; // Skip other patterns
                }
            }

            // OPERATIONAL MODULES: Handle operational management permissions (order-management, surat-jalan, etc.)
            $operationalModules = [
                'order-management' => 'order', // Map order-management to order for permission names
                'surat-jalan' => 'surat-jalan',
                'tanda-terima' => 'tanda-terima',
                'gate-in' => 'gate-in',
                'pranota-surat-jalan' => 'pranota-surat-jalan',
                'approval-surat-jalan' => 'approval-surat-jalan'
            ];

            foreach ($operationalModules as $moduleKey => $permissionPrefix) {
                if (strpos($permissionName, $permissionPrefix . '-') === 0) {
                    $module = $moduleKey; // Use module key for matrix (order-management)
                    $action = str_replace($permissionPrefix . '-', '', $permissionName);

                    // Initialize module array if not exists
                    if (!isset($matrixPermissions[$module])) {
                        $matrixPermissions[$module] = [];
                    }

                    // Map database actions to matrix actions
                    $actionMap = [
                        'view' => 'view',
                        'create' => 'create',
                        'update' => 'update',
                        'delete' => 'delete',
                        'print' => 'print',
                        'export' => 'export',
                        'approve' => 'approve',
                        'reject' => 'reject'
                    ];

                    $mappedAction = isset($actionMap[$action]) ? $actionMap[$action] : $action;
                    $matrixPermissions[$module][$mappedAction] = true;
                    continue 2; // Skip to next permissionName
                }
            }

            // Pattern 2: Master permissions (e.g., master-karyawan) - ONLY MODULE-LEVEL, NOT ACTION PERMISSIONS
            if (strpos($permissionName, 'master-') === 0) {
                // Check if this is a module-level permission (not an action permission)
                // Module-level permissions should not have action suffixes like -view, -create, etc.
                $actionSuffixes = ['-view', '-create', '-store', '-show', '-edit', '-update', '-delete', '-destroy', '-print', '-export', '-import', '-approve', '-index'];
                $isActionPermission = false;

                foreach ($actionSuffixes as $suffix) {
                    if (strpos($permissionName, $suffix) !== false) {
                        $isActionPermission = true;
                        break;
                    }
                }

                // Only process if it's NOT an action permission
                if (!$isActionPermission) {
                    $module = $permissionName;

                    // Initialize module array if not exists
                    if (!isset($matrixPermissions[$module])) {
                        $matrixPermissions[$module] = [];
                    }

                    // Set access permission to true for master permissions
                    $matrixPermissions[$module]['access'] = true;
                    continue; // Skip other patterns
                }
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

            // Special handling for tagihan-kontainer-sewa dash notation permissions
            if (strpos($permissionName, 'tagihan-kontainer-sewa-') === 0) {
                $module = 'tagihan-kontainer-sewa';
                $action = str_replace('tagihan-kontainer-sewa-', '', $permissionName);

                // Initialize module array if not exists
                if (!isset($matrixPermissions[$module])) {
                    $matrixPermissions[$module] = [];
                }

                // Map database actions to matrix actions
                $actionMap = [
                    'index' => 'view',
                    'create' => 'create',
                    'edit' => 'update',
                    'update' => 'update',
                    'destroy' => 'delete',
                    'export' => 'export'
                ];

                $mappedAction = isset($actionMap[$action]) ? $actionMap[$action] : $action;
                $matrixPermissions[$module][$mappedAction] = true;
                continue; // Skip other patterns
            }

            // Special handling for pranota-kontainer-sewa dash notation permissions
            if (strpos($permissionName, 'pranota-kontainer-sewa-') === 0) {
                $module = 'pranota-kontainer-sewa';
                $action = str_replace('pranota-kontainer-sewa-', '', $permissionName);

                // Initialize module array if not exists
                if (!isset($matrixPermissions[$module])) {
                    $matrixPermissions[$module] = [];
                }

                // Map database actions to matrix actions
                $actionMap = [
                    'view' => 'view',
                    'create' => 'create',
                    'edit' => 'edit',
                    'update' => 'update',
                    'delete' => 'delete',
                    'print' => 'print',
                    'export' => 'export'
                ];

                $mappedAction = isset($actionMap[$action]) ? $actionMap[$action] : $action;
                $matrixPermissions[$module][$mappedAction] = true;
                continue; // Skip other patterns
            }

            // Special handling for vendor-kontainer-sewa dash notation permissions
            if (strpos($permissionName, 'vendor-kontainer-sewa-') === 0) {
                $module = 'vendor-kontainer-sewa';
                $action = str_replace('vendor-kontainer-sewa-', '', $permissionName);

                // Initialize module array if not exists
                if (!isset($matrixPermissions[$module])) {
                    $matrixPermissions[$module] = [];
                }

                // Map database actions to matrix actions
                $actionMap = [
                    'view' => 'view',
                    'create' => 'create',
                    'edit' => 'update',
                    'update' => 'update',
                    'delete' => 'delete',
                    'export' => 'export',
                    'print' => 'print'
                ];

                $mappedAction = isset($actionMap[$action]) ? $actionMap[$action] : $action;
                $matrixPermissions[$module][$mappedAction] = true;
                continue; // Skip other patterns
            }

            // Special handling for tagihan-perbaikan-kontainer dash notation permissions
            if (strpos($permissionName, 'tagihan-perbaikan-kontainer-') === 0) {
                $module = 'tagihan-perbaikan-kontainer';
                $action = str_replace('tagihan-perbaikan-kontainer-', '', $permissionName);

                // Initialize module array if not exists
                if (!isset($matrixPermissions[$module])) {
                    $matrixPermissions[$module] = [];
                }

                // Map database actions to matrix actions
                $actionMap = [
                    'view' => 'view',
                    'create' => 'create',
                    'update' => 'update',
                    'delete' => 'delete',
                    'approve' => 'approve',
                    'print' => 'print',
                    'export' => 'export'
                ];

                $mappedAction = isset($actionMap[$action]) ? $actionMap[$action] : $action;
                $matrixPermissions[$module][$mappedAction] = true;
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
                        // Special handling for master-kode-nomor permissions
                        if (strpos($action, 'kode-nomor-') === 0) {
                            // For master-kode-nomor-view, extract the action
                            $action = str_replace('kode-nomor-', '', $action);
                            $module = 'master-kode-nomor';
                        }
                        // Special handling for master-pricelist-sewa-kontainer permissions
                        elseif (strpos($action, 'pricelist-sewa-kontainer-') === 0) {
                            // For master-pricelist-sewa-kontainer-view, extract the action
                            $action = str_replace('pricelist-sewa-kontainer-', '', $action);
                            $module = 'master-pricelist-sewa-kontainer';
                        }
                        // Special handling for master-pricelist-cat permissions
                        elseif (strpos($action, 'pricelist-cat-') === 0) {
                            // For master-pricelist-cat-view, extract the action
                            $action = str_replace('pricelist-cat-', '', $action);
                            $module = 'master-pricelist-cat';
                        }
                        // Special handling for master-tipe-akun permissions
                        elseif (strpos($action, 'tipe-akun-') === 0) {
                            // For master-tipe-akun-view, extract the action
                            $action = str_replace('tipe-akun-', '', $action);
                            $module = 'master-tipe-akun';
                        }
                        // Special handling for master-stock-kontainer permissions
                        elseif (strpos($action, 'stock-kontainer-') === 0) {
                            // For master-stock-kontainer-view, extract the action
                            $action = str_replace('stock-kontainer-', '', $action);
                            $module = 'master-stock-kontainer';
                        }
                        // Special handling for master-nomor-terakhir permissions
                        elseif (strpos($action, 'nomor-terakhir-') === 0) {
                            // For master-nomor-terakhir-view, extract the action
                            $action = str_replace('nomor-terakhir-', '', $action);
                            $module = 'master-nomor-terakhir';
                        } else {
                            // For master-karyawan-view, split further
                            $subParts = explode('-', $action, 2);
                            if (count($subParts) == 2) {
                                $module = $module . '-' . $subParts[0]; // master-karyawan
                                $action = $subParts[1]; // view
                            }
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

                    // Special handling for pembayaran-pranota-kontainer-* permissions
                    if ($module === 'pembayaran' && strpos($action, 'pranota-kontainer-') === 0) {
                        $action = str_replace('pranota-kontainer-', '', $action);
                        $module = 'pembayaran-pranota-kontainer';
                    }

                    // Special handling for pembayaran-pranota-cat-* permissions
                    if ($module === 'pembayaran' && strpos($action, 'pranota-cat-') === 0) {
                        $action = str_replace('pranota-cat-', '', $action);
                        $module = 'pembayaran-pranota-cat';
                    }

                    // Special handling for aktivitas-lainnya-* permissions
                    if (strpos($permissionName, 'aktivitas-lainnya-') === 0) {
                        $module = 'aktivitas-lainnya';
                        $action = str_replace('aktivitas-lainnya-', '', $permissionName);
                    }

                    // Special handling for pembayaran-aktivitas-lainnya-* permissions
                    if (strpos($permissionName, 'pembayaran-aktivitas-lainnya-') === 0) {
                        $module = 'pembayaran-aktivitas-lainnya';
                        $action = str_replace('pembayaran-aktivitas-lainnya-', '', $permissionName);
                    }

                    // Special handling for pranota-perbaikan-kontainer-* permissions
                    if ($module === 'pranota' && strpos($action, 'perbaikan-kontainer-') === 0) {
                        $action = str_replace('perbaikan-kontainer-', '', $action);
                        $module = 'pranota-perbaikan-kontainer';
                    }

                    // Special handling for user-approval-* permissions
                    if ($module === 'user' && strpos($action, 'approval-') === 0) {
                        $action = str_replace('approval-', '', $action);
                        $module = 'user-approval';
                    }

                    // Special handling for pembayaran-pranota-perbaikan-kontainer-* permissions
                    if ($module === 'pembayaran' && strpos($action, 'pranota-perbaikan-kontainer-') === 0) {
                        $action = str_replace('pranota-perbaikan-kontainer-', '', $action);
                        $module = 'pembayaran-pranota-perbaikan-kontainer';
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

                    // Special handling for permohonan-memo-* permissions
                    if ($module === 'permohonan' && strpos($action, 'memo-') === 0) {
                        $action = str_replace('memo-', '', $action);
                        $module = 'permohonan-memo';
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
                    if ($module === 'approval' && in_array($action, ['dashboard', 'mass_process', 'create', 'riwayat', 'view', 'approve', 'print'])) {
                        $module = 'approval';
                        // Map dashboard to view for consistency
                        if ($action === 'dashboard') {
                            $action = 'view';
                        }
                        // Action remains the same for others
                    }

                    // Skip approve action for tagihan-kontainer since it's not implemented yet
                    if ($module === 'tagihan-kontainer' && $action === 'approve') {
                        continue; // Skip processing this permission
                    }

                    // Special handling for tagihan-cat permissions
                    if ($module === 'tagihan' && strpos($action, 'cat-') === 0) {
                        // For tagihan-cat-view, split further
                        $action = str_replace('cat-', '', $action); // Remove 'cat-' prefix
                        $module = 'tagihan-cat'; // Set module to tagihan-cat
                    }

                    // Special handling for pranota-cat permissions
                    if ($module === 'pranota' && strpos($action, 'cat-') === 0) {
                        // For pranota-cat-view, split further
                        $action = str_replace('cat-', '', $action); // Remove 'cat-' prefix
                        $module = 'pranota-cat'; // Set module to pranota-cat
                    }

                    // Initialize module array if not exists
                    if (!isset($matrixPermissions[$module])) {
                        $matrixPermissions[$module] = [];
                    }

                    // Map database actions to matrix actions
                    $actionMap = [
                        'index' => 'view',
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

            // Pattern 5: Admin permissions (e.g., admin-debug-perms, admin-features)
            if (strpos($permissionName, 'admin-') === 0) {
                $parts = explode('-', $permissionName);
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
            if ($permissionName === 'storage-local') {
                $module = 'storage';

                // Initialize module array if not exists
                if (!isset($matrixPermissions[$module])) {
                    $matrixPermissions[$module] = [];
                }

                $matrixPermissions[$module]['local'] = true;
                continue; // Skip other patterns
            }

            // Pattern 8: Standalone dashboard permission
            if ($permissionName === 'dashboard') {
                $module = 'system';

                // Initialize module array if not exists
                if (!isset($matrixPermissions[$module])) {
                    $matrixPermissions[$module] = [];
                }

                $matrixPermissions[$module]['dashboard'] = true;
                continue; // Skip other patterns
            }

            // Pattern 9: Login/Logout permissions

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

        // Special handling: If user has approval-dashboard, also show approval-tugas permissions in matrix
        if (in_array('approval-dashboard', $permissionNames)) {
            // Initialize approval-tugas-1 if not exists
            if (!isset($matrixPermissions['approval-tugas-1'])) {
                $matrixPermissions['approval-tugas-1'] = [];
            }
            // Set view permission for approval-tugas-1
            $matrixPermissions['approval-tugas-1']['view'] = true;

            // Initialize approval-tugas-2 if not exists
            if (!isset($matrixPermissions['approval-tugas-2'])) {
                $matrixPermissions['approval-tugas-2'] = [];
            }
            // Set view permission for approval-tugas-2
            $matrixPermissions['approval-tugas-2']['view'] = true;
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

                // Handle system module permissions
                if ($module === 'system') {
                    // Explicitly check for dashboard permission (handle both checked and unchecked states)
                    $dashboardEnabled = isset($actions['dashboard']) && ($actions['dashboard'] == '1' || $actions['dashboard'] === true);

                    if ($dashboardEnabled) {
                        $permission = Permission::where('name', 'dashboard')->first();
                        if ($permission) {
                            $permissionIds[] = $permission->id;
                        }
                    }
                    // Note: if dashboard is not enabled (unchecked), it won't be added to $permissionIds
                    // This ensures sync() will remove it from user permissions
                    continue;
                }


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

                            // DIRECT FIX: Handle master-karyawan permissions explicitly
                            if ($module === 'master-karyawan' && in_array($action, ['view', 'create', 'update', 'delete', 'print', 'export', 'import'])) {
                                // Map action to correct permission name
                                $actionMap = [
                                    'view' => 'master-karyawan-view',
                                    'create' => 'master-karyawan-create',
                                    'update' => 'master-karyawan-update',
                                    'delete' => 'master-karyawan-delete',
                                    'print' => 'master-karyawan-print',
                                    'export' => 'master-karyawan-export',
                                    'import' => 'master-karyawan-import'
                                ];

                                if (isset($actionMap[$action])) {
                                    $permissionName = $actionMap[$action];
                                    $directPermission = Permission::where('name', $permissionName)->first();
                                    if ($directPermission) {
                                        $permissionIds[] = $directPermission->id;
                                        $found = true;
                                        continue; // Skip to next action
                                    }

                                    // Fallback: Try 4-dot format for import and print actions
                                    if ($action === 'import') {
                                        $fourDotPermission = Permission::where('name', 'master.karyawan.import.store')->first();
                                        if ($fourDotPermission) {
                                            $permissionIds[] = $fourDotPermission->id;
                                            $found = true;
                                            continue;
                                        }
                                    } elseif ($action === 'print') {
                                        $fourDotPermission = Permission::where('name', 'master.karyawan.print.single')->first();
                                        if ($fourDotPermission) {
                                            $permissionIds[] = $fourDotPermission->id;
                                            $found = true;
                                            continue;
                                        }
                                    }
                                }
                            }

                            // DIRECT FIX: Handle master-kontainer permissions explicitly
                            if ($module === 'master-kontainer' && in_array($action, ['view', 'create', 'update', 'delete', 'print', 'export'])) {
                                // Map action to correct permission name
                                $actionMap = [
                                    'view' => 'master-kontainer-view',
                                    'create' => 'master-kontainer-create',
                                    'update' => 'master-kontainer-update',
                                    'delete' => 'master-kontainer-delete',
                                    'print' => 'master-kontainer-print',
                                    'export' => 'master-kontainer-export'
                                ];

                                if (isset($actionMap[$action])) {
                                    $permissionName = $actionMap[$action];
                                    $directPermission = Permission::where('name', $permissionName)->first();
                                    if ($directPermission) {
                                        $permissionIds[] = $directPermission->id;
                                        $found = true;
                                        continue; // Skip to next action
                                    }
                                }
                            }

                            // DIRECT FIX: Handle master-tujuan permissions explicitly
                            if ($module === 'master-tujuan' && in_array($action, ['view', 'create', 'update', 'delete', 'print', 'export'])) {
                                // Map action to correct permission name
                                $actionMap = [
                                    'view' => 'master-tujuan-view',
                                    'create' => 'master-tujuan-create',
                                    'update' => 'master-tujuan-update',
                                    'delete' => 'master-tujuan-delete',
                                    'print' => 'master-tujuan-print',
                                    'export' => 'master-tujuan-export'
                                ];

                                if (isset($actionMap[$action])) {
                                    $permissionName = $actionMap[$action];
                                    $directPermission = Permission::where('name', $permissionName)->first();
                                    if ($directPermission) {
                                        $permissionIds[] = $directPermission->id;
                                        $found = true;
                                        continue; // Skip to next action
                                    }
                                }
                            }

                            // DIRECT FIX: Handle master-kegiatan permissions explicitly
                            if ($module === 'master-kegiatan' && in_array($action, ['view', 'create', 'update', 'delete', 'print', 'export'])) {
                                // Map action to correct permission name
                                $actionMap = [
                                    'view' => 'master-kegiatan-view',
                                    'create' => 'master-kegiatan-create',
                                    'update' => 'master-kegiatan-update',
                                    'delete' => 'master-kegiatan-delete',
                                    'print' => 'master-kegiatan-print',
                                    'export' => 'master-kegiatan-export'
                                ];

                                if (isset($actionMap[$action])) {
                                    $permissionName = $actionMap[$action];
                                    $directPermission = Permission::where('name', $permissionName)->first();
                                    if ($directPermission) {
                                        $permissionIds[] = $directPermission->id;
                                        $found = true;
                                        continue; // Skip to next action
                                    }
                                }
                            }

                            // DIRECT FIX: Handle master-permission permissions explicitly
                            if ($module === 'master-permission' && in_array($action, ['view', 'create', 'update', 'delete', 'print', 'export'])) {
                                // Map action to correct permission name
                                $actionMap = [
                                    'view' => 'master-permission-view',
                                    'create' => 'master-permission-create',
                                    'update' => 'master-permission-update',
                                    'delete' => 'master-permission-delete',
                                    'print' => 'master-permission-print',
                                    'export' => 'master-permission-export'
                                ];

                                if (isset($actionMap[$action])) {
                                    $permissionName = $actionMap[$action];
                                    $directPermission = Permission::where('name', $permissionName)->first();
                                    if ($directPermission) {
                                        $permissionIds[] = $directPermission->id;
                                        $found = true;
                                        continue; // Skip to next action
                                    }
                                }
                            }

                            // DIRECT FIX: Handle master-mobil permissions explicitly
                            if ($module === 'master-mobil' && in_array($action, ['view', 'create', 'update', 'delete', 'print', 'export'])) {
                                // Map action to correct permission name
                                $actionMap = [
                                    'view' => 'master-mobil-view',
                                    'create' => 'master-mobil-create',
                                    'update' => 'master-mobil-update',
                                    'delete' => 'master-mobil-delete',
                                    'print' => 'master-mobil-print',
                                    'export' => 'master-mobil-export'
                                ];

                                if (isset($actionMap[$action])) {
                                    $permissionName = $actionMap[$action];
                                    $directPermission = Permission::where('name', $permissionName)->first();
                                    if ($directPermission) {
                                        $permissionIds[] = $directPermission->id;
                                        $found = true;
                                        continue; // Skip to next action
                                    }
                                }
                            }

                            // DIRECT FIX: Handle master-kode-nomor permissions explicitly
                            if ($module === 'master-kode-nomor' && in_array($action, ['view', 'create', 'update', 'delete', 'print', 'export'])) {
                                // Map action to correct permission name
                                $actionMap = [
                                    'view' => 'master-kode-nomor-view',
                                    'create' => 'master-kode-nomor-create',
                                    'update' => 'master-kode-nomor-update',
                                    'delete' => 'master-kode-nomor-delete',
                                    'print' => 'master-kode-nomor-print',
                                    'export' => 'master-kode-nomor-export'
                                ];

                                if (isset($actionMap[$action])) {
                                    $permissionName = $actionMap[$action];
                                    $directPermission = Permission::where('name', $permissionName)->first();
                                    if ($directPermission) {
                                        $permissionIds[] = $directPermission->id;
                                        $found = true;
                                        continue; // Skip to next action
                                    }
                                }
                            }

                            // DIRECT FIX: Handle master-stock-kontainer permissions explicitly
                            if ($module === 'master-stock-kontainer' && in_array($action, ['view', 'create', 'update', 'delete'])) {
                                // Map action to correct permission name
                                $actionMap = [
                                    'view' => 'master-stock-kontainer-view',
                                    'create' => 'master-stock-kontainer-create',
                                    'update' => 'master-stock-kontainer-update',
                                    'delete' => 'master-stock-kontainer-delete'
                                ];

                                if (isset($actionMap[$action])) {
                                    $permissionName = $actionMap[$action];
                                    $directPermission = Permission::where('name', $permissionName)->first();
                                    if ($directPermission) {
                                        $permissionIds[] = $directPermission->id;
                                        $found = true;
                                        continue; // Skip to next action
                                    }
                                }
                            }

                            // DIRECT FIX: Handle master-nomor-terakhir permissions explicitly
                            if ($module === 'master-nomor-terakhir' && in_array($action, ['view', 'create', 'update', 'delete'])) {
                                // Map action to correct permission name
                                $actionMap = [
                                    'view' => 'master-nomor-terakhir-view',
                                    'create' => 'master-nomor-terakhir-create',
                                    'update' => 'master-nomor-terakhir-update',
                                    'delete' => 'master-nomor-terakhir-delete'
                                ];

                                if (isset($actionMap[$action])) {
                                    $permissionName = $actionMap[$action];
                                    $directPermission = Permission::where('name', $permissionName)->first();
                                    if ($directPermission) {
                                        $permissionIds[] = $directPermission->id;
                                        $found = true;
                                        continue; // Skip to next action
                                    }
                                }
                            }

                            // DIRECT FIX: Handle master-divisi permissions explicitly
                            if ($module === 'master-divisi' && in_array($action, ['view', 'create', 'update', 'delete', 'print', 'export'])) {
                                // Map action to correct permission name
                                $actionMap = [
                                    'view' => 'master-divisi-view',
                                    'create' => 'master-divisi-create',
                                    'update' => 'master-divisi-update',
                                    'delete' => 'master-divisi-delete',
                                    'print' => 'master-divisi-print',
                                    'export' => 'master-divisi-export'
                                ];

                                if (isset($actionMap[$action])) {
                                    $permissionName = $actionMap[$action];
                                    $directPermission = Permission::where('name', $permissionName)->first();
                                    if ($directPermission) {
                                        $permissionIds[] = $directPermission->id;
                                        $found = true;
                                        continue; // Skip to next action
                                    }
                                }
                            }

                            // DIRECT FIX: Handle master-user permissions explicitly
                            if ($module === 'master-user' && in_array($action, ['view', 'create', 'update', 'delete', 'print', 'export'])) {
                                // Map action to correct permission name
                                $actionMap = [
                                    'view' => 'master-user-view',
                                    'create' => 'master-user-create',
                                    'update' => 'master-user-update',
                                    'delete' => 'master-user-delete',
                                    'print' => 'master-user-print',
                                    'export' => 'master-user-export'
                                ];

                                if (isset($actionMap[$action])) {
                                    $permissionName = $actionMap[$action];
                                    $directPermission = Permission::where('name', $permissionName)->first();
                                    if ($directPermission) {
                                        $permissionIds[] = $directPermission->id;
                                        $found = true;
                                    }

                                    // Also include legacy dot notation permissions for backward compatibility
                                    $legacyActionMap = [
                                        'view' => 'master.user.index',
                                        'create' => 'master.user.create',
                                        'update' => 'master.user.edit',
                                        'delete' => 'master.user.destroy',
                                        'print' => 'master.user.print',
                                        'export' => 'master.user.export'
                                    ];

                                    if (isset($legacyActionMap[$action])) {
                                        $legacyPermissionName = $legacyActionMap[$action];
                                        $legacyPermission = Permission::where('name', $legacyPermissionName)->first();
                                        if ($legacyPermission) {
                                            $permissionIds[] = $legacyPermission->id;
                                        }
                                    }

                                    if ($found) {
                                        continue; // Skip to next action
                                    }
                                }
                            }
                                foreach ($possibleActions as $dbAction) {
                                    // 1. Cek master-karyawan-view (format yang benar untuk database)
                                    $permissionDash = Permission::where('name', $module . '-' . $dbAction)->first();
                                    if ($permissionDash) {
                                        $permissionIds[] = $permissionDash->id;
                                        $found = true;
                                        error_log("PATCH SUCCESS: Found {$module}-{$dbAction} with ID {$permissionDash->id}");
                                        break;
                                    }
                                    // 2. Fallback ke master-karyawan-view (legacy format)
                                    $permissionDot = Permission::where('name', $baseModule . '-' . $subModule . '-' . $dbAction)->first();
                                    if ($permissionDot) {
                                        $permissionIds[] = $permissionDot->id;
                                        $found = true;
                                        error_log("PATCH FALLBACK: Found {$baseModule}-{$subModule}-{$dbAction} with ID {$permissionDot->id}");
                                        break;
                                    }
                                }

                            // Special handling for pricelist (different pattern)
                            if (!$found && $subModule === 'pricelist' && isset($moduleParts[2])) {
                                $remainingParts = array_slice($moduleParts, 2);
                                $fullSubModule = implode('-', $remainingParts); // sewa-kontainer

                                foreach ($possibleActions as $dbAction) {
                                    $permissionName = $baseModule . '-' . $subModule . '-' . $fullSubModule . '-' . $dbAction;
                                    $permission = Permission::where('name', $permissionName)->first();

                                    if ($permission) {
                                        $permissionIds[] = $permission->id;
                                        $found = true;
                                        break;
                                    }
                                }
                            }

                            // If not found, try master-submodule pattern for simple permissions
                            if (!$found && in_array($action, ['view', 'access'])) {
                                $permissionName = $baseModule . '-' . $subModule;
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

                    // DIRECT FIX: Handle master-pekerjaan permissions explicitly
                    if ($module === 'master-pekerjaan' && in_array($action, ['view', 'create', 'update', 'delete', 'print', 'export'])) {
                        // Map action to correct permission name
                        $actionMap = [
                            'view' => 'master-pekerjaan-view',
                            'create' => 'master-pekerjaan-create',
                            'update' => 'master-pekerjaan-update',
                            'delete' => 'master-pekerjaan-destroy',
                            'print' => 'master-pekerjaan-print',
                            'export' => 'master-pekerjaan-export'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                            }
                        }
                    }

                    // DIRECT FIX: Handle master-pajak permissions explicitly
                    if ($module === 'master-pajak' && in_array($action, ['view', 'create', 'update', 'delete'])) {
                        // Map action to correct permission name
                        $actionMap = [
                            'view' => 'master-pajak-view',
                            'create' => 'master-pajak-create',
                            'update' => 'master-pajak-update',
                            'delete' => 'master-pajak-destroy'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                            }
                        }
                    }

                    // DIRECT FIX: Handle master-bank permissions explicitly
                    if ($module === 'master-bank' && in_array($action, ['view', 'create', 'update', 'delete'])) {
                        // Map action to correct permission name
                        $actionMap = [
                            'view' => 'master-bank-view',
                            'create' => 'master-bank-create',
                            'update' => 'master-bank-update',
                            'delete' => 'master-bank-destroy'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                            }
                        }
                    }

                    // DIRECT FIX: Handle master-kapal permissions explicitly
                    if ($module === 'master-kapal' && in_array($action, ['view', 'create', 'update', 'delete', 'print', 'export'])) {
                        // Map action to correct permission name (using dot notation)
                        $actionMap = [
                            'view' => 'master-kapal.view',
                            'create' => 'master-kapal.create',
                            'update' => 'master-kapal.edit',
                            'delete' => 'master-kapal.delete',
                            'print' => 'master-kapal.print',
                            'export' => 'master-kapal.export'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                            }
                        }
                    }

                    // DIRECT FIX: Handle master-coa permissions explicitly
                    if ($module === 'master-coa' && in_array($action, ['view', 'create', 'update', 'delete'])) {
                        // Map action to correct permission name
                        $actionMap = [
                            'view' => 'master-coa-view',
                            'create' => 'master-coa-create',
                            'update' => 'master-coa-update',
                            'delete' => 'master-coa-delete'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                            }
                        }
                    }

                    // DIRECT FIX: Handle master-tipe-akun permissions explicitly
                    if ($module === 'master-tipe-akun' && in_array($action, ['view', 'create', 'update', 'delete'])) {
                        // Map action to correct permission name
                        $actionMap = [
                            'view' => 'master-tipe-akun-view',
                            'create' => 'master-tipe-akun-create',
                            'update' => 'master-tipe-akun-update',
                            'delete' => 'master-tipe-akun-delete'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                            }
                        }
                    }

                    // DIRECT FIX: Handle master-cabang permissions explicitly
                    if ($module === 'master-cabang' && in_array($action, ['view', 'create', 'update', 'delete'])) {
                        // Map action to correct permission name
                        $actionMap = [
                            'view' => 'master-cabang-view',
                            'create' => 'master-cabang-create',
                            'update' => 'master-cabang-update',
                            'delete' => 'master-cabang-delete'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                            }
                        }
                    }

                    // DIRECT FIX: Handle master-vendor-bengkel permissions explicitly
                    if ($module === 'master-vendor-bengkel' && in_array($action, ['view', 'create', 'update', 'delete'])) {
                        // Map action to correct permission name
                        $actionMap = [
                            'view' => 'master-vendor-bengkel.view',
                            'create' => 'master-vendor-bengkel.create',
                            'update' => 'master-vendor-bengkel.update',
                            'delete' => 'master-vendor-bengkel.delete'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                            }
                        }
                    }

                    // DIRECT FIX: Handle master-pricelist-sewa-kontainer permissions explicitly
                    if ($module === 'master-pricelist-sewa-kontainer' && in_array($action, ['view', 'create', 'update', 'delete'])) {
                        // Map action to correct permission name
                        $actionMap = [
                            'view' => 'master-pricelist-sewa-kontainer-view',
                            'create' => 'master-pricelist-sewa-kontainer-create',
                            'update' => 'master-pricelist-sewa-kontainer-update',
                            'delete' => 'master-pricelist-sewa-kontainer-delete'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                            }
                        }
                    }

                    // DIRECT FIX: Handle master-pricelist-cat permissions explicitly
                    if ($module === 'master-pricelist-cat' && in_array($action, ['view', 'create', 'update', 'delete'])) {
                        // Map action to correct permission name
                        $actionMap = [
                            'view' => 'master-pricelist-cat-view',
                            'create' => 'master-pricelist-cat-create',
                            'update' => 'master-pricelist-cat-update',
                            'delete' => 'master-pricelist-cat-delete'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                            }
                        }
                    }

                    // DIRECT FIX: Handle master-pengirim permissions explicitly
                    if ($module === 'master-pengirim' && in_array($action, ['view', 'create', 'update', 'delete'])) {
                        // Map action to correct permission name
                        $actionMap = [
                            'view' => 'master-pengirim-view',
                            'create' => 'master-pengirim-create',
                            'update' => 'master-pengirim-update',
                            'delete' => 'master-pengirim-delete'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                            }
                        }
                    }

                    // DIRECT FIX: Handle master-jenis-barang permissions explicitly
                    if ($module === 'master-jenis-barang' && in_array($action, ['view', 'create', 'update', 'delete'])) {
                        // Map action to correct permission name
                        $actionMap = [
                            'view' => 'master-jenis-barang-view',
                            'create' => 'master-jenis-barang-create',
                            'update' => 'master-jenis-barang-update',
                            'delete' => 'master-jenis-barang-delete'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                            }
                        }
                    }

                    // DIRECT FIX: Handle master-term permissions explicitly
                    if ($module === 'master-term' && in_array($action, ['view', 'create', 'update', 'delete'])) {
                        // Map action to correct permission name
                        $actionMap = [
                            'view' => 'master-term-view',
                            'create' => 'master-term-create',
                            'update' => 'master-term-update',
                            'delete' => 'master-term-delete'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                            }
                        }
                    }

                    // Special handling for admin modules
                    if ($module === 'admin') {
                        foreach ($possibleActions as $dbAction) {
                            if ($dbAction === 'debug') {
                                $permissionName = 'admin-debug-perms';
                            } elseif ($dbAction === 'features') {
                                $permissionName = 'admin-features';
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
                        // Map matrix actions directly to permission names
                        $actionMap = [
                            'view' => 'user-approval',
                            'create' => 'user-approval-create',
                            'update' => 'user-approval-update',
                            'delete' => 'user-approval-delete',
                            'print' => 'user-approval-print',
                            'export' => 'user-approval-export'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $permission = Permission::where('name', $permissionName)->first();
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
                                // Use dash notation (pranota-supir-view)
                                $permissionName = $module . '-' . $dbAction;
                                $permission = Permission::where('name', $permissionName)->first();

                                if ($permission) {
                                    $permissionIds[] = $permission->id;
                                    $found = true;
                                    break;
                                }
                            }
                        }
                    }

                    // Special handling for pembayaran-pranota-tagihan-kontainer
                    if ($module === 'pembayaran-pranota-tagihan-kontainer') {
                        foreach ($possibleActions as $dbAction) {
                            $permissionName = 'pembayaran-pranota-tagihan-kontainer-' . $dbAction;
                            $permission = Permission::where('name', $permissionName)->first();

                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                                // NOTE: Removed automatic addition of 'store' permission when 'create' is found
                                // to prevent unwanted duplication
                            }
                        }
                    }

                    // Special handling for pranota-tagihan-kontainer (uses dot notation)
                    if ($module === 'pranota-tagihan-kontainer') {
                        foreach ($possibleActions as $dbAction) {
                            $permissionName = 'pranota-tagihan-kontainer.' . $dbAction;
                            $permission = Permission::where('name', $permissionName)->first();

                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                            }
                        }
                    }

                    // Special handling for pembayaran-pranota-kontainer
                    if ($module === 'pembayaran-pranota-kontainer') {
                        foreach ($possibleActions as $dbAction) {
                            $permissionName = 'pembayaran-pranota-kontainer-' . $dbAction;
                            $permission = Permission::where('name', $permissionName)->first();

                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                                // NOTE: Removed automatic addition of 'store' permission when 'create' is found
                                // to prevent unwanted duplication
                            }
                        }
                    }

                    // Special handling for pembayaran-pranota-cat
                    if ($module === 'pembayaran-pranota-cat') {
                        error_log("DEBUG: Processing pembayaran-pranota-cat with action: $action");
                        // Map matrix actions directly to permission names
                        $actionMap = [
                            'view' => 'view',
                            'create' => 'create',
                            'update' => 'update',
                            'delete' => 'delete',
                            'print' => 'print',
                            'export' => 'export'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = 'pembayaran-pranota-cat-' . $actionMap[$action];
                            error_log("DEBUG: Looking for permission: $permissionName");
                            $permission = Permission::where('name', $permissionName)->first();
                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                                error_log("DEBUG: Found permission ID: {$permission->id}");
                            } else {
                                error_log("DEBUG: Permission not found");
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
                            $permissionName = 'perbaikan-kontainer-' . $directActionMap[$action];
                            $permission = Permission::where('name', $permissionName)->first();

                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                            }
                        }

                        // Always assign perbaikan-kontainer-view when any perbaikan-kontainer action is checked
                        $viewPermission = Permission::where('name', 'perbaikan-kontainer-view')->first();
                        if ($viewPermission && !in_array($viewPermission->id, $permissionIds)) {
                            $permissionIds[] = $viewPermission->id;
                        }
                    }

                    // Special handling for pranota-perbaikan-kontainer module
                    if ($module === 'pranota-perbaikan-kontainer') {
                        // For pranota-perbaikan-kontainer, map matrix actions directly to permission names
                        $directActionMap = [
                            'view' => 'pranota-perbaikan-kontainer-view',
                            'create' => 'pranota-perbaikan-kontainer-create',
                            'update' => 'pranota-perbaikan-kontainer-update',
                            'delete' => 'pranota-perbaikan-kontainer-delete',
                            'print' => 'pranota-perbaikan-kontainer-print',
                            'export' => 'pranota-perbaikan-kontainer-export'
                        ];

                        if (isset($directActionMap[$action])) {
                            $permissionName = $directActionMap[$action];
                            $permission = Permission::where('name', $permissionName)->first();

                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                            }
                        }
                    }

                    // Special handling for pembayaran-pranota-perbaikan-kontainer module
                    if ($module === 'pembayaran-pranota-perbaikan-kontainer') {
                        // For pembayaran-pranota-perbaikan-kontainer, map matrix actions directly to permission names
                        $directActionMap = [
                            'view' => 'view',
                            'create' => 'create',
                            'update' => 'update',
                            'delete' => 'delete',
                            'print' => 'print',
                            'export' => 'export'
                        ];

                        if (isset($directActionMap[$action])) {
                            $permissionName = 'pembayaran-pranota-perbaikan-kontainer-' . $directActionMap[$action];
                            $permission = Permission::where('name', $permissionName)->first();

                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                            }
                        }
                    }

                    // Special handling for tagihan-perbaikan-kontainer module
                    if ($module === 'tagihan-perbaikan-kontainer') {
                        // For tagihan-perbaikan-kontainer, map matrix actions directly to permission names
                        $directActionMap = [
                            'view' => 'view',
                            'create' => 'create',
                            'update' => 'update',
                            'delete' => 'delete',
                            'approve' => 'approve',
                            'print' => 'print',
                            'export' => 'export'
                        ];

                        if (isset($directActionMap[$action])) {
                            $permissionName = 'tagihan-perbaikan-kontainer-' . $directActionMap[$action];
                            $permission = Permission::where('name', $permissionName)->first();

                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                            }
                        }
                    }

                    // Special handling for permohonan-memo module
                    if ($module === 'permohonan-memo') {
                        // For permohonan-memo, map matrix actions directly to permission names
                        $directActionMap = [
                            'view' => 'view',
                            'create' => 'create',
                            'update' => 'update',
                            'delete' => 'delete',
                            'print' => 'print',
                            'export' => 'export'
                        ];

                        if (isset($directActionMap[$action])) {
                            $permissionName = 'permohonan-memo-' . $directActionMap[$action];
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
                            // Use dash notation (permohonan-create)
                            $permissionName = $module . '-' . $dbAction;
                            $permission = Permission::where('name', $permissionName)->first();

                            if ($permission) {
                                $permissionIds[] = $permission->id;
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
                                $permission = Permission::where('name', 'profile-show')->first();
                            } elseif ($dbAction === 'update') {
                                $permission = Permission::where('name', 'profile-edit')->first();
                                if (!$permission) {
                                    $permission = Permission::where('name', 'profile-update-account')->first();
                                }
                            } elseif ($dbAction === 'delete') {
                                $permission = Permission::where('name', 'profile-destroy')->first();
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
                                $permission = Permission::where('name', 'supir-dashboard')->first();
                            } elseif ($dbAction === 'checkpoint') {
                                $permission = Permission::where('name', 'supir-checkpoint-create')->first();
                                if ($permission) {
                                    $permissionIds[] = $permission->id;
                                }
                                $permission = Permission::where('name', 'supir-checkpoint-store')->first();
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
                            if ($dbAction === 'view') {
                                // For view action, give both approval-view and approval-dashboard for compatibility
                                $permission = Permission::where('name', 'approval-view')->first();
                                if ($permission) {
                                    $permissionIds[] = $permission->id;
                                }
                                $permission = Permission::where('name', 'approval-dashboard')->first();
                                if ($permission) {
                                    $permissionIds[] = $permission->id;
                                }
                                $found = true;
                                break;
                            } elseif ($dbAction === 'mass_process') {
                                $permission = Permission::where('name', 'approval-mass-process')->first();
                            } elseif ($dbAction === 'create') {
                                $permission = Permission::where('name', 'approval-create')->first();
                                if (!$permission) {
                                    $permission = Permission::where('name', 'approval-store')->first();
                                }
                            } elseif ($dbAction === 'riwayat') {
                                $permission = Permission::where('name', 'approval-riwayat')->first();
                            } elseif ($dbAction === 'approve') {
                                $permission = Permission::where('name', 'approval-approve')->first();
                            } elseif ($dbAction === 'print') {
                                $permission = Permission::where('name', 'approval-print')->first();
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

                    // DIRECT FIX: Handle approval-tugas-1 permissions explicitly
                    if ($module === 'approval-tugas-1' && in_array($action, ['view', 'approve'])) {
                        // Map action to correct permission name
                        $actionMap = [
                            'view' => 'approval-tugas-1.view',
                            'approve' => 'approval-tugas-1.approve'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                                continue; // Skip to next action
                            }
                        }
                    }

                    // DIRECT FIX: Handle approval-tugas-2 permissions explicitly
                    if ($module === 'approval-tugas-2' && in_array($action, ['view', 'approve'])) {
                        // Map action to correct permission name
                        $actionMap = [
                            'view' => 'approval-tugas-2.view',
                            'approve' => 'approval-tugas-2.approve'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                                continue; // Skip to next action
                            }
                        }
                    }

                    // Special handling for storage module
                    if ($module === 'storage') {
                        if ($action === 'local') {
                            $permission = Permission::where('name', 'storage-local')->first();
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

                            // Use dash notation (tagihan-kontainer-view)
                            $permissionName = $module . '-' . $dbAction;
                            $permission = Permission::where('name', $permissionName)->first();

                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                                break;
                            }
                        }
                    }

                    // Special handling for tagihan-cat module
                    if ($module === 'tagihan-cat') {
                        foreach ($possibleActions as $dbAction) {
                            // Use dash notation (tagihan-cat-view)
                            $permissionName = $module . '-' . $dbAction;
                            $permission = Permission::where('name', $permissionName)->first();

                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                                break;
                            }
                        }
                    }

                    // Special handling for pranota-cat module
                    if ($module === 'pranota-cat') {
                        foreach ($possibleActions as $dbAction) {
                            // Use dash notation (pranota-cat-view)
                            $permissionName = $module . '-' . $dbAction;
                            $permission = Permission::where('name', $permissionName)->first();

                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                                break;
                            }
                        }
                    }

                    // Special handling for aktivitas-lainnya module
                    if ($module === 'aktivitas-lainnya') {
                        // Map matrix actions directly to permission names
                        $actionMap = [
                            'view' => 'aktivitas-lainnya-view',
                            'create' => 'aktivitas-lainnya-create',
                            'update' => 'aktivitas-lainnya-update',
                            'delete' => 'aktivitas-lainnya-delete',
                            'approve' => 'aktivitas-lainnya-approve',
                            'print' => 'aktivitas-lainnya-print',
                            'export' => 'aktivitas-lainnya-export'
                        ];

                        if (isset($actionMap[$action])) {
                            $permission = Permission::where('name', $actionMap[$action])->first();
                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                            }
                        }
                    }

                    // Special handling for pembayaran-aktivitas-lainnya module
                    if ($module === 'pembayaran-aktivitas-lainnya') {
                        // Map matrix actions directly to permission names
                        $actionMap = [
                            'view' => 'pembayaran-aktivitas-lainnya-view',
                            'create' => 'pembayaran-aktivitas-lainnya-create',
                            'update' => 'pembayaran-aktivitas-lainnya-update',
                            'delete' => 'pembayaran-aktivitas-lainnya-delete',
                            'approve' => 'pembayaran-aktivitas-lainnya-approve',
                            'print' => 'pembayaran-aktivitas-lainnya-print',
                            'export' => 'pembayaran-aktivitas-lainnya-export'
                        ];

                        if (isset($actionMap[$action])) {
                            $permission = Permission::where('name', $actionMap[$action])->first();
                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                            }
                        }
                    }

                    // Special handling for tagihan-kontainer-sewa module
                    if ($module === 'tagihan-kontainer-sewa') {
                        // Map matrix actions to permission names (using dash notation as they exist in DB)
                        $actionMap = [
                            'view' => 'tagihan-kontainer-sewa-index',
                            'create' => 'tagihan-kontainer-sewa-create',
                            'update' => 'tagihan-kontainer-sewa-update',
                            'delete' => 'tagihan-kontainer-sewa-destroy',
                            'export' => 'tagihan-kontainer-sewa-export'
                            // Note: 'approve' and 'print' permissions don't exist in database for this module
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $permission = Permission::where('name', $permissionName)->first();
                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                            }
                        }
                    }

                    // Special handling for pranota-kontainer-sewa module
                    if ($module === 'pranota-kontainer-sewa') {
                        // Map matrix actions to permission names (using dash notation as they exist in DB)
                        $actionMap = [
                            'view' => 'pranota-kontainer-sewa-view',
                            'create' => 'pranota-kontainer-sewa-create',
                            'edit' => 'pranota-kontainer-sewa-edit',
                            'update' => 'pranota-kontainer-sewa-update',
                            'delete' => 'pranota-kontainer-sewa-delete',
                            'print' => 'pranota-kontainer-sewa-print',
                            'export' => 'pranota-kontainer-sewa-export'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $permission = Permission::where('name', $permissionName)->first();
                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;

                                // Special case: if user gets update permission, also give edit permission
                                // because routes use pranota-kontainer-sewa-edit for both edit and update actions
                                if ($action === 'update') {
                                    $editPermission = Permission::where('name', 'pranota-kontainer-sewa-edit')->first();
                                    if ($editPermission && !in_array($editPermission->id, $permissionIds)) {
                                        $permissionIds[] = $editPermission->id;
                                    }
                                }
                            }
                        }
                    }

                    // Special handling for vendor-kontainer-sewa module
                    if ($module === 'vendor-kontainer-sewa') {
                        // Map matrix actions to permission names (using dash notation as they exist in DB)
                        $actionMap = [
                            'view' => 'vendor-kontainer-sewa-view',
                            'create' => 'vendor-kontainer-sewa-create',
                            'update' => 'vendor-kontainer-sewa-edit',
                            'delete' => 'vendor-kontainer-sewa-delete',
                            'export' => 'vendor-kontainer-sewa-export',
                            'print' => 'vendor-kontainer-sewa-print'
                        ];

                        if (isset($actionMap[$action])) {
                            $permission = Permission::where('name', $actionMap[$action])->first();
                            if ($permission) {
                                $permissionIds[] = $permission->id;
                                $found = true;
                            }
                        }
                    }

                    // OPERATIONAL MODULES: Handle operational management permissions explicitly
                    if ($module === 'order-management' && in_array($action, ['view', 'create', 'update', 'delete', 'print', 'export'])) {
                        Log::info("Processing order-management permission", [
                            'module' => $module,
                            'action' => $action
                        ]);

                        $actionMap = [
                            'view' => 'order-view',
                            'create' => 'order-create',
                            'update' => 'order-update',
                            'delete' => 'order-delete',
                            'print' => 'order-print',
                            'export' => 'order-export'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                                Log::info("Found order permission", [
                                    'permission_name' => $permissionName,
                                    'permission_id' => $directPermission->id
                                ]);
                            } else {
                                Log::warning("Order permission not found in database", [
                                    'permission_name' => $permissionName
                                ]);
                            }
                        }
                    }

                    // Handle surat-jalan permissions explicitly
                    if ($module === 'surat-jalan' && in_array($action, ['view', 'create', 'update', 'delete', 'print', 'export'])) {
                        $actionMap = [
                            'view' => 'surat-jalan-view',
                            'create' => 'surat-jalan-create',
                            'update' => 'surat-jalan-update',
                            'delete' => 'surat-jalan-delete',
                            'print' => 'surat-jalan-print',
                            'export' => 'surat-jalan-export'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                            }
                        }
                    }

                    // Handle tanda-terima permissions explicitly
                    if ($module === 'tanda-terima' && in_array($action, ['view', 'create', 'update', 'delete', 'print', 'export'])) {
                        $actionMap = [
                            'view' => 'tanda-terima-view',
                            'create' => 'tanda-terima-create',
                            'update' => 'tanda-terima-update',
                            'delete' => 'tanda-terima-delete',
                            'print' => 'tanda-terima-print',
                            'export' => 'tanda-terima-export'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                            }
                        }
                    }

                    // Handle gate-in permissions explicitly
                    if ($module === 'gate-in' && in_array($action, ['view', 'create', 'update', 'delete', 'print', 'export'])) {
                        $actionMap = [
                            'view' => 'gate-in-view',
                            'create' => 'gate-in-create',
                            'update' => 'gate-in-update',
                            'delete' => 'gate-in-delete',
                            'print' => 'gate-in-print',
                            'export' => 'gate-in-export'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                            }
                        }
                    }

                    // Handle pranota-surat-jalan permissions explicitly
                    if ($module === 'pranota-surat-jalan' && in_array($action, ['view', 'create', 'update', 'delete', 'print', 'export'])) {
                        $actionMap = [
                            'view' => 'pranota-surat-jalan-view',
                            'create' => 'pranota-surat-jalan-create',
                            'update' => 'pranota-surat-jalan-update',
                            'delete' => 'pranota-surat-jalan-delete',
                            'print' => 'pranota-surat-jalan-print',
                            'export' => 'pranota-surat-jalan-export'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
                            }
                        }
                    }

                    // Handle approval-surat-jalan permissions explicitly
                    if ($module === 'approval-surat-jalan' && in_array($action, ['view', 'approve', 'reject', 'print', 'export'])) {
                        $actionMap = [
                            'view' => 'approval-surat-jalan-view',
                            'approve' => 'approval-surat-jalan-approve',
                            'reject' => 'approval-surat-jalan-reject',
                            'print' => 'approval-surat-jalan-print',
                            'export' => 'approval-surat-jalan-export'
                        ];

                        if (isset($actionMap[$action])) {
                            $permissionName = $actionMap[$action];
                            $directPermission = Permission::where('name', $permissionName)->first();
                            if ($directPermission) {
                                $permissionIds[] = $directPermission->id;
                                $found = true;
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
                                $action . '-' . $module,
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

        // Special handling: Add approval-dashboard permission when user has approval-tugas permissions
        $hasApprovalTugasPermission = false;
        if (isset($matrixPermissions['approval-tugas-1']) && is_array($matrixPermissions['approval-tugas-1'])) {
            foreach ($matrixPermissions['approval-tugas-1'] as $action => $value) {
                if ($value == '1' || $value === true) {
                    $hasApprovalTugasPermission = true;
                    break;
                }
            }
        }
        if (!$hasApprovalTugasPermission && isset($matrixPermissions['approval-tugas-2']) && is_array($matrixPermissions['approval-tugas-2'])) {
            foreach ($matrixPermissions['approval-tugas-2'] as $action => $value) {
                if ($value == '1' || $value === true) {
                    $hasApprovalTugasPermission = true;
                    break;
                }
            }
        }

        // If user has any approval-tugas permission, also give them approval-dashboard
        if ($hasApprovalTugasPermission) {
            $dashboardPermission = Permission::where('name', 'approval-dashboard')->first();
            if ($dashboardPermission && !in_array($dashboardPermission->id, $permissionIds)) {
                $permissionIds[] = $dashboardPermission->id;
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

    /**
     * TEMPORARY PUBLIC METHOD FOR DEBUGGING
     * Convert permission names to matrix format
     */
    public function testConvertPermissionsToMatrix(array $permissionNames): array
    {
        return $this->convertPermissionsToMatrix($permissionNames);
    }
}
