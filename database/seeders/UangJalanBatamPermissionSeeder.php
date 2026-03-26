<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UangJalanBatamPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'uang-jalan-batam-view',
            'uang-jalan-batam-create',
            'uang-jalan-batam-update',
            'uang-jalan-batam-delete',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        // Assign to common admin accounts
        $adminUsers = User::whereIn('username', ['admin', 'administrator', 'superadmin'])->get();
        
        foreach ($adminUsers as $admin) {
            $permissionIds = Permission::whereIn('name', $permissions)->pluck('id')->toArray();
            $admin->permissions()->syncWithoutDetaching($permissionIds);
        }
    }
}
