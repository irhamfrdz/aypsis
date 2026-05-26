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
        // Create the permission if it doesn't exist
        $permissionData = [
            'name' => 'tagihan-ob-antar-gudang-view',
            'description' => 'Melihat halaman Tagihan OB Antar Gudang',
        ];

        $permission = Permission::firstOrCreate(
            ['name' => $permissionData['name']],
            $permissionData
        );

        // Assign to roles
        $roles = DB::table('roles')->whereIn('name', ['super-admin', 'admin', 'operational'])->pluck('id');

        if ($roles->isNotEmpty() && $permission) {
            $data = [];
            foreach ($roles as $roleId) {
                $data[] = [
                    'role_id' => $roleId,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('permission_role')->insertOrIgnore($data);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = Permission::where('name', 'tagihan-ob-antar-gudang-view')->first();

        if ($permission) {
            DB::table('permission_role')->where('permission_id', $permission->id)->delete();
            $permission->delete();
        }
    }
};
