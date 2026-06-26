<?php

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
                'name' => 'tagihan-pelindo-view',
                'description' => 'Melihat data Tagihan Pelindo',
            ],
            [
                'name' => 'tagihan-pelindo-create',
                'description' => 'Menambah data Tagihan Pelindo',
            ],
            [
                'name' => 'tagihan-pelindo-edit',
                'description' => 'Mengubah data Tagihan Pelindo',
            ],
            [
                'name' => 'tagihan-pelindo-delete',
                'description' => 'Menghapus data Tagihan Pelindo',
            ],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                [
                    'description' => $permission['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Give permission to admin (user_id = 1)
            $permissionId = DB::table('permissions')->where('name', $permission['name'])->value('id');
            if ($permissionId && DB::table('users')->where('id', 1)->exists()) {
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
            'tagihan-pelindo-view',
            'tagihan-pelindo-create',
            'tagihan-pelindo-edit',
            'tagihan-pelindo-delete',
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
