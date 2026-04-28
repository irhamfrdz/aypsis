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
                'name' => 'surat-jalan-tarik-kosong-batam-view',
                'description' => 'Melihat Daftar Surat Jalan Tarik Kosong Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'surat-jalan-tarik-kosong-batam-create',
                'description' => 'Membuat Surat Jalan Tarik Kosong Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'surat-jalan-tarik-kosong-batam-update',
                'description' => 'Mengubah Surat Jalan Tarik Kosong Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'surat-jalan-tarik-kosong-batam-delete',
                'description' => 'Menghapus Surat Jalan Tarik Kosong Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'surat-jalan-tarik-kosong-batam-print',
                'description' => 'Mencetak Surat Jalan Tarik Kosong Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('permissions')->insert($permissions);

        // Assign to Admin role (id: 1)
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
            'surat-jalan-tarik-kosong-batam-view',
            'surat-jalan-tarik-kosong-batam-create',
            'surat-jalan-tarik-kosong-batam-update',
            'surat-jalan-tarik-kosong-batam-delete',
            'surat-jalan-tarik-kosong-batam-print',
        ];

        DB::table('permissions')->whereIn('name', $permissionNames)->delete();
    }
};
