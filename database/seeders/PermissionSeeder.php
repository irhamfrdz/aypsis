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

        // Define modules and their actions (Complete system permissions)
        $modules = [
            // Dashboard
            'dashboard' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            
            // Master modules
            'master' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master-karyawan' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master-user' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master-kontainer' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master-tujuan' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master-kegiatan' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master-permission' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master-mobil' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master-coa' => ['view', 'create', 'update', 'delete'],
            'master-bank' => ['view', 'create', 'update', 'delete'],
            'master-pajak' => ['view', 'create', 'update', 'delete'],
            'master-pekerjaan' => ['view', 'create', 'update', 'delete', 'print', 'export'],
            'master-cabang' => ['view', 'create', 'update', 'delete'],
            'master-vendor-bengkel' => ['view', 'create', 'update', 'delete'],
            'master-pricelist-sewa-kontainer' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'master-pricelist-cat' => ['view', 'create', 'update', 'delete'],
            'master-tipe-akun' => ['view', 'create', 'update', 'delete'],
            'master-stock-kontainer' => ['view', 'create', 'update', 'delete'],
            'master-nomor-terakhir' => ['view', 'create', 'update', 'delete'],
            'master-kode-nomor' => ['view', 'create', 'update', 'delete'],
            'master-divisi' => ['view', 'create', 'update', 'delete', 'print', 'export'],
            
            // Tagihan modules
            'tagihan-kontainer' => ['view', 'create', 'update', 'delete', 'print', 'export'],
            'tagihan-cat' => ['view', 'create', 'update', 'delete', 'print', 'export'],
            'tagihan-kontainer-sewa' => ['view', 'create', 'update', 'delete', 'export'],
            'tagihan-perbaikan-kontainer' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            
            // Pranota modules
            'pranota-supir' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'pranota-cat' => ['view', 'create', 'update', 'delete', 'print', 'export'],
            'pranota-kontainer-sewa' => ['view', 'create', 'update', 'delete', 'print', 'export'],
            'pranota-perbaikan-kontainer' => ['view', 'create', 'update', 'delete'],
            
            // Pembayaran modules
            'pembayaran-pranota-supir' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'pembayaran-pranota-kontainer' => ['view', 'create', 'update', 'delete'],
            'pembayaran-pranota-cat' => ['view', 'create', 'update', 'delete'],
            'pembayaran-pranota-perbaikan-kontainer' => ['view', 'create', 'update', 'delete'],
            
            // Other modules
            'perbaikan-kontainer' => ['view', 'create', 'update', 'delete'],
            'permohonan' => ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'],
            'permohonan-memo' => ['view', 'create', 'update', 'delete'],
            'profile' => ['show', 'edit', 'update', 'destroy'],
            'supir' => ['dashboard', 'checkpoint'],
            'approval' => ['view', 'dashboard', 'mass_process', 'create', 'riwayat', 'approve', 'print'],
            'admin' => ['debug', 'features'],
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

        // Add special permissions that don't follow standard patterns
        $specialPermissions = [
            // Module-only permissions (no actions)
            'master-karyawan' => 'Master Karyawan',
            'master-kontainer' => 'Master Kontainer', 
            'master-tujuan' => 'Master Tujuan',
            'master-kegiatan' => 'Master Kegiatan',
            'master-permission' => 'Master Permission',
            'master-mobil' => 'Master Mobil',
            'tagihan-kontainer' => 'Tagihan Kontainer',
            'permohonan' => 'Permohonan',
            'user-approval' => 'User Approval',
            
            // Dot notation variants
            'master.karyawan' => 'Master Karyawan (dot notation)',
            'master.karyawan.index' => 'Master Karyawan Index',
            'master.karyawan.create' => 'Master Karyawan Create',
            'master.karyawan.store' => 'Master Karyawan Store',
            'master.karyawan.show' => 'Master Karyawan Show',
            'master.karyawan.edit' => 'Master Karyawan Edit',
            'master.karyawan.update' => 'Master Karyawan Update',
            'master.karyawan.destroy' => 'Master Karyawan Destroy',
            'master-vendor-bengkel.view' => 'Master Vendor Bengkel View',
            'master-vendor-bengkel.create' => 'Master Vendor Bengkel Create', 
            'master-vendor-bengkel.update' => 'Master Vendor Bengkel Update',
            'master-vendor-bengkel.delete' => 'Master Vendor Bengkel Delete',
            'permohonan.edit' => 'Permohonan Edit (dot notation)',
            'permohonan.create' => 'Permohonan Create (dot notation)',
            'tagihan-kontainer.view' => 'Tagihan Kontainer View (dot notation)',
            'tagihan-kontainer-sewa.index' => 'Tagihan Kontainer Sewa Index (dot notation)',
            'tagihan-kontainer-sewa.create' => 'Tagihan Kontainer Sewa Create (dot notation)',
            'tagihan-kontainer-sewa.update' => 'Tagihan Kontainer Sewa Update (dot notation)',
            'tagihan-kontainer-sewa.destroy' => 'Tagihan Kontainer Sewa Destroy (dot notation)',
            'tagihan-kontainer-sewa.export' => 'Tagihan Kontainer Sewa Export (dot notation)',
            
            // Auth permissions
            'login' => 'Login',
            'logout' => 'Logout',
            
            // Storage permissions
            'storage-local' => 'Storage Local',
            
            // Special action permissions
            'pembayaran-pranota-tagihan-kontainer' => 'Pembayaran Pranota Tagihan Kontainer',
            'master-pranota-tagihan-kontainer' => 'Master Pranota Tagihan Kontainer',
            
            // Checkpoint permissions
            'supir-checkpoint-create' => 'Supir Checkpoint Create',
            'supir-checkpoint-store' => 'Supir Checkpoint Store',
            
            // Profile update account
            'profile-update-account' => 'Profile Update Account',
            
            // Approval store
            'approval-store' => 'Approval Store',
            
            // Admin debug permissions
            'admin-debug-perms' => 'Admin Debug Permissions',
        ];

        foreach ($specialPermissions as $name => $description) {
            if (!in_array($name, $existingPermissions)) {
                $permissions[] = [
                    'id' => $nextId,
                    'name' => $name,
                    'description' => $description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $nextId++;
            }
        }

        // Insert new permissions in chunks
        if (!empty($permissions)) {
            foreach (array_chunk($permissions, 50) as $chunk) {
                Permission::insert($chunk);
            }
            echo "Added " . count($permissions) . " new permissions (matrix + special).\n";
        } else {
            echo "All permissions already exist.\n";
        }
    }
}
