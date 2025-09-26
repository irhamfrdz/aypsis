<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PembayaranPranotaCatPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds to add pembayaran-pranota-cat permissions.
     */
    public function run(): void
    {
        $this->command->info('📝 Adding pembayaran-pranota-cat permissions...');

        $permissions = [
            ['id' => 1213, 'name' => 'pembayaran-pranota-cat-view', 'description' => 'View pembayaran pranota CAT'],
            ['id' => 1214, 'name' => 'pembayaran-pranota-cat-create', 'description' => 'Create pembayaran pranota CAT'],
            ['id' => 1215, 'name' => 'pembayaran-pranota-cat-update', 'description' => 'Update pembayaran pranota CAT'],
            ['id' => 1216, 'name' => 'pembayaran-pranota-cat-delete', 'description' => 'Delete pembayaran pranota CAT'],
            ['id' => 1217, 'name' => 'pembayaran-pranota-cat-print', 'description' => 'Print pembayaran pranota CAT'],
            ['id' => 1218, 'name' => 'pembayaran-pranota-cat-export', 'description' => 'Export pembayaran pranota CAT'],
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
                    $this->command->warn("⚠️  Permission already exists: {$permission['name']}");
                } else {
                    throw $e;
                }
            }
        }

        $this->command->info("📊 Summary: $inserted inserted, $skipped skipped");
    }
}
