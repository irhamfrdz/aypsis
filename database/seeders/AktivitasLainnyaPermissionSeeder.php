<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AktivitasLainnyaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📝 Adding Aktivitas Lain-lain permissions...');

        // Permissions untuk Aktivitas Lain-lain
        $aktivitasLainnyaPermissions = [
            ['name' => 'aktivitas-lainnya-view', 'description' => 'View aktivitas lain-lain'],
            ['name' => 'aktivitas-lainnya-create', 'description' => 'Create aktivitas lain-lain'],
            ['name' => 'aktivitas-lainnya-update', 'description' => 'Update aktivitas lain-lain'],
            ['name' => 'aktivitas-lainnya-delete', 'description' => 'Delete aktivitas lain-lain'],
            ['name' => 'aktivitas-lainnya-approve', 'description' => 'Approve aktivitas lain-lain'],
            ['name' => 'aktivitas-lainnya-print', 'description' => 'Print aktivitas lain-lain'],
            ['name' => 'aktivitas-lainnya-export', 'description' => 'Export aktivitas lain-lain'],
        ];

        // Permissions untuk Pembayaran Aktivitas Lain-lain
        $pembayaranAktivitasLainnyaPermissions = [
            ['name' => 'pembayaran-aktivitas-lainnya-view', 'description' => 'View pembayaran aktivitas lain-lain'],
            ['name' => 'pembayaran-aktivitas-lainnya-create', 'description' => 'Create pembayaran aktivitas lain-lain'],
            ['name' => 'pembayaran-aktivitas-lainnya-update', 'description' => 'Update pembayaran aktivitas lain-lain'],
            ['name' => 'pembayaran-aktivitas-lainnya-delete', 'description' => 'Delete pembayaran aktivitas lain-lain'],
            ['name' => 'pembayaran-aktivitas-lainnya-approve', 'description' => 'Approve pembayaran aktivitas lain-lain'],
            ['name' => 'pembayaran-aktivitas-lainnya-print', 'description' => 'Print pembayaran aktivitas lain-lain'],
            ['name' => 'pembayaran-aktivitas-lainnya-export', 'description' => 'Export pembayaran aktivitas lain-lain'],
        ];

        $allPermissions = array_merge($aktivitasLainnyaPermissions, $pembayaranAktivitasLainnyaPermissions);

        $inserted = 0;
        $skipped = 0;

        foreach ($allPermissions as $permission) {
            // Check if permission already exists
            $exists = DB::table('permissions')
                        ->where('name', $permission['name'])
                        ->exists();

            if (!$exists) {
                DB::table('permissions')->insert([
                    'name' => $permission['name'],
                    'description' => $permission['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $inserted++;
                $this->command->info("✅ Added permission: {$permission['name']}");
            } else {
                $skipped++;
                $this->command->warn("⚠️  Permission already exists: {$permission['name']}");
            }
        }

        $this->command->info("📊 Summary: {$inserted} permissions added, {$skipped} skipped");
    }
}
