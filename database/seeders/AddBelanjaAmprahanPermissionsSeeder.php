<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddBelanjaAmprahanPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'belanja-amprahan-view',
            'belanja-amprahan-create',
            'belanja-amprahan-update',
            'belanja-amprahan-delete',
        ];

        $createdCount = 0;
        $existingCount = 0;

        foreach ($permissions as $permissionName) {
            $exists = DB::table('permissions')
                ->where('name', $permissionName)
                ->exists();

            if (!$exists) {
                DB::table('permissions')->insert([
                    'name' => $permissionName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $createdCount++;
            } else {
                $existingCount++;
            }
        }

        $this->command->info("Belanja Amprahan permissions created: {$createdCount}, existing: {$existingCount}");
        $this->command->info("Silakan atur permission untuk user melalui Menu Master User > Edit User");
    }
}
