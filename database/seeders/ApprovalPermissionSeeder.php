<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;

class ApprovalPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Approval Tugas 1 (Supervisor/Manager)
            'approval-tugas-1.view',
            'approval-tugas-1.approve',

            // Approval Tugas 2 (General Manager)
            'approval-tugas-2.view',
            'approval-tugas-2.approve',
        ];

        foreach ($permissions as $permissionName) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permissionName],
                [
                    'name' => $permissionName,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        $this->command->info('Approval permissions berhasil ditambahkan!');
    }
}
