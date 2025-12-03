<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PembayaranAktivitasLainPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            ['name' => 'pembayaran-aktivitas-lain-view', 'description' => 'Lihat Pembayaran Aktivitas Lain'],
            ['name' => 'pembayaran-aktivitas-lain-create', 'description' => 'Tambah Pembayaran Aktivitas Lain'],
            ['name' => 'pembayaran-aktivitas-lain-update', 'description' => 'Edit Pembayaran Aktivitas Lain'],
            ['name' => 'pembayaran-aktivitas-lain-delete', 'description' => 'Hapus Pembayaran Aktivitas Lain'],
            ['name' => 'pembayaran-aktivitas-lain-approve', 'description' => 'Approve Pembayaran Aktivitas Lain'],
        ];

        $createdPermissions = [];
        foreach ($permissions as $permission) {
            $perm = Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
            $createdPermissions[] = $perm->id;
        }

        // Assign all permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            // Sync permissions to role (using permission_role pivot table)
            $adminRole->permissions()->syncWithoutDetaching($createdPermissions);
            $this->command->info('Permissions assigned to admin role!');
        }

        $this->command->info('Pembayaran Aktivitas Lain permissions created successfully!');
    }
}
