<?php

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            ['name' => 'pembelian-bbm-batam-view', 'description' => 'Melihat list pembelian BBM Batam'],
            ['name' => 'pembelian-bbm-batam-create', 'description' => 'Membuat data pembelian BBM Batam'],
            ['name' => 'pembelian-bbm-batam-edit', 'description' => 'Mengubah data pembelian BBM Batam'],
            ['name' => 'pembelian-bbm-batam-delete', 'description' => 'Menghapus data pembelian BBM Batam'],
        ];

        $permissionIds = [];
        foreach ($permissions as $pData) {
            $permission = Permission::firstOrCreate(
                ['name' => $pData['name']],
                $pData
            );
            $permissionIds[] = $permission->id;
        }

        // Assign to Roles (super-admin, admin, operational)
        $roles = DB::table('roles')->whereIn('name', ['super-admin', 'admin', 'operational'])->pluck('id');
        if ($roles->isNotEmpty()) {
            $rolePermissions = [];
            foreach ($roles as $roleId) {
                foreach ($permissionIds as $pId) {
                    $rolePermissions[] = [
                        'role_id' => $roleId,
                        'permission_id' => $pId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            DB::table('permission_role')->insertOrIgnore($rolePermissions);
        }

        // Assign directly to admin user in user_permissions
        $admin = User::where('username', 'admin')->first();
        if ($admin) {
            $admin->permissions()->syncWithoutDetaching($permissionIds);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $names = [
            'pembelian-bbm-batam-view',
            'pembelian-bbm-batam-create',
            'pembelian-bbm-batam-edit',
            'pembelian-bbm-batam-delete',
        ];

        $permissions = Permission::whereIn('name', $names)->get();

        foreach ($permissions as $permission) {
            DB::table('permission_role')->where('permission_id', $permission->id)->delete();
            DB::table('user_permissions')->where('permission_id', $permission->id)->delete();
            $permission->delete();
        }
    }
};
