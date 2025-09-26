<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class EditMasterPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Updating Master Data permissions...');

        // Update Master Tipe Akun permissions
        $tipeAkunPermissions = [
            [
                'name' => 'master-tipe-akun-view',
                'description' => 'Melihat data tipe akun'
            ],
            [
                'name' => 'master-tipe-akun-create',
                'description' => 'Menambah tipe akun baru'
            ],
            [
                'name' => 'master-tipe-akun-update',
                'description' => 'Mengedit tipe akun'
            ],
            [
                'name' => 'master-tipe-akun-delete',
                'description' => 'Menghapus tipe akun'
            ],
        ];

        foreach ($tipeAkunPermissions as $permissionData) {
            Permission::updateOrCreate(
                ['name' => $permissionData['name']],
                ['description' => $permissionData['description']]
            );
        }

        $this->command->info('Master Tipe Akun permissions updated successfully!');

        // Update Master Kode Nomor permissions (moved from separate module to Master Data)
        $kodeNomorPermissions = [
            [
                'name' => 'master-kode-nomor-view',
                'description' => 'Melihat data kode nomor'
            ],
            [
                'name' => 'master-kode-nomor-create',
                'description' => 'Menambah kode nomor baru'
            ],
            [
                'name' => 'master-kode-nomor-update',
                'description' => 'Mengedit kode nomor'
            ],
            [
                'name' => 'master-kode-nomor-delete',
                'description' => 'Menghapus kode nomor'
            ],
        ];

        foreach ($kodeNomorPermissions as $permissionData) {
            Permission::updateOrCreate(
                ['name' => $permissionData['name']],
                ['description' => $permissionData['description']]
            );
        }

        $this->command->info('Master Kode Nomor permissions updated successfully!');

        // Remove old separate Kode Nomor module permissions if they exist
        $oldKodeNomorPermissions = [
            'kode-nomor-view',
            'kode-nomor-create',
            'kode-nomor-update',
            'kode-nomor-delete',
            'kode-nomor-approve',
            'kode-nomor-print',
            'kode-nomor-export'
        ];

        foreach ($oldKodeNomorPermissions as $oldPermission) {
            Permission::where('name', $oldPermission)->delete();
        }

        $this->command->info('Old separate Kode Nomor module permissions removed successfully!');

        $this->command->info('All Master Data permissions updated successfully!');
    }
}
