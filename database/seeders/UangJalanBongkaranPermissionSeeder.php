<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class UangJalanBongkaranPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'uang-jalan-bongkaran-view',
                'description' => 'Melihat data uang jalan bongkaran'
            ],
            [
                'name' => 'uang-jalan-bongkaran-create',
                'description' => 'Membuat data uang jalan bongkaran baru'
            ],
            [
                'name' => 'uang-jalan-bongkaran-update',
                'description' => 'Mengubah data uang jalan bongkaran'
            ],
            [
                'name' => 'uang-jalan-bongkaran-delete',
                'description' => 'Menghapus data uang jalan bongkaran'
            ],
            // Optional: print/export (uncomment if needed)
            // [
            //     'name' => 'uang-jalan-bongkaran-print',
            //     'description' => 'Mencetak data uang jalan bongkaran'
            // ],
            // [
            //     'name' => 'uang-jalan-bongkaran-export',
            //     'description' => 'Mengexport data uang jalan bongkaran'
            // ],
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

        $this->command->info('🎉 Uang Jalan Bongkaran permissions seeder completed!');
    }
}
