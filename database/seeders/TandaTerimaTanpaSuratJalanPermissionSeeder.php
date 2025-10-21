<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class TandaTerimaTanpaSuratJalanPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for Tanda Terima Tanpa Surat Jalan
        $permissions = [
            'tanda-terima-tanpa-surat-jalan-view',
            'tanda-terima-tanpa-surat-jalan-create',
            'tanda-terima-tanpa-surat-jalan-update',
            'tanda-terima-tanpa-surat-jalan-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $this->command->info("Permission '{$permission}' created or already exists.");
        }

        // Assign all permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->syncPermissions(array_merge($adminRole->permissions->pluck('name')->toArray(), $permissions));
            $this->command->info("All Tanda Terima Tanpa Surat Jalan permissions assigned to admin role.");
        } else {
            $this->command->warn("Admin role not found. Please assign permissions manually.");
        }

        $this->command->info("Tanda Terima Tanpa Surat Jalan permissions seeded successfully!");
    }
}
