<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;

class AddVendorInvoicePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vendor Invoice permissions
        $permissions = [
            [
                'name' => 'vendor-invoice-view',
                'description' => 'Melihat daftar invoice vendor'
            ],
            [
                'name' => 'vendor-invoice-create',
                'description' => 'Menambah data invoice vendor'
            ],
            [
                'name' => 'vendor-invoice-edit',
                'description' => 'Mengedit data invoice vendor'
            ],
            [
                'name' => 'vendor-invoice-delete',
                'description' => 'Menghapus data invoice vendor'
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

        $this->command->info('Vendor Invoice permissions created and assigned to admins successfully!');
    }
}
