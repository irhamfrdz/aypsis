<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;

class AddContainerTripPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Container Trip permissions
        $permissions = [
            [
                'name' => 'container-trip-view',
                'description' => 'Melihat daftar master sewa kontainer'
            ],
            [
                'name' => 'container-trip-create',
                'description' => 'Menambah data master sewa kontainer'
            ],
            [
                'name' => 'container-trip-edit',
                'description' => 'Mengedit data master sewa kontainer'
            ],
            [
                'name' => 'container-trip-delete',
                'description' => 'Menghapus data master sewa kontainer'
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                ['description' => $permissionData['description']]
            );
        }

        // Auto assign to admin users
        $adminUsers = User::where('role', 'admin')->get();
        $permissionIds = Permission::whereIn('name', array_column($permissions, 'name'))->pluck('id')->toArray();

        foreach ($adminUsers as $user) {
            $user->permissions()->syncWithoutDetaching($permissionIds);
        }

        $this->command->info('Container Trip permissions created and assigned to admins successfully!');
    }
}
