<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class MasterPermissionDotSeeder extends Seeder
{
    /**
     * Run the database seeds to migrate permissions to dot notation.
     */
    public function run(): void
    {
        // Migration mappings from old dash notation to new dot notation
        $migrations = [
            // Master permissions
            'master-karyawan' => 'master.karyawan',
            'master-user' => 'master.user',
            'master-kontainer' => 'master.kontainer',
            'master-tujuan' => 'master.tujuan',
            'master-kegiatan' => 'master.kegiatan',
            'master-permission' => 'master.permission',
            'master-mobil' => 'master.mobil',
            'master-divisi' => 'master.divisi',
            'master-pekerjaan' => 'master.pekerjaan',

            // Action permissions - these might need individual handling
            // For now, we'll focus on module-level permissions
        ];

        foreach ($migrations as $oldName => $newName) {
            $oldPermission = Permission::where('name', $oldName)->first();
            $newPermission = Permission::where('name', $newName)->first();

            if ($oldPermission && !$newPermission) {
                // Update the old permission to new name
                $oldPermission->update(['name' => $newName]);
            } elseif ($oldPermission && $newPermission) {
                // Both exist, remove the old one if users are migrated
                // For safety, we'll keep both for now
            }
        }

        // Additional dot notation permissions that should exist
        $dotPermissions = [
            // Auth
            ['name' => 'auth.login', 'description' => 'Auth login permission'],
            ['name' => 'auth.logout', 'description' => 'Auth logout permission'],

            // Profile
            ['name' => 'profile.show', 'description' => 'Show profile'],
            ['name' => 'profile.edit', 'description' => 'Edit profile'],
            ['name' => 'profile.update', 'description' => 'Update profile'],
            ['name' => 'profile.destroy', 'description' => 'Destroy profile'],

            // Admin
            ['name' => 'admin.debug', 'description' => 'Admin debug'],
            ['name' => 'admin.features', 'description' => 'Admin features'],

            // Storage
            ['name' => 'storage.local', 'description' => 'Local storage'],

            // Supir
            ['name' => 'supir.dashboard', 'description' => 'Supir dashboard'],
            ['name' => 'supir.checkpoint', 'description' => 'Supir checkpoint'],

            // Approval
            ['name' => 'approval.dashboard', 'description' => 'Approval dashboard'],
            ['name' => 'approval.mass_process', 'description' => 'Approval mass process'],
            ['name' => 'approval.riwayat', 'description' => 'Approval riwayat'],
        ];

        foreach ($dotPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
        }
    }
}
