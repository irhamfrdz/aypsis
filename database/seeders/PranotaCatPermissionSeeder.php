<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PranotaCatPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds to add pranota-cat permissions.
     */
    public function run(): void
    {
        $this->command->info('📝 Adding pranota-cat permissions...');

        $permissions = [
            // Pranota CAT permissions (dot notation)
            ['id' => 988, 'name' => 'pranota-cat.view', 'description' => 'Pranota CAT view'],
            ['id' => 989, 'name' => 'pranota-cat.create', 'description' => 'Pranota CAT create'],
            ['id' => 990, 'name' => 'pranota-cat.update', 'description' => 'Pranota CAT update'],
            ['id' => 991, 'name' => 'pranota-cat.delete', 'description' => 'Pranota CAT delete'],
            ['id' => 992, 'name' => 'pranota-cat.print', 'description' => 'Pranota CAT print'],
            ['id' => 993, 'name' => 'pranota-cat.export', 'description' => 'Pranota CAT export'],
            // Pranota CAT permissions (dash notation for middleware)
            ['id' => 994, 'name' => 'pranota-cat-view', 'description' => 'View pranota CAT'],
            ['id' => 995, 'name' => 'pranota-cat-create', 'description' => 'Create pranota CAT'],
            ['id' => 996, 'name' => 'pranota-cat-update', 'description' => 'Update pranota CAT'],
            ['id' => 997, 'name' => 'pranota-cat-delete', 'description' => 'Delete pranota CAT'],
            ['id' => 998, 'name' => 'pranota-cat-print', 'description' => 'Print pranota CAT'],
            ['id' => 999, 'name' => 'pranota-cat-export', 'description' => 'Export pranota CAT'],
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

        $this->command->info("✅ Pranota-cat permissions seeding completed! Inserted: {$inserted}, Skipped: {$skipped}");
    }
}
