<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PranotaKontainerSewaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds to add pranota-kontainer-sewa permissions.
     */
    public function run(): void
    {
        $this->command->info('📝 Adding pranota-kontainer-sewa permissions...');

        $permissions = [
            // Pranota Kontainer Sewa permissions (dot notation)
            ['id' => 1200, 'name' => 'pranota-kontainer-sewa.view', 'description' => 'Pranota Kontainer Sewa view'],
            ['id' => 1201, 'name' => 'pranota-kontainer-sewa.create', 'description' => 'Pranota Kontainer Sewa create'],
            ['id' => 1202, 'name' => 'pranota-kontainer-sewa.update', 'description' => 'Pranota Kontainer Sewa update'],
            ['id' => 1203, 'name' => 'pranota-kontainer-sewa.delete', 'description' => 'Pranota Kontainer Sewa delete'],
            ['id' => 1204, 'name' => 'pranota-kontainer-sewa.print', 'description' => 'Pranota Kontainer Sewa print'],
            ['id' => 1205, 'name' => 'pranota-kontainer-sewa.export', 'description' => 'Pranota Kontainer Sewa export'],
            // Pranota Kontainer Sewa permissions (dash notation for middleware)
            ['id' => 1206, 'name' => 'pranota-kontainer-sewa-view', 'description' => 'View pranota kontainer sewa'],
            ['id' => 1207, 'name' => 'pranota-kontainer-sewa-create', 'description' => 'Create pranota kontainer sewa'],
            ['id' => 1208, 'name' => 'pranota-kontainer-sewa-update', 'description' => 'Update pranota kontainer sewa'],
            ['id' => 1209, 'name' => 'pranota-kontainer-sewa-delete', 'description' => 'Delete pranota kontainer sewa'],
            ['id' => 1210, 'name' => 'pranota-kontainer-sewa-print', 'description' => 'Print pranota kontainer sewa'],
            ['id' => 1211, 'name' => 'pranota-kontainer-sewa-export', 'description' => 'Export pranota kontainer sewa'],
        ];

        $inserted = 0;
        $skipped = 0;

        foreach ($permissions as $permission) {
            try {
                DB::table('permissions')->insert([
                    'id' => $permission['id'],
                    'name' => $permission['name'],
                    'description' => $permission['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $inserted++;
                $this->command->info("✅ Added permission: {$permission['name']}");
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() == 23000) { // Duplicate entry error
                    $skipped++;
                    $this->command->info("⏭️  Skipped existing permission: {$permission['name']}");
                } else {
                    throw $e;
                }
            }
        }

        $this->command->info("✅ Pranota-kontainer-sewa permissions seeding completed! Inserted: {$inserted}, Skipped: {$skipped}");
    }
}
