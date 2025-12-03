<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PembayaranAktivitasLainPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'pembayaran-aktivitas-lain-view',
            'pembayaran-aktivitas-lain-create',
            'pembayaran-aktivitas-lain-update',
            'pembayaran-aktivitas-lain-delete',
            'pembayaran-aktivitas-lain-approve',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        $this->command->info('Pembayaran Aktivitas Lain permissions created and assigned to admin!');
    }
}
