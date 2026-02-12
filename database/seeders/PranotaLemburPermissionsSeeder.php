<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PranotaLemburPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Pranota Lembur Module
            ['name' => 'pranota-lembur', 'description' => 'Akses modul Pranota Lembur'],
            ['name' => 'pranota-lembur-view', 'description' => 'Melihat data Pranota Lembur'],
            ['name' => 'pranota-lembur-create', 'description' => 'Membuat Pranota Lembur'],
            ['name' => 'pranota-lembur-update', 'description' => 'Mengupdate Pranota Lembur'],
            ['name' => 'pranota-lembur-delete', 'description' => 'Menghapus Pranota Lembur'],
            ['name' => 'pranota-lembur-approve', 'description' => 'Approve Pranota Lembur'],
            ['name' => 'pranota-lembur-print', 'description' => 'Mencetak Pranota Lembur'],
            ['name' => 'pranota-lembur-export', 'description' => 'Mengekspor data Pranota Lembur'],
        ];

        foreach ($permissions as $permission) {
            // Check if permission already exists
            $existingPermission = Permission::where('name', $permission['name'])->first();
            
            if (!$existingPermission) {
                Permission::create($permission);
                $this->command->info("Created permission: {$permission['name']}");
            } else {
                $this->command->warn("Permission already exists: {$permission['name']}");
            }
        }

        $this->command->info('Pranota Lembur permissions seeded successfully!');
    }
}
