<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UangJalanBatamPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Array permission untuk Uang Jalan Batam
        $permissions = [
            [
                'name' => 'uang-jalan-batam.view',
                'description' => 'Permission to view uang jalan batam data',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'uang-jalan-batam.create',
                'description' => 'Permission to create new uang jalan batam data',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'uang-jalan-batam.edit',
                'description' => 'Permission to edit uang jalan batam data',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'uang-jalan-batam.delete',
                'description' => 'Permission to delete uang jalan batam data',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert permissions ke database
        foreach ($permissions as $permission) {
            // Check if permission already exists
            $existingPermission = DB::table('permissions')
                ->where('name', $permission['name'])
                ->first();

            if (!$existingPermission) {
                DB::table('permissions')->insert($permission);
                echo "Permission '{$permission['name']}' created successfully.\n";
            } else {
                echo "Permission '{$permission['name']}' already exists.\n";
            }
        }

        // Assign permissions to admin user (user_id = 1)
        $adminUserId = 1;
        
        // Get permission IDs
        $permissionIds = DB::table('permissions')
            ->whereIn('name', [
                'uang-jalan-batam.view',
                'uang-jalan-batam.create',
                'uang-jalan-batam.edit',
                'uang-jalan-batam.delete'
            ])
            ->pluck('id');

        foreach ($permissionIds as $permissionId) {
            // Check if user permission already exists
            $existingUserPermission = DB::table('user_permissions')
                ->where('user_id', $adminUserId)
                ->where('permission_id', $permissionId)
                ->first();

            if (!$existingUserPermission) {
                DB::table('user_permissions')->insert([
                    'user_id' => $adminUserId,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $permissionName = DB::table('permissions')
                    ->where('id', $permissionId)
                    ->value('name');
                    
                echo "Permission '{$permissionName}' assigned to admin user.\n";
            }
        }

        echo "\nUang Jalan Batam permissions seeding completed!\n";
    }
}