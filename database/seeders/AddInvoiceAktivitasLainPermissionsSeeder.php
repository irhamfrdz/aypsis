<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddInvoiceAktivitasLainPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Array of permissions to create
        $permissions = [
            'invoice-aktivitas-lain-view',
            'invoice-aktivitas-lain-create',
            'invoice-aktivitas-lain-update',
            'invoice-aktivitas-lain-delete',
        ];

        $createdCount = 0;
        $existingCount = 0;

        // Create permissions if they don't exist
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

        $this->command->info("Invoice Aktivitas Lain permissions created: {$createdCount}, existing: {$existingCount}");
        $this->command->info("Silakan atur permission untuk user melalui Menu Master User > Edit User");
    }
}
