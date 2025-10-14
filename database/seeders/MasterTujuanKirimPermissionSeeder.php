<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;

class MasterTujuanKirimPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Creating Master Tujuan Kirim Permissions...');

        // Define the permissions for master tujuan kirim
        $permissions = [
            'master-tujuan-kirim-view' => 'Lihat master tujuan kirim',
            'master-tujuan-kirim-create' => 'Tambah master tujuan kirim',
            'master-tujuan-kirim-update' => 'Edit master tujuan kirim',
            'master-tujuan-kirim-delete' => 'Hapus master tujuan kirim',
        ];

        // Create permissions
        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
            $this->command->info("✅ Permission created: {$name}");
        }

        // Assign all permissions to user_admin
        $userAdmin = User::where('username', 'user_admin')->first();
        if ($userAdmin) {
            $permissionIds = Permission::whereIn('name', array_keys($permissions))->pluck('id');

            // Get existing permissions and merge with new ones
            $existingPermissions = $userAdmin->permissions()->pluck('id');
            $allPermissions = $existingPermissions->merge($permissionIds)->unique();

            $userAdmin->permissions()->sync($allPermissions);

            $this->command->info("✅ All Master Tujuan Kirim permissions assigned to user_admin");
        } else {
            $this->command->warn("⚠️ user_admin not found. Please run UserAdminSeeder first.");
        }

        $this->command->info('🎉 Master Tujuan Kirim permissions seeding completed!');
    }
}
