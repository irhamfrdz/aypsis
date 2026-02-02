<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class MasterPricelistKanisirBanPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'master-pricelist-kanisir-ban-view', 'description' => 'Melihat daftar Pricelist Kanisir Ban'],
            ['name' => 'master-pricelist-kanisir-ban-create', 'description' => 'Membuat Pricelist Kanisir Ban baru'],
            ['name' => 'master-pricelist-kanisir-ban-update', 'description' => 'Mengupdate Pricelist Kanisir Ban'],
            ['name' => 'master-pricelist-kanisir-ban-delete', 'description' => 'Menghapus Pricelist Kanisir Ban'],
        ];

        foreach ($permissions as $perm) {
            $p = Permission::firstOrCreate(['name' => $perm['name']], ['description' => $perm['description']]);
            
            // Assign to admin role
            $adminRole = Role::where('name', 'admin')->first();
            if ($adminRole) {
                if (!$adminRole->permissions()->where('permission_id', $p->id)->exists()) {
                    $adminRole->permissions()->attach($p->id);
                }
            }
        }
    }
}
