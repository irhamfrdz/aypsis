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
            [
                'name' => 'master-vendor-asuransi-view',
                'description' => 'Melihat data Master Vendor Asuransi'
            ],
            [
                'name' => 'master-vendor-asuransi-create',
                'description' => 'Menambah data Master Vendor Asuransi'
            ],
            [
                'name' => 'master-vendor-asuransi-update',
                'description' => 'Mengubah data Master Vendor Asuransi'
            ],
            [
                'name' => 'master-vendor-asuransi-delete',
                'description' => 'Menghapus data Master Vendor Asuransi'
            ],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                [
                    'description' => $permission['description'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // Give permission to admin (user_id = 1)
            $permissionId = DB::table('permissions')->where('name', $permission['name'])->value('id');
            if ($permissionId) {
                DB::table('user_permissions')->updateOrInsert(
                    ['user_id' => 1, 'permission_id' => $permissionId],
                    []
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionNames = [
            'master-vendor-asuransi-view',
            'master-vendor-asuransi-create',
            'master-vendor-asuransi-update',
            'master-vendor-asuransi-delete',
        ];

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissionNames)
            ->pluck('id');

        if ($permissionIds->isNotEmpty()) {
            DB::table('user_permissions')
                ->whereIn('permission_id', $permissionIds)
                ->delete();

            DB::table('permissions')
                ->whereIn('id', $permissionIds)
                ->delete();
        }
    }
};
