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
                'name' => 'tanda-terima-surat-jalan-kontainer-sewa-view',
                'description' => 'Melihat tanda terima surat jalan kontainer sewa',
            ],
            [
                'name' => 'tanda-terima-surat-jalan-kontainer-sewa-create',
                'description' => 'Membuat tanda terima surat jalan kontainer sewa',
            ],
            [
                'name' => 'tanda-terima-surat-jalan-kontainer-sewa-update',
                'description' => 'Mengubah tanda terima surat jalan kontainer sewa',
            ],
            [
                'name' => 'tanda-terima-surat-jalan-kontainer-sewa-delete',
                'description' => 'Menghapus tanda terima surat jalan kontainer sewa',
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
            'tanda-terima-surat-jalan-kontainer-sewa-view',
            'tanda-terima-surat-jalan-kontainer-sewa-create',
            'tanda-terima-surat-jalan-kontainer-sewa-update',
            'tanda-terima-surat-jalan-kontainer-sewa-delete',
        ];

        DB::table('permissions')
            ->whereIn('name', $permissionNames)
            ->delete();
    }
};
