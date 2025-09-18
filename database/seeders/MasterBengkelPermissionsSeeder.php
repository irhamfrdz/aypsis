<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class MasterBengkelPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cek apakah permission sudah ada untuk menghindari duplikasi
        $existingPermissions = Permission::pluck('name')->toArray();
        $newPermissions = [];

        // Master Bengkel permissions
        $bengkelPermissions = [
            // Main permissions
            ['name' => 'master-bengkel', 'description' => 'Akses Master Bengkel'],
            ['name' => 'master-bengkel.view', 'description' => 'Melihat Master Bengkel'],
            ['name' => 'master-bengkel.create', 'description' => 'Membuat Master Bengkel'],
            ['name' => 'master-bengkel.update', 'description' => 'Mengupdate Master Bengkel'],
            ['name' => 'master-bengkel.delete', 'description' => 'Menghapus Master Bengkel'],

            // Route-based permissions
            ['name' => 'master.bengkel.index', 'description' => 'Index Master Bengkel'],
            ['name' => 'master.bengkel.create', 'description' => 'Create Master Bengkel'],
            ['name' => 'master.bengkel.store', 'description' => 'Store Master Bengkel'],
            ['name' => 'master.bengkel.show', 'description' => 'Show Master Bengkel'],
            ['name' => 'master.bengkel.edit', 'description' => 'Edit Master Bengkel'],
            ['name' => 'master.bengkel.update', 'description' => 'Update Master Bengkel'],
            ['name' => 'master.bengkel.destroy', 'description' => 'Destroy Master Bengkel'],

            // Additional permissions
            ['name' => 'master-bengkel.print', 'description' => 'Print Master Bengkel'],
            ['name' => 'master-bengkel.export', 'description' => 'Export Master Bengkel'],
            ['name' => 'master-bengkel.import', 'description' => 'Import Master Bengkel'],
        ];

        foreach ($bengkelPermissions as $permission) {
            if (!in_array($permission['name'], $existingPermissions)) {
                $newPermissions[] = $permission;
            }
        }

        // Insert permissions jika ada yang baru
        if (!empty($newPermissions)) {
            Permission::insert($newPermissions);
            $this->command->info('✅ Master Bengkel permissions berhasil ditambahkan: ' . count($newPermissions) . ' permission(s)');
        } else {
            $this->command->info('ℹ️ Semua Master Bengkel permissions sudah ada');
        }

        // Tampilkan summary
        $totalPermissions = Permission::where('name', 'like', '%bengkel%')->count();
        $this->command->info('🎉 Total Master Bengkel permissions: ' . $totalPermissions);
    }
}