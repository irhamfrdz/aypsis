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
                'name' => 'asuransi-tanda-terima-view',
                'description' => 'Melihat data Asuransi Tanda Terima'
            ],
            [
                'name' => 'asuransi-tanda-terima-create',
                'description' => 'Menambah data Asuransi Tanda Terima'
            ],
            [
                'name' => 'asuransi-tanda-terima-update',
                'description' => 'Mengubah data Asuransi Tanda Terima'
            ],
            [
                'name' => 'asuransi-tanda-terima-delete',
                'description' => 'Menghapus data Asuransi Tanda Terima'
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

            // Berikan akses ke admin (user_id = 1)
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
            'asuransi-tanda-terima-view',
            'asuransi-tanda-terima-create',
            'asuransi-tanda-terima-update',
            'asuransi-tanda-terima-delete',
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
