<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PERMISSIONS = [
        'asset-view' => 'Melihat Menu Asset',
        'asset-create' => 'Menambah Data Asset',
        'asset-update' => 'Mengubah Data Asset',
        'asset-delete' => 'Menghapus Data Asset',
        'master-asset-view' => 'Melihat Master Asset',
        'master-asset-create' => 'Menambah Master Asset',
        'master-asset-update' => 'Mengubah Master Asset',
        'master-asset-delete' => 'Menghapus Master Asset',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $adminRole = DB::table('roles')->where('name', 'admin')->first();

        foreach (self::PERMISSIONS as $name => $description) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name],
                [
                    'description' => $description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            if ($adminRole && Schema::hasTable('permission_role')) {
                $permissionId = DB::table('permissions')->where('name', $name)->value('id');
                if ($permissionId) {
                    DB::table('permission_role')->insertOrIgnore([
                        'permission_id' => $permissionId,
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
        $permissionNames = array_keys(self::PERMISSIONS);

        if (Schema::hasTable('permission_role')) {
            $permissionIds = DB::table('permissions')
                ->whereIn('name', $permissionNames)
                ->pluck('id');

            DB::table('permission_role')
                ->whereIn('permission_id', $permissionIds)
                ->delete();
        }

        DB::table('permissions')
            ->whereIn('name', $permissionNames)
            ->delete();
    }
};
