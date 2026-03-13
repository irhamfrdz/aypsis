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
                'name' => 'master-pricelist-perijinan-view',
                'description' => 'Melihat data Master Pricelist Perijinan'
            ],
            [
                'name' => 'master-pricelist-perijinan-create',
                'description' => 'Menambah data Master Pricelist Perijinan'
            ],
            [
                'name' => 'master-pricelist-perijinan-update',
                'description' => 'Mengubah data Master Pricelist Perijinan'
            ],
            [
                'name' => 'master-pricelist-perijinan-delete',
                'description' => 'Menghapus data Master Pricelist Perijinan'
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
            'master-pricelist-perijinan-view',
            'master-pricelist-perijinan-create',
            'master-pricelist-perijinan-update',
            'master-pricelist-perijinan-delete',
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
