<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            [
                'name' => 'saldo-utang-supir-view',
                'description' => 'Melihat Saldo Utang Supir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'saldo-utang-supir-create',
                'description' => 'Menambah Transaksi Saldo Utang Supir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('permissions')->insert($permissions);

        // Assign to Admin role if roles table exists and admin role exists
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if ($adminRole) {
            $permissionIds = DB::table('permissions')
                ->whereIn('name', ['saldo-utang-supir-view', 'saldo-utang-supir-create'])
                ->pluck('id');

            foreach ($permissionIds as $pid) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $adminRole->id,
                    'permission_id' => $pid,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionNames = ['saldo-utang-supir-view', 'saldo-utang-supir-create'];
        
        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissionNames)
            ->pluck('id');

        DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('name', $permissionNames)->delete();
    }
};
