<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AddPergerakanKapalPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:add-pergerakan-kapal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add permissions for pergerakan kapal functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissions = [
            ['name' => 'pergerakan-kapal.view', 'description' => 'View pergerakan kapal'],
            ['name' => 'pergerakan-kapal.create', 'description' => 'Create pergerakan kapal'],
            ['name' => 'pergerakan-kapal.edit', 'description' => 'Edit pergerakan kapal'],
            ['name' => 'pergerakan-kapal.delete', 'description' => 'Delete pergerakan kapal'],
        ];

        $this->info('Creating pergerakan kapal permissions...');

        foreach ($permissions as $permissionData) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                ['description' => $permissionData['description']]
            );
            $this->info("✓ Permission '{$permissionData['name']}' created");
        }

        // Assign to admin user
        $admin = User::where('username', 'admin')->first();
        if (!$admin) {
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

                if (!$hasPermission) {
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

        $this->info('✅ Pergerakan Kapal permissions setup completed!');

        return Command::SUCCESS;
    }
}
