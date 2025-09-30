<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class TagihanPerbaikanKontainerPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions for tagihan-perbaikan-kontainer module
        $permissions = [
            [
                'name' => 'tagihan-perbaikan-kontainer-view',
                'description' => 'View Tagihan Perbaikan Kontainer',
            ],
            [
                'name' => 'tagihan-perbaikan-kontainer-create',
                'description' => 'Create Tagihan Perbaikan Kontainer',
            ],
            [
                'name' => 'tagihan-perbaikan-kontainer-update',
                'description' => 'Update Tagihan Perbaikan Kontainer',
            ],
            [
                'name' => 'tagihan-perbaikan-kontainer-delete',
                'description' => 'Delete Tagihan Perbaikan Kontainer',
            ],
            [
                'name' => 'tagihan-perbaikan-kontainer-approve',
                'description' => 'Approve Tagihan Perbaikan Kontainer',
            ],
            [
                'name' => 'tagihan-perbaikan-kontainer-print',
                'description' => 'Print Tagihan Perbaikan Kontainer',
            ],
            [
                'name' => 'tagihan-perbaikan-kontainer-export',
                'description' => 'Export Tagihan Perbaikan Kontainer',
            ],
        ];

        // Check existing permissions to avoid duplicates
        $existingPermissions = Permission::pluck('name')->toArray();

        $newPermissions = [];
        foreach ($permissions as $permission) {
            if (!in_array($permission['name'], $existingPermissions)) {
                $newPermissions[] = [
                    'name' => $permission['name'],
                    'description' => $permission['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert new permissions
        if (!empty($newPermissions)) {
            Permission::insert($newPermissions);
            echo "Added " . count($newPermissions) . " tagihan-perbaikan-kontainer permissions.\n";
        } else {
            echo "All tagihan-perbaikan-kontainer permissions already exist.\n";
        }
    }
}