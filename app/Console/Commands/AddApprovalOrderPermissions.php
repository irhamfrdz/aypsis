<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddApprovalOrderPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:add-approval-order {--force : Force add permissions even if they exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add approval order permissions to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Adding Approval Order Permissions...');
        $this->newLine();

        // Define all approval-order permissions
        $permissions = [
            [
                'name' => 'approval-order-view',
                'description' => 'Melihat halaman approval order dan daftar order'
            ],
            [
                'name' => 'approval-order-create',
                'description' => 'Menambah term pembayaran untuk order baru'
            ],
            [
                'name' => 'approval-order-update',
                'description' => 'Mengedit dan memperbarui term pembayaran order'
            ],
            [
                'name' => 'approval-order-delete',
                'description' => 'Menghapus term pembayaran dari order'
            ],
            [
                'name' => 'approval-order-approve',
                'description' => 'Menyetujui dan approve order'
            ],
            [
                'name' => 'approval-order-reject',
                'description' => 'Menolak dan reject order'
            ],
            [
                'name' => 'approval-order-print',
                'description' => 'Mencetak dokumen approval order'
            ],
            [
                'name' => 'approval-order-export',
                'description' => 'Export data approval order ke Excel/PDF'
            ]
        ];

        $addedCount = 0;
        $existingCount = 0;
        $force = $this->option('force');

        foreach ($permissions as $permissionData) {
            // Check if permission already exists
            $exists = DB::table('permissions')
                ->where('name', $permissionData['name'])
                ->exists();
            
            if (!$exists || $force) {
                if ($exists && $force) {
                    // Update existing permission
                    DB::table('permissions')
                        ->where('name', $permissionData['name'])
                        ->update([
                            'description' => $permissionData['description'],
                            'updated_at' => now()
                        ]);
                    $this->line("🔄 Updated: <comment>{$permissionData['name']}</comment>");
                } else {
                    // Insert new permission
                    DB::table('permissions')->insert(array_merge($permissionData, [
                        'created_at' => now(),
                        'updated_at' => now()
                    ]));
                    $this->line("✅ Added: <info>{$permissionData['name']}</info>");
                }
                $addedCount++;
            } else {
                $this->line("⚪ Exists: <comment>{$permissionData['name']}</comment>");
                $existingCount++;
            }
        }

        $this->newLine();
        $this->info('📊 Summary:');
        $this->line("✅ Processed: <info>{$addedCount}</info>");
        $this->line("⚪ Skipped: <comment>{$existingCount}</comment>");
        $this->line("📊 Total permissions: <info>" . count($permissions) . "</info>");
        $this->newLine();

        if ($addedCount > 0) {
            $this->info('🎯 SUCCESS: Approval Order permissions have been processed!');
            $this->newLine();
            $this->line('<fg=yellow>Next steps:</>');
            $this->line('1. Go to Master User → Edit User');
            $this->line('2. Expand "Sistem Persetujuan" section');
            $this->line('3. Configure "Approval Order" permissions as needed');
            $this->line('4. Save the user permissions');
        } else {
            $this->info('ℹ️  All permissions already exist. Use --force to update.');
        }

        $this->newLine();
        $this->info('✨ Command completed successfully!');

        return Command::SUCCESS;
    }
}