<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permission;
use App\Models\User;

class CheckStockKontainerPermissions extends Command
{
    protected $signature = 'check:stock-kontainer-permissions';
    protected $description = 'Check stock kontainer permissions';

    public function handle()
    {
        $this->info('Checking Stock Kontainer Permissions...');

        // Check if stock kontainer permissions exist
        $permissions = Permission::where('name', 'like', '%stock-kontainer%')->get();

        $this->info("Found " . $permissions->count() . " stock kontainer permissions:");
        foreach ($permissions as $permission) {
            $this->info("- {$permission->name}: {$permission->description}");
        }

        // Check admin user permissions
        $admin = User::where('username', 'admin')->first();
        if ($admin) {
            $this->info("\nAdmin user stock kontainer permissions:");
            $adminPermissions = $admin->permissions()->where('name', 'like', '%stock-kontainer%')->get();
            foreach ($adminPermissions as $permission) {
                $this->info("- {$permission->name}");
            }
        }

        $this->info("\n✅ Permission check completed!");
    }
}
