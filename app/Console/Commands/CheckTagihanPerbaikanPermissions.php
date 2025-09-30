<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckTagihanPerbaikanPermissions extends Command
{
    protected $signature = 'check:tagihan-perbaikan-permissions {username}';
    protected $description = 'Check tagihan perbaikan kontainer permissions for a user';

    public function handle()
    {
        $username = $this->argument('username');

        $user = User::where('username', $username)->first();
        if (!$user) {
            $this->error("User {$username} not found!");
            return 1;
        }

        $this->info("User: {$user->name} (ID: {$user->id})");

        // Check different possible permission names
        $possiblePermissions = [
            'perbaikan-kontainer-view',
            'tagihan-perbaikan-kontainer-view',
            'tagihan-perbaikan-kontainer-index'
        ];

        foreach ($possiblePermissions as $perm) {
            $hasPermission = $user->can($perm);
            $this->info("Has '{$perm}' permission: " . ($hasPermission ? 'YES' : 'NO'));
        }

        $tagihanPerms = $user->permissions()->where('name', 'like', '%perbaikan%')->get();

        $this->info("\nPerbaikan-related permissions:");
        if ($tagihanPerms->isEmpty()) {
            $this->warn("No perbaikan permissions found!");
        } else {
            foreach ($tagihanPerms as $perm) {
                $this->line("- {$perm->name} (ID: {$perm->id})");
            }
        }

        // Check which one should work
        $sidebarCheck = $user->can('tagihan-perbaikan-kontainer-view');
        $oldCheck = $user->can('perbaikan-kontainer-view');

        $this->info("\n=== ANALYSIS ===");
        $this->info("Sidebar currently checks: 'tagihan-perbaikan-kontainer-view' = " . ($sidebarCheck ? 'YES' : 'NO'));
        $this->info("Old check was: 'perbaikan-kontainer-view' = " . ($oldCheck ? 'YES' : 'NO'));

        if ($sidebarCheck) {
            $this->info("✅ FIXED: Sidebar check now matches user permissions!");
        } else {
            $this->error("❌ Still not working - user missing correct permission");
        }

        return 0;
    }
}
