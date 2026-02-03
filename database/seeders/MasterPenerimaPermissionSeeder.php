<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class MasterPenerimaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'master-penerima-create',
            'master-penerima-delete',
            'master-penerima-update',
            'master-penerima-view',
        ];

        foreach ($permissions as $permission) {
            // Check if permission already exists
            $exists = Permission::where('name', $permission)->exists();
            
            if (!$exists) {
                Permission::create([
                    'name' => $permission,
                    'description' => 'Master penerima permission'
                ]);
            }
        }

        // Assign permissions to admin user
        $admin = \App\Models\User::where('username', 'admin')->first();
        if ($admin) {
            $permissionIds = Permission::whereIn('name', $permissions)->pluck('id');
            $admin->permissions()->syncWithoutDetaching($permissionIds);
            $this->command->info('Permissions assigned to admin user.');
        }
    }
}
