<?php

use App\Models\Permission;
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
            [
                'name' => 'pranota-ob-antar-gudang-view',
                'description' => 'Melihat daftar dan detail Pranota OB Antar Gudang',
            ],
            [
                'name' => 'pranota-ob-antar-gudang-delete',
                'description' => 'Menghapus Pranota OB Antar Gudang',
            ],
        ];

        $roles = DB::table('roles')->whereIn('name', ['super-admin', 'admin', 'operational'])->pluck('id');

        foreach ($permissions as $permData) {
            $permission = Permission::firstOrCreate(
                ['name' => $permData['name']],
                $permData
            );

            if ($roles->isNotEmpty() && $permission) {
                $rolePermissionData = [];
                foreach ($roles as $roleId) {
                    $rolePermissionData[] = [
                        'role_id' => $roleId,
                        'permission_id' => $permission->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('permission_role')->insertOrIgnore($rolePermissionData);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permNames = ['pranota-ob-antar-gudang-view', 'pranota-ob-antar-gudang-delete'];

        foreach ($permNames as $name) {
            $permission = Permission::where('name', $name)->first();

            if ($permission) {
                DB::table('permission_role')->where('permission_id', $permission->id)->delete();
                $permission->delete();
            }
        }
    }
};
