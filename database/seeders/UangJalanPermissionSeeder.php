<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class UangJalanPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define uang jalan permissions
        $uangJalanPermissions = [
            // Uang Jalan permissions
            [
                'name' => 'uang-jalan-view',
                'description' => 'Melihat data uang jalan'
            ],
            [
                'name' => 'uang-jalan-create',
                'description' => 'Membuat data uang jalan baru'
            ],
            [
                'name' => 'uang-jalan-update',
                'description' => 'Mengubah data uang jalan'
            ],
            [
                'name' => 'uang-jalan-delete',
                'description' => 'Menghapus data uang jalan'
            ],
            [
                'name' => 'uang-jalan-approve',
                'description' => 'Menyetujui data uang jalan'
            ],
            [
                'name' => 'uang-jalan-print',
                'description' => 'Mencetak data uang jalan'
            ],
            [
                'name' => 'uang-jalan-export',
                'description' => 'Mengexport data uang jalan'
            ],

            // Pranota Uang Jalan permissions  
            [
                'name' => 'pranota-uang-jalan-view',
                'description' => 'Melihat data pranota uang jalan'
            ],
            [
                'name' => 'pranota-uang-jalan-create',
                'description' => 'Membuat pranota uang jalan baru'
            ],
            [
                'name' => 'pranota-uang-jalan-update',
                'description' => 'Mengubah data pranota uang jalan'
            ],
            [
                'name' => 'pranota-uang-jalan-delete',
                'description' => 'Menghapus data pranota uang jalan'
            ],
            [
                'name' => 'pranota-uang-jalan-approve',
                'description' => 'Menyetujui pranota uang jalan'
            ],
            [
                'name' => 'pranota-uang-jalan-print',
                'description' => 'Mencetak pranota uang jalan'
            ],
            [
                'name' => 'pranota-uang-jalan-export',
                'description' => 'Mengexport data pranota uang jalan'
            ]
        ];

        // Insert permissions using DB transaction for better performance
        DB::transaction(function () use ($uangJalanPermissions) {
            foreach ($uangJalanPermissions as $permissionData) {
                // Check if permission already exists to avoid duplicates
                $existingPermission = Permission::where('name', $permissionData['name'])->first();
                
                if (!$existingPermission) {
                    Permission::create([
                        'name' => $permissionData['name'],
                        'description' => $permissionData['description'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    $this->command->info("✅ Permission '{$permissionData['name']}' created successfully");
                } else {
                    $this->command->warn("⚠️  Permission '{$permissionData['name']}' already exists, skipping...");
                }
            }
        });

        $this->command->info("🎉 Uang Jalan permission seeder completed!");
        $this->command->line("");
        $this->command->line("📋 Summary of permissions created:");
        $this->command->line("   • Uang Jalan: 7 permissions (view, create, update, delete, approve, print, export)");
        $this->command->line("   • Pranota Uang Jalan: 7 permissions (view, create, update, delete, approve, print, export)");
        $this->command->line("   • Total: 14 permissions");
        $this->command->line("");
        $this->command->line("🔧 Next steps:");
        $this->command->line("   1. Run the seeder: php artisan db:seed --class=UangJalanPermissionSeeder");
        $this->command->line("   2. Assign permissions to users via User Management interface");
        $this->command->line("   3. Test the permissions in Uang Jalan and Pranota Uang Jalan modules");
    }
}