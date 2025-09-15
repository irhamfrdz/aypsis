<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Instead of truncating, we'll add new permissions without deleting existing ones
        // This preserves existing user permissions while adding the new matrix-style permissions

        // Define modules and their actions (Accurate-style)
        $modules = [
            'dashboard' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master-karyawan' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master-user' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master-kontainer' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master-tujuan' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master-kegiatan' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master-permission' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master-mobil' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master-pricelist-sewa-kontainer' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'tagihan-kontainer' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'pranota-supir' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'pembayaran-pranota-supir' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'permohonan' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'user-approval' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
        ];

        $permissions = [];
        $existingPermissions = Permission::pluck('name')->toArray();
        $nextId = Permission::max('id') + 1;

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissionName = $module . '-' . $action;

                // Only add if permission doesn't already exist
                if (!in_array($permissionName, $existingPermissions)) {
                    $permissions[] = [
                        'id' => $nextId,
                        'name' => $permissionName,
                        'description' => ucfirst($action) . ' ' . str_replace('-', ' ', $module),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $nextId++;
                }
            }
        }

        // Insert new permissions in chunks
        if (!empty($permissions)) {
            foreach (array_chunk($permissions, 50) as $chunk) {
                Permission::insert($chunk);
            }
            echo "Added " . count($permissions) . " new matrix-style permissions.\n";
        } else {
            echo "All matrix-style permissions already exist.\n";
        }
    }
}
