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
        $permissions = [
            ['name' => 'surat-jalan-batam-approve', 'description' => 'Menyetujui/Menyelesaikan Surat Jalan Batam'],
            ['name' => 'surat-jalan-batam-print', 'description' => 'Mencetak Surat Jalan Batam']
        ];

        // Insert new permissions
        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                [
                    'description' => $permission['description'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        // Assign to Administrator role if it exists
        $adminRole = DB::table('roles')->where('name', 'like', '%administrator%')->orWhere('name', 'like', '%admin%')->first();
        if ($adminRole) {
            $permissionIds = DB::table('permissions')
                ->whereIn('name', array_column($permissions, 'name'))
                ->pluck('id');

            foreach ($permissionIds as $pId) {
                DB::table('permission_role')->updateOrInsert(
                    ['permission_id' => $pId, 'role_id' => $adminRole->id]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionNames = ['surat-jalan-batam-approve', 'surat-jalan-batam-print'];
        DB::table('permissions')->whereIn('name', $permissionNames)->delete();
    }
};
