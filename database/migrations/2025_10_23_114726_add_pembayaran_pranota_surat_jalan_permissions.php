<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create pembayaran pranota surat jalan permissions
        $permissions = [
            'pembayaran-pranota-surat-jalan-view',
            'pembayaran-pranota-surat-jalan-create',
            'pembayaran-pranota-surat-jalan-edit',
            'pembayaran-pranota-surat-jalan-delete',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Get admin role ID and permission IDs to assign permissions
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if ($adminRole) {
            foreach ($permissions as $permission) {
                $permissionRecord = DB::table('permissions')->where('name', $permission)->first();
                if ($permissionRecord) {
                    DB::table('role_permissions')->insertOrIgnore([
                        'permission_id' => $permissionRecord->id,
                        'role_id' => $adminRole->id,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'pembayaran-pranota-surat-jalan-view',
            'pembayaran-pranota-surat-jalan-create',
            'pembayaran-pranota-surat-jalan-edit',
            'pembayaran-pranota-surat-jalan-delete',
        ];

        // Remove role permissions first
        DB::table('role_permissions')
          ->whereIn('permission_id', function($query) use ($permissions) {
              $query->select('id')
                    ->from('permissions')
                    ->whereIn('name', $permissions);
          })
          ->delete();

        // Remove permissions
        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
