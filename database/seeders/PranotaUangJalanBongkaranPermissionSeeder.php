<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PranotaUangJalanBongkaranPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'pranota-uang-jalan-bongkaran-view',
                'description' => 'Melihat data pranota uang jalan bongkaran'
            ],
            [
                'name' => 'pranota-uang-jalan-bongkaran-create',
                'description' => 'Membuat pranota uang jalan bongkaran baru'
            ],
            [
                'name' => 'pranota-uang-jalan-bongkaran-update',
                'description' => 'Mengubah data pranota uang jalan bongkaran'
            ],
            [
                'name' => 'pranota-uang-jalan-bongkaran-delete',
                'description' => 'Menghapus data pranota uang jalan bongkaran'
            ],
            [
                'name' => 'pranota-uang-jalan-bongkaran-approve',
                'description' => 'Menyetujui pranota uang jalan bongkaran'
            ],
            [
                'name' => 'pranota-uang-jalan-bongkaran-print',
                'description' => 'Mencetak pranota uang jalan bongkaran'
            ],
            [
                'name' => 'pranota-uang-jalan-bongkaran-export',
                'description' => 'Mengexport data pranota uang jalan bongkaran'
            ]
        ];

        DB::transaction(function () use ($permissions) {
            foreach ($permissions as $permissionData) {
                $permission = Permission::where('name', $permissionData['name'])->first();

                if ($permission) {
                    $this->command->warn("⚠️ Permission '{$permissionData['name']}' already exists, skipping...");
                } else {
                    Permission::create([
                        'name' => $permissionData['name'],
                        'description' => $permissionData['description'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $this->command->info("✅ Permission '{$permissionData['name']}' created");
                }
            }
        });

        $this->command->info('🎉 Pranota Uang Jalan Bongkaran permissions seeder completed!');
    }
}
