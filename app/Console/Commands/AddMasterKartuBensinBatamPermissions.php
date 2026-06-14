<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddMasterKartuBensinBatamPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:add-kartu-bensin-batam';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add permissions for Master Kartu Bensin Batam functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissions = [
            ['name' => 'master-kartu-bensin-batam-view', 'description' => 'View Master Kartu Bensin Batam'],
            ['name' => 'master-kartu-bensin-batam-create', 'description' => 'Create Master Kartu Bensin Batam'],
            ['name' => 'master-kartu-bensin-batam-edit', 'description' => 'Edit Master Kartu Bensin Batam'],
            ['name' => 'master-kartu-bensin-batam-delete', 'description' => 'Delete Master Kartu Bensin Batam'],
        ];

        $this->info('Creating Master Kartu Bensin Batam permissions...');

        foreach ($permissions as $permissionData) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                ['description' => $permissionData['description']]
            );
            $this->info("✓ Permission '{$permissionData['name']}' created");
        }

        // Assign to admin user
        $admin = User::where('username', 'admin')->first();
        if (! $admin) {
            // Try to find user with role 'admin'
            $admin = User::where('role', 'admin')->first();
        }

        if ($admin) {
            foreach ($permissions as $permissionData) {
                $permission = Permission::where('name', $permissionData['name'])->first();

                // Check if user already has this permission
                $hasPermission = DB::table('user_permissions')
                    ->where('user_id', $admin->id)
                    ->where('permission_id', $permission->id)
                    ->exists();

                if (! $hasPermission) {
                    DB::table('user_permissions')->insert([
                        'user_id' => $admin->id,
                        'permission_id' => $permission->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            $this->info('✓ All permissions assigned to admin user');
        } else {
            $this->warn('⚠ Admin user not found');
        }

        $this->info('✅ Master Kartu Bensin Batam permissions setup completed!');

        return Command::SUCCESS;
    }
}
