<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            if (DB::getDriverName() === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF;');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }
            DB::table('user_permissions')->truncate();
            if (DB::getDriverName() === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
                // Admin user - all permissions
        foreach([
            ['user_id' => 1, 'permission_id' => 1],  // master-karyawan
            ['user_id' => 1, 'permission_id' => 2],  // master-user
            ['user_id' => 1, 'permission_id' => 3],  // master-kontainer
            ['user_id' => 1, 'permission_id' => 4],  // master-permohonan
            ['user_id' => 1, 'permission_id' => 5],  // permohonan-create
            ['user_id' => 1, 'permission_id' => 6],  // permohonan-view
            ['user_id' => 1, 'permission_id' => 7],  // permohonan-edit
            ['user_id' => 1, 'permission_id' => 8],  // permohonan-delete
            ['user_id' => 1, 'permission_id' => 9],  // master-tujuan
            ['user_id' => 1, 'permission_id' => 10], // master-kegiatan
            ['user_id' => 1, 'permission_id' => 11], // master-permission
            ['user_id' => 1, 'permission_id' => 12], // master-mobil
            ['user_id' => 1, 'permission_id' => 13], // master-pricelist-sewa-kontainer
            ['user_id' => 1, 'permission_id' => 14], // master-pranota-supir
        ] as $permission) {
            DB::table('user_permissions')->updateOrInsert($permission);
        }

        // Staff user - permissions for menu items they should see
        foreach([
            ['user_id' => 2, 'permission_id' => 4],  // master-permohonan (needed for Permohonan Memo and Penyelesaian Tugas menu)
            ['user_id' => 2, 'permission_id' => 5],  // permohonan-create
            ['user_id' => 2, 'permission_id' => 6],  // permohonan-view
            ['user_id' => 2, 'permission_id' => 14], // master-pranota-supir (needed for Pranota menu)
        ] as $permission) {
            DB::table('user_permissions')->updateOrInsert($permission);
        }
    }
}
