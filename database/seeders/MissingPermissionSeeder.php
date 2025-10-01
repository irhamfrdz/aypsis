<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;

class MissingPermissionSeeder extends Seeder
{
    public function run()
    {
        echo "=== ADDING MISSING PERMISSIONS ===\n";

        $permissions = [
            // Dashboard
            ['name' => 'dashboard', 'description' => 'Akses Dashboard Utama'],

            // Master Tujuan
            ['name' => 'master-tujuan-view', 'description' => 'Melihat Master Tujuan'],
            ['name' => 'master-tujuan-create', 'description' => 'Membuat Master Tujuan'],
            ['name' => 'master-tujuan-update', 'description' => 'Mengubah Master Tujuan'],
            ['name' => 'master-tujuan-delete', 'description' => 'Menghapus Master Tujuan'],
            ['name' => 'master-tujuan-print', 'description' => 'Cetak Master Tujuan'],
            ['name' => 'master-tujuan-export', 'description' => 'Export Master Tujuan'],

            // Master Divisi
            ['name' => 'master-divisi-view', 'description' => 'Melihat Master Divisi'],
            ['name' => 'master-divisi-create', 'description' => 'Membuat Master Divisi'],
            ['name' => 'master-divisi-update', 'description' => 'Mengubah Master Divisi'],
            ['name' => 'master-divisi-delete', 'description' => 'Menghapus Master Divisi'],
            ['name' => 'master-divisi-print', 'description' => 'Cetak Master Divisi'],
            ['name' => 'master-divisi-export', 'description' => 'Export Master Divisi'],

            // Master Pajak
            ['name' => 'master-pajak-view', 'description' => 'Melihat Master Pajak'],
            ['name' => 'master-pajak-create', 'description' => 'Membuat Master Pajak'],
            ['name' => 'master-pajak-update', 'description' => 'Mengubah Master Pajak'],
            ['name' => 'master-pajak-delete', 'description' => 'Menghapus Master Pajak'],
            ['name' => 'master-pajak-print', 'description' => 'Cetak Master Pajak'],
            ['name' => 'master-pajak-export', 'description' => 'Export Master Pajak'],

            // Master Cabang
            ['name' => 'master-cabang-view', 'description' => 'Melihat Master Cabang'],
            ['name' => 'master-cabang-create', 'description' => 'Membuat Master Cabang'],
            ['name' => 'master-cabang-update', 'description' => 'Mengubah Master Cabang'],
            ['name' => 'master-cabang-delete', 'description' => 'Menghapus Master Cabang'],
            ['name' => 'master-cabang-print', 'description' => 'Cetak Master Cabang'],
            ['name' => 'master-cabang-export', 'description' => 'Export Master Cabang'],

            // Master COA
            ['name' => 'master-coa-view', 'description' => 'Melihat Master COA'],
            ['name' => 'master-coa-create', 'description' => 'Membuat Master COA'],
            ['name' => 'master-coa-update', 'description' => 'Mengubah Master COA'],
            ['name' => 'master-coa-delete', 'description' => 'Menghapus Master COA'],
            ['name' => 'master-coa-print', 'description' => 'Cetak Master COA'],
            ['name' => 'master-coa-export', 'description' => 'Export Master COA'],

            // Master Bank
            ['name' => 'master-bank-view', 'description' => 'Melihat Master Bank'],
            ['name' => 'master-bank-create', 'description' => 'Membuat Master Bank'],
            ['name' => 'master-bank-update', 'description' => 'Mengubah Master Bank'],
            ['name' => 'master-bank-delete', 'description' => 'Menghapus Master Bank'],
            ['name' => 'master-bank-print', 'description' => 'Cetak Master Bank'],
            ['name' => 'master-bank-export', 'description' => 'Export Master Bank'],
        ];

        $created = 0;
        $existing = 0;

        foreach ($permissions as $permData) {
            $permission = Permission::firstOrCreate(
                ['name' => $permData['name']],
                ['description' => $permData['description']]
            );

            if ($permission->wasRecentlyCreated) {
                echo "✅ Created: {$permData['name']}\n";
                $created++;
            } else {
                echo "⚪ Exists: {$permData['name']}\n";
                $existing++;
            }
        }

        echo "\n=== SUMMARY ===\n";
        echo "Created: {$created}\n";
        echo "Already existed: {$existing}\n";
        echo "Total: " . count($permissions) . "\n\n";

        // Auto-assign ke admin user
        $admin = User::where('username', 'admin')->first();
        if ($admin) {
            $permissionIds = Permission::whereIn('name', array_column($permissions, 'name'))->pluck('id')->toArray();
            $admin->permissions()->syncWithoutDetaching($permissionIds);
            echo "✅ Auto-assigned permissions to admin user\n";
        }
    }
}
