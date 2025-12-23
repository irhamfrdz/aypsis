<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddPranotaRitKenekPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            ['name' => 'pranota-rit-kenek-view', 'description' => 'Pranota Rit Kenek - View'],
            ['name' => 'pranota-rit-kenek-create', 'description' => 'Pranota Rit Kenek - Create'],
            ['name' => 'pranota-rit-kenek-update', 'description' => 'Pranota Rit Kenek - Update'],
            ['name' => 'pranota-rit-kenek-delete', 'description' => 'Pranota Rit Kenek - Delete'],
            ['name' => 'pranota-rit-kenek-approve', 'description' => 'Pranota Rit Kenek - Approve'],
            ['name' => 'pranota-rit-kenek-print', 'description' => 'Pranota Rit Kenek - Print'],
            ['name' => 'pranota-rit-kenek-export', 'description' => 'Pranota Rit Kenek - Export'],
        ];

        foreach ($permissions as $perm) {
            // insert if not exists
            DB::table('permissions')->updateOrInsert([
                'name' => $perm['name']
            ], [
                'name' => $perm['name'],
                'description' => $perm['description'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->command->info('Ensured permission: ' . $perm['name']);
        }
    }
}
