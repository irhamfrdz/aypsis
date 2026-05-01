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
                'name' => 'tanda-terima-surat-jalan-tarik-kosong-batam-view',
                'description' => 'Melihat Daftar Tanda Terima SJ Tarik Kosong Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'tanda-terima-surat-jalan-tarik-kosong-batam-create',
                'description' => 'Membuat Tanda Terima SJ Tarik Kosong Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'tanda-terima-surat-jalan-tarik-kosong-batam-update',
                'description' => 'Mengubah Tanda Terima SJ Tarik Kosong Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'tanda-terima-surat-jalan-tarik-kosong-batam-delete',
                'description' => 'Menghapus Tanda Terima SJ Tarik Kosong Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'tanda-terima-surat-jalan-tarik-kosong-batam-print',
                'description' => 'Mencetak Tanda Terima SJ Tarik Kosong Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('permissions')->insert($permissions);

        // Assign to Admin role
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if ($adminRole) {
            $permissionIds = DB::table('permissions')
                ->whereIn('name', array_column($permissions, 'name'))
                ->pluck('id');
            
            $rolePermissions = [];
            foreach ($permissionIds as $pId) {
                $rolePermissions[] = [
                    'permission_id' => $pId,
                    'role_id' => $adminRole->id,
                ];
            }
            DB::table('permission_role')->insert($rolePermissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionNames = [
            'tanda-terima-surat-jalan-tarik-kosong-batam-view',
            'tanda-terima-surat-jalan-tarik-kosong-batam-create',
            'tanda-terima-surat-jalan-tarik-kosong-batam-update',
            'tanda-terima-surat-jalan-tarik-kosong-batam-delete',
            'tanda-terima-surat-jalan-tarik-kosong-batam-print',
        ];

        DB::table('permissions')->whereIn('name', $permissionNames)->delete();
    }
};
