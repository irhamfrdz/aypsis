<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Array permissions untuk pembayaran uang muka
        $permissions = [
            'pembayaran-uang-muka-view',
            'pembayaran-uang-muka-create',
            'pembayaran-uang-muka-edit',
            'pembayaran-uang-muka-delete',
        ];

        // Insert permissions if they don't exist
        foreach ($permissions as $permissionName) {
            $exists = DB::table('permissions')->where('name', $permissionName)->exists();
            if (!$exists) {
                DB::table('permissions')->insert([
                    'name' => $permissionName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Assign permissions to admin role
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if ($adminRole) {
            $permissionIds = DB::table('permissions')
                              ->whereIn('name', $permissions)
                              ->pluck('id');

            foreach ($permissionIds as $permissionId) {
                $exists = DB::table('role_has_permissions')
                           ->where('role_id', $adminRole->id)
                           ->where('permission_id', $permissionId)
                           ->exists();

                if (!$exists) {
                    DB::table('role_has_permissions')->insert([
                        'role_id' => $adminRole->id,
                        'permission_id' => $permissionId,
                    ]);
                }
            }
        }
    }

    public function down()
    {
        // Remove permissions
        $permissions = [
            'pembayaran-uang-muka-view',
            'pembayaran-uang-muka-create',
            'pembayaran-uang-muka-edit',
            'pembayaran-uang-muka-delete',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
