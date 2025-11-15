<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;

class ApprovalSuratJalanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Seeder Approval Surat Jalan ===');
        $this->command->newLine();

        // Daftar permissions untuk approval surat jalan
        $permissions = [
            // Basic permissions
            [
                'name' => 'approval-surat-jalan-view',
                'description' => 'View Approval Surat Jalan',
            ],
            [
                'name' => 'approval-surat-jalan-approve',
                'description' => 'Approve Surat Jalan',
            ],
            [
                'name' => 'approval-surat-jalan-reject',
                'description' => 'Reject Surat Jalan',
            ],
            [
                'name' => 'approval-surat-jalan-print',
                'description' => 'Print Approval Surat Jalan',
            ],
            [
                'name' => 'approval-surat-jalan-export',
                'description' => 'Export Approval Surat Jalan',
            ],

            // Level-based approval permissions
            [
                'name' => 'surat-jalan-approval-level-1-view',
                'description' => 'View surat jalan yang perlu approval level 1',
            ],
            [
                'name' => 'surat-jalan-approval-level-1-approve',
                'description' => 'Approve surat jalan level 1',
            ],
            [
                'name' => 'surat-jalan-approval-level-2-view',
                'description' => 'View surat jalan yang perlu approval level 2',
            ],
            [
                'name' => 'surat-jalan-approval-level-2-approve',
                'description' => 'Approve surat jalan level 2',
            ],

            // Dashboard permission
            [
                'name' => 'surat-jalan-approval-dashboard',
                'description' => 'Access to surat jalan approval dashboard',
            ],
        ];

        // Create or update permissions
        $this->command->info('Creating/Updating Permissions:');
        $created = 0;
        $existing = 0;

        foreach ($permissions as $permData) {
            $permission = Permission::firstOrCreate(
                ['name' => $permData['name']],
                $permData
            );
            
            if ($permission->wasRecentlyCreated) {
                $this->command->info("✓ Created: {$permData['name']}");
                $created++;
            } else {
                $this->command->comment("- Already exists: {$permData['name']}");
                $existing++;
            }
        }

        $this->command->newLine();
        $this->command->info('=== Seeder Completed Successfully ===');
        $this->command->info("Total Permissions: " . count($permissions));
        $this->command->info("Created: {$created}");
        $this->command->info("Already Exist: {$existing}");
    }
}
